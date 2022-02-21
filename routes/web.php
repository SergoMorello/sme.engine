<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix' => 'test'], function(){
	
	route::get('/', function(){
		return View('test');
	});
	route::post('/submit', function(){
		request::validate([
			'tt' => 'min:11|required',
			'dd' => 'min:0'
		]);
		dd(request()->input('tt'));
	})->name('submit');
	// route::get('/post', function(){
	// 	request::validate([
	// 		'test' => 'size:2'
	// 	]);
	// 	$res = request::input('test');
	// 	dd($res);
	// });
	
});
