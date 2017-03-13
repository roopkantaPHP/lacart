<section class="dish-ingredient-sec">
	<section class="create-dish-title User-title clearfix">
		<div class="container wdth100">
			<h3 class="fleft">Preferences</h3> 
		</div>
	</section>
	<?php 	echo $this->Form->create('User');
			echo $this->Session->flash();
	$checkDine = true; ?>
	<section class="allergens clearfix">
		<h3 class="ingred-title-bar">Allergens</h3><br/>
		<section class="common-alergen-checkbox">
			<?php
				$UserAllergy = array();
				$UserDiet = array();
				if(isset($UserDetails['User']['allergy']) && !empty($UserDetails['User']['allergy']))
					$UserAllergy = explode('::::::::',$UserDetails['User']['allergy']);

				if(isset($UserDetails['User']['diet']) && !empty($UserDetails['User']['diet']))
					$UserDiet = explode(',',$UserDetails['User']['diet']);
				
				if(isset($allergies) && !empty($allergies)){
					foreach ($allergies as $aId => $allergyValue) {
						$checkDine = false;
						if(in_array($allergyValue, $UserAllergy))
							$checkDine = true;
					 ?>
						<span class="portion-checkbox">
							<?php echo $this->Form->checkbox('User.allergy.'.$allergyValue,array('id'=>$allergyValue,'label'=>false,'div'=>false,'checked'=>$checkDine,'hiddenField' => false)); ?>
							<label for="<?php echo $allergyValue; ?>"><span></span><?php echo $allergyValue; ?></label>
						</span>			
					<?php }
				}
			?>
			
			<span class="portion-checkbox">
				<?php echo $this->Form->checkbox('User.allergy.other',array('id'=>'other','label'=>false,'div'=>false,'hiddenField' => false)); ?>
				<label for="other"><span></span>others</label>
			</span>
			<br>
			<?php echo $this->Form->input('User.other_allergy_text',array('div'=>false,'label'=>false,'class'=>'kitc-infield hide','placeholder'=>"Type your allergy")); ?>
		</section>
	</section>

	<section class="Commmon-allergens clearfix alldiets">
		<h3 class="ingred-title-bar">Diet</h3><br/>
		<section class="common-alergen-checkbox">
			<?php
				$checkNonVeg = $checkVeg = $checkVegan = false;
				if(!empty($UserDiet)){
					if(in_array('Non-Vegetarian', $UserDiet))
						$checkNonVeg = true;
					if(in_array('Vegetarian', $UserDiet))
						$checkVeg = true;
					if(in_array('Vegan', $UserDiet))
						$checkVegan = true;
				}	 
			?>
			<span class="portion-checkbox">
				<?php echo $this->Form->checkbox('User.diet.Non-Vegetarian',array('id'=>'nveg-diet','label'=>false,'div'=>false,'checked'=>$checkNonVeg,'hiddenField' => false)); ?>
				<label for="nveg-diet"><span></span>Non Vegetarian</label>
			</span>
			<span class="portion-checkbox">
				<?php echo $this->Form->checkbox('User.diet.Vegetarian',array('id'=>'veg-diet','label'=>false,'div'=>false,'checked'=>$checkVeg,'hiddenField' => false)); ?>
				<label for="veg-diet"><span></span>Vegetarian</label>
			</span>
			<span class="portion-checkbox">
				<?php echo $this->Form->checkbox('User.diet.Vegan',array('id'=>'vegan-diet','label'=>false,'div'=>false,'checked'=>$checkVegan,'hiddenField' => false)); ?>
				<label for="vegan-diet"><span></span>Vegan</label>
			</span>
			<br/>
			<?php echo $this->Form->submit('Save',array('class'=>'btn-next')); ?>
	</section>
	<?php echo $this->Form->end(); ?>
</section>
<script>
$('#other').change(function(){
	if($(this).is(':checked')){
		$('#UserOtherAllergyText').show();
	}else{
		$('#UserOtherAllergyText').hide();
	}
});
$(".alldiets input:checkbox").change(function() {  
	var currentCheck = this;
	$(".alldiets input:checkbox").each(function() {
		if($(this).is(':checked') && $(currentCheck).attr('id') != $(this).attr('id')){
			$(this).attr('checked',false);
			$(this).parent('span').removeClass('selected');
		}
    });
});
</script>