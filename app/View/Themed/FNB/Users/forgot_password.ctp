<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
		    <h3 class="fleft"><?php echo "Forgot Password"; ?></h3> 
		</main>
	</section>

    <section class="createdish-mid-sec clearfix" style="padding-top:0px;">
		<div style="height:55px; background-color:#FAFFBD;">
			<?php echo "Please enter your email id. A link to reset your password will be sent to your email id." ?>
		</div>
	    <?php  echo $this->Form->create('User',array('style'=>'margin-top:0px;')); ?>
			<main>
				<div style="height:55px;">
					<?php echo $this->Session->flash(); ?>
				</div>

				<li class="textbox marginleftAuto">
					<?php 
					echo $this->Form->input('User.email',array('class'=>'disc-field', 'style'=>'margin-bottom:15px;','placeholder'=>'Email Address','label'=>false,'div'=>false,'autocomplete'=>'off'));
					?>
				</li>
				
				<input type="submit" value="Reset" class="place-btn">
			</main>
		<?php echo $this->Form->end(); ?>
	</section>		  
</section>
