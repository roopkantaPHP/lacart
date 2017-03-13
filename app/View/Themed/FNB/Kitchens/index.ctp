	<?php  
	$styleCode =  "";
	if(isset($kitchenDetails['Kitchen']['cover_photo']) && !empty($kitchenDetails['Kitchen']['cover_photo'])){
		if(FILE_EXISTS(KITCHEN_IMAGE_URL.$kitchenDetails['Kitchen']['cover_photo'])){
			$imgName = KITCHEN_IMAGE_FOLDER.$kitchenDetails['Kitchen']['cover_photo'];
			$imgUrl =  $this->Image->url($imgName);
			$styleCode =  "background:url('".$imgUrl."') no-repeat left center;";
		}
	}
	?>
	<?php if(isset($errors) && !empty($errors)){
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
	 <div class="profile-section" style="<?php echo $styleCode; ?>">
	     <div class="user-profile-box clearfix">
		   <figure>
				<?php 
				$imgName = $this->Common->getKitchenImage($kitchenDetails);
				echo $this->Image->resize($imgName, 150, 150, true); 
	        	?>
		   </figure>
		   <div class="figure-content">
			 <h3><?php echo $kitchenDetails['Kitchen']['name']; ?></h3>
			 <!--<h4><?php echo $kitchenDetails['Kitchen']['address']; ?></h4>-->
			 <?php echo $this->Common->getRatingIcon($kitchenDetails['Kitchen']['avg_rating']); ?>
			 <!--<ul class="search-icons clearfix">
			 	<?php if(isset($kitchenDetails['Kitchen']['dining_dine_in']) && $kitchenDetails['Kitchen']['dining_dine_in']==1)
			 	{ ?>
			 		<li>
			 			<?php 
			 			echo $this->Html->link($this->Html->image('icon-1.png'),'javascript:void(0);',array('escape'=>false, 'title'=>'Dine-in')); 
			 			?>
			 		</li>
			 	<?php } ?>
			 	<?php if(isset($kitchenDetails['Kitchen']['dining_take_out']) && $kitchenDetails['Kitchen']['dining_take_out']==1)
			 	{ ?>
			 		<li>
			 			<?php 
			 			echo $this->Html->link($this->Html->image('icon-2.png'),'javascript:void(0);',array('escape'=>false, 'title'=>'Take-out')); 
			 			?>
			 		</li>
			 	<?php } ?>
			 </ul>
			 --!>
			 <div class="clearfix">&nbsp;</div>
			 <?php echo $this->Html->link('View Profile',array('controller'=>'users','action'=>'about_me',$kitchenDetails['Kitchen']['user_id']),array('class'=>'place-btn')); ?>
		   </div>
	     </div>
	     <?php
	     if(isset($userDetails['User']['id']) && !empty($userDetails['User']['id']) && $kitchenDetails['Kitchen']['user_id'] != $userDetails['User']['id'])
	     	echo $this->Html->link('',array('controller'=>'conversations','action'=>'new_message',$kitchenDetails['Kitchen']['user_id']),array('class'=>'send-message iframe')); 
	    ?>
	 </div>
	 
	 <div class="dishes-section">
	   <div class="container">
	     <div class="tabs-bar clearfix">
		   <ul>
			 <li><?php echo $this->Html->link('Dishes','javascript:void(0);',array('class'=>'active','data'=>'dishes-content')); ?></li>  
		     <li><?php echo $this->Html->link('About','javascript:void(0);',array('data'=>'about-tab')); ?></li>  
		     <li><?php echo $this->Html->link('Photos','javascript:void(0);',array('data'=>'photos-tab')); ?></li>  
		     <li><?php echo $this->Html->link('Reviews','javascript:void(0);',array('data'=>'review-tab')); ?></li>  
		   </ul>
		 </div>
	   </div>
	   <div class="tabs-content-area">
	   <!-- dishes -->
	   <?php echo $this->Form->create('OrderDish'); ?>
       <div class="dishes-content con-tabs clearfix">
		  <div class="container">
		    <div class="left-sec">
			  <ul class="dishes-list clearfix">
			    <?php 
			    	if(isset($kitchenDetails['Dish']) && !empty($kitchenDetails['Dish']))
			    	{
			    		foreach ($kitchenDetails['Dish'] as $dkey => $dData)
			    		{	
			    			if(!empty($dData['p_small']) || !empty($dData['p_big']) || (!empty($dData['p_custom']) && $dData['is_custom_price_active']==1))
			    			{ ?>
							<li class="grey-style">
							  <div class="image-con">
								<div class="checkbox-otr">
									 <?php 	echo $this->Form->checkbox('OrderDish.'.$dData['id'].'.is_checked', array('hiddenField'=>false,'class'=>'checkbox dishSelect','label'=>false,'div'=>false));
									 	   	echo $this->Form->input('OrderDish.'.$dData['id'].'.dish_id', array('type'=>'hidden', 'value'=>$dData['id'], 'rel'=> 'dishId')); 
									 	   	echo $this->Form->input('OrderDish.'.$dData['id'].'.dish_name', array('type'=>'hidden', 'value'=>$dData['name'], 'rel'=> 'dishName'));
									 	   	$defaultQuantity = 0;
									 	   	if(isset($this->request->data['OrderDish'][$dData['id']]['quantity']) && !empty($this->request->data['OrderDish'][$dData['id']]['quantity']))
									 	   	{
									 	   		$defaultQuantity = $this->request->data['OrderDish'][$dData['id']]['quantity'];
									 	   	} 	   		
									 	   	echo $this->Form->input('OrderDish.'.$dData['id'].'.quantity', array('type'=>'hidden', 'rel'=> 'dishQuantity', 'value'=>$defaultQuantity));

									 	   	$defaultPrice = 0;
									 	   	if(isset($this->request->data['OrderDish'][$dData['id']]['price']) && !empty($this->request->data['OrderDish'][$dData['id']]['price']))
									 	   	{
									 	   		$defaultPrice = $this->request->data['OrderDish'][$dData['id']]['price'];
									 	   	} 	
								 			echo $this->Form->input('OrderDish.'.$dData['id'].'.price', array('type'=>'hidden','rel'=>'dishPrice', 'value' => $defaultPrice));

								 			echo $this->Form->input('OrderDish.'.$dData['id'].'.portion', array('type'=>'hidden', 'rel'=>'dishPortion'));
									 	    ?>
								</div>
							    <div class="image">
							    	<?php
							    		$imgName = $this->Common->getDishImage($dData);
							    		echo $this->Image->resize($imgName, 150, 150, true);
							    		if(isset($search_data['time']) && !empty($search_data['time']))
							    		{
							    			$startTime = $search_data['time'];
							    			$endTime = $dData['serve_end_time'];
							    		}
							    		else
							    		{
							    			$startTime = $dData['serve_start_time'];
							    			$endTime = $dData['serve_end_time'];
							    		}
							    	?>
							    </div>
								<div class="image-data">
								  <h3><?php echo $dData['name']; ?></h3>
								  <div class="time">
								  	<span>
									  	<b>
									  		<?php echo $startTime; ?>
									  	</b>
								  	</span>
								  	<span>
								  		<?php echo "Last order ".$endTime; ?>
								  	</span>
								  </div>
								  <?php
								     if(isset($userDetails['User']['id']) && !empty($userDetails['User']['id']) && $kitchenDetails['Kitchen']['user_id'] != $userDetails['User']['id'])
								     	{ ?>
								     	  <div class="wishlist-icon" title="Add to my Wishlist" rel="<?php echo $dData['id']; ?>">&nbsp;</div>
								     	<?php 
								     	}
								     	?>
								</div>
							  </div>
							<div class="dish-portion">
							    <h6>PORTION</h6>
								<div class="clearfix pecies">
									<main class="portionOuter">
										<?php if(!empty($dData['p_small']))
										{
											$budgetPortionDiv = '';
											if(isset($this->request->data['OrderDish'][$dData['id']]['portion']) && $this->request->data['OrderDish'][$dData['id']]['portion']=='budget')
											{
												$budgetPortionDiv = 'active';
											}
										 ?>
											<div rel="budget" price="<?php echo $dData['p_small_price']; ?>" class="<?php echo $budgetPortionDiv; ?>">
												Budget
												<br/>
												<?php echo $dData['p_small_quantity'].$dData['p_small_unit']; ?>
											</div>
										<?php }
											if(!empty($dData['p_big']))
											{
												$premiumPortionDiv = '';
												if(isset($this->request->data['OrderDish'][$dData['id']]['portion']) && $this->request->data['OrderDish'][$dData['id']]['portion']=='premium')
												{
													$premiumPortionDiv = 'active';		
												}
											?>
											<div rel="premium" price="<?php echo $dData['p_big_price']; ?>" class="<?php echo $premiumPortionDiv; ?>">
												Premium
												<br/>
												<?php echo $dData['p_big_quantity'].$dData['p_big_unit']; ?>
											</div>
										<?php }
											if(!empty($dData['p_custom']) && $dData['is_custom_price_active']==1)
											{
												$premiumCustomDiv = '';
												if(isset($this->request->data['OrderDish'][$dData['id']]['portion']) && $this->request->data['OrderDish'][$dData['id']]['portion']=='custom')
												{
													$premiumCustomDiv = 'active';	
												}
											?>
											<div rel="custom" price="<?php echo $dData['p_custom_price']; ?>" class="<?php echo $premiumCustomDiv; ?>">
												Custom
												<br/>
												<?php echo $dData['p_custom_quantity'].$dData['p_custom_unit']; ?>
											</div>
										<?php } ?>
										
									</main>
								</div>
								<h6>QUANTITY</h6>
								<div class="clearfix pecies quantity">
								  <a href="javascript:void(0);" class="bigpc qMinus left-ra">-</a>
								  <div class="showQuantity">
								  	<?php
								  		if(isset($this->request->data['OrderDish'][$dData['id']]['quantity']) && !empty($this->request->data['OrderDish'][$dData['id']]['quantity']))
										{
											echo $this->request->data['OrderDish'][$dData['id']]['quantity'];	
										}
										else
										{
											echo 1;
										}
								  	?>
								  </div>
								  <a href="javascript:void(0);" class="bigpc qPlus">+</a>
								  <div class="dish-price">
								  	<?php
								  		if($dData['p_custom'] && $dData['is_custom_price_active']==1)
									 		echo '$'.$dData['p_custom_price'];
									 	else if ($dData['p_big']) 
									 		echo '$'.$dData['p_big_price'];
									 	else if ($dData['p_small']) 
									 		echo '$'.$dData['p_small_price'];
									?>
								  </div>
								</div>
							  </div>
							  <div class="clr"></div>
							  <div class="accordian">
							     <div class="acdian-hdg">More</div>
								 <div class="acdian-data" style="display:none;">
								   <?php if(isset($dData['cuisine']) && !empty($dData['cuisine'])){ ?>
									   <h6>CUISINE</h6>
									   <p><?php echo $dData['cuisine']; ?></p>
								   <?php } ?>
								   <?php if(isset($dData['allergens']) && !empty($dData['allergens'])){ ?>
									   <h6>Allergy</h6>
									   <p><?php echo str_replace('::::::::',',',$dData['allergens']); ?></p>
								   <?php } ?>
								   <?php if(isset($dData['diet']) && !empty($dData['diet'])){ ?>
									   <h6>DIET</h6>
									   <p><?php echo $dData['diet']; ?></p>
								   <?php } ?>
								   <?php if(isset($dData['repeat']) && !empty($dData['repeat'])){ ?>
									   <h6>REPEAT-ON</h6>
									   <p><?php echo $dData['repeat']; ?></p>
								   <?php } ?>
								 </div>
							  </div>
							</li>    			
			    		<?php
			    			}
			    		}
			    	}
			    ?>
			  </ul>
			</div>
			<div class="right-sec">
			  <div class="order-tbl">
			    <h4>Your Order</h4>
				<table>
				  	<tr class="service_fee">
						<td>
							<div><b>Delivery Fee</b></div>
						</td>
						<td>
							<div class="alignright"><b>$0</b></div>
						</td>
					</tr>
					<tr class="before_Total">
						<td>
							<div><b>Order value </b></div>
						</td>
						<td>
							<div class="alignright"><b>$0</b></div>
						</td>
					</tr>
					<tr class="total">
						<td>
							<div><b>Sales Tax(<?php echo $kitchenDetails['Kitchen']['sales_tax'].'%'; ?>)</b></div>
						</td>
						<td>
							<div class="alignright"><b>$0</b></div>
						</td>
					</tr>
					<tr class="maintotal">
						<td>
							<div><b>Total</b></div>
						</td>
						<td>
							<div class="alignright"><b>$0</b></div>
						</td>
					</tr>
				</table>
				<?php
				if(isset($userDetails['User']['id']) && !empty($userDetails['User']['id']) && $userDetails['User']['id'] == $kitchenDetails['User']['id'])
				{ }
				else
				{ ?>
					<div>
						<?php echo $this->Form->submit('Place Order',array('class'=>'place-btn')); ?>
					</div>
				<?php } ?>
			  </div>
			</div>
		  </div>
	   </div>
	   <?php echo $this->Form->end(); ?>
       <!-- dishes -->
	   
	   
	   <!-- about -->
	   <div class="about-tab con-tabs" style="display:none;">
	     <div class="container">
			<?php echo $kitchenDetails['Kitchen']['description']; ?>
		 </div>
	   </div>
	   <!-- about -->
	   
	   
	   <!-- photos -->
	    <div class="photos-tab con-tabs" style="display:none;">
	     <div class="container">
		   <ul class="photos-list clearfix">
		   <?php
		   		if(isset($kitchenDetails['UploadImage']) && !empty($kitchenDetails['UploadImage'])){
		   			foreach($kitchenDetails['UploadImage'] as $id=>$name){ ?>
		   				<li>
		   					<?php
		   					if(isset($name['name']) && !empty($name['name'])){
								if(FILE_EXISTS(KITCHEN_IMAGE_URL.$name['name'])){
									$imgName = KITCHEN_IMAGE_FOLDER.$name['name'];
									$imgUrl = $this->Image->url($imgName);
									
									$imageDetails['UploadImage'][0]['name'] =  $name['name'];
				   					$imgName = $this->Common->getKitchenImage($imageDetails);
									
									$imageForli = $this->Image->resize($imgName, 150, 150, true); 
									echo $this->Html->link($imageForli, $imgUrl, array('class'=>'fancybox-button','rel'=>'fancybox-button','escape'=>false));
								}
							}
		   					?>
		   				</li>	
		   		<?php	}
		   		}
		   ?>
		   </ul>
		 </div>
	   </div>
	   <!-- photos -->
	   
	   
	   <!-- review -->
	   <div class="review-tab con-tabs" style="display:none;">
	     <div class="container">
		   <ul class="reviw-list">
		    <?php 
		    	$reviewed = false;

		   		if(isset($kitchenDetails['Review']) && !empty($kitchenDetails['Review'])){
		   			foreach($kitchenDetails['Review'] as $id=>$reviewData){
		   				if(isset($userDetails['User']['id']) && !empty($userDetails['User']['id']) && $reviewData['user_id'] == $userDetails['User']['id'])
		   				{
		   					$reviewed = true;
		   				}
		   			 ?>
		   				<li>
						    <div class="image">
						    	<div class="inr-img">
						    		<?php
										$imgName = $this->Common->getProfileImage($reviewData);
										echo $this->Image->resize($imgName, 150, 150, true); 
									?>
						    	</div>
						    </div>
							<h5><?php echo $reviewData['User']['name'];  ?>
							    <?php echo $this->Common->getRatingIcon($reviewData['rating']); ?>
			 					<span><?php echo $this->Common->getTimeAgo($reviewData['timestamp']); ?> ago</span></h5>
							<p><?php echo $reviewData['feedback']; ?></p>
					   </li>	
		   		<?php	}
		   		}
		   ?>
		   <?php
			    if(!$reviewed && isset($userDetails['User']['id']) && !empty($userDetails['User']['id']) && $kitchenDetails['Kitchen']['user_id'] != $userDetails['User']['id'] && $kitchenDetails['is_order_placed_for_kitchen']){ ?>
			     	<li class="reply">
			     		<div class="image">
					    	<div class="inr-img">
					    		<?php
									$imgName = $this->Common->getProfileImage($userDetails);
									echo $this->Image->resize($imgName, 150, 150, true); 
								?>
					    	</div>
					    </div>

						<?php echo $this->Form->create('Review'); ?>
							<div class="textbox marginleftAuto">
								<h4>Post a review</h4>
								<div class="basic" data-average="0" data-id="2"></div>
								<div class="clearfix"></div>
								<?php echo $this->Form->textarea('Review.feedback',array('class'=>'count200')); ?>
								<?php echo $this->Form->hidden('Review.rating',array('value'=>3)); ?>
								<?php echo $this->Form->hidden('Review.kitchen_id',array('value'=>$kitchenDetails['Kitchen']['id'])); ?>
								<span class="char-info"> 200 characters</span>
								 <?php echo $this -> Js -> submit('Submit review', array('url' => array('controller' => 'kitchens', 'action' => 'addFeedback'),
								 	'async' => true,
								 	'type' => 'html',
									'class' => "comment-sub-btn",
									'before' => 'return validateForm("ReviewIndexForm");',
									'success' => 'appendHtmlData(data)')
									); ?>
							</div>
						<?php echo $this->Form->end(); ?>
					</li>
			<?php  }
			?>
		  </ul>
		</div>
	   </div>
	   <!-- review -->
	   </div>
	   
	 </div>
 
  <script>
	$(document).ready(function($) {

		$("a.iframe").fancybox({
		    width : 400,
			height : 750,
			type : 'iframe',
			autoSize : false,
			padding : 0,
			helpers : {
		        overlay : {
		            css : {
		                'background' : 'rgba(58, 42, 45, 0.50)'
		            }
		        }
		    }
		});

		<?php
		if(isset($clickLogin) && $clickLogin==1)
		{
		?>
			$('.loginbtn').click();
		<?php
		}
		if(isset($openVerify) && $openVerify==1)
		{
		?>
			$('.verifyme').click();
		<?php
		}
		?>
		/* This is basic - uses default settings */
		$(".dishes-list .wishlist-icon").click(function(){
			var dishId = $(this).attr('rel');
			var kitchenId = <?php echo $kitchenDetails['Kitchen']['id']; ?>;
			$.ajax({
				'url':'<?php echo $this->Html->url(array('controller'=>'users','action'=>'add_wishlist')); ?>',
				'type':'post',
				'data':{'dish_id':dishId, 'kitchen_id':kitchenId},
				'success':function(data){
					if(data==2)
					{
						alert('Dish already added in your Wishlist.');
					}
					else if(data==1)
					{
						alert('Dish has been successfully added in your Wishlist.');
					}
					else
					{
						alert('Sorry, Dish can not be added to your wishlist, please try again.');
					}
				}
			});
		});

		// you can rate 3 times ! After, jRating will be disabled
	    $(".basic").jRating({
	        canRateAgain : true,
	        rateMax : 5,
	        nbRates : 10,
	        step : true,
	        showRateInfo : false,
	        sendRequest : false,
	        onClick : function(element,rate) {
	        	$('#ReviewRating').val(rate);
	        }
	    });

		$('.qPlus').click(function(){
			var dishLi = $(this).parents('li');
			var dishPortion = $(dishLi).find('input[rel="dishPortion"]').val();
			if(dishPortion=='')
			{
				$(dishLi).find('main.portionOuter div:first').click();
			}
			var curValue = $(dishLi).find('input[rel="dishQuantity"]').val();
			var afterPlus = parseInt(curValue)+1;
			$(this).siblings('div.showQuantity').html(afterPlus);
			$(dishLi).find('input[rel="dishQuantity"]').val(afterPlus);
			refreshOrderChart(this);
		});

		$('.qMinus').click(function(){
			var dishLi = $(this).parents('li');
			var dishPortion = $(dishLi).find('input[rel="dishPortion"]').val();
			if(dishPortion=='')
			{
				$(dishLi).find('main.portionOuter div:first').click();
			}
			var curValue = $(dishLi).find('input[rel="dishQuantity"]').val();
			var afterMinus = parseInt(curValue)-1;
			if(afterMinus >= 1){
				$(this).siblings('div.showQuantity').html(afterMinus);
				$(dishLi).find('input[rel="dishQuantity"]').val(afterMinus);
			}
			refreshOrderChart(this);
		});

		$('main.portionOuter div').click(function(){
			var dishLi = $(this).parents('li');
			$(this).siblings('div').removeClass('active');
			$(this).addClass('active');
			$(dishLi).find('input[rel="dishPortion"]').val($(this).attr('rel'));

			var qtyVal = $(dishLi).find('input[rel="dishQuantity"]').val();
			if(qtyVal==0)
				$(dishLi).find('.qPlus').click();
			refreshOrderChart(this);
		});

		$('.dishSelect').change(function(){ 
			if(this.checked){ 
				$(this).parents('li').removeClass('grey-style');
			}
			else
			{
				$(this).parents('li').addClass('grey-style');
			}
			refreshOrderChart(this);
		});

		$('.tabs-bar ul li a').click(function(){
			$('.tabs-bar ul li a').removeClass('active');
			$(this).addClass('active');
			var getatr = $(this).attr('data');
			$('.con-tabs').fadeOut(300);
			$('.'+getatr).fadeIn(300);
		});
		  
		$(".fancybox-button").fancybox({
			margin: [0, 0, 80, 0],
			prevEffect		: 'none',
			nextEffect		: 'none',
			closeBtn		: false,
			helpers		: {
				title	: { type : 'inside' },
				buttons	: {},
				overlay : {
		            css : {
		                'background' : 'rgba(58, 42, 45, 0.50)'
		            }
		        }
			}
	    });


		$("#iframe").fancybox({
			margin: [0, 0, 80, 0],
			prevEffect		: 'none',
			nextEffect		: 'none',
			closeBtn		: false,
			helpers		: {
				title	: { type : 'inside' },
				buttons	: {},
				overlay : {
		            css : {
		                'background' : 'rgba(58, 42, 45, 0.50)'
		            }
		        }
			}	
		});

		if($('.dishes-list input[type="checkbox"][value="1"]').length > 0)
		{
			loadOrderChart();		
		}
 
	});

	function refreshOrderChart(obj){
		var totalPrice = 0;
		var isChecked = 0;
		if($(obj).attr('type')=='checkbox')
		{
			isChecked = 1;
		}
		$('.order-tbl table tbody tr.service_fee').find('.alignright').html('<b>$0</b>');
		$('.order-tbl table tbody tr.before_Total').find('.alignright').html('<b>$0</b>');
		$('.order-tbl table tbody tr.total').find('.alignright').html('<b>$0</b>');
		$('.order-tbl table tbody tr.maintotal').find('.alignright').html('<b>$0</b>');

		$('.dishes-list input[type="checkbox"]').each(function(){
			var dishLi = $(this).parents('li');
			var dishId = $(dishLi).find('input[rel="dishId"]').val();
			var dishName = $(dishLi).find('input[rel="dishName"]').val();
			var dishPortion = $(dishLi).find('input[rel="dishPortion"]').val();
			var dishPrice = $(dishLi).find('input[rel="dishPrice"]').val();
			var dishQuantity = $(dishLi).find('input[rel="dishQuantity"]').val();
			if(dishPortion!='')
			{
				var selectedPortionPrice = $(dishLi).find('div[rel="'+dishPortion+'"]').attr('price');
				var calculatedPrice = parseInt(selectedPortionPrice)*parseInt(dishQuantity);
				$(dishLi).find('div.dish-price').html('$'+calculatedPrice);

			}
			if(this.checked)
			{

				if(dishPortion=='')
				{
					alert('Please specify portion size.');
					if(isChecked)
					{
						$(this).click();
					}
				}
				else
				{
					if($(dishLi).find('.portionOuter div[class="active"]').length != 0)
					var dishQuanityHtml = $(dishLi).find('.portionOuter div[class="active"]').html().replace('<br>','-');
					if($('.order-tbl table tbody tr[rel="'+dishId+'"]').length==0 && isChecked)
					{
						var rowHtml = '<tr rel="'+dishId+'">';
						rowHtml += '<td>';
						rowHtml += '<div>'+dishName+' - '+dishQuantity+'</div>';
						rowHtml += '<p>'+dishQuanityHtml+'</p>';
						rowHtml += '</td>';
						rowHtml += '<td>';
						rowHtml += '<div class="alignright">$'+calculatedPrice+'</div>';
						rowHtml += '</td>';
						rowHtml += '</tr>';
						$('.order-tbl table tbody tr.service_fee').before(rowHtml);
					}
					else if($('.order-tbl table tbody tr[rel="'+dishId+'"]').length && !isChecked)
					{
						var rowHtml = '<td>';
						rowHtml += '<div>'+dishName+' - '+dishQuantity+'</div>';
						rowHtml += '<p>'+dishQuanityHtml+'</p>';
						rowHtml += '</td>';
						rowHtml += '<td>';
						rowHtml += '<div class="alignright">$'+calculatedPrice+'</div>';
						rowHtml += '</td>';
						$('.order-tbl table tbody tr[rel="'+dishId+'"]').html(rowHtml);
					}
					totalPrice += parseInt(calculatedPrice);
					var salesTax = <?php echo $kitchenDetails['Kitchen']['sales_tax']; ?>;
					var serviceFee = <?php echo $paymentSettings['PaymentSetting']['service_fee']; ?>;
					totalPrice = totalPrice + serviceFee;
					var calculatedTax = (salesTax / 100) * totalPrice;
					calculatedTax = Math.round((calculatedTax) * 100) / 100;
					var widTax = calculatedTax + totalPrice;
					widTax = Math.round((widTax) * 100) / 100; 
					$('.order-tbl table tbody tr.service_fee').find('.alignright').html('<b>$'+serviceFee+'</b>');
					$('.order-tbl table tbody tr.before_Total').find('.alignright').html('<b>$'+totalPrice+'</b>');
					$('.order-tbl table tbody tr.total').find('.alignright').html('<b>$'+calculatedTax+'</b>');
					$('.order-tbl table tbody tr.maintotal').find('.alignright').html('<b>$'+widTax+'</b>');
				}
			}
			else
			{
					
				if($('.order-tbl table tbody tr[rel="'+dishId+'"]').length)
				{
					$('.order-tbl table tbody tr[rel="'+dishId+'"]').remove();
				}
			}
		});

		var kitchenId = <?php echo $kitchenDetails['Kitchen']['id']; ?>;
		var fromData = $('#OrderDishIndexForm').serializeArray();	
		$.ajax({
			'url':'<?php echo $this->Html->url(array('controller'=>'orders','action'=>'saveMySession')); ?>',
			'type':'POST',
			'data':fromData,
		});
	}

	function loadOrderChart(obj){
		var totalPrice = 0;
		
		$('.dishes-list input[type="checkbox"]').each(function(){
			var dishLi = $(this).parents('li');
			var dishId = $(dishLi).find('input[rel="dishId"]').val();
			var dishName = $(dishLi).find('input[rel="dishName"]').val();
			var dishPortion = $(dishLi).find('input[rel="dishPortion"]').val();
			var dishPrice = $(dishLi).find('input[rel="dishPrice"]').val();
			var dishQuantity = $(dishLi).find('input[rel="dishQuantity"]').val();
			if(dishPortion!='')
			{
				var selectedPortionPrice = $(dishLi).find('div[rel="'+dishPortion+'"]').attr('price');
				var calculatedPrice = parseInt(selectedPortionPrice)*parseInt(dishQuantity);
				$(dishLi).find('div.dish-price').html('$'+calculatedPrice);

				//if($(dishLi).find('.portionOuter div[class="active"]').length == 0)
				//	$(dishLi).find('div[rel="'+dishPortion+'"]').click();

			}

			if(this.checked)
			{
				var dishQuanityHtml = $(dishLi).find('.portionOuter div[class="active"]').html().replace('<br>','-');
				if($('.order-tbl table tbody tr[rel="'+dishId+'"]').length==0)
				{
					var rowHtml = '<tr rel="'+dishId+'">';
					rowHtml += '<td>';
					rowHtml += '<div>'+dishName+' - '+dishQuantity+'</div>';
					rowHtml += '<p>'+dishQuanityHtml+'</p>';
					rowHtml += '</td>';
					rowHtml += '<td>';
					rowHtml += '<div class="alignright">$'+calculatedPrice+'</div>';
					rowHtml += '</td>';
					rowHtml += '</tr>';
					$('.order-tbl table tbody tr.service_fee').before(rowHtml);
				}
			
				totalPrice += parseInt(calculatedPrice);
				var salesTax = <?php echo $kitchenDetails['Kitchen']['sales_tax']; ?>;
				var serviceFee = <?php echo $paymentSettings['PaymentSetting']['service_fee']; ?>;
				totalPrice = totalPrice + serviceFee;
				var calculatedTax = (salesTax / 100) * totalPrice;
				calculatedTax = Math.round((calculatedTax) * 100) / 100;
				var widTax = calculatedTax + totalPrice;
				widTax = Math.round((widTax) * 100) / 100; 
				$('.order-tbl table tbody tr.service_fee').find('.alignright').html('<b>$'+serviceFee+'</b>');
				$('.order-tbl table tbody tr.before_Total').find('.alignright').html('<b>$'+totalPrice+'</b>');
				$('.order-tbl table tbody tr.total').find('.alignright').html('<b>$'+calculatedTax+'</b>');
				$('.order-tbl table tbody tr.maintotal').find('.alignright').html('<b>$'+widTax+'</b>');
			}
		});

	}

	function appendHtmlData(htmlData){
			if(htmlData){ 
				$('li.reply').before(htmlData);
				$('li.reply').remove();
			}
			else{
				alert("Sorry, your review has not added.");
			}
		}

	</script>