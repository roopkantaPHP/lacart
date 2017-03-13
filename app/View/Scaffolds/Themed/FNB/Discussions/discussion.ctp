<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft discu-title">Discussions </h3>
			<div class="clearfix"></div>
			<div class="comunity-bradcrum">
			<?php echo $this->Html->link('All Communities',
										array('controller'=>'discussions','action'=>'explore_com'),
										array('class'=>'red-clr'));
			if(isset($discussion['Community']) && !empty($discussion['Community'])){
				echo " . ";
				echo $this->Html->link($discussion['Community']['title'],
										array('controller'=>'discussions','action'=>'community',$discussion['Community']['id']),
										array('class'=>'red-clr'));
			}
			?>		
			</div>
		</main>
	</section>
	<section class="community-sec clearfix">
		<main class="container">
			<?php if(isset($discussion['Discussion']['title']) && !empty($discussion['Discussion']['title'])){ ?>
				<h4 class="comm-title">
					<?php echo $discussion['Discussion']['title']; ?><br/>
					<span><?php echo "Posted on ".date('M d, g:ia',strtotime($discussion['Discussion']['created'])); ?> 
					<?php if(isset($discussion['User']['name']) && !empty($discussion['User']['name']))
						echo "by ".$discussion['User']['name'];
					?>
					</span>
				</h4>
				<ul class="discussion alignleft">
					<?php if(isset($discussion['Comment']) && !empty($discussion['Comment'])){
						foreach ($discussion['Comment'] as $key => $commentData) { ?>
							<li>
								<figure class="discuss-user">
								<?php 
									$imgName = 'img1.png';
									if(isset($commentData['User']['image']) && !empty($commentData['User']['image']))
									{
										if(FILE_EXISTS(PROFILE_IMAGE_URL.$commentData['User']['image'])){
											$imgName = PROFILE_IMAGE_FOLDER.$commentData['User']['image'];
										}
									}
									echo $this->Image->resize($imgName, 150, 150, true); 
								?>
								</figure>
								<div class="bubble">
									<h4><?php echo $commentData['User']['name']; ?></h4>
									<p><?php echo $commentData['comment']; ?></p>
									<span class="buble-info"><?php echo date('M dS, g:ia',strtotime($commentData['created'])); ?></span>
								</div>
							</li> 	
					<?php } 
					 }

					if(isset($userDetails['User']) && !empty($userDetails['User'])){ ?>
						<li class="reply">
							<figure class="discuss-user">
								<?php 
									$imgName = $this->Common->getProfileImage($userDetails);
									echo $this->Image->resize($imgName, 150, 150, true); 
								?>
							</figure>
							<?php echo $this->Form->create('Comment'); ?>
							<div class="textbox">
								<h4>Post a reply</h4>
								<?php echo $this->Form->textarea('Comment.comment',array('class'=>'count200')); ?>
								<?php echo $this->Form->hidden('Comment.discussion_id',array('value'=>$discussion['Discussion']['id'])); ?>
								<span class="char-info"> 200 characters</span>
								 <?php echo $this -> Js -> submit('Submit comment', array('url' => array('controller' => 'discussions', 'action' => 'addComment'),
								 	'async' => true,
								 	'type' => 'html',
									'class' => "comment-sub-btn",
									'before' => 'return validateForm("CommentDiscussionForm");',
									'success' => 'appendHtmlData(data)')
									); ?>
							</div>
							<?php echo $this->Form->end(); ?>
						</li>
					<?php } ?>
				</ul>
				<div class="clearfix"></div>
			<?php } ?>
		</main>
	</section>
</section>
<script>
	function appendHtmlData(htmlData){
		if(htmlData){ 
			$('li.reply').before(htmlData);
		}
		else{
			alert("Sorry, your comment has not added.");
		}
		$('#CommentComment').val('');
	}
</script>