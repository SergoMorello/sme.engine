<?php
namespace App\Controllers;

use SME\Http\Request;

class TestController extends Controller {

	public function index($test, $test2) {
		dd($test);
		return $id;
	}
	
	public static function test() {
		return 321;
	}
}