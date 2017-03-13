 <header class="header">
 <div class="container wdth100">

   <div class="logo">
	<?php echo $this->Html->link($this->Html->image('logo.png'),'/',array('escape' => false)); ?>
   </div>
   <div class="Hright-sec">
   	<?php
   		if($this->request->action == 'index' && $this->request->controller == 'users')
		{
		}
		else
		{ ?>
			<div class="search-btn"><?php echo $this->Html->link('SEARCH','javascript:void(1);',array('id'=>'searchBtnHeader')); ?></div>
		<?php
		}
   	?>

     <?php if(!$this->Session->check('Auth.User')) {?>
	 <ul class="login-sgnup">
	   <li><a href="#" class="su-btn signupbtn" onClick="commonJs.openClosePopup('.signuppopup')">Sign up</a>
	    <div class="sl-popup popup-signuplogin signup signuppopup" style="display:none;">
		  <div class="sl-inner">
		   <div class="overlay"><?php echo $this->Html->image('bx_loader.gif')?></div>
		   <div class="social-login"><?php echo $this->Html->image("fb.png", array(
										"alt" => "Signin with Facebook",
										'url' => array('controller'=>'social_login','action'=>'Facebook')
									)); ?>
			</div>
		   <div class="social-login"><?php echo $this->Html->image("google.png", array(
											"alt" => "Signin with Google",
											'url' => array('controller'=>'social_login','action'=>'Google')
											)); ?>
			</div>
		   <div class="or-devider"><?php echo $this->Html->image('or.png')?></div>
		   <div class="popup-form">
		      <?php echo $this -> Form -> create('User', array('url' => array('controller' => 'users', 'action' => 'signup'), 'id' => 'UserSignupForm', 'class' => '', 'inputDefaults' => array('label' => false, 'div' => false))); ?>
			   <div id="successMessage"></div>
			   <div id="errorMessage"></div>
			   <div class="form-field"><?php echo $this->Form->input('email', array('placeholder'=>'Email','class'=>'required email','id'=>'UserSignupEmail')) ?></div>
			   <div class="form-field"><?php echo $this->Form->input('phone', array('placeholder'=>'Mobile','id'=>'UserSignupPhone')) ?></div>
			   <div class="form-field"><?php echo $this->Form->input('password', array('placeholder'=>'Password', 'class'=>'required','id'=>'UserSignupPassword')) ?></div>
			   <div class="form-field"><?php echo $this->Form->input('confirm_password', array('type'=>'password','placeholder'=>'Re-type password', 'class'=>'required', 'equalTo'=>'#UserSignupPassword','id'=>'UserSignupConfirmPassword')) ?></div>
			   <div class="term"> <?php echo $this->Form->checkbox('i_agree', array('class'=>'required checkbox','div'=>'true')) ?> I agree to the terms & conditions</div>
			   <div class="form-field">
			   <?php echo $this -> Js -> submit('Sign up', array('url' => array('controller' => 'users', 'action' => 'signup'),
							'class' => "submit-btn",
							'before' => 'return validateForm("UserSignupForm");',
							'complete' => 'endloading();',
							'success' => 'showAjaxReturnMessageForPopup(data)')
							); ?>
			   </div>
			<?php echo $this -> Form -> end(); ?>
		   </div>
		  </div>
		</div>
	   </li>
	   <li><a href="#" class="su-btn loginbtn" onClick="commonJs.openClosePopup('.loginpopup')">Sign in</a>
	    <div class="sl-popup popup-signuplogin login loginpopup" style="display:none;">
		  <div class="sl-inner">
		   	<div class="overlay"><?php echo $this->Html->image('bx_loader.gif')?></div>
		     <div class="social-login"><?php echo $this->Html->image("fb.png", array(
										"alt" => "Signin with Facebook",
										'url' => array('controller'=>'social_login','action'=>'Facebook')
									)); ?>
			</div>
		   <div class="social-login"><?php echo $this->Html->image("google.png", array(
											"alt" => "Signin with Google",
											'url' => array('controller'=>'social_login','action'=>'Google')
											)); ?>
			</div>
		   <div class="or-devider"><?php echo $this->Html->image('or.png')?></div>
		   <div class="popup-form">
		      <?php

		      echo $this->Session->flash();

		      echo $this -> Form -> create('User', array('url' => array('controller' => 'users', 'action' => 'login'), 'id' => 'UserLoginForm', 'class' => '', 'inputDefaults' => array('label' => false, 'div' => false))); ?>
			   <div id="successMessage"></div>
			   <div id="errorMessage"></div>
			   <div class="form-field"><?php echo $this->Form->input('email', array('placeholder'=>'Email','class'=>'required email','id'=>'UserLoginEmail')) ?></div>
			   <div class="form-field"><?php echo $this->Form->input('password', array('placeholder'=>'Password','class'=>'required','UserLoginPassword'))?></div>
			   <div class="form-field">
			   	<?php echo $this -> Js -> submit('Sign In', array('url' => array('controller' => 'users', 'action' => 'login'),
			   			 'class' => "submit-btn",
			   			 'before' => 'return validateForm("UserLoginForm");',
			   			'complete' => 'endloading();',
			   			'success' => 'showAjaxReturnMessageForPopup(data)'
			   	)); ?>
			  </div>
			 <?php echo $this -> Form -> end(); ?>
		   </div>
		  </div>
		</div>
	   </li>
	 </ul>
	 <?php } else {?>
	 <ul class="login-sgnup">
	 	<li><?php
	 	if(!isset($userinfo['is_verified']) || empty($userinfo['is_verified']))
	 	{
	 		echo $this->Html->link('Verify', array('controller'=>'users','action'=>'send_verification'), array('class' => 'su-btn signupbtn verifyme'));
	 	}?>
	 	</li>
	 	<li><?php echo $this->Html->link('Logout', array('controller'=>'users','action'=>'logout'), array('class' => 'su-btn signupbtn')); ?></li>
	 </ul>
	 <?php }?>
   </div>
 </div>
  </header>
<?php echo $this->element('search_box')?>
<script>
	$(document).ready(function(){
		<?php if(isset($isLogin) && $isLogin="pleaselogin"){ ?>
			$('.loginbtn').click();
		<?php } ?>

		$(".verifyme").fancybox({
		    type : 'iframe',
		    autoSize : false,
			width: 500,
			height: 530,
        	padding : 0,
			helpers : {
		        overlay : {
		            css : {
		                'background' : 'rgba(58, 42, 45, 0.50)'
		            }
		        }
		    },
		});

		$('#searchBtnHeader').click(function(){
			$('.search-banner').slideToggle();
		});
	});
</script>
