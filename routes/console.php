<?php
//route::console("command","main@index");

console::command('test {id?} {ids?}', function(){
	return request::route('id');
});