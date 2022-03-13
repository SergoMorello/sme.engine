<?php
namespace App\Controllers;

use SME\Http\Request;

class TestController extends Controller {

	public function index($test, $test2) {
		dd($test2);
		return 123;
	}
	
	public static function test() {
		return 321;
	}
}