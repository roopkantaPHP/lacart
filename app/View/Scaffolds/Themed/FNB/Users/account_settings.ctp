<section class="inner-middle-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                 <?php echo $this->element('profile_side_bar_menu', array('active_class' => 'acc-settings', 'profile_image' => 'ProfilePicture'))?>
                <div class="full-profile-info">
                    <div class="title-bar">
                        <h1>Account Settings</h1>
                    </div>
                    <div class="primary-info">
                            <div class="form-group">                                        
                                <div class="row" style="height: 50px;">
                                    <div class="col-md-10">
                                    	<?php echo $this->Form->create('User', array('class' => 'email_change_form', 'role' => 'form'));?>
                                        <div class="row">
                                            <label for="name" class="col-md-3 control-label">Current Email </label>
                                            <div class="col-md-6 no-pd">
                                            	<?php
	                                        	 	echo $this->Form->hidden('action', array('value' => 'email_change'));
													echo $this->Form->input('id');
						                        	echo $this->Form->input(
						                        		'email', array(
						                        	 		'label' => false, 'class' => 'form-control col-sm-12',
						                        	 		'required' => true, 'div' => false
														)
													);
						                    	 ?>
                                            </div> 
                                            <div class="col-md-3">
                                            	<?php echo $this->Form->submit('Change', array('class' => 'btn black-btn col-sm-12'))?>
                                            </div>
                                        </div>
                                        <?php echo $this->Form->end();?>
                                    </div>
                                </div>
                            </div>                                   
                            <h4>Change Password</h4>
                            <hr />
                            <?php echo $this->Form->create('User', array('role' => 'form', 'class' => 'password_change_form'));?>
                            <div class="form-group">                                        
                                <div class="row" style="height: 50px;">
                                    <div class="col-md-10">
                                        <div class="row">
                                            <label for="name" class="col-md-3 control-label">Old Password </label>
                                            <div class="col-md-5 no-pd">
                                            	 <?php
	                                        	 	echo $this->Form->hidden('action', array('value' => 'change_password'));
						                        	echo $this->Form->input(
						                        		'old_password', array(
						                        	 		'label' => false, 'class' => 'form-control col-sm-12',
						                        	 		'required' => true, 'div' => false, 'type' => 'password'
														)
													);
						                    	 ?>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>      
                            <div class="form-group">                                        
                                <div class="row" style="height: 50px;">
                                    <div class="col-md-10">
                                        <div class="row">
                                            <label for="name" class="col-md-3 control-label">New Password </label>
                                            <div class="col-md-5 no-pd">
                                                <?php
													echo $this->Form->input(
						                        		'new_password', array(
						                        	 		'label' => false, 'class' => 'form-control col-sm-12',
						                        	 		'required' => true, 'div' => false, 'type' => 'password'
														)
													);
						                    	 ?>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">                                        
                                <div class="row" style="height: 50px;">
                                    <div class="col-md-10">
                                        <div class="row">
                                            <label for="name" class="col-md-3 control-label">Retype  Password </label>
                                            <div class="col-md-5 no-pd">
                                                 <?php
													echo $this->Form->input(
						                        		'confirm_new_password', array(
						                        	 		'label' => false, 'class' => 'form-control col-sm-12',
						                        	 		'required' => true, 'div' => false, 'type' => 'password'
														)
													);
						                    	 ?>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="form-group">                                        
                                <div class="row" style="height: 50px;">
                                    <div class="col-md-10">
                                        <div class="row">
                                        <div class="col-sm-3"></div>
                                        	<?php echo $this->Form->submit('Confirm', array('class' => 'yellowBtn'))?>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                    	<?php echo $this->Form->end();?>
                        <div class="clear"></div>
                    </div>
                    <div class="get-social deletAcount">  
                        <h4>Delete Account </h4>
                        <hr />
                        <p><strong>Caution :</strong> Deleting your account is permanent, and any purchases you've made cannot be transferred to a new account.</p>
                        <div class="col-md-2 no-pd mt10">
	                      	<?php echo $this->Form->postLink('Delete Account', array('controller' => 'users', 'action' => 'remove_profile'), array('class' => 'btn black-btn col-sm-12'), __('Are you sure you want to delete your account'))?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<?php echo $this->element('trending'); ?>
<?php echo $this->element('newsletter'); ?>
<!--Trending section Ends Here-->
<style>
	.browse_button{
		display:none !important;
	}
	
</style>
<!--Subscribe section Ends Here-->
<script>
$(document).ready(function() {
	$('.email_change_form').validate({
		rules : {
			'data[User][email]' : {
				'required': true,
				'email' : true
			}
		}
	});
	$('.password_change_form').validate({
		rules : {
				'data[User][old_password]': {
					'required': true,
					minlength: 6
			     },
			     'data[User][new_password]': {
					'required': true,
					minlength: 6
			     },
			    'data[User][confirm_new_password]': {
			      equalTo: "#UserNewPassword"
			    }
			}
	});
	$('.upload_picture').click(function(e)
	{
		e.preventDefault();
		$(this).closest('form').find('.browse_button').click();
	});
	$('.browse_button').change(function(e) {
    	readURL(this);
	});
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
            	console.log($(input));
                $(input).closest('form').find('.user_imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
        $("#UserEditForm").submit();
    }
	$('.summernote').summernote({
        height: 200,
        toolbar:
        [
            //['style', ['style']], // no style button
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            //['insert', ['picture', 'link']], // no insert buttons
        ]
    });
 });
</script>
