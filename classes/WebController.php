<?php
if(!defined('PATH')) die;

/**
 * Handles layout rendering and JSON generation for the web game
 * @author Andriyan Ivanov <andriya.ivanov@gmail.com>
 */
class WebController
{
	protected $game;

	const MSG_START = 'The game has started!';
	const MSG_SHOOT = 'Click on a cell to shoot!';
	const MSG_STATE_SHOOT = 0;

	/**
	 * Calss constructor
	 * @param WebGame $game A valid WebGame object
	 */
	public function __construct(WebGame $game) {
		$this->game = $game;
	}

	/**
	 * Route differnt requested actions, if none - render the
	 * layout
	 * 
	 * @param  string $action null|Action to be executed
	 * @param  array  $params Merge of $_GET and $_POST
	 * @return void           Different results
	 */
	public function routeAction($action, $params) {
		switch($action) {
			case 'shoot':
				return $this->shoot($params);
			case 'getMapVisible':
				return $this->actionGetMapVisible();
			case 'getMapHidden':
				return $this->actionGetMapHidden();
			default:
				// render layout if not a valid action
				require TEMPLATE_PATH.'layout.php';
				return true;
		}
	}

	/**
	 * Produce an in-game shot
	 * @param  array $params Merge of $_POST and $_GET
	 * @return void          Generate JSON Output and send it to the browser
	 */
	public function shoot($params) {
		$x = $params['x'];
		$y = $params['y'];

		$data = array();
		$data['messageState'] = $this->game->shoot($y, $x);
		$data['message'] = $this->game->getMessage($data['messageState']);
		$data['coords'] = $this->game->getMatrixVisible();
		$data['shots'] = $this->game->getShots();
		$data['gameState'] = $this->game->getGameState();

		return $this->sendJson($data);
	}

	/**
	 * Returns the map matrix in normal mode (e.g. targets are hidden)
	 * @return void Generate JSON Output and send it to the browser
	 */
	public function actionGetMapVisible() {
		$data = array();
		$data['coords'] = $this->game->getMatrixVisible();
		$data['shots'] = $this->game->getShots();
		$data['gameState'] = $this->game->getGameState();

		// if game is over
		if($data['gameState']) {
			$data['message'] = $this->game->getMessage(true);
			$data['messageState'] = true;
		} else {
			$data['message']  = 0 == $data['shots'] ? self::MSG_START.' ' : '';
			$data['message'] .= self::MSG_SHOOT;
			$data['messageState'] = self::MSG_STATE_SHOOT;
		}

		return $this->sendJson($data);
	}

	/**
	 * Returns the map matrix in hacker mode (e.g. targets are visible)
	 * @return void Generate JSON Output and send it to the browser
	 */
	public function actionGetMapHidden() {
		$data = array();
		$data['coords'] = $this->game->getMatrixHidden();
		$data['shots'] = $this->game->getShots();
		$data['gameState'] = $this->game->getGameState();
		$data['message'] = $this->game->getMessage('hacker');
		$data['messageState'] = 'hacker';

		return $this->sendJson($data);
	}

	/**
	 * Outputs a JSON response
	 * @param  mixed $data Any data object
	 * @return void        Sends JSON headers, output JSON encoded data and exits
	 */
	public function sendJson($data) {
		header('Content-type: application/json');
		echo json_encode($data);
		exit;		
	}
}