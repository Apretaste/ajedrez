<?php

use GuzzleHttp\Client;

/**
 * Retrieves the tactics problem of the day from Shredder website.
 */
class AjedrezService extends ApretasteService
{

    const NONE = 0, WHITE = 1, BLACK = -1;

    const EASY = 0, MEDIUM = 1, HARD = 2;

    const BLANK = '&nbsp;';

    /**
     * Function executed when the service is called
     */
    public function _main()
    {
        // Get requested difficulty level
        if (empty($this->request->input->data->query)) {
            $level = self::MEDIUM;
        } elseif (stripos($this->request->input->data->query, 'f') === 0) {
            $level = self::EASY;
        } elseif (stripos($this->request->input->data->query, 'd') === 0) {
            $level = self::HARD;
        } else {
            $level = self::MEDIUM;
        }

        // Return cached response if fetched today
        $today = mktime(0, 0, 0);
        /* $cached = @unserialize(file_get_contents(__DIR__."/cache/$level.ser"));
         if ($cached && $cached['date'] == $today) {
             return $cached['response'];
         }
 */
        $levelMap = [
            self::EASY   => 'Fácil',
            self::MEDIUM => 'Intermedio',
            self::HARD   => 'Difícil'
        ];

        $puzzle = $this->fetchPuzzle($level);
        if (!$puzzle) {
            $puzzle = $this->getBackupPuzzle($level);
        }

        $content = [
            'board'        => $puzzle['board'],
            'solution'     => $puzzle['solution'],
            'solutionData' => $puzzle['solutionData'],
            'level'        => $levelMap[$level],
            'levelNumber' =>  $level + 1,
            'turnStr'      => $puzzle['turn'] == self::WHITE ? 'blancas' : 'negras'

        ];

        $this->response->setCache("day");
        $this->response->setLayout('ajedrez.ejs');
        $this->response->setTemplate("basic.ejs", $content);

        // Cache response
        $cache = [
            'date'     => $today,
            'response' => $this->response
        ];

        file_put_contents(__DIR__."/cache/$level.ser", serialize($cache));

    }

    /**
     * Retrieve daily puzzle from the remote site
     *
     * @param integer $level (0 = easy, 1 = medium, 2 = hard)
     *
     * @return array
     */
    protected function fetchPuzzle($level)
    {
        $client = new Client();
        $url = "http://www.shredderchess.com/online/playshredder/fetch.php?action=tacticsoftheday&day=0&level=".strval($level);
        $response = $client->get($url);

        $data = $response->getBody()->__toString();

        if (!$data) {
            return null;
        }

        $puzzle = [];

        // Parse position and create board
        $data = substr($data, strpos($data, '|') + 1);
        $data = explode(' ', $data);

        $pos = $data[0];
        $puzzle['turn'] = $data[1] === 'b' ? self::WHITE : self::BLACK;
        for ($i = 0, $j = count($data); $i < $j; $i++) {
            if (strpos($data[$i], '_') !== false) {
                break;
            }
        }
        $moves = array_slice($data, $i + 1);

        $fm = explode('_', $data[$i]);
        $firstMove = explode('-', $fm[1]);

        $puzzle['board'] = $this->makeBoardHtml($pos, $puzzle['turn'], $firstMove);

        // Parse solution
        $solution = '';
        $puzzle['solutionData'] = [];
        for ($i = 0, $j = count($moves); $i < $j; $i++) {
            $arr = explode('-', $moves[$i]);
            $start = $this->numToSq($arr[0]);
            $end = $this->numToSq($arr[1]);
            $ss = $start.'-'.$end.' ';
            $solution .= $ss;
            $puzzle['solutionData'][] = [
                'start' => $start,
                'end'   => $end
            ];
        }

        $puzzle['solution'] = rtrim($solution);

        return $puzzle;
    }

    /**
     * If the site is down and we can't get a puzzle, load a backup puzzle
     *
     * @param integer $level
     *
     * @return array
     */
    protected function getBackupPuzzle($level)
    {
        $json = file_get_contents(__DIR__."/backup/$level.ser");
        $puzzle = json_decode($json, true);

        return $puzzle;
    }

    /**
     * Returns an HTML representation of the board with the given FEN position
     *
     * @param string  $fen
     * @param integer $turn
     * @param array   $firstMove
     *
     * @return string
     */
    private function makeBoardHtml($fen, $turn, $firstMove)
    {
        $pieceMap = [
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
        ];

        $board = array_pad([], 64, self::NONE);
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

        $html = '<table id="board" border="1" cellpadding="3" cellspacing="0">';
        for ($i = 0; $i < 8; $i++) {
            $ii = $turn == self::WHITE ? 8 - $i : $i + 1;
            $html .= "<tr><td width=\"20\" align=\"center\"><small>$ii</small></td></td>";

            for ($j = 0; $j < 8; $j++) {
                $jj = $turn == self::WHITE ? $j : 7 - $j;
                $letter = chr(97 + $jj);
                $pos = $turn == self::WHITE ? (7 - $i) * 8 + $j : $i * 8 + (7 - $j);
                $color = ($i + $j) % 2 == 0 ? 'white' : '#C1C1C1';
                $piece = $board[$pos] === self::NONE ? self::BLANK : $pieceMap[$board[$pos]];
                $html .= "<td class=\"fritz-cell\" id=\"$letter{$ii}\" width=\"20\" heigth=\"20\" bgcolor=\"$color\" align=\"center\" valign=\"middle\">$piece</td>";
            }
            $html .= '</tr>';
        }

        $html .= '<tr><td>&nbsp;</td>';
        for ($i = 0; $i < 8; $i++) {
            $ii = $turn == self::WHITE ? $i : 7 - $i;
            $html .= '<td align="center"><small>'.chr(97 + $ii).'</small></td>';
        }
        $html .= '</tr></table>';

        return $html;
    }

    /**
     * Converts a board index (0-63) to algebraic coordinate (a1-h8)
     *
     * @param integer $i
     *
     * @return string
     */
    protected function numToSq($i)
    {
        return chr(97 + $i % 8).((int)($i / 8) + 1);
    }
}
