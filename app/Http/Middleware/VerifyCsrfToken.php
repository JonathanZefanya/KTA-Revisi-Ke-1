<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class VerifyCsrfToken {
    public function handle(Request $request, Closure $next){
        if($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')){
            $token = $request->input('_token');
            if(!$token || $token !== $request->session()->token()){
                return new Response('CSRF token mismatch', 419);
            }
        }
        return $next($request);
    }
}
