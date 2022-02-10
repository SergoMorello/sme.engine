<?php

// route::get("/request","main@index");

route::get("/request", function(){
	return ['test' => 123];
});