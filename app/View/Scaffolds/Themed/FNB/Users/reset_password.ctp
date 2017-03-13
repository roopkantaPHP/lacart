<section class="main-blocks eiensteen-block">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mt50">
                <figure>
            		<?php
            			//echo $this->Html->image('/images/img.png', array('class' => 'full-width', 'alt' => 'about-us')) 
            		?>
                </figure>	
            </div>
            <div class="col-md-4 col-md-offset-1 block-text">
				<h3 class="blocks-heading mb10 fullwidth"> Reset Password </h3>
				<?php echo $this->Form->create('User');?>
					<?php ///echo $this->Form->hidden('ident', array('value' => $ident))?>
					<?php //echo $this->Form->hidden('activate', array('value' => $activate))?>
					<div class="form-group">
						<label for="inputEmail">New Password</label>
						<?php echo $this->Form->input('password', array('div' => false, 'class' => 'form-control', 'required', 'Placeholder' => 'Password', 'label' => false, 'id' => 'reset_password')); ?>
					</div>
					<div class="form-group">
						<label for="inputPasswordss">Confirm Password</label>
						<?php echo $this->Form->input('confirm_password', array('type' => 'password', 'div' => false, 'class' => 'form-control', 'required', 'Placeholder' => 'Confirm Password', 'label' => false, 'id' => 'confirm_reset_password')); ?>
					</div>
					<div class="login-wrap text-left">
						<?php echo $this->Form->button('Reset', array('class' => 'btn btn-primary login')); ?>
					</div>
				<?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
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
	});
</script>