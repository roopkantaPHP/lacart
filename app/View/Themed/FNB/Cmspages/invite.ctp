<?php 
     if(isset($errors) && !empty($errors)){
		$errr = '';
		foreach ($errors as $key => $value) {
			$errr .= '<div>'.$value[0].'</div>';
		}
		if(!empty($errr)){ ?>
			<div class="error_popup">
				<div class="error_title"><strong>Please make proper entries.</strong></div>
				<div onclick="close_error()" id="close_error">
					<?php echo $this->Html->image('cross_grey_small.png',array('height'=>10)); ?>
				</div>
				<?php echo $errr; ?>
				<div style="clear:both;"></div>
			</div>
		<?php }
	} ?>
<section class="create-dish-sec">
	<div class="flashDiv">
		<div> <?php echo $this->Session->flash(); ?> </div>
	</div>
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft">Invite Friends  </h3> 
		</main>
	</section>

	<section class="community-sec clearfix">
		<main class="discussion-container invite-frnds">
			<h4 class="comm-title">Invite friends to use lacart!</h4>
			<div class="invite-btns">
			<?php echo $this->Html->link('<button class="in-frnd share">Share it</button>','https://www.facebook.com/sharer/sharer.php?u=lacart.com',array('escape'=>false,'target'=>'_blank','onclick'=>"window.open(this.href, 'mywin',
'left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;")); ?>
			
			<?php echo $this->Html->link('<button class="in-frnd tweet">Tweet it</button>','http://twitter.com/share?url=http://lacart.com&text=Join Lacart!&via=Lacart&related=Fuisine',array('escape'=>false,'target'=>'_blank','onclick'=>"window.open(this.href, 'mywin','left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;")); ?>

			</div>
			<?php echo $this->Form->create('Invitation'); ?>
			<ul>
				<li>
					<lable>Invite by email</lable>
					<?php echo $this->Form->input('emailaddress',array('div'=>false,'label'=>false,'placeholder'=>'Add friend'."'".'s email addresses','class'=>'disc-field','style'=>'padding:auto;')); ?>
					<span class="char-info left-align">Separate email ids with commas</span>
				</li>
				<li>
					<lable>Invite by sms</lable>
					<?php echo $this->Form->input('phone',array('div'=>false,'label'=>false,'placeholder'=>'Add friend'."'".'s phone numbers','class'=>'disc-field','required'=>false,'style'=>'padding:auto;')); ?>
					<button class="send-invite clearfix">Send Invite</button>
					<span class="char-info left-align">Separate numbers with commas</span>
				</li>
			</ul>
			<?php echo $this->Form->end(); ?>
			<div class="clearfix"></div>
		</main>
	</section>
</section>
	