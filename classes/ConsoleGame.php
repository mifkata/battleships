<?php
if(!defined('PATH')) die;

require_once CLASS_PATH.'Messanger.php';

/**
 * The console game class, uses Map to render console output
 * @author Andriyan Ivanov <andriya.ivanov@gmail.com>
 */
class ConsoleGame
{
	protected $map;
	protected $message;
	protected $messanger;
	protected $clearCommand;

	static $stateHidden = array('.', '.', '-', 'x');
	static $stateVisible = array(' ', 'x', ' ', 'x');

	const ASCII_A = 65;
	const MAX_X = 10;
	const POSITION_REGEX = '/^[a-j][0-9]$/i';
	const TOGGLE_SHOW_COMMAND = 'show';

	const NUMERIC_INDEX = '1234567890';

	const MSG_ENTER_COORDINATES = 'Enter coordinates (row, col), e.g. A5 = ';
	const MSG_INVALID_COORDINATE = 'Oops! You entered invalid coordinates';

	/**
	 * Creates a console game object
	 * @param Map $map Takes a map object as init parameter, usually a new map
	 */
	
	public function __construct(Map $map) {
		$this->map = $map;
		$this->messanger = new Messanger($map, false);
		$this->clearCommand = DIRECTORY_SEPARATOR == '/' ? 'clear' : 'cls';
	}

	/**
	 * Parces user input and either shoots at a position or enters
	 * hacker mode, which shows all target on map
	 * @param  mixed $position 	Array containing x/y coordinate or 'hacker'
	 * @return void          	Excutes the render() method to refresh the
	 *                          game state
	 */
	
	public function shoot($position) {
		if(self::TOGGLE_SHOW_COMMAND == strtolower($position)) {
			$this->message = $this->messanger->get('hacker');
			return $this->render(true);
		}

		if(!preg_match(self::POSITION_REGEX, $position)) {
			$this->message = self::MSG_INVALID_COORDINATE;
			return $this->render();
		}

		$y = $this->translateY($position[0]);
		$x = $this->translateX($position[1]);

		$shot = $this->map->shoot($y, $x);
		$this->message = $this->messanger->get($shot);

		return $this->render(false, true !== $shot );
	}

	/**
	 * Converts a string literal coordinate into an integer one
	 * @param  string $char A char in the scope of A-J
	 * @return integer      The corresponding integer value
	 */
	
	public function translateY($char) {
		return ord(strtoupper($char)) - self::ASCII_A;
	}

	/**
	 * Converts the user friendly integer value into a proper 
	 * X coordinate value
	 * 
	 * @param  integer $int Entry data (0-9)
	 * @return integer      True coordinate value (e.g. 0 == 9, 1 == 0)
	 */
	
	public function translateX($int) {
		return (int) $int !== 0 ? --$int : 9;
	}

	/**
	 * Renders a terminal map view with coordinates
	 * @param  boolean $showHidden    True if you want to see the hidden 
	 *                                targets
	 * @param  boolean $continueInput False if you want to stop output, 
	 *                                used to end the game
	 * @return mixed                  Returns a result from Map::shoot
	 */
	
	public function render($showHidden = false, $continueInput = true) {

		passthru($this->clearCommand); // clear the terminal

		$output  = $this->generateMapOutput($showHidden);
		$output .= $this->message."\n";

		if(false === $continueInput)
			die($output."\n");  // game won -> game over

		$output .= self::MSG_ENTER_COORDINATES;

		echo $output;

		$stdin = fopen("php://stdin", "r");
		$result = trim(fgets($stdin));
		fclose($stdin);

		$this->shoot($result);
	}

	/**
	 * Generates a terminal string with matrix coordinates and
	 * proper cell values, based on their current state
	 * @param  boolean $showHidden Indicates if it should generate
	 *                             output for the hidden targets
	 * @return string              Output ready map string
	 */
	
	public function generateMapOutput($showHidden = false) {
		$matrix = $this->map->getMatrix();
		$output = "\n   ".self::NUMERIC_INDEX."\n";

		for($i = 0; $i < sizeof($matrix); $i++) {
			$output .= ' '.chr(self::ASCII_A+$i).' ';

			foreach($matrix[$i] as $cell) {
				$output .= $showHidden ? self::$stateVisible[$cell] : self::$stateHidden[$cell];
			}

			$output .= "\n";
		}

		$output .= "\n";
		return $output;
	}
}