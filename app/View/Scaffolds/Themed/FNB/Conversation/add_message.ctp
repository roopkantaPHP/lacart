<?php 
	if(isset($freshMessages) && !empty($freshMessages))
	{
		foreach ($freshMessages as $mkey => $mData)
		{ 
			if($mData['ConversationReply']['user_id'] == $userDetails['User']['id'])
			{ 
		?>
			<li rel="<?php echo $mData['ConversationReply']['created']; ?>" class="messageLi">
				<figure class="discuss-user">
					<?php
						$imgName = $this->Common->getProfileImage($mData);
						echo $this->Image->resize($imgName, 150, 150, true); 
					?>
				</figure>
				<div class="bubble">
					<h4><?php echo $mData['User']['name']; ?></h4>
					<p><?php echo $mData['ConversationReply']['reply']; ?></p>
					<span class="buble-info">
						<?php echo date('M dS, g:ia', $mData['ConversationReply']['date_time']); ?>
					</span>
				</div>
			</li>
			<?php
			}
			else
			{
			?>
			<li class="rgt-sec messageLi" rel="<?php echo $mData['ConversationReply']['created']; ?>">
				<div class="bubble1">
				<h4><?php echo $mData['User']['name']; ?></h4>
				<p><?php echo $mData['ConversationReply']['reply']; ?></p>
				<span class="buble-info">
					<?php echo date('M dS, g:ia', $mData['ConversationReply']['date_time']); ?>
				</span>
				</div>
				<figure class="discuss-user">
					<?php
						$imgName = $this->Common->getProfileImage($mData);
						echo $this->Image->resize($imgName, 150, 150, true); 
					?>
				</figure>
			</li>
			<?php
			}
		} 
	}
	?>