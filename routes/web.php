<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix' => 'test2'], function(){
	route::get('/', function(){
		return 123;
	});
	route::group(['prefix' => 'test3', 'middleware' => 'test'], function(){
		route::get('/', function(){
			cache::put('test2', 123);
			return cache::has('test2');
		});
	});
});
