<?php
namespace App\Middleware;

use SME\Core\Middleware;

class apiMiddleware extends Middleware {
	
	public function handle($request, $next) {
		
		return $next($request);
	}
}