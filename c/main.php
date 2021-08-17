<?php
class main extends controller {
	function index() {
		return View('home',['message'=>'hello world']);
	}
}