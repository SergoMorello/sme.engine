<?php
class test extends middleware {
	public function handle($request, $next) {
		
		return $next($request);
	}
}