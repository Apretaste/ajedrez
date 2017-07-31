<?php

use GuzzleHttp\Client;

/**
 * Retrieves the tactics problem of the day from Shredder website.
 */
class Ajedrez extends Service
{
	const NONE = 0, WHITE = 1, BLACK = -1;
	const EASY = 0, MEDIUM = 1, HARD = 2;
	const BLANK = '&nbsp;';

	/**
	 * Function executed when the service is called
	 * @param Request $request
	 * @return Response
	 * */
	public function _main(Request $request)
	{
		// Get requested difficulty level
		if (empty($request->query)) {
			$level = self::MEDIUM;
		} elseif (stripos($request->query, 'f') === 0) {
			$level = self::EASY;
		} elseif (stripos($request->query, 'd') === 0) {
			$level = self::HARD;
		} else {
			$level = self::MEDIUM;
		}

		// Return cached response if fetched today
		$today = mktime(0, 0, 0);
		$cached = @unserialize(file_get_contents(__DIR__ . "/cache/$level.ser"));
		if ($cached && $cached['date'] == $today) {
			return $cached['response'];
		}

		$levelMap = array(
			self::EASY => 'Fácil',
			self::MEDIUM => 'Intermedio',
			self::HARD => 'Difícil'
		);

		$puzzle = $this->fetchPuzzle($level);
		if (! $puzzle) $puzzle = $this->getBackupPuzzle($level);

		$content = array(
			'board' => $puzzle['board'],
			'solution' => $puzzle['solution'],
			'level' => $levelMap[$level],
			'turnStr' => $puzzle['turn'] == self::WHITE ? 'blancas' : 'negras'
		);

		$response = new Response();
		$response->setResponseSubject("Problema de ajedrez");
		$response->createFromTemplate("basic.tpl", $content);

		// Cache response
		$cache = array(
			'date' => $today,
			'response' => $response
		);
		file_put_contents(__DIR__ . "/cache/$level.ser", serialize($cache));

		return $response;
	}


	/**
	 * Retrieve daily puzzle from the remote site
	 * @param integer $level (0 = easy, 1 = medium, 2 = hard)
	 * @return array
	 */
	protected function fetchPuzzle($level)
	{
		$client = new Client();
		$url = "http://www.shredderchess.com/online/playshredder/fetch.php?action=tacticsoftheday&day=0&level=" . strval($level);
		$response = $client->get($url);

		$data = $response->getBody()->__toString();

		if (! $data) return null;

		$puzzle = array();

		// Parse position and create board
		$data = substr($data, strpos($data, '|') + 1);
		$data = explode(' ', $data);

		$pos = $data[0];
		$puzzle['turn'] = $data[1] == 'b' ? self::WHITE : self::BLACK;
		for ($i = 0, $j = count($data); $i < $j; $i++) {
			if (strpos($data[$i], '_') !== false) break;
		}
		$moves = array_slice($data, $i + 1);

		$fm = explode('_', $data[$i]);
		$firstMove = explode('-', $fm[1]);

		$puzzle['board'] = $this->makeBoardHtml($pos, $puzzle['turn'], $firstMove);

		// Parse solution
		$solution = '';
		for ($i = 0, $j = count($moves); $i < $j; $i++) {
			$arr = explode('-', $moves[$i]);
			$solution .= $this->numToSq($arr[0]) . '-' . $this->numToSq($arr[1]) . ' ';
		}
		$puzzle['solution'] = rtrim($solution);

		return $puzzle;
	}


	/**
	 * If the site is down and we can't get a puzzle, load a backup puzzle
	 * @param integer $level
	 * @return array
	 */
	protected function getBackupPuzzle($level)
	{
		$json = file_get_contents(__DIR__ . "/backup/$level.ser");
		$puzzle = json_decode($json, true);
		return $puzzle;
	}


	/**
	 * Returns an HTML representation of the board with the given FEN position
	 * @param string $fen
	 * @param integer $turn
	 * @param array $firstMove
	 * @return string
	 */
	private function makeBoardHtml ($fen, $turn, $firstMove)
	{
		$pieceMap = array(
			'K' => "&#9812;",
			'Q' => "&#9813;",
			'R' => "&#9814;",
			'B' => "&#9815;",
			'N' => "&#9816;",
			'P' => "&#9817;",
			'k' => "&#9818;",
			'q' => "&#9819;",
			'r' => "&#9820;",
			'b' => "&#9821;",
			'n' => "&#9822;",
			'p' => "&#9823;"
		);

		$board = array_pad(array(), 64, self::NONE);
		for ($i = 0, $j = strlen($fen), $k = 56; $i < $j; $i++) {
			if ($fen[$i] == '/') {
				$k -= 16;
			} elseif (is_numeric($fen[$i])) {
				$k += intval($fen[$i]);
			} else {
				$board[$k++] = $fen[$i];
			}
		}

		// Make initial move
		$board[$firstMove[1]] = $board[$firstMove[0]];
		$board[$firstMove[0]] = self::NONE;

		$html = '<table id="board">';
		for ($i = 0; $i < 8; $i++) {
			$ii = $turn == self::WHITE ? 8 - $i : $i + 1;
			$html .= "<tr style='vertical-align: bottom;'><td class='num'>$ii</td></td>";

			for ($j = 0; $j < 8; $j++) {
				$pos = $turn == self::WHITE ? (7 - $i) * 8 + $j : $i * 8 + (7 - $j);
				$color = ($i + $j) % 2 == 0 ? 'light' : 'dark';
				$piece = $board[$pos] === self::NONE ? self::BLANK : $pieceMap[$board[$pos]];

				$html .= "<td class='square $color'>$piece</td>";
			}
			$html .= '</tr>';
		}

		$html .= '<tr><td>&nbsp;</td>';
		for ($i = 0; $i < 8; $i++) {
			$ii = $turn == self::WHITE ? $i : 7 - $i;
			$html .= '<td class="num">' . chr(97 + $ii) . '</td>';
		}
		$html .= '</tr></table>';

		return $html;
	}

	/**
	 * Converts a board index (0-63) to algebraic coordinate (a1-h8)
	 * @param integer $i
	 * @return string
	 */
	protected function numToSq ($i)
	{
		return chr(97 + $i % 8) . (intval($i / 8) + 1);
	}
}