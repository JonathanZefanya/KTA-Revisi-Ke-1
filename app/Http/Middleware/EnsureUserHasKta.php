<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasKta
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if(!$user){
            return redirect()->route('login');
        }
        // Consider a user 'has KTA' if membership_card_number exists and not expired
        if(!$user->hasActiveMembershipCard()){
            if($request->expectsJson()){
                return response()->json(['message'=>'Belum memiliki KTA aktif'], 403);
            }
            return redirect()->route('kta')->with('error','Anda belum memiliki KTA aktif.');
        }
        
        // Additional check for renewal routes - must be eligible for renewal
        if($request->routeIs('kta.renew.*') && !$user->isEligibleForRenewal()){
            $eligibleDate = $user->getRenewalEligibilityDate();
            $expiryDate = $user->membership_card_expires_at ? \Carbon\Carbon::parse($user->membership_card_expires_at) : null;
            
            if ($eligibleDate && $expiryDate) {
                $message = "Perpanjangan KTA baru dapat dilakukan mulai tanggal " . $eligibleDate->format('d M Y') . 
                          " (1 minggu sebelum masa berlaku berakhir pada " . $expiryDate->format('d M Y') . ").";
            } else {
                $message = "Perpanjangan KTA belum dapat dilakukan saat ini.";
            }
            
            if($request->expectsJson()){
                return response()->json(['message'=>$message], 403);
            }
            return redirect()->route('kta')->with('error', $message);
        }
        
        return $next($request);
    }
}
