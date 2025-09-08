<?php
namespace App\Mail; use App\Models\Invoice; use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable; use Illuminate\Queue\SerializesModels;
class InvoiceCreated extends Mailable { use Queueable, SerializesModels; public function __construct(public Invoice $invoice){} public function build(){ return $this->subject('Invoice Baru '.$this->invoice->number)->view('emails.invoice_created'); }}
