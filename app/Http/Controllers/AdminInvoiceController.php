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
}
