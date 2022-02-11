<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix' => 'test2'], function(){
	
	route::group(['prefix' => 'test3', 'middleware' => 'test'], function(){
		route::get('/{id}', function($id){
			return 1234;
		});
	});
});
