<?php if(isset($reviewData['Review']) && !empty($reviewData['Review'])){ ?>
			<li>
			    <div class="image">
			    	<div class="inr-img">
			    		<?php
							$imgName = $this->Common->getProfileImage($reviewData);
							echo $this->Image->resize($imgName, 150, 150, true); 
						?>
			    	</div>
			    </div>
				<h5><?php echo $reviewData['User']['name']; ?>
				<?php echo $this->Common->getRatingIcon($reviewData['Review']['rating']); ?>
			 	<span><?php echo $this->Common->getTimeAgo($reviewData['Review']['timestamp']); ?> ago</span></h5>
				<p><?php echo $reviewData['Review']['feedback']; ?></p>
		   </li>	
<?php } ?>