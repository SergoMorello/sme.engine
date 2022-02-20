<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix' => 'test'], function(){
	
	route::get('/', function(){
		return view('test');
	});
	// route::get('/post', function(){
	// 	request::validate([
	// 		'test' => 'size:2'
	// 	]);
	// 	$res = request::input('test');
	// 	dd($res);
	// });
	
});
