<?php

Route::get("/","main@index")->name('home');

Route::get("/doc","main@doc")->name('doc');


Route::group(['prefix' => 'test'], function(){
	
	//Route::get('/{id}/{dd}', 'TestController@index')->middleware('api');

	Route::get('/{id}/{dd}', function($test, $test2){
		dd($test);
	})->middleware('api');

	// Route::get('/', function(){
	// 	return \SME\Support\View::make('test');
	// })->name('test');
	Route::post('/submit', function(){
		SME\Core\Request\Request::validate([
			'checkbox' => 'accepted',
			'tt' => 'required|min:10',
			'file' => 'required|file|mimes:jpg,png,exe|size:13948'
		]);
		foreach(request()->file('file') as $key => $file) {
			$file->storeAs('/test', $key.'test.'.$file->getExtension());
		}
		return redirect()->route('test');
	})->name('submit');
	Route::get('/post/{dd?}/{aa?}', function(){
		$tst = \SME\http\Request::route('dd');
		dd($tst);
	});
	
});