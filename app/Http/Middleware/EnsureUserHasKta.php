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
        return $next($request);
    }
}
