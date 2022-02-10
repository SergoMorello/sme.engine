<?php
class test extends middleware {
	public function handle($request, $next) {
		return response('21132')->json();
		return $next($request);
	}
}