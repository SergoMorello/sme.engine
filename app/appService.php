<?php
class appService extends core {
	public function boot() {
		//
	}
	
	public function register() {

		middleware::declare('test');

		app::singleton('testClass', function(){
			return new class{
				public function test() {
					return 123;
				}
			};
		});
	}
}