<?php
namespace App\Providers;

use SME\Core\App;
use SME\Core\Middleware;
use SME\Core\Request\Validate;

class AppServiceProvider {
	public function boot() {
		//
	}
	
	public function register() {

		Validate::rule('test',function($var){
			if ($var == 5)
				return false;
			return true;
		});

		Middleware::declare('test');
		
		App::singleton('testClass', function($app){
			return $app;
			return new class{
				public function test() {
					return 123;
				}
			};
		});
	}
}