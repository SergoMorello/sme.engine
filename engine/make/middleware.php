<?php

class __NAME__ extends middleware {
	
	public function handle($request, $next) {
		
		return $next($request);
	}
}