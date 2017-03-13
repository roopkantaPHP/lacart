<h1>Administrator Login</h1>
<div align="center" class="whitebox mtop15">
	<?php echo $this->Session->flash()?>	
	<?php echo $this->Form->create('User'); ?>			
		<p class="top15">
			<?php echo $this->Form->input('username', array('class' => 'input', 'error' => false, 'label' => false, 'style' => 'width:350px', 'placeholder' => 'Username', 'div' => false)) ?>
					</p>
		<p class="mtop15">
			<?php echo $this->Form->input('password', array('class' => 'input', 'error' => false, 'label' => false,  'style' => 'width:350px', 'placeholder' => 'Password', 'div' => false)) ?>
			
		</p>

		<div class="top30">
			<div class=" top15">
<!--				<input type="checkbox" value="checkbox" class="checkbox" name="checkbox">Remember my login details-->
			</div>
			<br clear="all"/>
			<div class=""><input border="0" type="image" alt="Submit" src="<?php echo $this->webroot; ?>admin_images/submit-btn.gif" ></div>
		</div>
	<?php echo $this->Form->end(); ?>
</div>
