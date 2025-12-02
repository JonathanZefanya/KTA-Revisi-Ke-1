<?php
namespace App\Http\Controllers; use Illuminate\Http\Request; use App\Models\Invoice; use Illuminate\Support\Facades\Gate;
class InvoiceController extends Controller {
    public function index(Request $r){ $uid = $r->user()?->id; $invoices = Invoice::where('user_id',$uid)->latest()->paginate(20); return view('invoices.index', compact('invoices')); }
    public function show(Request $r, Invoice $invoice){ $uid = $r->user()?->id; abort_unless($invoice->user_id==$uid,403); return view('invoices.show', compact('invoice')); }
    public function downloadPdf(Request $r, Invoice $invoice){ $uid=$r->user()?->id; abort_unless($invoice->user_id==$uid,403); $invoice->load('user','company');
        $logo=\App\Models\Setting::getValue('site_logo_path'); $signature=\App\Models\Setting::getValue('signature_path');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice','logo','signature'))->setPaper('A4');
        return $pdf->download('Invoice-'.$invoice->number.'.pdf'); }
    public function uploadProof(Request $r, Invoice $invoice){ $uid = $r->user()?->id; abort_unless($invoice->user_id==$uid,403); abort_if($invoice->isPaid(),403,'Invoice sudah dibayar');
        $data=$r->validate(['payment_proof'=>['required','file','max:10240','mimes:pdf,jpg,jpeg,png']]);
        $file=$r->file('payment_proof'); $path=$file->store('uploads/payment_proofs','public');
        $invoice->payment_proof_path=$path; $invoice->proof_uploaded_at=now(); $invoice->status=\App\Models\Invoice::STATUS_AWAITING; $invoice->save();
        return back()->with('success','Bukti pembayaran diunggah & menunggu verifikasi admin'); }
    public function selectBank(Request $r, Invoice $invoice){ $uid=$r->user()?->id; abort_unless($invoice->user_id==$uid,403); abort_if($invoice->isPaid(),403,'Sudah dibayar');
        $data=$r->validate(['bank_account_id'=>['required','exists:bank_accounts,id']]);
        $invoice->bank_account_id=$data['bank_account_id']; $invoice->save();
        return back()->with('success','Metode transfer dipilih'); }
}
