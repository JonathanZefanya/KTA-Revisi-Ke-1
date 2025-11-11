<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Http\Controllers\AdminCompanyController; use App\Models\Invoice; use App\Models\PaymentRate; use Illuminate\Support\Facades\Log; use Illuminate\Support\Facades\Mail; use App\Mail\InvoiceCreated;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users with simple search & pagination.
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $status = $request->get('status'); // approved / pending
    $users = User::with(['companies'])
            ->when($q, function($query) use ($q){
                $query->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('email','like',"%$q%")
                      ->orWhere('phone','like',"%$q%")
                      ;
                });
            })
            ->when($status === 'approved', fn($q2)=>$q2->whereNotNull('approved_at'))
            ->when($status === 'pending', fn($q2)=>$q2->whereNull('approved_at'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users','q','status'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function show(User $user)
    {
        $user->load('companies');
        return view('admin.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'phone' => ['nullable','string','max:30'],
            'password' => ['required','string','min:6'],
            'approve' => ['nullable','boolean']
        ]);
        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'phone'=>$data['phone'] ?? null,
            
            'password'=>$data['password'],
            'approved_at'=> !empty($data['approve']) ? now() : null,
            'email_verified_at'=> !empty($data['approve']) ? now() : null,
        ]);
        return redirect()->route('admin.users.edit',$user)->with('success','User dibuat');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone' => ['nullable','string','max:30'],
            'password' => ['nullable','string','min:6'],
            'approve' => ['nullable','boolean']
        ]);
        $payload = [
            'name'=>$data['name'],
            'email'=>$data['email'],
            'phone'=>$data['phone'] ?? null,
            
        ];
        if(!empty($data['password'])) $payload['password'] = $data['password'];
        if(!empty($data['approve']) && !$user->approved_at){
            $payload['approved_at'] = now();
            if(!$user->email_verified_at) $payload['email_verified_at'] = now();
        }
        $user->update($payload);
        return back()->with('success','User diperbarui');
    }

    public function destroy(User $user)
    {
        // collect companies before deleting user
        $companies = $user->companies()->get();
        $user->delete();
        foreach($companies as $company){
            if($company->users()->count() === 0){
                AdminCompanyController::deleteCompanyFiles($company);
                $company->delete();
            }
        }
        return redirect()->route('admin.users.index')->with('success','User dihapus');
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        if($ids){
            User::whereIn('id',$ids)->whereNull('approved_at')->update(['approved_at'=>now(),'email_verified_at'=>now()]);
        }
        return back()->with('success','User diproses');
    }

    public function approve(User $user)
    {
        if(!$user->approved_at){
            $user->approved_at = now();
            if(!$user->email_verified_at) $user->email_verified_at = now();
            $user->save();
            // Create registration invoice if not exists
            try {
                $company = $user->companies()->first();
                if(!$company){ Log::warning('Approve user: no company found for user '.$user->id); }
                if($company){
                    $existing = Invoice::where('user_id',$user->id)->where('type','registration')->first();
                    if($existing){ Log::info('Approve user: invoice already exists for user '.$user->id.' invoice '.$existing->id); }
                    if(!$existing){
                        $rate = PaymentRate::where('jenis',$company->jenis)->where('kualifikasi',$company->kualifikasi)->first();
                        if(!$rate){ Log::warning('Approve user: no rate found jenis='.$company->jenis.' kual='.$company->kualifikasi.' user '.$user->id); }
                        $amount = $rate?->amount ?? 0;
                        $invoice = Invoice::create([
                            'number' => Invoice::generateNumber(),
                            'user_id' => $user->id,
                            'company_id' => $company->id,
                            'type' => 'registration',
                            'amount' => $amount,
                            'issued_date' => today(),
                            'due_date' => today()->addDays(14),
                            'status' => 'unpaid',
                            'meta' => [
                                'company_name' => $company->name,
                                'jenis' => $company->jenis,
                                'kualifikasi' => $company->kualifikasi,
                            ],
                        ]);
                        try { Mail::to($user->email)->queue(new InvoiceCreated($invoice)); } catch(\Throwable $ex){ Log::error('Mail invoice create failed: '.$ex->getMessage()); }
                        Log::info('Approve user: invoice created id='.$invoice->id.' user='.$user->id);
                    }
                }
            } catch(\Throwable $e){ Log::error('Invoice create failed: '.$e->getMessage()); }
        }
        return back()->with('success','User disetujui');
    }

    public function generateRegistrationInvoice(User $user)
    {
        $company=$user->companies()->first();
        if(!$company) return back()->with('error','User belum memiliki perusahaan');
        $existing=Invoice::where('user_id',$user->id)->where('type','registration')->first();
        if($existing) return back()->with('info','Invoice registrasi sudah ada');
        $rate=PaymentRate::where('jenis',$company->jenis)->where('kualifikasi',$company->kualifikasi)->first();
        $amount=$rate?->amount ?? 0;
        $invoice=Invoice::create([
            'number'=>Invoice::generateNumber(),
            'user_id'=>$user->id,
            'company_id'=>$company->id,
            'type'=>'registration',
            'amount'=>$amount,
            'issued_date'=>today(),
            'due_date'=>today()->addDays(14),
            'status'=>'unpaid',
            'meta'=>[
                'company_name'=>$company->name,
                'jenis'=>$company->jenis,
                'kualifikasi'=>$company->kualifikasi,
            ],
        ]);
        try { Mail::to($user->email)->queue(new InvoiceCreated($invoice)); } catch(\Throwable $ex){ Log::error('Mail invoice create manual failed: '.$ex->getMessage()); }
        return back()->with('success','Invoice registrasi dibuat');
    }

    public function export(Request $request)
    {
        $q = trim($request->get('q', ''));
        $status = $request->get('status');
        
        $query = User::with(['companies'])
            ->when($q, function($query) use ($q){
                $query->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('email','like',"%$q%")
                      ->orWhere('phone','like',"%$q%");
                });
            })
            ->when($status === 'approved', fn($q2)=>$q2->whereNotNull('approved_at'))
            ->when($status === 'pending', fn($q2)=>$q2->whereNull('approved_at'))
            ->latest();

        $filename = 'data-users-' . date('Y-m-d-His') . '.xlsx';
        return Excel::download(new UsersExport($query), $filename);
    }
}
