<?php
Route::get("/","main@index")->name('home');

Route::get("/doc","main@doc")->name('doc');

Route::group(['prefix' => 'test'], function(){
	
	Route::get('/', function(){
		
		return View('test');
	});
	Route::post('/submit', function(){
		SME\Core\Request\Request::validate([
			'tt' => 'min:11|required',
			'dd' => 'min:0'
		]);
		dd(request()->input('tt'));
	})->name('submit');
	// Route::get('/post', function(){
	// 	Request::validate([
	// 		'test' => 'size:2'
	// 	]);
	// 	$res = Request::input('test');
	// 	dd($res);
	// });
	
});
