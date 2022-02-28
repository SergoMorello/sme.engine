<?php
namespace App;

use SME\Core\app;
use SME\Core\core;
use SME\Core\middleware;

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