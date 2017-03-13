<li>
	<div class="top-section col-md-12 no-pd mb10">
		<div class="col-md-1 no-pd name-section">
		<div class="circle short pull-left">
			<?php 
				echo $this->CustomImage->show_user_avatar($this->Session->read('Auth.User'), 'ProfilePicture', 40, 40, array('class' => 'user_imagePreview circular'));
			?>
		</div>
		<a href="courses-checkout.html">
				<?php echo $this->Session->read('Auth.User.name')?>
		</a>
		</div>
		<div class="name start-course col-md-7">
			<p><?php echo $comment['Comment']['name']?></p>
		</div>
		<div class="date col-md-3"><?php echo $this->Time->timeAgoInWords($comment['Comment']['created']);?> </div>
	</div>
</li>