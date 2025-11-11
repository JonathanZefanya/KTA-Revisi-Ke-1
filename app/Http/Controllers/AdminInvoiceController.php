<?php
namespace App\Http\Controllers; use Illuminate\Http\Request; use App\Models\Invoice; use Illuminate\Support\Facades\Log; use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;
class AdminInvoiceController extends Controller {
    public function index(Request $r){ $status=$r->get('status'); $q=Invoice::with('user')->latest(); if($status){ $q->where('status',$status);} $invoices=$q->paginate(40)->withQueryString(); return view('admin.invoices.index',compact('invoices','status')); }
    public function create(){
        $users = \App\Models\User::orderBy('name')->get(['id','name','email']);
        $companies = \App\Models\Company::orderBy('name')->get(['id','name']);
        $banks = \App\Models\BankAccount::orderBy('sort')->get(['id','bank_name','account_number','account_name']);
        // Map user -> company (one company per user enforced)
        $userCompanyMap = \Illuminate\Support\Facades\DB::table('company_user')->pluck('company_id','user_id');
        return view('admin.invoices.create', compact('users','companies','banks','userCompanyMap'));
    }
    public function store(Request $r){
        $data = $r->validate([
            'user_id' => ['required','exists:users,id'],
            'company_id' => ['nullable','exists:companies,id'],
            'type' => ['required','in:registration,renewal,other'],
            'amount' => ['required','numeric','min:0'],
            'currency' => ['nullable','string','max:10'],
            'issued_date' => ['nullable','date'],
            'due_date' => ['nullable','date','after_or_equal:today'],
            'bank_account_id' => ['nullable','exists:bank_accounts,id'],
            'note' => ['nullable','string','max:255'],
            'mark_paid' => ['nullable','boolean'],
        ]);
        $inv = new Invoice();
        $inv->fill([
            'number' => Invoice::generateNumber(),
            'user_id' => $data['user_id'],
            'company_id' => $data['company_id'] ?? null,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'IDR',
            'issued_date' => $data['issued_date'] ?? now()->toDateString(),
            'due_date' => $data['due_date'] ?? now()->addDays(7)->toDateString(),
            'status' => $r->boolean('mark_paid') ? Invoice::STATUS_PAID : Invoice::STATUS_UNPAID,
            'paid_at' => $r->boolean('mark_paid') ? now() : null,
            'meta' => ['note' => $data['note'] ?? null],
        ]);
        $inv->save();
        // If marked paid, optionally trigger downstream effects similar to approve
        if($inv->status === Invoice::STATUS_PAID){
            try{
                $user=$inv->user; if($user && $user->approved_at){ $user->issueMembershipCardIfNeeded(); }
            }catch(\Throwable $e){ Log::error('Auto-issue after admin create paid invoice failed: '.$e->getMessage()); }
        }
        return redirect()->route('admin.invoices.show',$inv)->with('success','Invoice berhasil dibuat');
    }
    public function show(Invoice $invoice){ $invoice->load('user','company'); return view('admin.invoices.show',compact('invoice')); }
    public function verify(Request $r, Invoice $invoice){ $data=$r->validate(['action'=>['required','in:approve,reject'],'note'=>['nullable','string','max:255']]); if($invoice->status!==Invoice::STATUS_AWAITING){ return back()->with('error','Status tidak valid untuk diverifikasi'); }
        if($data['action']==='approve'){ $invoice->status=Invoice::STATUS_PAID; $invoice->paid_at=now(); }
        else { $invoice->status=Invoice::STATUS_REJECTED; }
        $invoice->verified_by=$r->user('admin')->id; $invoice->verified_at=now(); $invoice->verification_note=$data['note']??null; $invoice->save();
        // Issue membership card if first successful payment and user approved
        if($invoice->status===Invoice::STATUS_PAID){
            try{
                $user=$invoice->user;
                if($user && $user->approved_at){
                    if($invoice->type==='registration'){
                        $user->issueMembershipCardIfNeeded();
                    } elseif($invoice->type==='renewal') {
                        // find linked renewal and apply extension
                        $renewal=\App\Models\KtaRenewal::where('invoice_id',$invoice->id)->first();
                        if($renewal && !$renewal->isProcessed()){
                            // Extend only if new_expires_at is greater
                            $currentExp = $user->membership_card_expires_at ? \Carbon\Carbon::parse($user->membership_card_expires_at) : null;
                            $renewalNew = $renewal->new_expires_at ? \Carbon\Carbon::parse($renewal->new_expires_at) : null;
                            if($renewalNew && (!$currentExp || $renewalNew->gt($currentExp))){
                                $user->forceFill(['membership_card_expires_at'=>$renewalNew])->save();
                            }
                            $renewal->processed_at=now();
                            $renewal->save();
                        }
                    }
                }
            }catch(\Throwable $e){ Log::error('Process membership/renewal failed: '.$e->getMessage()); }
        }
        try{ \Illuminate\Support\Facades\Mail::to($invoice->user->email)->queue(new \App\Mail\InvoicePaymentVerified($invoice)); }catch(\Throwable $e){ Log::error('Mail verify failed: '.$e->getMessage()); }
        return back()->with('success','Invoice diverifikasi'); }
    
    public function export(Request $request)
    {
        $status = $request->get('status');
        
        $query = Invoice::with(['user', 'user.companies'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest();

        $filename = 'data-invoices-' . date('Y-m-d-His') . '.xlsx';
        return Excel::download(new InvoicesExport($query), $filename);
    }
}
