<?php

session_name('smeSession');
session_start();

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

// Core
require_once(CORE.'database.php');
require_once(CORE.'core.php');
require_once(CORE.'config.php');
require_once(CORE.'model.php');
require_once(CORE.'controller.php');
require_once(CORE.'compiler.php');
require_once(CORE.'view.php');
require_once(CORE.'response.php');
require_once(CORE.'route.php');
require_once(CORE.'http.php');
require_once(CORE.'exceptions.php');
require_once(CORE.'middleware.php');
require_once(CORE.'request.php');
require_once(CORE.'redirect.php');
require_once(CORE.'cache.php');
require_once(CORE.'storage.php');
require_once(CORE.'app.php');
require_once(CORE.'log.php');

// Helpers
require_once(HELPERS.'app.php');
require_once(HELPERS.'config.php');
require_once(HELPERS.'view.php');
require_once(HELPERS.'response.php');
require_once(HELPERS.'dd.php');
require_once(HELPERS.'cookie.php');
require_once(HELPERS.'session.php');
require_once(HELPERS.'route.php');
require_once(HELPERS.'request.php');
require_once(HELPERS.'redirect.php');
require_once(HELPERS.'abort.php');
require_once(HELPERS.'asset.php');
require_once(HELPERS.'old.php');
require_once(HELPERS.'log.php');