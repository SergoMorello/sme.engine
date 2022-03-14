<?php
namespace App\Middleware;

class testMiddleware {
	
	public function handle($request, $next) {
		//dd($request);
		//return 123;
		//return $next(123, 456);
		return $next($request);
	}
}