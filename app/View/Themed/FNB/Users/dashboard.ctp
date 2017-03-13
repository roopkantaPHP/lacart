<?php if(!empty($userDetails['Kitchen']['cover_photo']))
		$backImage =  'background:url('.$userDetails['Kitchen']['cover_photo'].') no-repeat left center';
		else
		$backImage = '';

		 ?>
 <section class="profile-section" style="<?php echo $backImage; ?>">
	<div class="user-profile-box clearfix">
	   <figure>
   			<?php   
   			$imgName = $this->Common->getProfileImage($userDetails);
			echo $this->Image->resize($imgName, 150, 150, true);
			?>
	   </figure>
	   <div class="figure-content">
		<?php if(isset($userDetails['Kitchen']['name']) && !empty($userDetails['Kitchen']['name']))
				echo "<h3>".$userDetails['Kitchen']['name']."</h3>";
				else
				echo "<h3>".$userDetails['User']['name']."</h3>";

			if(isset($userDetails['Kitchen']['address']) && !empty($userDetails['Kitchen']['address']))
				echo "<h4>".$userDetails['Kitchen']['address']."</h4>";
				else
				echo "<h4>".$userDetails['User']['address']."</h4>";
					

		
		if(isset($userDetails['Kitchen']['avg_rating']) && !empty($userDetails['Kitchen']['avg_rating']))
			echo $this->Common->getRatingIcon($userDetails['Kitchen']['avg_rating']); ?>
		 <!--
		 <ul class="search-icons clearfix">
		 	<?php if(isset($userDetails['Kitchen']['dining_dine_in']) && $userDetails['Kitchen']['dining_dine_in']==1){ ?>
		 		<li>
		 			<?php 
		 			echo $this->Html->link($this->Html->image('icon-1.png'),'javascript:void(0);',array('escape'=>false, 'title'=>'Dine-in')); 
		 			?>
		 		</li>
		 	<?php } ?>
		 	<?php if(isset($userDetails['Kitchen']['dining_take_out']) && $userDetails['Kitchen']['dining_take_out']==1){ ?>
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
 </section>
 
 <section class="item-order-section">
   <main class="container">
        <?php if(isset($userDetails['Kitchen']['activeDish']))
	   			{ 
	   				echo $this->Html->link('<article class="active-dis-count clearfix"><h3>You have '.$userDetails['Kitchen']['activeDish'].' active dishes</h3></article>',array('controller'=>'dishes','action'=>'list_dishes'),array('escape'=>false));
	   				?>
	    		
	    <?php 	}
	    		else
	    		{ ?>		
	    		<article class="active-dis-count clearfix"><h3><?php echo "You have 0 active dishes"; ?></h3></article>
	    <?php 	} ?>
	 <?php if(isset($userDetails['ActivityLog']) && !empty($userDetails['ActivityLog']))
	 	{
	 		foreach ($userDetails['ActivityLog'] as $actKey => $actData) {
	 			if($actData['activity_id']==1 && isset($actData['data']['OrderDish']) && count($actData['data']['OrderDish'])>0) //Order Placed
	 			{  ?>
					<article class="order-dishes-div clearfix">
						<div class="order-title-bar">
							<span class="fleft 	ordr-icon"> Order Placed</span> 
							<span class="fright"><?php echo $this->Common->getTimeAgo($actData['timestamp']); ?> ago</span>
						</div>
						<div class="odder-inner">
							<h2> <?php echo $this->Html->link(count($actData['data']['OrderDish'])." Dishes($".$actData['data']['amount'].")",array('controller'=>'orders','action'=>'orders','action'=>'history',$actData['data']['id'])) ; ?></h2>
							<div class="serving">
								<span>Delivery Time</span><br/>
								<b class="serving-timer"><?php echo date('H:i a',strtotime($actData['data']['delivery_time'])); ?></b>
							</div>
							<!--
							<div class="serving">
								<span>Dinning options</span><br/>
								<?php 
									if($actData['data']['dine_type'] == 1)
									{ ?>
									<b class="dinnig-con"> Dine-in</b>	
									<?php
									}
									else
									{
									?>	
									<b class="takeout-con"> Take-out</b>	
									<?php
									}
								?>
								
							</div>
							
							<div class="serving">
								<span>Location</span><br/>
								<b class="location-icon">
								<?php if(isset($actData['data']['order_id']['OrderDish'][0]['Kitchen']['lat']) && !empty($actData['data']['OrderDish'][0]['Kitchen']['lat']))
									echo $this->common->distance($actData['data']['OrderDish'][0]['Kitchen']['lat'], $actData['data']['OrderDish'][0]['Kitchen']['lng'], $userDetails['User']['lat'], $userDetails['User']['lng'], "M") . " miles"; ?>
								</b><br/><small>
								<?php echo $actData['data']['OrderDish'][0]['Kitchen']['address']; ?></small>
							</div>
							-->
							<div class="dashboard">
								<?php
								if($actData['data']['is_verified']==1)
								echo $this->Html->link('<input type="button" value="Complete Order" class="mt20">','javascript:void(0);',array('escape'=>false,'class'=>'completeOrder','ordId'=>$actData['data']['id'],'kitchenId'=>$actData['data']['OrderDish'][0]['kitchen_id']));
								if($actData['data']['is_verified']==0)
								echo $this->Html->link('<input type="button" value="Cancel Order" class="mt20">','javascript:void(0);',array('escape'=>false,'class'=>'cancelOrder','ordId'=>$actData['data']['id'],'kitchenId'=>$actData['data']['OrderDish'][0]['kitchen_id'])); ?>
							</div>
						</div>
					</article>
	 			<?php
	 			} 
	 			else if($actData['activity_id']==2 && isset($actData['data']['OrderDish']) && count($actData['data']['OrderDish'])>0) //Order Received
	 			{  ?>
					<article class="order-dishes-div clearfix">
						<div class="order-title-bar">
							<span class="fleft 	receiv-icon"> Order Received</span> 
							<span class="fright"><?php echo $this->Common->getTimeAgo($actData['timestamp']); ?> ago</span>
						</div>
						<div class="odder-inner">
							<h2> <?php echo $this->Html->link(count($actData['data']['OrderDish'])." Dishes($".$actData['data']['amount'].")",array('controller'=>'orders','action'=>'orders','action'=>'history',$actData['data']['id'])) ; ?></h2>
							<div class="serving">
								<span>Delivery Time</span><br/>
								<b class="serving-timer"><?php echo date('H:i a',strtotime($actData['data']['delivery_time'])); ?></b>
							</div>
							<!--
							<div class="serving">
								<span>Dinning options</span><br/>
								<?php 
									if($actData['data']['dine_type'] == 1)
									{ ?>
									<b class="dinnig-con"> Dine-in</b>	
									<?php
									}
									else
									{
									?>	
									<b class="takeout-con"> Take-out</b>	
									<?php
									}
								?>
							</div>
							-->
							<?php 
								$toLat = $toLng = $fromLat = $fromLng = '';
								if(isset($actData['data']['OrderAddress']['address_lat']) && !empty($actData['data']['OrderAddress']['address_lat']))
								{
									$toLat = $actData['data']['OrderAddress']['address_lat'];	
								}
								
								if(isset($actData['data']['OrderAddress']['address_lng']) && !empty($actData['data']['OrderAddress']['address_lng']))
								{
									$toLng = $actData['data']['OrderAddress']['address_lng'];	
								}
								
								if(isset($userDetails['Kitchen']['lat']) && !empty($userDetails['Kitchen']['lat']))
								{
									$fromLat = $userDetails['Kitchen']['lat'];	
								}
								
								if(isset($userDetails['Kitchen']['lng']) && !empty($userDetails['Kitchen']['lng']))
								{
									$fromLng = $userDetails['Kitchen']['lng'];	
								}
								
								if($toLat != '' && $toLng != '' && $fromLat != '' && $fromLng != '')
								{
								?>
								<div class="serving">
									<span>Location</span><br/>
									<b class="location-icon">
										<?php echo $this->common->distance($fromLat, $fromLng, $toLat, $toLng, "M") . " miles";  ?>
									</b><br/><small>
									<?php
									if(isset($actData['data']['OrderAddress']['delivery_location']) && !empty($actData['data']['OrderAddress']['delivery_location']))
									{
										echo $actData['data']['OrderAddress']['delivery_location'];		
									}
									else if(isset($actData['data']['OrderAddress']['order_address']) && !empty($actData['data']['OrderAddress']['order_address']))
									{
										echo $actData['data']['OrderAddress']['order_address'];		
									}
									?></small>
								</div>
								<?php
								}
								?>
							<div class="serving mt20">
								<div class="img-icon">
									<?php
										$imgName = $this->Common->getProfileImage($actData['data']);
										echo $this->Image->resize($imgName, 150, 150, true);
									?>
								</div>
								<b>	<?php echo $actData['data']['User']['name'];  ?></b><br/><small>
									<?php echo (isset($actData['data']['User']['Order'][0]['Order'][0]['noOfPlacedOrders']))?$actData['data']['User']['Order'][0]['Order'][0]['noOfPlacedOrders']:"0" ?> orders</small>
							</div>
							<div class="dashboard">
								<?php
								if($actData['data']['is_verified']==0)
								{
									echo $this->Html->link('<input type="button" value="Decline Order" class="mt20 width150">','javascript:void(0);',array('escape'=>false,'class'=>'confirmOrder','rel'=>0,'ordId'=>$actData['data']['id'],'kitchenId'=>$actData['data']['OrderDish'][0]['kitchen_id'])); 
								
									echo $this->Html->link('<input type="button" value="Confirm Order" class="mt20 width150">','javascript:void(0);',array('escape'=>false,'class'=>'confirmOrder','rel'=>1,'ordId'=>$actData['data']['id'],'kitchenId'=>$actData['data']['OrderDish'][0]['kitchen_id'])); 
								}
								?>
							</div>
						</div>
					</article>
	 			<?php
	 			} 
				else if($actData['activity_id']==3 && isset($actData['data']['name'])) //Dish Added
				{ ?>
					<article class="order-dishes-div clearfix" rel="<?php echo $actData['activitylog_id']; ?>">
						<div class="order-title-bar">
							<span class="fleft alert"> Dish Added</span> 
							<span class="fright"><?php echo $this->Common->getTimeAgo($actData['timestamp']); ?> ago</span>
						</div>
						<div class="odder-inner msg-block">
							<div class="descon">
								Dish 		
								<?php if(isset($actData['data']['name']) && !empty($actData['data']['name'])) ?>	  
								<b class=""><?php echo $actData['data']['name']; ?></b> has been added. 
							</div>
							<div class="dashboard">
								<input type="button" class="dismissAlert" rel="<?php echo $actData['activitylog_id']; ?>" value="Dismiss">
							</div>
						</div>
					</article>
				<?php
	 			}
				else if($actData['activity_id']==4 && isset($actData['data']['name'])) //Dish Activated
				{ ?>
					<article class="order-dishes-div clearfix" rel="<?php echo $actData['activitylog_id']; ?>">
						<div class="order-title-bar">
							<span class="fleft alert"> Alert</span> 
							<span class="fright"><?php echo $this->Common->getTimeAgo($actData['timestamp']); ?> ago</span>
						</div>
						<div class="odder-inner msg-block">
							<div class="descon">
								your Dish 			  
								<?php if(isset($actData['data']['name']) && !empty($actData['data']['name'])) ?>	  
								<b class=""><?php echo $actData['data']['name']; ?></b> has become active. 
							</div>
							<div class="dashboard">
								<input type="button" class="dismissAlert" rel="<?php echo $actData['activitylog_id']; ?>" value="Dismiss">
							</div>
						</div>
					</article>
				<?php
	 			}
				else if($actData['activity_id']==5 && isset($actData['data']['name'])) //Dish Offline
				{ ?>
					<article class="order-dishes-div clearfix" rel="<?php echo $actData['activitylog_id']; ?>">
						<div class="order-title-bar">
							<span class="fleft alert"> Alert</span> 
							<span class="fright"><?php echo $this->Common->getTimeAgo($actData['timestamp']); ?> ago</span>
						</div>
						<div class="odder-inner msg-block">
							<div class="descon">
								<?php if(isset($actData['data']['name']) && !empty($actData['data']['name'])) ?>	  
								<b class=""><?php echo $actData['data']['name']; ?></b> has gone offline. 
							</div>
							<div class="dashboard">
								<input type="button" class="dismissAlert" rel="<?php echo $actData['activitylog_id']; ?>" value="Dismiss">
							</div>
						</div>
					</article>
				<?php
				}
				else if($actData['activity_id']==6 && isset($actData['data']['name'])) //Conversation updated
				{  ?>
					<article class="order-dishes-div clearfix" rel="<?php echo $actData['activitylog_id']; ?>">
						<div class="order-title-bar">
							<span class="fleft msg-icon"> Messages</span> 
							<span class="fright"><?php echo $this->Common->getTimeAgo($actData['timestamp']); ?> ago</span>
						</div>
						<div class="odder-inner msg-block">
							<div class="descon">
								<div class="img-icon">
									<?php
									$imgName = $this->Common->getProfileImage($actData['data']['ConversationReply'][0]);
									echo $this->Image->resize($actData['data']['ConversationReply'][0]['User']['image'], 150, 150, true); 
						        	?>
								</div>
								<b class="">
									<?php echo $actData['data']['ConversationReply'][0]['User']['name']; ?>
								</b><br/>
								<small>
									<?php echo $actData['data']['ConversationReply'][0]['reply']; ?>	
								</small>
							</div>
							<div class="dashboard">
								<?php
								if($userDetails['User']['id']!=$actData['data']['ConversationReply'][0]['User']['id'])
								echo $this->Html->link('<input type="button" value="Reply">',array('controller'=>'conversations','action'=>'new_message',$actData['data']['ConversationReply'][0]['User']['id']),array('escape'=>false,'class'=>'iframe'));
								?>
							</div>
						</div>
					</article>
				<?php
				}
				else if($actData['activity_id']==7 && isset($actData['data']['Request']['dish_name'])) //Dish request has been answered
				{  ?>
					<article class="order-dishes-div clearfix" rel="<?php echo $actData['activitylog_id']; ?>">
						<div class="order-title-bar">
							<span class="fleft alert"> Request Answered</span> 
							<span class="fright"><?php echo $this->Common->getTimeAgo($actData['timestamp']); ?> ago</span>
						</div>
						<div class="odder-inner msg-block">
							<div class="descon">
								<?php if(isset($actData['data']['Request']['dish_name']) && !empty($actData['data']['Request']['dish_name'])) ?>	  
								<?php echo "Your requested dish <b>".$actData['data']['Request']['dish_name']."</b> has been answered by kitchen <b>".$actData['data']['Dish']['Kitchen']['name']."</b> with <b>".$actData['data']['Dish']['name']."</b> dish."; ?> 
							</div>
							<div class="dashboard">
								<input type="button" class="dismissAlert" rel="<?php echo $actData['activitylog_id']; ?>" value="Dismiss">
							</div>
						</div>
					</article>
				<?php
	 			}
	 		}
	 	} ?>
	  </main>
  </section>
  
<script>
$(document).ready(function(){
	$("a.iframe").fancybox({
	    width : 700,
		height : 575,
		type : 'iframe',
		autoScale : false,
		padding : 0,
	});

	$('.confirmOrder').click(function(){

		var status = $(this).attr('rel');
		var orderId = $(this).attr('ordId');
		var kitchenId = $(this).attr('kitchenId');
		var obj = this;
		if (confirm('Are you sure you want to confirm this order?')) {
			$.ajax({
		    	'url': '<?php echo $this->Html->url(array('controller'=>'orders','action'=>'order_confirmation')); ?>',
		    	'async': true,
		    	'data' : {'order_id':orderId,'kitchen_id':kitchenId,'status':status},
		    	'type': 'post',
		    	'dataType' : 'json',
		    	'success': function(data){
		    		if(data.status!=0)
		    		{
		    			alert(data.message);
		    			$(obj).parent().find('a.confirmOrder').hide();	
		    		}
		    		else
		    		{
		    			alert(data.message);
		    		}
		    	}
		    });
		}
	});

	$('.completeOrder').click(function(){
		var orderId = $(this).attr('ordId');
		var kitchenId = $(this).attr('kitchenId');
		
		var obj = this;
		if (confirm('Are you sure you want to complete this order?')) {
			$.ajax({
		    	'url': '<?php echo $this->Html->url(array('controller'=>'orders','action'=>'order_completion')); ?>',
		    	'async': true,
		    	'data' : {'order_id':orderId,'kitchen_id':kitchenId},
		    	'type': 'post',
		    	'dataType' : 'json',
		    	'success': function(data){
		    		if(data.status!=0)
		    		{
		    			alert(data.message);
		    			$(obj).hide();
		    		}
		    		else
		    		{
		    			alert(data.message);
		    		}
		    	}
		    });
		}
	});

	$('.cancelOrder').click(function(){
		var orderId = $(this).attr('ordId');
		var kitchenId = $(this).attr('kitchenId');
		
		var obj = this;
		if (confirm('Are you sure you want to cancel this order?')) {
			$.ajax({
		    	'url': '<?php echo $this->Html->url(array('controller'=>'orders','action'=>'order_cancellation')); ?>',
		    	'async': true,
		    	'data' : {'order_id':orderId,'kitchen_id':kitchenId},
		    	'type': 'post',
		    	'dataType' : 'json',
		    	'success': function(data){
		    		if(data.status!=0)
		    		{
		    			alert(data.message);
		    			$(obj).hide();
		    		}
		    		else
		    		{
		    			alert(data.message);
		    		}
		    	}
		    });
		}
	});

	$('.dismissAlert').click(function(){
		var activityLogId = $(this).attr('rel');
		var obj = this;
		 $.ajax({
		    	'url': '<?php echo $this->Html->url(array('controller'=>'dishes','action'=>'dismiss_alert')); ?>',
		    	'async': true,
		    	'data' : {'id':activityLogId},
		    	'type': 'post',
		    	'success': function(data){
		    		if(data)
		    		{
		    			$(obj).parents('article').remove();
		    		}
		    		else
		    		{
		    			alert('This activity can not be dismissed');
		    		}
		    	}
		    });
	});
});
</script>

