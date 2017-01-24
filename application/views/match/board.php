<h2>Game Area</h2>

<div>
Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  
</div>

<div id='status'> 
<?php 
	if ($status == "playing")
		echo "Playing " . $otherUser->login;
	else
		echo "Waiting on " . $otherUser->login;
?>
</div>
<?php
if (isset($board)) {
    echo "<section id='game-area'>";
    $this->load->view("match/board_view", $data);
    echo "</section>";
}
?>