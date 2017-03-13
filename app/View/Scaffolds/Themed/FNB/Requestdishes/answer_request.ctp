<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container">
		    <p class="fleft">Select a dish,or create one! </p> 
		    <span class="fright">
		    	<?php echo $this->Html->link('<input type="button" value="+ Create dish" class="create-dish-btn">',array('controller'=>'dishes','action'=>'add'),array('escape'=>false)); ?>
		    </span>
		</main>
	</section>

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
	<section class="createdish-mid-sec clearfix">
		<main class="container">
			<?php echo $this->Session->flash(); ?>
			<ul class="create-dishes-list alignleft">
				<?php 
				echo $this->Form->create('RequestAnswer');
				echo $this->Form->hidden('RequestAnswer.dish_id',array('value'=>''));
				echo $this->Form->end();
				
				if(isset($myAllDishes) && !empty($myAllDishes))
				{
					foreach ($myAllDishes as $dkey => $dishData)
					{
					?>
					<li>
						<figure class="dish-thum">
							<?php $imgName = $this->common->getDishImage($dishData);
								  echo $this->Image->resize($imgName, 150, 150, true); ?>
						</figure>
						<section class="dish-decripton clearfix">
							<h4><?php echo $dishData['Dish']['name']; ?></h4>
							<b class="timer"><?php echo $dishData['Dish']['serve_start_time'].'<small>Last order '.$dishData['Dish']['serve_end_time'].'</small>'; ?> </b><br/>
							<h3>$ &nbsp; 
							<?php 	if($dishData['Dish']['p_custom'])
								 		echo $dishData['Dish']['p_custom_price'];
								 	elseif ($dishData['Dish']['p_big']) 
								 		echo $dishData['Dish']['p_big_price'];
								 	else
								 		echo $dishData['Dish']['p_small_price'];
								?>
							 </h3><br>
						</section>
						<div class="createdish-dashboard">
							<input type="button" class="dishSelect" dishName="<?php echo $dishData['Dish']['name']; ?>" rel="<?php echo $dishData['Dish']['id']; ?>" onclick="confirm('Are you sure you want',makeit())" value="Select Dish">
						</div>
						<div class="clearfix"></div>
					</li>
					<?php
					}
				}
				?>
			</ul>
		</main>
	</section>		  
</section>
<script>
$(document).ready(function(){
	$('.dishSelect').click(function(){
		var dishName = $(this).attr('dishName');
		if(confirm("Do you want to select "+dishName+" as answer?"))
		{
			var dish_id = $(this).attr('rel');
			$('#RequestAnswerDishId').val(dish_id);
			$('#RequestAnswerAnswerRequestForm').submit();
		}
	});
});
</script>