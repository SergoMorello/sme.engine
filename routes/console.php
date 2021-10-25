<?php
//route::console("command","main@index");

route::console('test {id?} {ids?}', function(){
	return request::route('ids');
});