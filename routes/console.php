<?php
//Route::console("command","main@index");

console::command('test {id?} {ids?}', function(){
	Storage::put('qwr.txt',123);
	return Request::route('id');
});