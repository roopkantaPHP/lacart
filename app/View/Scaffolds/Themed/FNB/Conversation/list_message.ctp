<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft">Messages </h3> 
		</main>
	</section>

	<section class="community-sec clearfix">
		<main class="container">
			<ul class="messages-sec">
				<?php
				if(isset($messages) && !empty($messages))
				{
					foreach ($messages as $mKey => $mData) 
					{
						if($userId == $mData['Conversation']['sender_id'])
						{
							$other_person = $mData['Reciever'];
						}
						else 
						{
							$other_person = $mData['Sender'];
						}
					?>
				<a href="<?php echo $this->Html->url(array('controller'=>'conversations','action'=>'detail',$mData['Conversation']['id'])) ?>">	
					<li>
						<figure class="discuss-user">
							<?php 
							$userDetails['User']['image'] = $other_person['image']; 
							$imgName = $this->Common->getProfileImage($userDetails);
							echo $this->Image->resize($imgName, 150, 150, true); 
							?>
						</figure>
						<span class="msg-details">
							<b class="msg-title"><?php echo $other_person['name']; ?></b>
							<p><?php echo $mData['ConversationReply']['0']['reply']; ?></p>
							<span class="buble-info">
								<?php echo date('M dS, g:ia', $mData['ConversationReply']['0']['date_time']); ?>
								<b class="fright"><?php echo $mData['Conversation']['conversation_reply_count']; ?></b>
							</span>
						</span>
					</li>
				</a>			 	
					<?php
					} 
				}
				else
				{
					echo "Your inbox is empty.";
				}
				?>
			</ul>
		</main>
	</section>
</section>
