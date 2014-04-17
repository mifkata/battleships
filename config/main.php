<?php

// DIRECTORY_SEPARATOR SHORTAG
define('DS', DIRECTORY_SEPARATOR);

// PROJECT PATH
define('PATH', realpath(__DIR__.DS.'..').DS);

// CLASSES PATH
define('CLASS_PATH', PATH.'classes'.DS);

// TEMPLATES PATH
define('TEMPLATE_PATH', PATH.'templates'.DS);

// DATA PATH
define('DATA_PATH', PATH.'data'.DS);

if(defined('WEBAPP') && (!is_dir(DATA_PATH) || !is_writable(DATA_PATH)) ) {
	die(sprintf('The data dir: [%s] is either missing or not writable!', DATA_PATH));
}