<?php

define('ROOT',realpath(__DIR__ .'/..').'/');
define('ROUTES',ROOT.'/routes/');
define('APP',ROOT.'/app/');
define('EXCEPTIONS',ROOT.'/app/exceptions/');
define('MIDDLEWARE',ROOT.'/app/middleware/');
define('STORAGE',ROOT.'/storage/');
define('LOGS',ROOT.'/storage/.logs/');
define('ENGINE',ROOT.'/engine/');
define('CORE',ROOT.'/engine/core/');
define('HELPERS',ROOT.'/engine/helpers/');
define('INC',ROOT.'/engine/inc/');
define('TEMP',ROOT.'/storage/.tmp/');

foreach(require_once(INC.'engine.php') as $path=>$files)
	foreach($files as $file)
		require_once(constant($path).$file.'.php');
