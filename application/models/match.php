<?php
//done
class Match {
    const ACTIVE = 1;
    const U1WON = 2;
    const U2WON = 3;

    public $id;

    public $user1_id;
    public $user2_id;

    public $match_status_id = self::ACTIVE;

    public $board_state;

    public function initialize() {
        $board = new Board_model();
        $board->initialize(7);
        $this->board_state = base64_encode(serialize($board));
    }

    public function move($player, $column) {
        if ($column < 0 or $column >= 7) {
            return; //not a valid move, do nothing
        }

        if ($this->match_status_id == self::ACTIVE) {
            $board = unserialize(base64_decode($this->board_state));
            
            if ($board->turn != $player) {
                return; //fail, do nothing
            }           
             	
            if (count($board->columns[$column]) < 6) $board->columns[$column][] = $player;
            
            $board->turn = !$board->turn; //changing turn
            	
            $this->board_state = base64_encode(serialize($board));
        }
    }
    
    private static function count_recursive($board, $c, $r, $dx, $dy, $player) {
        if (self::invalid($board, $c, $r, $dx, $dy, $player)) {
            return 0;
        }
        
        if ($c + $dx >= 0 and $c + $dx < 7) {
            $i = count($board[$c + $dx]);
            if ($r + $dy >= 0 and $r + $dy < $i) {
                return 1 + self::count_recursive($board, $c + $dx, $r + $dy, $dx, $dy, $player);
            }
        }
        return 1;
    }

   public function won($column) {
        $board = unserialize(base64_decode($this->board_state))->columns;
        
        $row = count($board[$column]) - 1;
        $player = $board[$column][$row];
        
        $found = false;
        
        $dir = array(
            array(0, -1, 0, -1),  array(0, 1, 0, 1),
            array(-1, 0, -1, 0), array(1, 0, 1, 0),
            array(-1, -1, -1, -1), array(1, 1, 1, 1),
            array(-1, 1, -1, 1), array(1, -1, 1, -1)
        );
        
       for ($i = 0; $i < 8; $i+=2) {
            if (self::count_recursive($board, self::operation($column, $dir[$i][0]), self::operation($row,$dir[$i][1]), $dir[$i][2], $dir[$i][3], $player)
                + self::count_recursive($board, self::operation($column, $dir[($i+1)][0]), self::operation($row,$dir[($i+1)][1]), $dir[($i+1)][2], $dir[($i+1)][3], $player) == 3) {
                    $found = true;
            }
        }
        
        return $found;
    }
    
    private static function operation($a, $b) {
        if ($b < 0) return $a - abs($b);
        else return $a + $b;
    }
    
    private static function invalid($board, $col, $row, $dx, $dy, $player) {
        return $col < 0 or $col >= 7 or $row < 0 or $row >= count($board[$col]) or $board[$col][$row] != $player;
    }

}