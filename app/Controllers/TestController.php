<?php
namespace App\Controllers;

use SME\Http\Request;

class TestController extends Controller {

	public function index(Request $req, $test, $test2) {
		dd($test2);
		return $id;
	}
	
	public static function test() {
		return 321;
	}
}