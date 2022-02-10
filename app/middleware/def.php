<?php
class def extends middleware {
	
	public function handle($request, $next) {
		
		return $next($request);
	}
}