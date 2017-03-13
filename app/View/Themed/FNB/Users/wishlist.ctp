<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft"> My Wishlist </h3> 
			<span class="fright">
				<?php echo $this->Html->link('<input type="button" value="Answer a Wish" class="create-dish-btn">',array('controller'=>'requestdishes','action'=>'myrequest'),array('escape'=>false)); ?>
			</span>
		</main>
	</section>
	<section class="createdish-mid-sec create-dish-list clearfix">
		<main class="container wdth100">
			<ul class="create-dishes-list">
			<?php if(isset($wishlists) && !empty($wishlists)){
					foreach ($wishlists as $key => $wishData) { 
						$imgName = $this->Common->getDishImage($wishData['Dish']);
					?>	
					<li>
						<figure class="dish-thum">
							<?php 
							echo $this->Image->resize($imgName, 150, 150, true); 
				        	?>
						</figure>
						<section class="dish-decripton clearfix">
							<h4><?php echo $wishData['Dish']['name']; ?></h4>
							<b class="timer"><?php echo $wishData['Dish']['serve_start_time'].'<small>Last order '.$wishData['Dish']['serve_end_time'].'</small>'; ?> </b><br/>
							<h3>$ &nbsp; 
							<?php 	if($wishData['Dish']['p_custom'])
								 		echo $wishData['Dish']['p_custom_price'];
								 	elseif ($wishData['Dish']['p_big']) 
								 		echo $wishData['Dish']['p_big_price'];
								 	else
								 		echo $wishData['Dish']['p_small_price'];
								?>
							 </h3><br/>
							<div class="on-off-btn" rel="<?php echo $wishData['Dish']['id']; ?>">
							 <?php if(isset($wishData['Dish']['status']) && !empty($wishData['Dish']['status']) && $wishData['Dish']['status']=='off'){ ?>
								  	<a class="off whole_border_3">OFF</a>		
								  <?php	}
								  	else{ ?>
								  	<a class="On whole_border_3">ON</a>
								  <?php } ?>
							</div>
							<!--<div class="removeWish">X</div>-->
						</section>
					</li>
			<?php	}
				 }
				else{ 
					echo "Your Wish list is empty.";
					} ?>	
			</ul>
		</main>
	</section>		  
</section>
<script>
	
</script>	
