<?php
class main extends controller {
	
	public function index() {
		$links = [
			[
				'link' => 'https://github.com/SergoMorello/sme.engine',
				'name' => 'Github'
			],
			[
				'link' => 'https://vk.com/serov.sergey',
				'name' => 'С предложениями сюда'
			]
		];
		return View('home',[
			'info' => 'Это нечто похожее на laravel но намного быстрее и проще',
			'text' => 'Можно легко создать быстрое веб приложение используя привычный синтаксис laravel',
			'links' => $links
		]);
	}
	
	public function doc() {
		return redirect('http://sme.inmsk.net/doc');
	}
}