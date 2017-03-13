<section class="create-dish-sec">
    <section class="createdish-mid-sec clearfix">
	    <?php echo $this->Form->create('ConversationReply'); ?>
			<main>
			<?php echo $this->Session->flash(); ?>
				<span class="user-image">
					<div class="dish-img" style="width:100px; height:100px;">
						<?php
						$imgName = $this->Common->getProfileImage($userDetails);
						echo $this->Image->resize($imgName, 100, 100, true); 
						?>
					</div>
					<?php if($userDetails['User']['group_id'] != 1)
					{ ?>
					<span class="user-name"><?php echo $userDetails['User']['name']; ?></span>
					<?php
					}
					else
					{
					?>	
					<span class="user-name"><?php echo 'Admin'; ?></span>
					<?php } ?>
					<span class="user-address"><?php echo $userDetails['User']['address']; ?></span>
				</span>
				<br/>
				<li class="textbox marginleftAuto">
					<b class="about-me">Message</b><br/>
					<?php echo $this->Form->textarea('ConversationReply.reply',array('class'=>'community-select count500', 'style'=>'height:150px;','required'=>true));
					?>
					<span class="char-info"><?php echo "500 characters"; ?></span>
				</li>
				<input type="submit" value="Send" class="place-btn">
			</main>
		<?php echo $this->Form->end(); ?>
	</section>		  
</section>
<?php if(isset($closeFancy) && $closeFancy==1)
{ ?>
<script>
$(document).ready(function(){
	parent.$.fancybox.close();
});
</script>
<?php } ?>