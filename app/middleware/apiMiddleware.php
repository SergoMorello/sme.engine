<?php
namespace App\Middleware;

use SME\Core\middleware;

class apiMiddleware extends middleware {
	
	public function handle($request, $next) {
		
		return $next($request);
	}
}