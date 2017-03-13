<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
		    <h3 class="fleft"><?php echo "Reset Password"; ?></h3> 
		</main>
	</section>

    <section class="createdish-mid-sec clearfix" style="padding-top:0px;">
		<?php  echo $this->Form->create('User',array('style'=>'margin-top:0px;')); ?>
			<main>
				<div style="height:55px;">
					<?php echo $this->Session->flash(); ?>
				</div>

				<li class="textbox marginleftAuto">
					<?php 
					echo $this->Form->input('password',array('type'=>'password','class'=>'disc-field', 'style'=>'margin-bottom:15px;','placeholder'=>'New Password','label'=>false,'div'=>false,'autocomplete'=>'off'));
					?>
				</li>
				<li class="textbox marginleftAuto">
					<?php 
					echo $this->Form->input('confirm_password',array('type'=>'password','class'=>'disc-field', 'style'=>'margin-bottom:15px;','placeholder'=>'Confirm Password','label'=>false,'div'=>false,'autocomplete'=>'off'));
					?>
				</li>
				
				<input type="submit" value="Reset" class="place-btn">
			</main>
		<?php echo $this->Form->end(); ?>
	</section>		  
</section>

<!--main-blocks section Ends Here-->

<script>
	$(document).ready(function ()
	{
		$('#UserResetPasswordForm').validate({
			rules : {
				'data[User][password]': {
					'required': true,
					minlength: 6
			     },
			    'data[User][confirm_password]': {
			      equalTo: "#reset_password"
			    }
			}
		});

		<?php if(isset($closeFancy) && $closeFancy==1)
		{ ?>
			window.top.location.href = '<?php echo $this->Html->url(array('controller'=>'users','action'=>'index','pleaselogin')); ?>'; 
		<?php } ?>
	});
</script>