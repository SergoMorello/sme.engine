<?php
route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');

route::group(['prefix'=>'test'],function(){
	route::get("/", function(){
		return view('test');
		$db = controller::model('test');
		//return response::make('test')->code(202);
		//dd(response('test')->code(200)->header('Conten-Type', 'text/javascript'));
		//dd(env('APP_NAME', 'SME'));
		//dd(config::newGet('app.test'));
		//$obj = $db->select('id','email')->find(32)->first();
		// $db->find(34);
		// $db->uid = 1;
		// $db->login = 'test';
		// $db->email = '123@123.123';
		// $db->text = 'ttt';
		// $db->stat = 1;
		// $res = $db->save();
		// dd($res);
		// dd($db->select('userlist.id as ssdd','userlists as table')->leftJoin('userlist',function($join){
		// 	return $join->on(['userlist.id','tasklist.uid']);
		// })->groupBy('userlist.id')->__sql());
		//dd(http::withBasicAuth('Администратор','')->asMultipart()->post('http://194.170.100.185/test/hs/site/GetItems',['code'=>'НФ-00004846','date'=>'2021-11-25']));
		$var = [['123']];
		dd($var);
		dd(route('doc'));
		return 123;
	});
	
	route::get("/qq/{id?}", function(){
		return [
			'des'=>'',
			'hide'=>'',
			'files'=>[
				[
					'id'=>'e5c507b8-3ac2-11eb-4a86-fa163e9e9024',
					'ext'=>'jpg',
					'edit'=>'20211028135103',
					'add'=>'20211028135103',
					'hash'=>'17198178f3b4955c041e639a9895b35a',
					'default'=>true
				],
				[
					'id'=>'2fcfc7e4-1363-11eb-509a-fa163e9e9024',
					'ext'=>'pdf',
					'edit'=>'20211028135103',
					'add'=>'20211028135103',
					'hash'=>'396d880ffa4cf3ab3e6fce55765ae0c5',
					'default'=>false
				]
			]
		];
		dd(request::route('id'));
		return 123;
	})->where(['id'=>'[0-9]+']);
});