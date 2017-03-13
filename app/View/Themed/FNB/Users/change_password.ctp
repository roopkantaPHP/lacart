<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<div class="container wdth100">
			<h3 class="fleft">Password  </h3> 
		</div>
	</section>
	<?php if(isset($errors) && !empty($errors)){
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
	<?php echo $this->Form->create('PaymentMethod');?>
	<section class="community-sec clearfix">
		<div class="discussion-container">
		<?php echo $this->session->flash();  ?>
			<ul>
				<?php 
				if(!isset($socialLogin) || !$socialLogin)
				{ ?>
				<li>
					<lable>Old password</lable>
					<?php echo $this->Form->password('User.old_password', array('label'=>false,'div'=>false,'class'=>'disc-field')); ?>
				</li>
				<?php } ?>
				<li>
					<lable>Password</lable>
					<?php echo $this->Form->password('User.password', array('label'=>false,'div'=>false,'class'=>'disc-field')); ?>
				</li>
				<li class="textbox">
					<lable>Re-type password</lable>
					<?php echo $this->Form->password('User.confirm_password', array('label'=>false,'div'=>false,'class'=>'disc-field'));
					echo $this->Form->submit('Save',array('class'=>'comment-sub-btn'));
					?>
				</li>
			</ul>			
			<div class="clearfix"></div>
		</div>
	</section>
	<?php echo $this->Form->end(); ?>
</section>