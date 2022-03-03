<?php

define('ROOT',realpath(__DIR__ .'/..').'/');
define('ROUTES',ROOT.'/routes/');
define('APP',ROOT.'/app/');
define('CONFIG',ROOT.'/config/');
define('CONTROLLER',ROOT.'/app/Controllers/');
define('MODEL',ROOT.'/app/Models/');
define('EXCEPTIONS',ROOT.'/app/Exceptions/');
define('MIDDLEWARE',ROOT.'/app/Middleware/');
define('STORAGE',ROOT.'/storage/');
define('LOGS',ROOT.'/storage/.logs/');
define('ENGINE',ROOT.'/engine/');
define('CORE',ROOT.'/engine/core/');
define('MODULES',ROOT.'/engine/modules/');
define('HELPERS',ROOT.'/engine/helpers/');
define('INC',ROOT.'/engine/inc/');
define('TEMP',ROOT.'/storage/.tmp/');

foreach(require_once(INC.'engine.php') as $path => $files)
	foreach($files as $file)
		require_once(constant($path).$file.'.php');
