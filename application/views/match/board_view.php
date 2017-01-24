<table class="board">
    <tr>
    <?php
    $index = 0;
    foreach ($board->columns as $column) {
	
		if ($num != $board->turn || $match_status != Match::ACTIVE) {
			echo "<th id='insert-disk-$index' class=\"no-click\" chip-$color'>
					<div class='chip-$color no-click'></div></th>";
		}
        else {
			echo "<th id='insert-disk-$index' chip-$color'>
					<div class='chip-$color '></div></th>";
			$index++;
		}
    }
    ?>
    </tr>
    <tbody>
    <?php
    for ($index = 5; $index >= 0; $index--) {
        echo "<tr>";
        foreach ($board->columns as $column) {
            if (count($column) > $index) {
                $tmp = $column[$index] == 1 ? "yellow" : "red";
                echo "<td class='p$column[$index]'><div class='chip-$tmp'></div></td>";
            } else {
                echo "<td><div class='empty-square'></div></td>";
            }
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<br>
<?php if (($match_status == Match::U1WON and $num == Board_model::P1) or 
        ($match_status == Match::U2WON and $num == Board_model::P2)) {
    echo "<h3>:) You won! :) </h3>";
} else if (($match_status == Match::U1WON and $num == Board_model::P2) or 
        ($match_status == Match::U2WON and $num == Board_model::P1)) {
    echo "<h3>:( You lost! :( </h3>";
} else if ($status == "waiting") {
    echo "<h3>Waiting for the game Start.</h3>";
} else if ($num != $board->turn) {
    echo "<h3>Waiting the other player...</h3>";
} else {
    echo "<h3>Your turn! Play!</h3>";
} ?>
