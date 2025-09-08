<?php
namespace App\Http\Controllers; use Illuminate\Http\Request; use App\Models\Invoice; use Illuminate\Support\Facades\Log; use Illuminate\Support\Facades\DB;
class AdminInvoiceController extends Controller {
    public function index(Request $r){ $status=$r->get('status'); $q=Invoice::with('user')->latest(); if($status){ $q->where('status',$status);} $invoices=$q->paginate(40)->withQueryString(); return view('admin.invoices.index',compact('invoices','status')); }
    public function show(Invoice $invoice){ $invoice->load('user','company'); return view('admin.invoices.show',compact('invoice')); }
    public function verify(Request $r, Invoice $invoice){ $data=$r->validate(['action'=>['required','in:approve,reject'],'note'=>['nullable','string','max:255']]); if($invoice->status!==Invoice::STATUS_AWAITING){ return back()->with('error','Status tidak valid untuk diverifikasi'); }
        if($data['action']==='approve'){ $invoice->status=Invoice::STATUS_PAID; $invoice->paid_at=now(); }
        else { $invoice->status=Invoice::STATUS_REJECTED; }
        $invoice->verified_by=$r->user('admin')->id; $invoice->verified_at=now(); $invoice->verification_note=$data['note']??null; $invoice->save();
        // Issue membership card if first successful payment and user approved
        if($invoice->status===Invoice::STATUS_PAID){
            try{ $user=$invoice->user; if($user && $user->approved_at){ $user->issueMembershipCardIfNeeded(); } }catch(\Throwable $e){ Log::error('Issue membership card failed: '.$e->getMessage()); }
        }
        try{ \Illuminate\Support\Facades\Mail::to($invoice->user->email)->queue(new \App\Mail\InvoicePaymentVerified($invoice)); }catch(\Throwable $e){ Log::error('Mail verify failed: '.$e->getMessage()); }
        return back()->with('success','Invoice diverifikasi'); }
}
