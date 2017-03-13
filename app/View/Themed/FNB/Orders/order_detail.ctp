<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft"> Order Details </h3> 
		</main>
	</section>
	<section class="order-details orders-total">
		<ul style="width:70%;">
		<?php if(isset($order_details) && !empty($order_details))
		{ ?>
			<li>
				<div class="width50percent fleft">
					<b class="trans-number">  TRANSACTION NUMBER </b><br/>
					<b class="oder-numb">
						<?php
							if($order_details['Order']['payment_type']==0)
								echo str_replace('/orders/', '', $order_details['Order']['order_href']);
							else
								echo $order_details['Order']['transaction_id'];
						?>
					</b>
				</div>
				<div class="width50percent fright">
					<b class="trans-number">  PAYMENT METHOD </b><br/>
					<b class="oder-numb">
						<?php
							if($order_details['Order']['payment_type']==0)
								echo 'Stripe Payment';
							else
								echo 'Paypal Payment';
						?>
					</b>
				</div>
				<div class="clearfix"></div>
			</li>
			<li>
				<span class="ordersummery">Order Summary</span>
				<div class="order-detal">
					<b>
						<?php
							echo $order_details['OrderDish'][0]['Kitchen']['name'];
						?>
					</b>
					<div class="items-info">
						<?php
							if(isset($order_details['OrderDish']) && !empty($order_details['OrderDish']))
							{
								foreach ($order_details['OrderDish'] as $odKey => $odData)
								{
								?>
									<span class="fleft"><?php echo $odData['dish_name']; ?></span>
									<span class="fright"><?php echo "$".$odData['price']; ?></span>
									<div class="clear"></div>
								<?php	
								}
							}
						?>
					</div>
				</div>
				<div class="total-payment">
					<span class="fleft">Order Value</span>
					<span class="fright"><?php echo "$".$order_details['Order']['order_value']; ?></span>
				</div>
				<div class="total-payment">
					<span class="fleft">Service Fee</span>
					<span class="fright"><?php echo "$".$order_details['Order']['service_fee']; ?></span>
				</div>
				<div class="total-payment">
					<span class="fleft">Sales Tax(<?php echo $order_details['Order']['tax_percent']."%"; ?>)</span>
					<span class="fright"><?php echo "$".$order_details['Order']['sale_tax']; ?></span>
				</div>
				<div class="total-payment">
					<span class="fleft">Total</span>
					<span class="fright"><?php echo "$".$order_details['Order']['amount']; ?></span>
				</div>
			</li>
			<li>
				<div class="dinning-options">
					<span>
						<b>Date </b><br/>
						<b class="date-icon"><?php echo date('M d, Y',strtotime($order_details['Order']['created'])); ?></b>
						</span>
						<!--
						<span>
						<b>Dining options </b><br/>
						<?php 
							if($order_details['Order']['dine_type'] == 1)
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
					</span>
					-->
				</div>
			</li>
		<?php } ?>
		</ul>
	</section>
</section>