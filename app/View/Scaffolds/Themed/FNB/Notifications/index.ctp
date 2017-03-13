<section class="notification-wrap">
	<div class="container">
		<div class="row">
			<div class="full-profile-info col-sm-12 m0">
				<h3 class="headings">Notifications</h3>
				<ul class="notification-list">
					<?php foreach($notifications as $nott) {?>
					<li>
						<figure>
							<?php 
								echo $this->CustomImage->show_user_avatar($nott['Sender'], 'ProfilePicture', 132, 132, array('class' => 'user_imagePreview circular'));
							?>
						</figure>
						<article>
							<span class="note-time"><?php echo $nott['Notification']['created']?></span>
							<p>
								<?php echo $nott['Notification']['notification_data']?>
							</p>
						</article>
						<div class="clear"></div>
					</li>
					<?php }?>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--main-blocks section Ends Here-->