<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use ZipArchive;

class AdminCompanyController extends Controller
{
    public static function deleteCompanyFiles(Company $company): void
    {
        foreach(['photo_pjbu_path','npwp_bu_path','nib_file_path','ktp_pjbu_path','npwp_pjbu_path'] as $col){
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
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'bentuk' => ['nullable','string','max:30'],
            'jenis' => ['nullable','string','max:30'],
            'kualifikasi' => ['nullable','string','max:30'],
            'penanggung_jawab' => ['nullable','string','max:255'],
            'npwp' => ['nullable','string','max:32','unique:companies,npwp'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:500'],
            'province_code' => ['nullable','string','max:10'],
            'province_name' => ['nullable','string','max:100'],
            'city_code' => ['nullable','string','max:10'],
            'city_name' => ['nullable','string','max:100'],
            'postal_code' => ['nullable','string','max:10'],
            'photo_pjbu' => ['nullable','image','mimes:png,jpg,jpeg','max:3072'],
            'npwp_bu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'nib_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'ktp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'npwp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
        ]);
        $paths = $this->storeDocs($request);
        $company = Company::create(array_merge($data, $paths));
        return redirect()->route('admin.companies.edit',$company)->with('success','Perusahaan dibuat');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'bentuk' => ['nullable','string','max:30'],
            'jenis' => ['nullable','string','max:30'],
            'kualifikasi' => ['nullable','string','max:30'],
            'penanggung_jawab' => ['nullable','string','max:255'],
            'npwp' => ['nullable','string','max:32', Rule::unique('companies','npwp')->ignore($company->id)],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:500'],
            'province_code' => ['nullable','string','max:10'],
            'province_name' => ['nullable','string','max:100'],
            'city_code' => ['nullable','string','max:10'],
            'city_name' => ['nullable','string','max:100'],
            'postal_code' => ['nullable','string','max:10'],
            'photo_pjbu' => ['nullable','image','mimes:png,jpg,jpeg','max:3072'],
            'npwp_bu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'nib_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'ktp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
            'npwp_pjbu_file' => ['nullable','mimetypes:application/pdf','max:10240'],
        ]);
        $paths = $this->storeDocs($request, $company);
        $company->update(array_merge($data,$paths));
        return back()->with('success','Perusahaan diperbarui');
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
            'npwp-bu' => $company->npwp_bu_path,
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
}
