<?php
if(!defined('PATH')) die;

/**
 * The map class, handles matrix generation, ship positioning
 * and records info about the current map state, targets left
 * and shots spent
 * @author Andriyan Ivanov <andriya.ivanov@gmail.com>
 */
class Map
{
	protected $map = array();
	protected $totalTargets = 0;
	protected $shots = 0;
	
	static $ships = array(5, 4, 4);
	static $orientation = array('horizontal', 'vertical');
	static $verticalDirection = array('left', 'right');
	static $horizontalDirection = array('up', 'down');

	const MAP_X = 10;
	const MAP_Y = 10;

	const LEFT = 'left';
	const RIGHT = 'right';
	const HORIZONTAL = 'horizontal';
	const VERTICAL = 'vertical';
	const UP = 'up';
	const DOWN = 'down';

	/**
	 * Generate a new map, calculate the targets on the map
	 * and populate the map with targets
	 */
	public function __construct() {
		$this
			->calculateMaxTargets()
			->generateMap()
			->populateMap();
	}

	/**
	 * Map::map accessor
	 * @return array The map matrix
	 */
	public function getMatrix() {
		return $this->map;
	}

	/**
	 * Map::totalTargets accessor
	 * @return integer Amount of remaining targets on the map
	 */
	public function getTargetCount() {
		return $this->totalTargets;
	}

	/**
	 * Calculate the amount of targets on the map, based on the
	 * Map::$ships entries
	 * @return Map 	Return class object for chaining
	 */
	public function calculateMaxTargets() {
		foreach(self::$ships as $shipSize)
			$this->totalTargets += $shipSize;

		return $this;
	}

	/**
	 * Generate an empty map matrix with 0 values in each node
	 * @return Map 	Return class object for chaining
	 */
	public function generateMap() {
		for($y = 0; $y < static::MAP_Y; $y++) {
			for($x = 0; $x < static::MAP_X; $x++) {
				$this->map[$x][$y] = 0;
			}
		}

		return $this;
	}

	/**
	 * Populate the map matrix by setting 1 for each node containig
	 * a target
	 * @return Map 	Return class object for chaining
	 */
	public function populateMap() {
		foreach(static::$ships as $shipSize)
			$this->addToMap($shipSize);

		return $this;
	}

	/**
	 * Attempt to put a ship with a certain size on the map
	 * @param boolean Returns true with the ship is placed on the map
	 */
	public function addToMap($shipSize) {
		while(true) {
			$position = $this->getRandomEmptyCoordinate();
			$orientation = $this->getRandomOrientation();
			$direction = $this->getRandomDirection($orientation);

			if($this->canPositionShipAt($shipSize, $position, $direction)) {
				$this->positionShipAt($shipSize, $position, $direction);
				return true;
			}
		}
	}

	/**
	 * Checks if a ship can be placed at a position
	 * @param  integer $shipSize  The size of the ship
	 * @param  Array   $position  Contains coordinates (usually random generated)
	 * @param  string  $direction One of four directions (left, right, up, down)
	 * @return boolean            True if a ship can be placed at the position
	 */
	public function canPositionShipAt($shipSize, Array $position, $direction) {
		list($x, $y) = $position;

		for($i = 0; $i < $shipSize; $i++) {
			switch($direction) {
				case self::LEFT:
					if($this->isOccupiedCoordinate($x - $i, $y)) return false;
					break;

				case self::RIGHT:
					if($this->isOccupiedCoordinate($x + $i, $y)) return false;
					break;

				case self::UP:
					if($this->isOccupiedCoordinate($x, $y - $i)) return false;
					break;

				case self::DOWN:
					if($this->isOccupiedCoordinate($x, $y + $i)) return false;
			}
		}

		return true;
	}

	/**
	 * Fills an array of positions based on ship size, starting coordinate
	 * and direction
	 * @param  integer $shipSize  The size of the ship
	 * @param  Array   $position  Contains coordinates (usually random generated)
	 * @param  string  $direction One of four directions (left, right, up, down)
	 * @return boolean            Always return true, automatically assuming the
	 *                            positions were available
	 */
	public function positionShipAt($shipSize, Array $position, $direction) {
		list($x, $y) = $position;

		for($i = 0; $i < $shipSize; $i++) {
			switch($direction) {
				case self::LEFT:
					$this->fillCoordinate($x - $i, $y);
					break;

				case self::RIGHT:
					$this->fillCoordinate($x + $i, $y);
					break;

				case self::UP:
					$this->fillCoordinate($x, $y - $i);
					break;

				case self::DOWN:
					$this->fillCoordinate($x, $y + $i);
			}
		}

		return true;
	}

	/**
	 * Checks if a certain coordinate is available
	 * @param  integer  $x The X Coordinate
	 * @param  integer  $y The Y coordinate
	 * @return boolean     If the position equals 0, return true
	 */
	public function isOccupiedCoordinate($x, $y) {
		if($x < 0 || $x > self::MAP_X - 1 || $y < 0 || $y > self::MAP_Y - 1)
			return true;

		return 0 !== $this->map[$x][$y];
	}

	/**
	 * Change the value of certain coordinate to 1
	 * @param  integer  $x The X Coordinate
	 * @param  integer  $y The Y coordinate
	 * @return integer     Always return 1
	 */
	public function fillCoordinate($x, $y) {
		return $this->map[$x][$y] = 1;
	}

	/**
	 * Returns a random direction based on orientation, being it
	 * either vertical or horizontal
	 * @param  string $orientation vertical|horizontal
	 * @return string              left|right for horizontal, up|down for vertical
	 */
	public function getRandomDirection($orientation)
	{
		if(self::HORIZONTAL === $orientation)
			return self::$horizontalDirection[rand(0,1)];

		return self::$verticalDirection[rand(0,1)];
	}
	
	/**
	 * Picks an orientation randomly
	 * @return string horizontal|vertical
	 */
	public function getRandomOrientation() {
		return self::$orientation[rand(0,1)];
	}

	/**
	 * Coorindate generator
	 * @return array  X/Y coordinate container of a spot that equals 0
	 */
	public function getRandomEmptyCoordinate() {
		while(true) {
			$randX = rand(0, static::MAP_X - 1);
			$randY = rand(0, static::MAP_Y - 1);
			if(0 === $this->map[$randX][$randY]) {
				return array($randX, $randY);
			}
		}
	}

	/**
	 * Shoot at a X/Y coordinate by increasing its value with 2
	 * @param  integer  $x The X Coordinate
	 * @param  integer  $y The Y coordinate
	 * @return mixed 	   Returns on of the following values, based on result
	 *                       2 - shot went thru, missed a target
	 *                       3 - shot went thru, hit a target
	 *                       false - location has been shot at already
	 *                       true - game over, all targets are down
	 */
	public function shoot($x, $y) {
		$state = (int) $this->map[$x][$y];
		switch($state) {
			case 0:
				$this->shots++;
				break;
			case 1:
				$this->shots++;
				$this->totalTargets--;
				break;
			case 2:
			case 3:
				return false;
		}

		$newState = $this->map[$x][$y] += 2;

		if(0 === $this->totalTargets)
			return true;

		return $newState;
	}

	/**
	 * Map::$shots accessor
	 * @return integer Amount of shots spent in the game
	 */
	public function getShotCount() {
		return $this->shots;
	}
}