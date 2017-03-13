<?php if(isset($commentData) && !empty($commentData)){ ?>
	<li>
		<figure class="discuss-user">
		<?php 
			$imgName = 'img1.png';
			 if(isset($commentData['User']['image']) && !empty($commentData['User']['image'])){
				if(FILE_EXISTS(PROFILE_IMAGE_URL.$commentData['User']['image'])){
					$imgName = PROFILE_IMAGE_FOLDER.$commentData['User']['image'];
				}
			}
			echo $this->Image->resize($imgName, 150, 150, true); 
		?>
		</figure>
		<div class="bubble">
			<h4><?php echo $commentData['User']['name']; ?></h4>
			<p><?php echo $commentData['Comment']['comment']; ?></p>
			<span class="buble-info"><?php echo date('M dS, g:ia',strtotime($commentData['Comment']['created'])); ?></span>
		</div>
	</li> 	
<?php }
	else {
	echo 0;					
} ?>