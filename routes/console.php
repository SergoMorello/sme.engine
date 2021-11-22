<?php
//route::console("command","main@index");

console::command('test {id?} {ids?}', function(){
	storage::put('qwr.txt',123);
	return request::route('id');
});