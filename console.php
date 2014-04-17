#!/usr/bin/php
<?php
	require_once __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'main.php';
	require_once CLASS_PATH.'Map.php';
	require_once CLASS_PATH.'ConsoleGame.php';

	$game = new ConsoleGame(new Map());
	$game->render();