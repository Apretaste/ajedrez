<?php

	use Goutte\Client;

	class Ajedrez extends Service
	{
		const NONE = 0, WHITE = 1, BLACK = -1;
		
		/**
		 * Function executed when the service is called
		 *
		 * @param Request
		 * @return Response
		 * */
		public function _main(Request $request)
		{
			if (empty($request->query)) $diff = 1;
			elseif (stripos($request->query, 'f') === 0) $diff = 0;
			elseif (stripos($request->query, 'd') === 0) $diff = 2;
			else $diff = 1;
			
			$images = array(
				"{$this->pathToService}/images/wk.png",
				"{$this->pathToService}/images/wq.png",
				"{$this->pathToService}/images/wr.png",
				"{$this->pathToService}/images/wb.png",
				"{$this->pathToService}/images/wn.png",
				"{$this->pathToService}/images/wp.png",
				"{$this->pathToService}/images/bk.png",
				"{$this->pathToService}/images/bq.png",
				"{$this->pathToService}/images/br.png",
				"{$this->pathToService}/images/bb.png",
				"{$this->pathToService}/images/bn.png",
				"{$this->pathToService}/images/bp.png"
			);
			
			$response = new Response();
			$response->setResponseSubject("Problema de ajedrez");
			$response->createFromTemplate("basic.tpl", $this->getPuzzle($diff), $images);
			return $response;
		}

		
		/**
		 * Get daily puzzle 
		 * 
		 * @param int difficulty (0 = easy, 1 = medium, 2 = hard)
		 * @return array (board, solution)
		 * */
		private function getPuzzle($diff)
		{
			$client = new Client();
			
			$url = "http://www.shredderchess.com/online/playshredder/fetch.php?action=tacticsoftheday&day=0&level=" . strval($diff);
			$data = $client->request('GET', $url);
		
			$data = substr($data, strpos($data, '|') + 1);
			$data = explode(' ', $data);
			
			// Parse response
			$pos = $data[0];
			$toMove = $data[1] == 'b' ? self::WHITE : self::BLACK;
			for ($i = 0, $j = count($data); $i < $j; $i++) {
				if (strpos($data[$i], '_') !== false) break;
			}
			$moves = array_slice($data, $i + 1);
			
			$fm = explode('_', $data[$i]);
			$firstMove = explode('-', $fm[1]);
			
			// Create board html
			$board = $this->printBoard($pos, $toMove, $firstMove);
			
			// Parse solution
			$solution = '';
			for ($i = 0, $j = count($moves); $i < $j; $i++) {
				$arr = explode('-', $moves[$i]);
				$solution .= $this->numToSq($arr[0]) . '-' . $this->numToSq($arr[1]) . ' ';
			}
			$solution = rtrim($solution);
			
			// Return response content
			return array(
				'board'  => $board,
				'solution' => $solution,
				'turn' => $toMove == self::WHITE ? 'blancas' : 'negras'
			);
		}
		
		/**
		 * Returns an HTML representation of the board with the given FEN position
		 *
		 * @param String fen
		 * @param int toMove
		 * @param array firstMove
		 * @return String html
		 */
		private function printBoard ($fen, $toMove, $firstMove) {
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
			
			$html = '<table style="text-align:center;border-spacing:0pt;font-family:\'Arial Unicode MS\';border-collapse:collapse;border-color:#888888;border-width:1pt;">';
			for ($i = 0; $i < 8; $i++) {
				$ii = $toMove == self::WHITE ? 8 - $i : $i + 1;
				$html .= "<tr style='vertical-align: bottom;'><td style='vertical-align:middle;width:12pt;font-size:9pt;'>$ii</td></td>";
				for ($j = 0; $j < 8; $j++) {
					$pos = $toMove == self::WHITE ? (7 - $i) * 8 + $j : $i * 8 + (7 - $j);
					$bgc = ($i + $j) % 2 == 0 ? '#FFFFFF' : '#DDDDDD';
					
					if ($board[$pos] === self::NONE) $piece = '&nbsp;';
					else {
						$pp = (ord($board[$pos]) < 97 ? 'w' : 'b') . strtolower($board[$pos]);
						$piece = "<img src='{$this->pathToService}/images/$pp.png' width='36' height='36'>";
					}
					
					$html .= "<td style='width:38pt;height:38pt;font-size:28pt;padding:0;background-color:$bgc;border-collapse:collapse;border-style:solid;border-width: 1pt 0pt 0pt 0pt;border-color:#888888;vertical-align:middle;'>$piece</td>";
				}
				$html .= '</tr>';
			}
			$html .= '<tr><td>&nbsp;</td>';
			for ($i = 0; $i < 8; $i++) {
				$ii = $toMove == self::WHITE ? $i : 7 - $i;
				$html .= '<td style="font-size:9pt;">'.chr(97 + $ii).'</td>';
			}
			$html .= '</tr>';
			$html .= '</table>';
			
			return $html;
		}

		/**
		 * Converts a board index (0-63) to algebraic coordinate (a1-h8)
		 *
		 * @param int
		 * @return String
		 */
		private function numToSq ($i) {
			return chr(97 + $i % 8) . (intval($i / 8) + 1);
		}
	}
?>