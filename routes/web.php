<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix' => 'test'], function(){
	
	route::get('/', function(){
		$model = controller::model('test');
		dd($model->leftJoin('rf_exercise_categories',function($join){
			return $join->on('rf_exercises.cat_id','=','rf_exercise_categories.id')->on('rf_exercises.id','=','1');
		})
		->select('rf_exercise_categories.name as name', 'rf_exercises.title as title')->get());
	});
	// route::get('/post', function(){
	// 	request::validate([
	// 		'test' => 'size:2'
	// 	]);
	// 	$res = request::input('test');
	// 	dd($res);
	// });
	
});
