<?php
//Route::console("command","main@index");

Console::command('test', function(){
	dd(app('path.public'));
});