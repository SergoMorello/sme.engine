<?php
class main extends controller {
	function index() {
		return View('home',['info'=>'Это нечто похожее на laravel но намного быстрее и проще']);
	}
}