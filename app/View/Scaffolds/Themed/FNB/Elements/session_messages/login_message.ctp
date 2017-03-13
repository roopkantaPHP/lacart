<?php
	$message_session = $this->Session->flash('flash');
	if((stristr($message_session, 'Link')) || (stristr($message_session, 'wrong')) || (stristr($message_session, 'no')) || stristr($message_session, 'Invalid')) {
?>
<div class="alert-pop alert alert-danger">
	<button class="close" data-close="alert"></button>
	<span>
		<?php echo $message_session;?>
	</span>
</div>
<?php } else {?>
	<div class="alert-pop alert alert-success">
		<button class="close" data-close="alert"></button>
		<span>
			<?php echo $message_session;?>
		</span>
	</div>
<?php }?>
