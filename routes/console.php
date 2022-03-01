<?php
//Route::console("command","main@index");

Console::command('test {id?} {ids?}', function(){
	Storage::put('qwr.txt',123);
	return Request::route('id');
});