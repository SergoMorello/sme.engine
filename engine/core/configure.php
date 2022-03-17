<?php
namespace SME\Core;

use SME\Core\Route\Console;
use SME\Core\View\Compiler;
use SME\Core\View\View;
use SME\Core\Request\Request;

if (App::isConsole()) {
	ini_set('default_charset','UTF-8');
}else{
	if (!file_exists(TEMP))
		mkdir(TEMP);
	session_save_path(TEMP);
	session_name('smeSession');
	session_start();
	header('Content-Type: text/html; charset=utf-8');
}

// Init

Env::init();

Middleware::init();

// Config

Config::set('app', App::include('config.app'));

Config::set('database', App::include('config.database'));

Config::set('storage', App::include('config.storage'));

Config::set('cache', App::include('config.cache'));


App::include('engine.support.app');

App::include('engine.support.route');

App::include('engine.support.http');

App::include('engine.core.configure.exceptions');

if (App::isConsole()) {

	App::include('engine.core.configure.commands');

}else{

	App::include('engine.core.configure.compiler');

	if (config('app.compressorEnabled'))
		\Route::get('/'.config("app.compressorName").'/{hash}/{name}', 'SME\\Modules\\compressor@get')->name('compressor-get');
	
	Middleware::declare('api', function($request, $next){
		return $next($request);
	});
}