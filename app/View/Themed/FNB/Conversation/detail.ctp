
<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft">Messages  </h3> 
		</main>
	</section>
	<section class="community-sec clearfix">
		<main class="container">
			<ul class="discussion alignleft">
				<?php 
				if(isset($messages) && !empty($messages))
				{
					foreach ($messages as $mkey => $mData)
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
				<?php
				if(isset($userDetails['User']) && !empty($userDetails['User'])){ ?>
						<li class="reply">
							<figure class="discuss-user">
								<?php 
									$imgName = $this->Common->getProfileImage($userDetails);
									echo $this->Image->resize($imgName, 150, 150, true); 
								?>
							</figure>
							<?php echo $this->Form->create('ConversationReply'); ?>
							<div class="textbox">
								<h4>Post a reply</h4>
								<?php echo $this->Form->textarea('ConversationReply.reply',array('class'=>'count500','required'=>true)); ?>
								<?php echo $this->Form->hidden('ConversationReply.conversation_id',array('value'=>$messages[0]['ConversationReply']['conversation_id']));
									  echo $this->Form->hidden('ConversationReply.last_reply_id',array('value'=>$messages[count($messages)-1]['ConversationReply']['created'])); ?>
								<span class="char-info"> 500 characters</span>
								 <?php echo $this -> Js -> submit('Reply', array('url' => array('controller' => 'conversations', 'action' => 'add_message'),
								 	'async' => true,
								 	'type' => 'html',
									'class' => "comment-sub-btn",
									'before' => 'return validateForm("ConversationReplyDetailForm");',
									'success' => 'appendHtmlData(data)')
									); ?>
							</div>
							<?php echo $this->Form->end(); ?>
						</li>
					<?php } ?>
			</ul>
			<div class="clearfix"></div>
		</main>
	</section>
</section>
<script>
	$('#ConversationReplyReply').focusout(function(){
		var lastMessageOn = $('ul.discussion li.messageLi:last').attr('rel');
		$('#ConversationReplyLastReplyId').val(lastMessageOn);
	});
	function appendHtmlData(htmlData){
		if(htmlData){ 
			$('#ConversationReplyReply').val('');
			$('li.reply').before(htmlData);
		}
		else{
			alert("Sorry, message sending failed.");
		}
	}
	$(document).ready(function(){
		setInterval(function() {
			var lastMessageOn = $('ul.discussion li.messageLi:last').attr('rel');
			var conversationId = $('#ConversationReplyConversationId').val();
		
		    $.ajax({
		    	'url': '<?php echo $this->Html->url(array('controller'=>'conversations','action'=>'get_message')); ?>',
		    	'async': true,
		    	'data' : {'last_reply_id':lastMessageOn,'conversation_id':conversationId},
		    	'type': 'post',
		    	'success': function(data){
		    		if(data)
		    		{
		    			$('li.reply').before(data);
		    			var lastMessageOn = $('ul.discussion li.messageLi:last').attr('rel');
						$('#ConversationReplyLastReplyId').val(lastMessageOn);
		    		}
		    	}
		    });
		}, 5000);
	});
</script>