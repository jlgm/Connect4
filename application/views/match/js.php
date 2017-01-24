var otherUser = "<?= $otherUser->login ?>";
var user = "<?= $user->login ?>";
var status = "<?= $status ?>";
var board = [];

$(function(){
	$('body').everyTime(2000,function(){
		if (status == 'waiting') {
			$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
				if (data && data.status=='rejected') {
					alert("Sorry, your invitation to play was declined!");
					window.location.href = '<?= base_url() ?>arcade/index';
				}
				if (data && data.status=='accepted') {
					status = 'playing';
					$('#status').html('Playing ' + otherUser);
				}
					
			});
		} else if (status == 'playing') {    		
    		// Check whether the game is still being played.
    		$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
				if (data && data.status=='rejected') {
					alert("The other player has forfeited the game.");
					window.location.href = '<?= base_url() ?>arcade/index';
			    } else {
			        // Update the chat room.
            		var url = "<?= base_url() ?>board/getMsg";
            		$.getJSON(url, function (data,text,jqXHR){
            			if (data && data.status=='success') {
            				var conversation = $('[name=conversation]').val();
            				var msg = data.message;
            				if (msg.length > 0)
            					$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
            			}
            		});
            		
            		// Update the game board.
            		url = "<?= base_url("board/getState") ?>";
            		$("#game-area").load(url);
			    }
			});
		}
	});

	<?php if (isset($board)) { ?>
        <?php for ($i = 0; $i < count($board->columns); $i++) { ?>
        $("#game-area").on("click", "#insert-disk-<?= $i ?>", function() {
	        if (!$(this).hasClass("full-column")) {
	            var url = "<?= base_url("board/move/$i") ?>";
	            $.post(url, null, function(data, status, jqXHR) {
	                var response = jQuery.parseJSON(data);
	                if (response.status == 'success') {
	                    $("#game-area").load("<?= base_url("board/getState")?>");
	                } else {
	                    alert(response.message);
	                }
	            });
	        }
        });
        <?php } ?>
    <?php } ?>
});