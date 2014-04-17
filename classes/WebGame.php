<?php
if(!defined('PATH')) die;

require_once CLASS_PATH.'Map.php';
require_once CLASS_PATH.'Messanger.php';

/**
 * The web game class, generates a new games, loads old ones,
 * generates map matrix data based on hacker/normal mode
 * @author Andriyan Ivanov <andriya.ivanov@gmail.com>
 */
class WebGame 
{
	protected $map;
	protected $messanger;
	protected $gameId;

	const MD5_REGEX = '/^[0-9a-f]{32}$/i';

	/**
	 * Class constructor
	 * @param string $gameId A valid gameId
	 */
	public function __construct($gameId = null) {
		if(!$this->gameExists($gameId)) {
			return $this->createNewGame();
		}

		$this->gameId = $gameId;
		$this->map = $this->loadMap($gameId);
		$this->messanger = new Messanger($this->map, true);
	}

	/**
	 * Messanger accessor
	 * @param  mixed $state integer|boolean|'hacker'
	 * @return string       The produced message
	 */
	public function getMessage($state) {
		return $this->messanger->get($state);
	}

	/**
	 * Checks if game exists
	 * @param  string  $gameId Game Id
	 * @return boolean         Returns true if game is found
	 */
	public function gameExists($gameId) {
		if(!preg_match(self::MD5_REGEX, $gameId) || !file_exists($this->getGameFile($gameId)))
			return false;

		return true;
	}

	/**
	 * Load map data from game file based on gameId
	 * @param  string $gameId Game Id
	 * @return Map         	  Unserialized Map object from file
	 */
	public function loadMap($gameId) {
		$data = file_get_contents($this->getGameFile($gameId));
		return unserialize($data);
	}

	/**
	 * Find an available game Id, generate a new map and save data
	 * for it, redirect to another URL to load the game
	 * @return void
	 */
	public function createNewGame() {
		$gameId = $this->findAvailableGameId();
		$this->saveToFile($gameId, new Map());
		$this->redirectToGame($gameId);
	}

	/**
	 * Finds an available game Id for a file that doesn't exist yet
	 * @return string Random MD5 hash
	 */
	public function findAvailableGameId() {
		while(true) {
			$gameId = md5(rand(0,99999999));
			if(!$this->gameExists($gameId)) {
				return $gameId;
			}
		}
	}

	/**
	 * Generate a game filepath
	 * @param  string $gameId Game Id
	 * @return string         Path to game file
	 */
	public function getGameFile($gameId) {
		return DATA_PATH.$gameId;
	}

	/**
	 * Saves a Map object to file
	 * @param  string $gameId Game Id
	 * @param  Map 	  $map    Unserialized map object
	 * @return void
	 */
	public function saveToFile($gameId, $map) {
		return file_put_contents($this->getGameFile($gameId), serialize($map));
	}

	/**
	 * Redirects to index based on game Id
	 * @param  string $gameId Game Id
	 * @return void
	 */
	public function redirectToGame($gameId) {
		header('Location: //'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?gameId='.$gameId);
		exit;
	}

	/**
	 * Produces an array map matrix for normal mode (e.g. no hidden objects)
	 * @return array The visible map matrix
	 */
	public function getMatrixVisible() {
		$mapMatrix = $this->map->getMatrix();
		$outputMatrix = array();
		foreach($mapMatrix as $y => $row) {
			foreach($row as $x => $cellValue) {
				$value = $cellValue === 1 ? 0 : $cellValue;
				$outputMatrix[$y][$x] = $value;
			}
		}

		return $outputMatrix;
	}

	/**
	 * Produces an array map matrix for hacker mode (e.g. only target objects)
	 * @return array The hidden map matrix
	 */
	public function getMatrixHidden() {
		$mapMatrix = $this->map->getMatrix();
		$outputMatrix = array();
		foreach($mapMatrix as $y => $row) {
			foreach($row as $x => $cellValue) {
				$value = 0;
				if($cellValue === 1 || $cellValue === 3) {
					$value = 1;
				}

				$outputMatrix[$y][$x] = $value;
			}
		}

		return $outputMatrix;
	}

	/**
	 * Produce a web shot based on coordinate
	 * @param  	integer 	$x 	The X Coordinate
	 * @param  	integer 	$y  The Y coordinate
	 * @return 	mixed 	   
	 * @see 	Map::shoot
	 */
	public function shoot($y, $x) {
		$state = $this->map->shoot($y, $x);
		$this->saveToFile($this->gameId, $this->map);
		return $state;
	}

	/**
	 * Map::getShotCount accessor
	 * @return integer Amount of shots spent
	 */
	public function getShots() {
		return $this->map->getShotCount();
	}

	/**
	 * The current game state
	 * @return integer Return 1 if the game has finished, 0 otherwise
	 */
	public function getGameState() {
		return 0 == $this->map->getTargetCount() ? 1 : 0;
	}

}