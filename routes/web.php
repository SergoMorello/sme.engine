<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix'=>'test'],function(){
	route::get("/", function(){
		dd(route('doc'));
		return 123;
	});
	
	route::get("/qq/{id?}", function(){
		dd(request::route('id'));
		return 123;
	});
});