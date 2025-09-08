<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if($user && is_null($user->approved_at)) {
            if($request->expectsJson()){
                return response()->json(['message'=>'Akun belum terverifikasi'], 403);
            }
            return redirect()->route('dashboard')->with('error','Akun Anda belum terverifikasi.');
        }
        return $next($request);
    }
}
