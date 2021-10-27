<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix'=>'test'],function(){
	route::get("/", function(){
		dd(http::withBasicAuth('Администратор','')->asMultipart()->post('http://194.170.100.85/test/hs/site/GetItems',['code'=>'НФ-00004846','date'=>'2021-10-25'])->json());
		dd(route('doc'));
		return 123;
	});
	
	route::get("/qq/{id?}", function(){
		dd(request::route('id'));
		return 123;
	})->where(['id'=>'[0-9]+']);
});