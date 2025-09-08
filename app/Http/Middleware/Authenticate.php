<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class Authenticate {
	public function handle(Request $request, Closure $next, ...$guards){
		if(empty($guards)) $guards = [null];
		foreach($guards as $guard){
			if(Auth::guard($guard)->check()){
				Auth::shouldUse($guard ?? 'web');
				return $next($request);
			}
		}
		// not authenticated
		if(in_array('admin',$guards)){
			return redirect()->route('admin.login');
		}
		return redirect()->route('login');
	}
}
