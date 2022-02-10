<?php
class test extends middleware {
	public function handle($request, $next) {
		//dd($request);
		return $next($request);
	}
}