<p>Halo {{ $invoice->user->name }},</p>
<p>Status pembayaran untuk invoice <strong>{{ $invoice->number }}</strong> telah diperbarui.</p>
<p>Status sekarang: <strong>{{ strtoupper($invoice->status) }}</strong></p>
@if($invoice->verification_note)
<p>Catatan: {{ $invoice->verification_note }}</p>
@endif
<p>Terima kasih.</p>
