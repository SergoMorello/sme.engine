<?php

Route::get("/","main@index")->name('home');

Route::get("/doc","main@doc")->name('doc');


Route::group(['prefix' => 'test'], function(){
	
	//Route::get('/{id}/{dd}', 'TestController@index')->middleware('test');

	// Route::get('/{id}/{dd}', function($test, $test2){
	// 	dd($test);
	// })->middleware(['api','test']);

	Route::get('/', function(){
		$storage = \SME\Modules\Storage::class;
		return \SME\Core\App::startTime();
		//dd($storage::put('12/34/56/test.txt', 123));
		//return view('test');
		$testList = \App\Models\Test2::get();
		//$testList->offsetUnset(0);
		dd($testList);
		return \SME\Support\View::make('test',[
			'testList' => $testList
		]);
	})->name('test');
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