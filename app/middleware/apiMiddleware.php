<?php

class apiMiddleware extends middleware {
	
	public function handle($request, $next) {
		
		return $next($request);
	}
}