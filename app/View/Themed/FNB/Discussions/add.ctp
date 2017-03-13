<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
		    <h3 class="fleft">Discussions  </h3> 
		</main>
	</section>
	<section class="community-sec clearfix">
		<main class="discussion-container">
			<h4 class="comm-title">New Discussion</h4>
			<?php echo $this->Form->create('Discussion'); ?>
			<ul>
				<li>
					<?php echo $this->Form->input('Discussion.community_id',array('options'=>$communities,'class'=>'community-select','empty'=>'Select Community')); ?>
				</li>
				<li>
					<lable>Discussion title</lable>
					<?php echo $this->Form->input('Discussion.title',array('class'=>'disc-field count75','label'=>false,'div'=>false)); ?>
					<span class="char-info"> 75 characters</span>
				</li>
				<li class="textbox">
					<lable>Description</lable>
					<?php echo $this->Form->textarea('Discussion.description',array('class'=>'disc-field count200','label'=>false)); ?>
					<span class="char-info">200 characters</span>
					<?php echo $this->Form->submit('Post',array('class'=>'comment-sub-btn')); ?>
				</li>
			</ul>
			<?php echo $this->Form->end(); ?>			
			<div class="clearfix"></div>
		</main>
	</section>
</section>
	
