<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CompaniesExport;
use App\Exports\CompaniesImportTemplate;
use App\Imports\CompaniesImport;

class AdminCompanyController extends Controller
{
    public static function deleteCompanyFiles(Company $company): void
    {
        foreach(['photo_pjbu_path','npwp_bu_path','akte_bu_path','nib_file_path','ktp_pjbu_path','npwp_pjbu_path'] as $col){
            if($company->$col && Storage::disk('public')->exists($company->$col)){
                Storage::disk('public')->delete($company->$col);
            }
        }
    }
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        $jenis = $request->get('jenis');
        $kualifikasi = $request->get('kualifikasi');
        $companies = Company::query()
            ->when($q, function($query) use ($q){
                $query->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('npwp','like',"%$q%")
                      ->orWhere('penanggung_jawab','like',"%$q%")
                      ->orWhere('email','like',"%$q%")
                      ->orWhere('phone','like',"%$q%");
                });
            })
            ->when($jenis, fn($x)=>$x->where('jenis',$jenis))
            ->when($kualifikasi, fn($x)=>$x->where('kualifikasi',$kualifikasi))
            ->latest()
            ->paginate(25)
            ->withQueryString();
        return view('admin.companies.index', compact('companies','q','jenis','kualifikasi'));
    }

    public function show(Company $company)
    {
        $company->load('users');
        return view('admin.companies.show', compact('company'));
    }

    public function create()
    {
        // Limit list to recent users for selection; admins can also create a new user inline
        $users = User::orderByDesc('created_at')->limit(100)->get(['id','name','email']);
        return view('admin.companies.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'bentuk' => ['nullable','string','max:30'],
            'jenis' => ['nullable','string','max:30'],
            'kualifikasi' => ['nullable','string','max:30'],
            'membership_type' => ['nullable','in:AB,ALB'],
            // penanggung_jawab will be taken from selected/created user
            'penanggung_jawab' => ['nullable','string','max:255'],
            'npwp' => ['nullable','string','max:32','unique:companies,npwp'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:500'],
            'asphalt_mixing_plant_address' => ['nullable','string','max:500'],
            'concrete_batching_plant_address' => ['nullable','string','max:500'],
            'province_code' => ['nullable','string','max:10'],
            'province_name' => ['nullable','string','max:100'],
            'city_code' => ['nullable','string','max:10'],
            'city_name' => ['nullable','string','max:100'],
            'postal_code' => ['nullable','string','max:10'],
            'photo_pjbu' => ['nullable','image','mimes:png,jpg,jpeg','max:3072'],
            'npwp_bu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'akte_bu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'nib_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'ktp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'npwp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            // user binding/creation
            'user_mode' => ['required','in:new,existing'],
            'existing_user_id' => ['nullable','integer','exists:users,id'],
            'user_name' => ['nullable','string','max:255'],
            'user_email' => ['nullable','email','max:255','unique:users,email'],
            'user_phone' => ['nullable','string','max:30'],
            'user_password' => ['nullable','string','min:8','confirmed'], // expects user_password_confirmation
        ]);
        // Resolve or create user based on mode
        if($data['user_mode'] === 'existing'){
            if(empty($data['existing_user_id'])){
                return back()->withInput()->with('error','Pilih pengguna yang akan menjadi penanggung jawab.');
            }
            $user = User::find($data['existing_user_id']);
        } else {
            // Validate required fields for new user
            $request->validate([
                'user_name' => ['required','string','max:255'],
                'user_email' => ['required','email','max:255','unique:users,email'],
                'user_password' => ['required','string','min:8','confirmed'],
            ]);
            $user = User::create([
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'phone' => $data['user_phone'] ?? null,
                'password' => Hash::make($data['user_password']),
            ]);
        }

        // Enforce: one user can only belong to one company
        if($user->companies()->exists()){
            return back()->withInput()->with('error','Pengguna ini sudah terhubung ke badan usaha lain. Satu pengguna hanya boleh memiliki satu badan usaha.');
        }

        // Prepare company payload; force PJBU name from user
        $paths = $this->storeDocs($request);
        $payload = array_merge($data, $paths);
        $payload['penanggung_jawab'] = $user->name;
        unset($payload['user_mode'],$payload['existing_user_id'],$payload['user_name'],$payload['user_email'],$payload['user_phone'],$payload['user_password'],$payload['user_password_confirmation']);

        $company = Company::create($payload);
        $company->users()->attach($user->id);

        return redirect()->route('admin.companies.edit',$company)->with('success','Perusahaan dibuat dan ditautkan ke pengguna: '.$user->name);
    }

    public function edit(Company $company)
    {
        $selectedUserId = optional($company->users()->first())->id;
        $users = User::orderByDesc('created_at')->limit(100)->get(['id','name','email']);
        if($selectedUserId && !$users->pluck('id')->contains($selectedUserId)){
            $current = User::where('id',$selectedUserId)->get(['id','name','email']);
            $users = $current->concat($users); // ensure selected user is present at top
        }
        return view('admin.companies.edit', compact('company','users','selectedUserId'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'bentuk' => ['nullable','string','max:30'],
            'jenis' => ['nullable','string','max:30'],
            'kualifikasi' => ['nullable','string','max:30'],
            'membership_type' => ['nullable','in:AB,ALB'],
            'npwp' => ['nullable','string','max:32', Rule::unique('companies','npwp')->ignore($company->id)],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:500'],
            'asphalt_mixing_plant_address' => ['nullable','string','max:500'],
            'concrete_batching_plant_address' => ['nullable','string','max:500'],
            'province_code' => ['nullable','string','max:10'],
            'province_name' => ['nullable','string','max:100'],
            'city_code' => ['nullable','string','max:10'],
            'city_name' => ['nullable','string','max:100'],
            'postal_code' => ['nullable','string','max:10'],
            'photo_pjbu' => ['nullable','image','mimes:png,jpg,jpeg','max:3072'],
            'npwp_bu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'akte_bu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'nib_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'ktp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'npwp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            // user reassignment (edit uses existing only)
            'existing_user_id' => ['nullable','integer','exists:users,id'],
        ]);
        // Do not allow direct penanggung_jawab override via form; only via user reassignment
        unset($data['penanggung_jawab']);

        // Check if membership_type or province_code is changing
        $membershipTypeChanged = isset($data['membership_type']) && $data['membership_type'] !== $company->membership_type;
        $provinceChanged = isset($data['province_code']) && $data['province_code'] !== $company->province_code;
        $needsKtaRegeneration = $membershipTypeChanged || $provinceChanged;

        // Handle reassignment if a user is selected
        if($request->filled('existing_user_id')){
            $newUser = User::find($request->integer('existing_user_id'));
            $currentUser = $company->users()->first();
            if(!$currentUser || $currentUser->id !== $newUser->id){
                // enforce: new user not linked to any other company
                if($newUser->companies()->where('companies.id','!=',$company->id)->exists()){
                    return back()->withInput()->with('error','Pengguna ini sudah terhubung ke badan usaha lain. Satu pengguna hanya boleh memiliki satu badan usaha.');
                }
                // swap relation
                $company->users()->sync([$newUser->id]);
                // auto set PJBU from new user
                $data['penanggung_jawab'] = $newUser->name;
            }
        }
        $paths = $this->storeDocs($request, $company);
        $company->update(array_merge($data,$paths));

        // Regenerate KTA number if membership type or province changed
        if($needsKtaRegeneration){
            $user = $company->users()->first();
            if($user){
                $user->regenerateKtaNumber();
            }
        }

        return back()->with('success','Perusahaan diperbarui' . ($needsKtaRegeneration ? ' dan nomor KTA diperbarui' : ''));
    }

    public function destroy(Company $company)
    {
    $company->users()->detach();
    self::deleteCompanyFiles($company);
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success','Perusahaan dihapus');
    }

    public function downloadAll(Company $company)
    {
        $files = [
            'foto-pjbu' => $company->photo_pjbu_path,
            'npwp_bu' => $company->npwp_bu_path,
            'akte-bu' => $company->akte_bu_path,
            'nib' => $company->nib_file_path,
            'ktp-pjbu' => $company->ktp_pjbu_path,
            'npwp-pjbu' => $company->npwp_pjbu_path,
        ];
        $zip = new ZipArchive();
        $zipName = 'dokumen-company-'.$company->id.'.zip';
        $tmpPath = storage_path('app/tmp/'.$zipName);
        if(!is_dir(dirname($tmpPath))) @mkdir(dirname($tmpPath),0777,true);
        if($zip->open($tmpPath, ZipArchive::CREATE|ZipArchive::OVERWRITE) === true){
            foreach($files as $label=>$rel){
                if($rel && Storage::disk('public')->exists($rel)){
                    $zip->addFile(Storage::disk('public')->path($rel), $label.'-'.basename($rel));
                }
            }
            $zip->close();
            return response()->download($tmpPath)->deleteFileAfterSend();
        }
        return back()->with('error','Gagal membuat arsip');
    }

    private function storeDocs(Request $request, ?Company $company = null): array
    {
        $out = [];
        $map = [
            'photo_pjbu' => 'photo_pjbu_path',
            'npwp_bu_file' => 'npwp_bu_path',
            'akte_bu_file' => 'akte_bu_path',
            'nib_file' => 'nib_file_path',
            'ktp_pjbu_file' => 'ktp_pjbu_path',
            'npwp_pjbu_file' => 'npwp_pjbu_path',
        ];
        foreach($map as $input=>$column){
            if($request->hasFile($input)){
                // optionally delete old
                if($company && $company->$column && Storage::disk('public')->exists($company->$column)){
                    Storage::disk('public')->delete($company->$column);
                }
                $out[$column] = $request->file($input)->store('uploads/company','public');
            }
        }
        return $out;
    }

    public function export(Request $request)
    {
        $q = trim($request->get('q',''));
        $jenis = $request->get('jenis');
        $kualifikasi = $request->get('kualifikasi');
        
        $query = Company::query()
            ->when($q, function($query) use ($q){
                $query->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('npwp','like',"%$q%")
                      ->orWhere('penanggung_jawab','like',"%$q%")
                      ->orWhere('email','like',"%$q%")
                      ->orWhere('phone','like',"%$q%");
                });
            })
            ->when($jenis, fn($x)=>$x->where('jenis',$jenis))
            ->when($kualifikasi, fn($x)=>$x->where('kualifikasi',$kualifikasi))
            ->latest();

        $filename = 'data-companies-' . date('Y-m-d-His') . '.xlsx';
        return Excel::download(new CompaniesExport($query), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120', // max 5MB
        ]);

        try {
            // Tingkatkan execution time untuk import besar
            set_time_limit(300); // 5 menit
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '512M');

            $import = new CompaniesImport();
            Excel::import($import, $request->file('file'));

            $message = "Import berhasil! {$import->getImported()} data diproses";
            if ($import->getSkipped() > 0) {
                $message .= ", {$import->getSkipped()} dilewati";
            }
            if (count($import->getErrors()) > 0) {
                $message .= ". Error: " . implode(', ', $import->getErrors());
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filename = 'template-import-companies.xlsx';
        return Excel::download(new CompaniesImportTemplate(), $filename);
    }
}
