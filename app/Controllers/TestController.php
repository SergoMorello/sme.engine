<?php
namespace App\Controllers;

class TestController extends Controller {

	public function index() {
		return 123;
	}
	
	public static function test() {
		return 321;
	}
}