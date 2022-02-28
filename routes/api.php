<?php

// Route::get("/request","main@index");

Route::get("/request", function(){
	return ['test' => 123];
});