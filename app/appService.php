<?php
namespace App;

use SME\Core\App;
use SME\Core\Core;
use SME\Core\middleware;

class appService extends Core {
	public function boot() {
		//
	}
	
	public function register() {

		middleware::declare('test');

		App::singleton('testClass', function(){
			return new class{
				public function test() {
					return 123;
				}
			};
		});
	}
}