<section class="main-blocks eiensteen-block">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mt50">
                <figure>
            		<?php
            			echo $this->Html->image('/images/img.png', array('class' => 'full-width', 'alt' => 'about-us')) 
            		?>
                </figure>	
            </div>
            <div class="col-md-4 col-md-offset-1 block-text">
				<h2 class="blocks-heading mb10 fullwidth"> Restricted Page </h2>
				Please Go Back To Home Page
				<?php 
					echo $this->Html->link('Go', array('controller' => 'users', 'action' => 'index'));
				?>
            </div>
        </div>
    </div>
</section>
<!--main-blocks section Ends Here-->
<?php echo $this->element('newsletter'); ?>
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