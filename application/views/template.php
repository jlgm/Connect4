<!DOCTYPE html>

<html lang="en">
	<head>
        <title>Connect4 Multiplayer</title>
        <link href="<?= base_url("css/template.css") ?>" rel="stylesheet" />
    	<script src="http://code.jquery.com/jquery-latest.js"></script>
    	<script src="<?= base_url("js/jquery.timers.js") ?>"></script>
    	<?php if (isset($script)) { ?><script><?php $this->load->view($script, $data) ?></script> <?php } ?>
	</head>
	<body>
	    <header>
                <h1><center>Connect 4</center></h1>
	    </header>
	    <div class="container">
	        <div id="main">
                    <center><?php $this->load->view($main); ?></center>
	        </div>
	    </div>
	</body>
</html>