<?php
Route::get("/","main@index")->name('home');

Route::get("/doc","main@doc")->name('doc');

Route::group(['prefix' => 'test'], function(){
	
	Route::get('/', function(){
		
		return View('test');
	})->name('test');
	Route::post('/submit', function(){
		SME\Core\Request\Request::validate([
			'dd' => 'min:11|unique:Test,id',
			'file' => 'file|mimes:jpg,png,exe|size:3563',
			'tt' => 'test'
		]);
		foreach(request()->file('file') as $key => $file) {
			$file->storeAs('/test', $key.'test.'.$file->getExtension());
		}
		return redirect()->route('test');
	})->name('submit');
	// Route::get('/post', function(){
	// 	Request::validate([
	// 		'test' => 'size:2'
	// 	]);
	// 	$res = Request::input('test');
	// 	dd($res);
	// });
	
});
