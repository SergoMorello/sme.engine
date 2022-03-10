<?php

Route::get("/","main@index")->name('home');

Route::get("/doc","main@doc")->name('doc');


Route::group(['prefix' => 'test'], function(){
	
	Route::get('/', function(){
		//dd(parse_url(\SME\Http\Request::server('QUERY_STRING')));
		//dd(\SME\Http\Request::server());
		$testList = \SME\Support\DB::table('files')->select('file')->paginate(5);
		return \SME\Support\View::make('test', [
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