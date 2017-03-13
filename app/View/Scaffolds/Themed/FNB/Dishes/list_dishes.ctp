<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft"> Your dishes </h3> 
			<span class="fright">
				<?php echo $this->Html->link('<input type="button" value="+ Create dish" class="create-dish-btn">',array('controller'=>'dishes','action'=>'add'),array('escape'=>false)); ?>
			</span>
		</main>
	</section>
	<section class="createdish-mid-sec create-dish-list clearfix">
		<main class="container wdth100">
			<ul class="create-dishes-list">
			<?php if(isset($dishes) && !empty($dishes)){
					foreach ($dishes as $key => $dishData) {
						$imgName = $this->Common->getDishImage($dishData);
					?>	
					<li>
						<figure class="dish-thum">
							<?php 
							echo $this->Image->resize($imgName, 150, 150, true); 
				        	?>
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
							 </h3><br/>
							<div class="on-off-btn" rel="<?php echo $dishData['Dish']['id']; ?>">
							 <?php if(isset($dishData['Dish']['status']) && !empty($dishData['Dish']['status']) && $dishData['Dish']['status']=='off'){ ?>
								  	<a class="active"> </a>
								  	<a class="off">OFF</a>		
								  <?php	}
								  	else{ ?>
								  	<a class="On">ON</a>
								  	<a class="active"> </a>
								  <?php } ?>
							</div>
						</section>
						<div class="clearfix"></div>
						<?php echo $this->Html->link('Edit dish',array('controller'=>'dishes','action'=>'edit',$dishData['Dish']['id']),array('class'=>'edit-dish'));  ?>
					</li>
			<?php	}
				 }
				else{ 
					echo "Your Dish list is empty, Click on Create Dish button to list your dishes now.";
					} ?>	
			</ul>
		</main>
	</section>		  
</section>
<script>
	$('.on-off-btn').click(function(){ 
			var len = $(this).find('.On').length;
			var id = $(this).attr('rel');
			var obj = this;
			
			$.ajax({
					'url':'<?php echo $this->Html->url(array('controller'=>'dishes','action'=>'active_dish')); ?>',
					'type':'post',
					'data':{'id':id},
					'success':function(data){
						if(data==1){
							if(len){
								$(obj).find('.On').remove();
								$(obj).append("<a class='off'>OFF</a>");
							}
							else{
								$(obj).find('.off').remove();
								$(obj).prepend("<a class='On'>ON</a>");
							}
						}
						else{
							alert('Sorry, Dish status can not be changed, please try again.');
						}
					}
				});
		});
</script>	
