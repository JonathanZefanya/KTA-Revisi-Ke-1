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
        $bulanBerakhir = $request->get('bulan_berakhir'); // format YYYY-MM
        $ktaStatus = $request->get('kta_status'); // 1=aktif, 0=tidak aktif
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
            ->when($bulanBerakhir, function($q) use ($bulanBerakhir){
            // format input di FE: YYYY-MM
            $q->whereYear('membership_card_expires_at', substr($bulanBerakhir, 0, 4))
              ->whereMonth('membership_card_expires_at', substr($bulanBerakhir, 5, 2));
            })
            ->when(isset($ktaStatus), function($q) use ($ktaStatus){
                $q->where('is_active', $ktaStatus);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users','q','status', 'bulanBerakhir', 'ktaStatus'));
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

    public function toggleActive(Request $request, User $user)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $user->is_active = $request->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Status user berhasil diubah',
            'is_active' => $user->is_active
        ]);
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();
            
            // 1. Delete all invoices related to this user
            Invoice::where('user_id', $user->id)->delete();
            
            // 2. Collect companies before deleting user
            $companies = $user->companies()->get();
            
            // 3. Detach user from companies
            $user->companies()->detach();
            
            // 4. Delete the user (this will cascade delete KTA data)
            $user->delete();
            
            // 5. Delete companies that have no more users
            foreach($companies as $company){
                if($company->users()->count() === 0){
                    AdminCompanyController::deleteCompanyFiles($company);
                    $company->delete();
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.users.index')->with('success','User beserta KTA, transaksi, dan perusahaan terkait berhasil dihapus');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete user error: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error','Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        if($ids){
            User::whereIn('id',$ids)->whereNull('approved_at')->update(['approved_at'=>now(),'email_verified_at'=>now()]);
        }
        return back()->with('success','User diproses');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada user yang dipilih');
        }

        try {
            DB::beginTransaction();
            
            $deletedCount = 0;
            foreach ($ids as $userId) {
                $user = User::find($userId);
                
                if ($user) {
                    // 1. Delete all invoices related to this user
                    Invoice::where('user_id', $user->id)->delete();
                    
                    // 2. Get companies related to this user
                    $companies = $user->companies;
                    
                    // 3. Detach user from companies
                    $user->companies()->detach();
                    
                    // 4. Delete companies that have no more users
                    foreach ($companies as $company) {
                        // Check if company has any other users
                        if ($company->users()->count() === 0) {
                            // Delete company files
                            AdminCompanyController::deleteCompanyFiles($company);
                            // Delete company
                            $company->delete();
                        }
                    }
                    
                    // 5. Delete the user (this will cascade delete KTA data)
                    $user->delete();
                    
                    $deletedCount++;
                }
            }
            
            DB::commit();
            
            return back()->with('success', "Berhasil menghapus {$deletedCount} user beserta KTA, transaksi, dan perusahaan terkait");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete users error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
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
        $bulanBerakhir = $request->get('bulan_berakhir'); // format YYYY-MM
        $ktaStatus = $request->get('kta_status'); // 1=aktif, 0=tidak aktif
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
            ->when($bulanBerakhir, function($q) use ($bulanBerakhir){
            // format input di FE: YYYY-MM
            $q->whereYear('membership_card_expires_at', substr($bulanBerakhir, 0, 4))
              ->whereMonth('membership_card_expires_at', substr($bulanBerakhir, 5, 2));
            })
            ->when(isset($ktaStatus), function($q) use ($ktaStatus){
                $q->where('is_active', $ktaStatus);
            })
            ->latest();

        $filename = 'data-users-' . date('Y-m-d-His') . '.xlsx';
        return Excel::download(new UsersExport($query), $filename);
    }

    /**
     * Search users for AJAX autocomplete (used in invoice creation)
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $users = User::with(['companies'])
            ->where(function($query) use ($q){
                $query->where('name', 'like', "%$q%")
                      ->orWhere('email', 'like', "%$q%")
                      ->orWhere('phone', 'like', "%$q%");
            })
            ->whereNotNull('approved_at')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company_id' => $user->companies->first()?->id,
                    'company_name' => $user->companies->first()?->name,
                ];
            });

        return response()->json($users);
    }
}
