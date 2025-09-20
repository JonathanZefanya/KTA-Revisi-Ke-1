<?php
namespace App\Http\Controllers;

use App\Models\KtaRenewal;
use App\Models\RenewalPaymentRate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KtaRenewalController extends Controller
{
    public function form(Request $r)
    {
        $user = $r->user();
        
        // Check renewal eligibility (7 weeks before expiry)
        if (!$user->isEligibleForRenewal()) {
            $eligibleDate = $user->getRenewalEligibilityDate();
            $expiryDate = $user->membership_card_expires_at ? \Carbon\Carbon::parse($user->membership_card_expires_at) : null;
            
            if ($eligibleDate && $expiryDate) {
                $message = "Perpanjangan KTA baru dapat dilakukan mulai tanggal " . $eligibleDate->format('d M Y') . 
                          " (1 minggu sebelum masa berlaku berakhir pada " . $expiryDate->format('d M Y') . ").";
            } else {
                $message = "Perpanjangan KTA belum dapat dilakukan saat ini.";
            }
            
            return redirect()->route('kta')->with('error', $message);
        }
        
        $currentExpiry = $user->membership_card_expires_at ? \Carbon\Carbon::parse($user->membership_card_expires_at) : null;
        $proposed = ($currentExpiry && $currentExpiry->greaterThan(now()))
            ? $currentExpiry->clone()->addYear()
            : now()->addYear();
        $company = $user->companies()->first();
        $rate = null;
        if($company){
            $rate = RenewalPaymentRate::where('jenis',$company->jenis ?? $company->kualifikasi ?? null)
                ->orWhere(function($q) use ($company){
                    $q->where('jenis',$company->jenis ?? null)->where('kualifikasi',$company->kualifikasi ?? null);
                })->first();
        }
        $amount = $rate?->amount ?? 0;
        $renewals = KtaRenewal::where('user_id',$user->id)->orderByDesc('created_at')->get();
        $pendingInvoice = \App\Models\Invoice::where('user_id',$user->id)
            ->where('type','renewal')
            ->whereIn('status',[\App\Models\Invoice::STATUS_UNPAID, \App\Models\Invoice::STATUS_AWAITING])
            ->latest()->first();
        return view('kta.renew', compact('user','currentExpiry','proposed','amount','renewals','pendingInvoice'));
    }

    public function submit(Request $r)
    {
        $user = $r->user();
        if(!$user->membership_card_number){
            return back()->with('error','Anda belum memiliki KTA untuk diperpanjang.');
        }
        
        // Check renewal eligibility (7 weeks before expiry)
        if (!$user->isEligibleForRenewal()) {
            $eligibleDate = $user->getRenewalEligibilityDate();
            $expiryDate = $user->membership_card_expires_at ? \Carbon\Carbon::parse($user->membership_card_expires_at) : null;
            
            if ($eligibleDate && $expiryDate) {
                $message = "Perpanjangan KTA baru dapat dilakukan mulai tanggal " . $eligibleDate->format('d M Y') . 
                          " (H-7 minggu sebelum masa berlaku berakhir pada " . $expiryDate->format('d M Y') . ").";
            } else {
                $message = "Perpanjangan KTA belum dapat dilakukan saat ini.";
            }
            
            return back()->with('error', $message);
        }
        
        // Prevent duplicate request if there is already a pending renewal invoice
        $existingPending = \App\Models\Invoice::where('user_id',$user->id)
            ->where('type','renewal')
            ->whereIn('status',[\App\Models\Invoice::STATUS_UNPAID, \App\Models\Invoice::STATUS_AWAITING])
            ->exists();
        if($existingPending){
            return back()->with('error','Masih ada invoice perpanjangan yang menunggu pembayaran / verifikasi.');
        }
        $currentExpiry = $user->membership_card_expires_at ? \Carbon\Carbon::parse($user->membership_card_expires_at) : null;
        $newExpiry = ($currentExpiry && $currentExpiry->greaterThan(now()))
            ? $currentExpiry->clone()->addYear()
            : now()->addYear();
        $company = $user->companies()->first();
        $rate = null;
        if($company){
            $rate = RenewalPaymentRate::where('jenis',$company->jenis ?? $company->kualifikasi ?? null)
                ->orWhere(function($q) use ($company){
                    $q->where('jenis',$company->jenis ?? null)->where('kualifikasi',$company->kualifikasi ?? null);
                })->first();
        }
        $amount = $rate?->amount ?? 0;
    DB::beginTransaction();
        try {
            // Create invoice first
            $invoice = \App\Models\Invoice::create([
                'number' => \App\Models\Invoice::generateNumber(),
                'user_id' => $user->id,
                'company_id' => $company?->id,
                'type' => 'renewal',
                'amount' => $amount,
                'currency' => 'IDR',
                'issued_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'status' => \App\Models\Invoice::STATUS_UNPAID,
            ]);
            // Create renewal record (link to invoice, but DO NOT update user expiry yet)
            KtaRenewal::create([
                'user_id'=>$user->id,
                'invoice_id'=>$invoice->id,
                'previous_expires_at'=>$currentExpiry,
                'new_expires_at'=>$newExpiry,
                'amount'=>$amount,
            ]);
            DB::commit();
        } catch(\Throwable $e){
            DB::rollBack();
            return back()->with('error','Gagal membuat pengajuan perpanjangan: '.$e->getMessage());
        }
        return redirect()->route('pembayaran',['invoice'=>$invoice->id])
            ->with('success','Pengajuan perpanjangan dibuat. Silakan lakukan pembayaran invoice.');
    }
}