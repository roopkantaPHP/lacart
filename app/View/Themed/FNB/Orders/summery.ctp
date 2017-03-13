<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
  // This identifies your website in the createToken call below
  var api_key = '<?php echo $api_key ?>';
  Stripe.setPublishableKey(api_key);
  // ...
</script>
<?php
     if(isset($errors) && !empty($errors)){
		$errr = '';
		foreach ($errors as $key => $value) {
			$errr .= '<div>'.$value[0].'</div>';
		}
		if(!empty($errr))
			{ ?>
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

<section class="dish-ingredient-sec">
	<ul class="billing-info clearfix">
		 <li data="billing" class="active">Billing</li>
		 <li data="summery">Order Summary</li>
		 <li data="payment">Payment</li>
	</ul>
 	<?php echo $this->Form->create('Order',array('controller'=>'orders','action'=>'summery')); ?>
	<section class="oder-sumery-section clearfix" id="billing">
		<div class="bank-desct clearfix">
			<p>Please make sure that the following details are correct.</p>
		</div>
		<ul class="bank-account  billing-section">
			<li>
				<label class="lable">Mobile</label>
				<?php echo $this->Form->input('User.mobile',array('div'=>false,'label'=>false,'class'=>'kitc-infield numeric','readonly'=>'readonly','value'=>$userDetails['User']['phone'])); ?>
			</li>
			<br/>
			<li>
				<label class="lable">Billing Address</label>
				<span class="portion-checkbox clearfix" style="width:75%;">
					<?php echo $this->Form->checkbox('User.sameAddress',array('div'=>false,'hiddenField'=>false,'label'=>false,'id'=>'ad1')); ?>
					<label for="ad1"><span></span><small>Same as home address</small></label>
				</span>
				<?php 
					$addressLine1 = '';
					$addressLine2 = '';	
					if(!empty($userDetails['User']['address']))
					{
						$wordArray = explode(' ', $userDetails['User']['address']);
						$addressLines = array_chunk($wordArray,count($wordArray) / 2,true);
        				if(isset($addressLines[0]))
        					$addressLine1 = implode(' ', $addressLines[0]);
						if(isset($addressLines[1]))
							$addressLine2 = implode(' ', $addressLines[1]);
						if(isset($addressLines[2]))
							$addressLine2 .= ' '.implode(' ', $addressLines[2]);
						
						
					}
				 	echo $this->Form->input('HomeAddress.line-1',array('type'=>'hidden','value'=>$addressLine1));
					echo $this->Form->input('HomeAddress.line-2',array('type'=>'hidden','value'=>$addressLine2));
					echo $this->Form->input('HomeAddress.state',array('type'=>'hidden','value'=>$userDetails['User']['state_id']));
					echo $this->Form->input('HomeAddress.city',array('type'=>'hidden','value'=>$userDetails['User']['city_id']));
					echo $this->Form->input('HomeAddress.zipcode',array('type'=>'hidden','value'=>$userDetails['User']['zipcode']));
				?>
				<?php
					echo $this->Form->input('User.line-1',array('div'=>false,'label'=>false,'class'=>'kitc-infield','placeholder'=>'line 1')); ?>
			</li>
			<li>
				<?php echo $this->Form->input('User.line-2',array('div'=>false,'label'=>false,'class'=>'kitc-infield','placeholder'=>'line 2')); ?>
			</li>
			<li>
				<?php echo $this->Form->input('User.state',array('options'=>$states,'div'=>false,'label'=>false,'class'=>'kitc-infield','empty'=>'Select State')); ?>
			</li>
			<li>
				<?php echo $this->Form->input('User.city',array('type'=>'text','div'=>false,'label'=>false,'class'=>'kitc-infield state-fld','empty'=>'Select City'));

					 echo $this->Form->input('User.zipcode',array('div'=>false,'label'=>false,'class'=>'kitc-infield numeric zip-fld','placeholder'=>'Zipcode'));
				?>
			</li>
			<li>
				<?php echo $this->Html->link('Next','javascript:void(0);',array('class'=>'btn-next gotoNext','style'=>'text-align:center;')); ?>
			</li>
		</ul>
	</section>
 	<section class="oder-sumery-section clearfix hide" id="summery">
	    <section class="order-summary" id="orderSummery">
	    	<div class="order-detal">
			    <div class="kitn-detail mb20">
			    	<b><?php echo $kitchenForOrderSummery['Kitchen']['name']; ?></b>
			    	<a class="fright editOrder" href="javascript:void(0);">Edit Order</a>
			    </div>
			    <div class="items-info">
					<?php
					$total = 0;
					$totalTax = 0;
					$service_fee =  $paymentSettings['PaymentSetting']['service_fee'];
					$mainTotal = 0;
					foreach ($this->request->data['Order'] as $key => $value)
					{ 
					?>	
						<span class="fleft"><?php echo $value['Dish']['Dish']['name']; ?></span>
						<span class="fright">
						<?php
							if($value['OrderDish']['portion'] == 'budget')
								$price = $value['Dish']['Dish']['p_small_price'] * $value['OrderDish']['quantity'];
							else if($value['OrderDish']['portion'] == 'premium')
								$price = $value['Dish']['Dish']['p_big_price'] * $value['OrderDish']['quantity'];
							else if($value['OrderDish']['portion'] == 'custom')
								$price = $value['Dish']['Dish']['p_custom_price'] * $value['OrderDish']['quantity'];
							
							$total += $price;
							$totalForTax = $total+$service_fee;
							$totalTax = round((($kitchenForOrderSummery['Kitchen']['sales_tax'] / 100 ) * $totalForTax),2);
							$mainTotal = $totalForTax+$totalTax;
							$mainTotal = round($mainTotal,2);
							echo "$".$price;
						?>
						</span>
						<div class="clear"></div>
					<?php
					}
					?>
				</div>
			</div>
			<div class="total-payment">
				<span class="fleft">Delivery Fee</span>
				<span class="fright"><?php echo "$".$service_fee; ?></span>
			</div>
			<div class="total-payment">
				<span class="fleft">Order value</span>
				<span class="fright"><?php echo "$".$totalForTax; ?></span>
			</div>
		</section>

		<ul class="dishes-list clearfix hide" id="orderDetails">
		   <?php 
			    	if(isset($this->request->data['Order']) && !empty($this->request->data['Order']))
			    	{
			    		foreach ($this->request->data['Order'] as $dkey => $dData)
			    		{	
			    			if(!empty($dData['Dish']['Dish']['p_small']) || !empty($dData['Dish']['Dish']['p_big']) || (!empty($dData['Dish']['Dish']['p_custom']) && $dData['Dish']['Dish']['is_custom_price_active']==1))
			    			{ ?>
							<li>
							  <div class="image-con">
								<div class="checkbox-otr">
									 <?php 	
									 		if($dData['OrderDish']['portion'] == 'budget')
									 		{
												$price = $dData['Dish']['Dish']['p_small_price'] * $dData['OrderDish']['quantity'];
									 		}
											else if($dData['OrderDish']['portion'] == 'premium')
											{
												$price = $dData['Dish']['Dish']['p_big_price'] * $dData['OrderDish']['quantity'];
											}
											else if($dData['OrderDish']['portion'] == 'custom')
											{
												$price = $dData['Dish']['Dish']['p_custom_price'] * $dData['OrderDish']['quantity'];
											}
											
											$total += $price;
											$checked = false;
											if($dData['Dish']['Dish']['id']==$dData['OrderDish']['dish_id'])
												$checked = true;
									 		echo $this->Form->checkbox('OrderDish.'.$dData['Dish']['Dish']['id'].'.is_checked', array('hiddenField'=>false,'class'=>'checkbox dishSelect', 'div'=>'true','checked'=>$checked));
									 	   	echo $this->Form->input('OrderDish.'.$dData['Dish']['Dish']['id'].'.dish_id', array('type'=>'hidden', 'value'=>$dData['Dish']['Dish']['id'], 'rel'=> 'dishId')); 
									 	   	echo $this->Form->input('OrderDish.'.$dData['Dish']['Dish']['id'].'.dish_name', array('type'=>'hidden', 'value'=>$dData['Dish']['Dish']['name'], 'rel'=> 'dishName'));
									 	   	echo $this->Form->input('OrderDish.'.$dData['Dish']['Dish']['id'].'.quantity', array('type'=>'hidden', 'rel'=> 'dishQuantity','value'=>$dData['OrderDish']['quantity']));
									 	   	echo $this->Form->input('OrderDish.'.$dData['Dish']['Dish']['id'].'.kitchen_id', array('type'=>'hidden','value'=>$kitchenForOrderSummery['Kitchen']['id']));
								 			
								 			echo $this->Form->input('OrderDish.'.$dData['Dish']['Dish']['id'].'.price', array('type'=>'hidden','value'=>0,'rel'=>'dishPrice'));
								 			echo $this->Form->input('OrderDish.'.$dData['Dish']['Dish']['id'].'.portion', array('type'=>'hidden', 'rel'=>'dishPortion','value'=>$dData['OrderDish']['portion']));
									 	    ?>
								</div>
							    <div class="image">
							    	<?php
							    		$imgName = $this->Common->getDishImage($dData['Dish']);
							    		echo $this->Image->resize($imgName, 150, 150, true);
							    	?>
							    </div>
								<div class="image-data">
								  <h3><?php echo $dData['Dish']['Dish']['name']; ?></h3>
								  <div class="time">
								  	<span>
									  	<b>
									  		<?php echo $dData['Dish']['Dish']['serve_start_time']; ?>
									  	</b>
								  	</span>
								  	<span>
								  		<?php echo "Last order ".$dData['Dish']['Dish']['serve_end_time']; ?>
								  	</span>
								  </div>
								</div>
							  </div>
							<div class="dish-portion">
							    <h6>PORTION</h6>
								<div class="clearfix pecies">
									<main class="portionOuter">
										<?php if(!empty($dData['Dish']['Dish']['p_small']))
											{
												$className = '';
												if($dData['OrderDish']['portion'] == 'budget')
													$className = 'active';
											?>
											<div rel="budget" class="<?php echo $className; ?>" price="<?php echo $dData['Dish']['Dish']['p_small_price']; ?>">
												Budget
												<br/>
												<?php echo $dData['Dish']['Dish']['p_small_quantity'].$dData['Dish']['Dish']['p_small_unit']; ?>
											</div>
										<?php }
											if(!empty($dData['Dish']['Dish']['p_big']))
											{ 
												$className = '';
												if($dData['OrderDish']['portion'] == 'premium')
													$className = 'active';
											?>
											<div rel="premium" class="<?php echo $className; ?>" price="<?php echo $dData['Dish']['Dish']['p_big_price']; ?>">
												Premium
												<br/>
												<?php echo $dData['Dish']['Dish']['p_big_quantity'].$dData['Dish']['Dish']['p_big_unit']; ?>
											</div>
										<?php }
											if(!empty($dData['Dish']['Dish']['p_custom']) && $dData['Dish']['Dish']['is_custom_price_active']==1)
											{ 
												$className = '';
												if($dData['OrderDish']['portion'] == 'custom')
													$className = 'active';
											?>
											<div rel="custom" class="<?php echo $className; ?>" price="<?php echo $dData['Dish']['Dish']['p_custom_price']; ?>">
												Custom
												<br/>
												<?php echo $dData['Dish']['Dish']['p_custom_quantity'].$dData['Dish']['Dish']['p_custom_unit']; ?>
											</div>
										<?php } ?>
										
									</main>
								</div>
								<h6>QUANTITY</h6>
								<div class="clearfix pecies quantity">
								  <a href="javascript:void(0);" class="bigpc qMinus left-ra">-</a>
								  <div class="showQuantity"><?php echo $dData['OrderDish']['quantity']; ?></div>
								  <a href="javascript:void(0);" class="bigpc qPlus">+</a>
								  <div class="dish-price">
								  	<?php echo "$".$price; ?>
								  </div>
								</div>
							  </div>
							  <div class="clr"></div>
							  <div class="accordian">
							     <div class="acdian-hdg">More</div>
								 <div class="acdian-data" style="display:none;">
								   <?php if(isset($dData['Dish']['Dish']['cuisine']) && !empty($dData['Dish']['Dish']['cuisine'])){ ?>
									   <h6>CUISINE</h6>
									   <p><?php echo $dData['Dish']['Dish']['cuisine']; ?></p>
								   <?php } ?>
								   <?php if(isset($dData['Dish']['Dish']['allergens']) && !empty($dData['Dish']['Dish']['allergens'])){ ?>
									   <h6>Allergy</h6>
									   <p><?php echo $dData['Dish']['Dish']['allergens']; ?></p>
								   <?php } ?>
								   <?php if(isset($dData['Dish']['Dish']['diet']) && !empty($dData['Dish']['Dish']['diet'])){ ?>
									   <h6>DIET</h6>
									   <p><?php echo $dData['Dish']['Dish']['diet']; ?></p>
								   <?php } ?>
								   <?php if(isset($dData['Dish']['Dish']['repeat']) && !empty($dData['Dish']['Dish']['repeat'])){ ?>
									   <h6>REPEAT-ON</h6>
									   <p><?php echo $dData['Dish']['Dish']['repeat']; ?></p>
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
		<section class="dinng-sec">
			<div class="right-sec">
			  <div class="order-tbl" style="padding:0px 16%;">
				 	<span>
						<table>
						<tr class="taxtotal">
							<td>
								<div><b>Sales Tax(<?php echo $kitchenForOrderSummery['Kitchen']['sales_tax'].'%'; ?>)</b></div>
							</td>
							<td>
								<div class="alignright"><b><?php echo '$'.$totalTax;  ?></b></div>
							</td>
						</tr>
						<tr class="total">
							<td>
								<div><b>Total</b></div>
							</td>
							<td>
								<div class="alignright"><b><?php echo '$'.$mainTotal; ?></b></div>
							</td>
						</tr>
						</table>
					</span>
			  </div>
			</div>
		</section> 
		<section class="dinng-sec">
	         <div class="dinning-options">
			    <span>
			     	<b>DELIVERY TIME </b>
			     	<br>
					<b class="serving-timer">
						<?php
							$searchData =  $this->Session->read('search_data.time');
							if(isset($searchData) && !empty($searchData))
								echo $searchData;
							else
								echo $this->request->data['Order'][0]['Dish']['Dish']['serve_start_time'].' - '.$this->request->data['Order'][0]['Dish']['Dish']['serve_end_time'];	 
						?>
					</b>
				</span>
				<span>
					<b>LOCATION </b><br>
					 <?php if(isset($kitchenForOrderSummery['Kitchen']['lat']) && !empty($kitchenForOrderSummery['Kitchen']['lat']) && isset($userDetails['User']['lat']) && !empty($userDetails['User']['lat']))
					 { ?>
					 <b class="location-icon">
					 	<?php echo $this->common->distance( $kitchenForOrderSummery['Kitchen']['lat'],  $kitchenForOrderSummery['Kitchen']['lng'], $this->Session->read('search_data.latitude'), $this->Session->read('search_data.longitude'), "M") . " miles"; ?>
					 </b>
					 <?php } ?>
	                 <!-- <small><?php echo $kitchenForOrderSummery['Kitchen']['address']; ?></small>-->
				</span>
			 </div>
	    </section>

	    <section class="Commmon-allergens clearfix dineOptions">
		   		<!--<b>Dinning Options </b><br/>-->
		     	<section class="common-alergen-checkbox">
		      		<!--<?php
		      		if(isset($kitchenForOrderSummery['Kitchen']['dining_dine_in']) && $kitchenForOrderSummery['Kitchen']['dining_dine_in']==1)
		      		{	
		      			$checkedDine = false;
		      			if(isset($this->request->data['User']['dining_dine_in']) && $this->request->data['User']['dining_dine_in']==1)
		      				$checkedDine = true;

		      		?>
		      		<span class="portion-checkbox">
		      			<?php 
		      			echo $this->Form->checkbox('User.dining_dine_in',array('label'=>false,'div'=>false,'id'=>'dine-in','hiddenField' => false,'checked' => $checkedDine)); ?>
   						<label for="dine-in"><span></span><?php echo $this->Html->image('dine-in.png'); ?><br/><b>Dine-in</b></label>
					</span>
					<?php 
					}
					
					if(isset($kitchenForOrderSummery['Kitchen']['dining_take_out']) && $kitchenForOrderSummery['Kitchen']['dining_take_out']==1)
		      		{
		      			$checkedTake = false;
		      			if(isset($this->request->data['User']['dining_take_out']) && $this->request->data['User']['dining_take_out']==1)
		      				$checkedTake = true;	
		      		?>
		      		<span class="portion-checkbox">
						<?php 
						echo $this->Form->checkbox('User.dining_take_out',array('label'=>false,'div'=>false,'id'=>'take-out','hiddenField' => false,'checked' => $checkedTake)); ?>
   					    <label for="take-out"><span></span><?php echo $this->Html->image('icon-2.png'); ?><br/><b>Take-out</b></label>
					</span>
					<?php 
					}
					?>
					<br/>
					-->
					<div><?php echo $this->Html->link('Next','javascript:void(0);',array('class'=>'btn-next gotoNext')); ?></div>
				</section>
		</section>
	</section>

	<section class="oder-sumery-section clearfix hide" id="payment">
		<div class="dishes-section">
		    <div class="container">
				<div class="tabs-bar clearfix">
					<ul>
					 <li><?php echo $this->Html->link('Saved Cards','javascript:void(0);',array('class'=>'active','data'=>'cards-tab')); ?></li>  
					 <?php if($kitchenForOrderSummery['User']['is_paypal_verified'])
					 {
					 ?>
					 <li><?php echo $this->Html->link('Paypal','javascript:void(0);',array('data'=>'paypal-tab')); ?></li>  
					 <?php
					 }
					 ?>
					 <li><?php echo $this->Html->link('Credit Card','javascript:void(0);',array('data'=>'receipt-tab')); ?></li>  
					</ul>
			</div>
		</div>
		<div class="tabs-content-area" style="text-align:left;">
			<!-- Cards -->
			<div class="cards-tab con-tabs clearfix">
					<section class="community-sec clearfix">
				  		<div class="popupContainer">
							<ul class="cart-info">
								<?php if(isset($paymentDetails) && !empty($paymentDetails)){
									foreach($paymentDetails as $pid => $pValue){ 
								 ?>
								<li>
									<?php
									$checkedPay = false;
									if(isset($this->request->data['PaymentMethod']['id']) && !empty($this->request->data['PaymentMethod']['id']))
									{ 
										if(array_key_exists($pValue['id'], $this->request->data['PaymentMethod']['id']))
										$checkedPay = true;
									}
									echo $this->Form->checkbox('PaymentMethod.id.'.$pValue['PaymentMethod']['id'], array('hiddenField'=>false,'class'=>'checkbox', 'div'=>'true','checked' => $checkedPay,'rel'=>$pValue['PaymentMethod']['id'],'class'=>'selectedCard'));
									echo $this->Form->input('PaymentMethod.card_no.'.$pValue['PaymentMethod']['id'], array('type'=>'hidden','value'=>$pValue['PaymentMethod']['card_no']));
									echo $this->Form->input('PaymentMethod.exp_month.'.$pValue['PaymentMethod']['id'], array('type'=>'hidden','value'=>$pValue['PaymentMethod']['exp_month']));
									echo $this->Form->input('PaymentMethod.exp_year.'.$pValue['PaymentMethod']['id'], array('type'=>'hidden','value'=>$pValue['PaymentMethod']['exp_year']));
									echo $this->Form->input('PaymentMethod.card_name.'.$pValue['PaymentMethod']['id'], array('type'=>'hidden','value'=>$pValue['PaymentMethod']['card_name']));

									?>
									<figure class="card-img"><?php echo $this->Html->image($pValue['PaymentMethod']['type'].'.png'); ?></figure>
									<span class="card-number"><?php echo $pValue['PaymentMethod']['card_no']; ?></span>
								</li>	
								<?php
									}
								?>
								<li>
									<div class="width50percent fleft">
										<label>Cvv No.</label>
										<?php echo $this->Form->input('PaymentMethod.cvv_no1', array('label'=>false,'div'=>false,'class'=>'disc-field numeric','autocomplete'=>"off")); ?>
									</div>
								</li>		
								<?php	
								 }else{
								 	echo "You don"."'"."t have any saved cards.";
								 	} ?>
							</ul>			
							<div class="clearfix"></div>
						</div>
					</section>		
			</div>

			<!-- Paypal -->
			<div class="paypal-tab con-tabs" style="display:none;">
	   				<section class="community-sec clearfix">
				  		<div class="discussion-container">
							<ul>
								<li>
									<label> Id</label>
									<?php echo $this->Form->text('User.paypal_id', array('label'=>false,'div'=>false,'class'=>'disc-field','required'=>false,'autocomplete'=>"off")); ?>
								</li>
								<br/>
							</ul>
						</div>
					</section>
			</div>

			<!-- Reciept -->
			<div class="receipt-tab con-tabs" style="display:none;">
					<section class="community-sec clearfix">
				  		<div class="discussion-container">
							<ul>
								<li>
									<label> Card number</label>
									<?php echo $this->Form->input('PaymentMethod.card_no', array('label'=>false,'div'=>false,'class'=>'disc-field numeric','autocomplete'=>"off",'data-stripe'=>"number", 'max'=>16));
										echo $this->Form->input('PaymentMethod.stripeToken',array('type'=>'hidden')); ?>
								</li>
								<li>
									<label> Card holder name</label>
									<?php echo $this->Form->input('PaymentMethod.card_name', array('label'=>false,'div'=>false,'class'=>'disc-field','autocomplete'=>"off",'data-stripe'=>"name")); ?>
								</li>
								<br/>
								<li>
									<div class="width50percent fleft">
										<label>Exp Month</label>
										<?php
										for($i=1;$i<=12;$i++){
									        $expMonth[$i] = $i;
									    }
										echo $this->Form->input('PaymentMethod.exp_month', array('class'=>'community-select','empty'=>'Exp Month','label'=>false,'options'=>$expMonth,'data-stripe'=>"exp-month")); 
										?>
									</div>
									<div class="width50percent fright">
										<label>Exp Year</label>
										<?php
										for($i=0;$i<21;$i++){
									        $expYear[date('Y',strtotime('+'.$i.' year'))] = date('Y',strtotime('+'.$i.' year'));
									    }
										echo $this->Form->input('PaymentMethod.exp_year', array('class'=>'community-select','empty'=>'Exp Year','label'=>false,'options'=>$expYear,'data-stripe'=>"exp-year")); ?>
									</div>
								</li>
								<br/>
								<li>
									<div class="width50percent fleft">
										<label>Cvv No.</label>
										<?php echo $this->Form->input('PaymentMethod.cvv_no', array('label'=>false,'div'=>false,'class'=>'disc-field numeric','autocomplete'=>"off",'data-stripe'=>"cvc", 'max'=>4)); ?>
									</div>
									<div class="width50percent fright">
										<label>Card type</label>
										<?php echo $this->Form->input('PaymentMethod.type', array('class'=>'community-select','empty'=>'Card type','label'=>false,'options'=>array('visa'=>'Visa', 'mastercard'=>'Mastercard', 'amex'=>'Amex','discover'=>'Discover'))); ?>
									</div>
									
								</li>
								<br/>
								<li>
									<div class="width50percent fleft">
										<label><?php echo "Save Card"; ?></label>
										<?php echo $this->Form->checkbox('PaymentMethod.save_card',array('id'=>'show_pass','hiddenField' => false)); ?>
									</div>
								</li>
							</ul>		
							<div class="clearfix"></div>
						</div>
					</section>		
			</div>
		</div>
		<div class="discussion-container">
			<?php echo $this->Form->submit('Pay',array('class'=>'btn-next')); ?>
		</div>
	</div>
	</section>
	
	<?php
	echo $this->Form->input('PaymentMethod.paytype',array('type'=>'hidden','value'=>1)); 
	echo $this->Form->end(); ?>
</section>
<div class="ajax-loading">
			<div>
				<span class = 'loading_message'>
					<?php echo $this->Html->image('../images/ajax-loader.gif'); ?>
				</span>
				<span class = "message_update">
					<b><?php echo "Please wait while your transaction is being processed." ?></b>
				</span>
			</div>
		</div>
<script>

$(document).ready(function(){
	<?php 
	if(isset($errors) && !empty($errors))
	{ ?>
	$('ul.billing-info li').removeClass('active');
	$('section.oder-sumery-section').hide();
	if($('#dine-in').is(':checked') == false && $('#take-out').is(':checked') == false)
	{
		$('ul.billing-info li[data="summery"]').addClass('active');
		$('section#summery').show();
	}
	else
	{
		$('ul.billing-info li[data="payment"]').addClass('active');
		$('section#payment').show();
	}	
	<?php } ?>
	
	$('.tabs-bar ul li a').click(function(){
	    $('.tabs-bar ul li a').removeClass('active');
		$(this).addClass('active');
		var getatr = $(this).attr('data');
		if(getatr == 'cards-tab')
		{
			$('#PaymentMethodPaytype').val(1);
		}
		else if(getatr == 'paypal-tab')
		{
			$('#PaymentMethodPaytype').val(2);
		}
		else if(getatr == 'receipt-tab')
		{
			$('#PaymentMethodPaytype').val(3);
		}
		
		$('.con-tabs').fadeOut(300);
		$('.'+getatr).fadeIn(300);
	  });

	<?php
	if(isset($this->request->data['PaymentMethod']['paytype']) && !empty($this->request->data['PaymentMethod']['paytype']))
	{ ?>
		var paytype = <?php echo $this->request->data['PaymentMethod']['paytype']; ?>;
		if(paytype == 1)
		{
			$('.tabs-bar ul li a[data="cards-tab"]').click();
		}
		else if(paytype == 2)
		{
			$('.tabs-bar ul li a[data="paypal-tab"]').click();
		}
		else
		{
			$('.tabs-bar ul li a[data="receipt-tab"]').click();
		}
	<?php
	}
	?>

	 $('#show_pass').change(function(){
	 	if($(this).is(':checked')){
	 		$('#UserPaypalPass').attr('type','text');
	 	}
	 	else{
	 		$('#UserPaypalPass').attr('type','password');
	 	}
	 });

	$('.editOrder').click(function(){
		$('#orderSummery').hide();
		$('#orderDetails').show();
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

	$('.gotoNext').click(function(){
		var errors = '';
			if($('#UserMobile').is(':visible') && $('#UserMobile').val() == '')
				errors += '<div>Please enter your mobile number.</div>';
			if($('#UserLine-1').is(':visible') && $('#UserLine-1').val() == '')
				errors += '<div>Please enter your address line 1.</div>';
			if($('#UserLine-2').is(':visible') && $('#UserLine-2').val() == '')
				errors += '<div>Please enter your address line 2.</div>';
			if($('#UserState').is(':visible') && $('#UserState').val() == '')
				errors += '<div>Please select your state.</div>';
			if($('#UserCity').is(':visible') && $('#UserCity').val() == '')
				errors += '<div>Please select your city.</div>';
			if($('#UserZipcode').is(':visible') && $('#UserZipcode').val() == '')
				errors += '<div>Please enter your zipcode.</div>';
		
		if($('#dine-in').is(':visible') && $('#dine-in').is(':checked') == false && $('#take-out').is(':visible') && $('#take-out').is(':checked') == false)
		{
			errors += '<div>Please select your Dining Option.</div>';
		}

		if(errors != '')
		{
			var appendError =	'<div class="error_popup">';
				appendError +=	'<div class="error_title"><strong>Please make proper entries.</strong></div>';
				appendError +=	'<div onclick="close_error()" id="close_error">';
				appendError +=	'<?php echo $this->Html->image('cross_grey_small.png',array('height'=>10)); ?>';
				appendError +=	'</div>';
				appendError +=	errors;
				appendError +=	'<div style="clear:both;"></div>';
				appendError +=	'</div>';
				$('.error_popup').remove();
				$('.dish-ingredient-sec').append(appendError);
			}
		else
		{
			if($('.error_popup').length)
			$('.error_popup').remove();

			var data = $('ul.billing-info li[class="active"]').attr('data');
			$('ul.billing-info li').removeClass('active');
			$('section.oder-sumery-section').hide();
			if(data=='billing')
			{
				$('ul.billing-info li[data="summery"]').addClass('active');
				$('section#summery').show();
			}
			else if(data=='summery')
			{
				$('ul.billing-info li[data="payment"]').addClass('active');
				$('section#payment').show();
			}
		}
	});

	$(".dineOptions input:checkbox").change(function() {  
            var currentCheck = this;
            $(".dineOptions input:checkbox").each(function() {
                if($(this).is(':checked') && $(currentCheck).attr('id') != $(this).attr('id')){
                    $(this).attr('checked',false);
                    $(this).parent('span').removeClass('selected');
                }
            });
        });

	$(".cards-tab .popupContainer input:checkbox").change(function() {  
            var currentCheck = this;
            $(".cards-tab .popupContainer input:checkbox").each(function() {
                if($(this).is(':checked') && $(currentCheck).attr('id') != $(this).attr('id')){
                    $(this).attr('checked',false);
                    $(this).parent('span').removeClass('selected');
                }
            });
        });

	$("ul.bank-account input:checkbox").change(function() {  
            if($(this).is(':checked'))
            {
            	$('#UserLine-1').val($('#HomeAddressLine-1').val());
            	$('#UserLine-2').val($('#HomeAddressLine-2').val());
            	$('#UserState').val($('#HomeAddressState').val());
            	$('#UserZipcode').val($('#HomeAddressZipcode').val());
            	if($('#UserState').val()!='')
            	{
            		$.ajax({
						'url':'<?php echo $this->Html->url(array('controller'=>'users','action'=>'getCityOptions')); ?>/'+$('#HomeAddressState').val(),
						'success': function(output)
						{
							$('#UserCity').html(output);
							$('#UserCity').val($('#HomeAddressCity').val());
            			}
					});
            	}
            }
            else
            {
           		$('#UserLine-1').val('');
            	$('#UserLine-2').val('');
            	$('#UserState').val('');
            	$('#UserCity').val('');
            	$('#UserZipcode').val('');
            }
        });

	
	$('#OrderSummeryForm').submit(function() {
		if($('#PaymentMethodPaytype').val()==1)
		{
			var payId = $('.selectedCard:checked:first').attr('rel');
			if(payId!='')
			{
				var cvv = $('#PaymentMethodCvvNo1').val();
				if(cvv=='')
				{
					alert('Please enter card cvv number.');
					return false;
				}
				else
				{
					$('#PaymentMethodCardNo').val($('#PaymentMethodCardNo'+payId).val());
					$('#PaymentMethodExpMonth').val($('#PaymentMethodExpMonth'+payId).val());
					$('#PaymentMethodExpYear').val($('#PaymentMethodExpYear'+payId).val());
					$('#PaymentMethodCvvNo').val($('#PaymentMethodCvvNo1').val());
				}
			}
			else
			{
				$('#PaymentMethodCardNo').val('');
				$('#PaymentMethodExpMonth').val('');
				$('#PaymentMethodExpYear').val('');
				$('#PaymentMethodCvvNo').val('');
			}	
		}
		else
		{
			var cvv = $('#PaymentMethodCvvNo').val();
		}
		if(cvv!='')
		{
			var $form = $(this);

		    // Disable the submit button to prevent repeated clicks
		    $form.find('button').prop('disabled', true);

		    Stripe.card.createToken($form, stripeResponseHandler);

		    // Prevent the form from submitting with the default action

		    $('.ajax-loading').show(); // show animation
		    return false; // allow regular form submission
		}
	});
});

function stripeResponseHandler(status, response) {
  var $form = $('#OrderSummeryForm');

  if (response.error) {
    // Show the errors on the form
    var appendError =	'<div class="error_popup">';
	appendError +=	'<div class="error_title"><strong>Please make proper entries.</strong></div>';
	appendError +=	'<div onclick="close_error()" id="close_error">';
	appendError +=	'<?php echo $this->Html->image('cross_grey_small.png',array('height'=>10)); ?>';
	appendError +=	'</div>';
	appendError +=	response.error.message;
	appendError +=	'<div style="clear:both;"></div>';
	appendError +=	'</div>';
	$('.error_popup').remove();
	$('.dish-ingredient-sec').append(appendError);
    $('.ajax-loading').hide();
  } else {
    // response contains id and card, which contains additional card details
    var token = response.id;
    // Insert the token into the form so it gets submitted to the server
    $('#PaymentMethodStripeToken').val(token);
    // and submit
    $form.get(0).submit();
  }
};

function refreshOrderChart(obj){
		var isChecked = 0;
		var totalPrice = 0;
		if($(obj).attr('type')=='checkbox')
		{
			isChecked = 1;
		}
		//$('.order-tbl table tbody tr.total').find('.alignright').html('<b>$0</b>');
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
						$(this).click();
				}
				else
				{
					var dishQuanityHtml = $(dishLi).find('.portionOuter div[class="active"]').html().replace('<br>','-');
					if($('.order-tbl table tbody tr[rel="'+dishId+'"]').length==0 && isChecked)
					{
						//$('.order-tbl table tbody tr.total').before(rowHtml);
					}
					else if($('.order-tbl table tbody tr[rel="'+dishId+'"]').length && !isChecked)
					{
						//$('.order-tbl table tbody tr[rel="'+dishId+'"]').html(rowHtml);
					}
					totalPrice += parseInt(calculatedPrice);
					var serviceFee = <?php echo $paymentSettings['PaymentSetting']['service_fee']; ?>;
					totalPrice = totalPrice + serviceFee;
					
					var salesTax = <?php echo $kitchenForOrderSummery['Kitchen']['sales_tax']; ?>;
					var calculatedTax = (salesTax / 100) * totalPrice;
					calculatedTax = Math.round((calculatedTax) * 100) / 100;
					var widTax = calculatedTax + totalPrice;
					widTax = Math.round((widTax) * 100) / 100; 
					$('.total-payment .fright').html('<b>$'+totalPrice+'</b>');
					$('.order-tbl table tbody tr.taxtotal').find('.alignright').html('<b>$'+calculatedTax+'</b>');
					$('.order-tbl table tbody tr.total').find('.alignright').html('<b>$'+widTax+'</b>');
				}
			}
		});
	}
</script>