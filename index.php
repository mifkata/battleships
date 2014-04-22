<?php
	if('cli' !== php_sapi_name()) {
		define('WEBAPP', true);	
	}

	require_once __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'main.php';
	require_once CLASS_PATH.'Map.php';

	// if a web app
	if(defined('WEBAPP')) {
		require_once CLASS_PATH.'WebGame.php';
		require_once CLASS_PATH.'WebController.php';

		$params = array_merge($_GET, $_POST);
		$game = new WebGame($params['gameId']);
		$controller = new WebController($game);
		$controller->routeAction($params['action'], $params);

	// if a console app
	} else {

		require_once CLASS_PATH.'ConsoleGame.php';
		$game = new ConsoleGame(new Map());
		$game->render();
	}