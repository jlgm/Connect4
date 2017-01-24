<?php
//done
class Board_model {
    const P1 = 0, P2 = 1;
    
    public $turn = self::P1;
    public $p1_updated = TRUE, $p2_updated = TRUE;
    public $columns = array();
    
    public function initialize($value) {
        for ($i = 0; $i < $value; $i++) {
            $this->columns[] = array();
        }
    }
}