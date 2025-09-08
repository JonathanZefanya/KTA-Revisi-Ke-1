<p>Halo {{ $invoice->user->name }},</p>
<p>Invoice baru telah dibuat untuk akun Anda.</p>
<p><strong>No:</strong> {{ $invoice->number }}<br>
<strong>Total:</strong> Rp {{ number_format($invoice->amount,0,',','.') }}<br>
<strong>Jatuh Tempo:</strong> {{ $invoice->due_date?->format('d M Y') }}</p>
<p>Silakan lakukan pembayaran dan unggah bukti pada portal.</p>
<p>Terima kasih.</p>
