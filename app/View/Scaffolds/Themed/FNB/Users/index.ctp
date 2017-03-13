<div class="love-dish">
	<div class="container">
		<ul class="dish-lst clearfix">
			<li>
			 <h3>LOVE COOKING?</h3>
			 <div class="data">
			  <div class="image"><?php echo $this->Html->image('img1.png'); ?></div>
			   <p>Some text about how he can fulfil his dream of being a chef or something.</p>
			   <p>
			   	<?php echo $this->Html->link('List your dish',array('controller'=>'dishes','action'=>'add'),array('class'=>'btn lovedish')); ?>
			   </p>
			 </div>
			</li>
			<li class="margin_left">
			 <h3>CAN'T FIND YOUR DISH</h3>
			 <div class="data">
			   <div class="image"><?php echo $this->Html->image('img2.png'); ?></div>
			   <p>Some text about how he can fulfil his dream of being a chef or something.</p>
			   <p>
			   	<?php echo $this->Html->link('Make a request',array('controller'=>'requestdishes','action'=>'newrequest'),array('class'=>'btn makerequset')); ?>
			   </p>
			 </div>
			</li>
		</ul>
		<ul class="dish-lst clearfix">
			<li class="margin_bottom">
		   		<h3>Featured Kitchens</h3>
				<div>
				    <ul class="bxslider">
						<?php 
						if(!empty($cmsDetails['Kitchen']))
						{ 
					   		foreach ($cmsDetails['Kitchen'] as $kitchenKey => $kitchenDetails)
						  	{ 
								$styleCode =  "background:#ccc;";

								if(isset($kitchenDetails['Kitchen']['cover_photo']) && !empty($kitchenDetails['Kitchen']['cover_photo'])){
									if(!empty($kitchenDetails['Kitchen']['cover_photo'])){
										$imgUrl =  $kitchenDetails['Kitchen']['cover_photo'];
										$styleCode =  "background:url('".$imgUrl."') no-repeat left center;";
									}
								}
								?>
								<li style="height:220px; width:330px; padding:10px 50px; <?php echo $styleCode; ?>" class="featured">
								<a href="<?php echo $this->Html->url(array('controller'=>'kitchens','action'=>'index',$kitchenDetails['Kitchen']['id'])); ?>">
									<div class="data">
								   <figure>
										<?php 
										$imgName = $this->Common->getKitchenImage($kitchenDetails);
										echo $this->Image->resize($imgName, 100, 100, true); 
							        	?>
								   </figure>
								   <div class="figure-content">
									 <h3><?php echo $kitchenDetails['Kitchen']['name']; ?></h3>
									 <h4><?php // echo $kitchenDetails['Kitchen']['address']; ?></h4>
									 <?php echo $this->Common->getRatingIcon($kitchenDetails['Kitchen']['avg_rating']); ?>
									 <!--<ul class="search-icons clearfix">
									 	<?php if(isset($kitchenDetails['Kitchen']['dining_dine_in']) && $kitchenDetails['Kitchen']['dining_dine_in']==1){ ?>
									 		<li>
									 			<?php 
									 			echo $this->Html->link($this->Html->image('icon-1.png'),'javascript:void(0);',array('escape'=>false, 'title'=>'Dine-in')); 
									 			?>
									 		</li>
									 	<?php } ?>
									 	<?php if(isset($kitchenDetails['Kitchen']['dining_take_out']) && $kitchenDetails['Kitchen']['dining_take_out']==1){ ?>
									 		<li>
									 			<?php 
									 			echo $this->Html->link($this->Html->image('icon-2.png'),'javascript:void(0);',array('escape'=>false, 'title'=>'Take-out')); 
									 			?>
									 		</li>
									 	<?php } ?>
									 </ul>
									 -->
								   </div>
							     </div>
								</a>
								</li>
							<?php			  	
						  	} 
					  	}
					 	?>
					</ul>
				</div>	  
			</li>
			<li class="margin_left margin_bottom">
		   		<h3>Tuckle Guide</h3>
				<div>
					<ul class="bxslider">
						<?php 
						if(!empty($cmsDetails['Video']))
						{ 
					   		foreach ($cmsDetails['Video'] as $kitchenKey => $videoDetails)
						  	{ 
							?>
								<li style="height:220px; width:330px; background:#ccc;  padding:10px 50px;" class="featured">
								 	<iframe width="340" height="200" src="http://www.youtube.com/embed/<?php echo $videoDetails['Video']['video_id']; ?>"></iframe>
								</li>
							<?php			  	
						  	} 
					  	}
					 	?>
					</ul>
				</div>	  
			</li>
		</ul>
	</div>
</div>
<?php 
if(!empty($cmsDetails['Testimonial']))
{
?>
<div class="love-dish testemonials">
	<div class="container">	
		<ul class="dish-lst clearfix">
			<li class="margin_bottom">
				<h3>Testimonials</h3>
				<div class="slider">
					<ul class="bxslider">
						<?php 
							foreach ($cmsDetails['Testimonial'] as $testeKey => $testeData)
						  	{
					  		?>
							<li class="featured" style="padding: 25px 50px; height:200px; background: rgb(204, 204, 204);">	
								<div class="data">
									<figure>
										<?php 
										$imgName = $this->Common->getKitchenImage($testeData);
										echo $this->Image->resize($imgName, 100, 100, true); 
							        	?>
								    </figure>
									<div class="figure-content">
										<h3><?php echo $testeData['Testimonial']['name']; ?></h3>
										<?php echo $testeData['Testimonial']['message']; ?>
								    </div>
								</div>
							</li>
							<?php			  	
						  	} 
					 	?>
					</ul>
				</div>	  
			</li>
		</ul>
	</div>
</div>
<?php } ?>