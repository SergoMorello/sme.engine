<?php

define('ROOT',realpath(__DIR__ .'/..').'/');
define('ROUTES',ROOT.'/routes/');
define('APP',ROOT.'/app/');
define('CONFIG',ROOT.'/config/');
define('CONTROLLER',ROOT.'/app/Controllers/');
define('MODEL',ROOT.'/app/Models/');
define('VIEW',ROOT.'/app/View/');
define('EXCEPTIONS',ROOT.'/app/Exceptions/');
define('MIDDLEWARE',ROOT.'/app/Middleware/');
define('STORAGE',ROOT.'/storage/');
define('LOGS',ROOT.'/storage/.logs/');
define('ENGINE',ROOT.'/engine/');
define('CORE',ROOT.'/engine/core/');
define('SVIEW',ROOT.'/engine/View/');
define('ENGINE_EXCEPTIONS',ROOT.'/engine/exceptions/');
define('MODULES',ROOT.'/engine/modules/');
define('HELPERS',ROOT.'/engine/helpers/');
define('INC',ROOT.'/engine/inc/');
define('TEMP',ROOT.'/storage/.tmp/');

(function(){
	foreach(require_once(INC.'init.php') as $inc)
		foreach(require_once(INC.$inc.'.php') as $path => $files)
			foreach($files as $file)
				require_once(constant($path).$file.'.php');
})();
