<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft"> Order History </h3> 
		</main>
	</section>
	<section class="order-history clearfix">
		<ul class="order-links"> 
			<li><a href="#" data="order-placed" class="active-link">Orders Placed </a></li>
			<li class=""><a href="#" data="order-received">Orders Received </a></li>
		</ul>
	</section>
	<section class="order-details">
		<ul class="order-placed">
		  <?php
		  	if(isset($results['order_placed']) && !empty($results['order_placed']))
		  	{
		  		foreach ($results['order_placed'] as $oKey => $oData) 
		  		{
		  		?>	
		  			<li>
					     <span class="date"><?php echo date('M d',strtotime( $oData['Order']['created'])); ?></span>
						 <div>
						    <b>
							   <span class="fleft">
							   <?php
							   if(isset($oData['OrderDish']) && !empty($oData['OrderDish']))
							   { 
								   	foreach ($oData['OrderDish'] as $ordkey => $ordish) {
								   		echo $ordish['quantity']." ".$ordish['dish_name']."<br/>";
								   	}
							   	} ?>
							    </span>
							   <span class="fright"><?php echo '$'.$oData['Order']['amount']; ?></span>
							</b>
							<label>
							<?php 
								echo $this->Html->link($oData['OrderDish'][0]['Kitchen']['name'],array('controller' => 'kitchens','action' => 'index',$oData['OrderDish'][0]['kitchen_id']),array('escape' => false, 'class' => 'fleft'));
								echo $this->Html->link('view bill',array('controller' => 'orders','action' => 'order_detail',$oData['Order']['id']),array('escape' => false, 'class' => 'fright iframe', 'id' => "order".$oData['Order']['id'])); ?>
							</labe>
						 </div>
					  </li>		
		  		<?php	
		  		}
		  	}
		  	else
		  		echo "<li>You have not placed any order yet.</li>";
		  ?>
		</ul>
		<ul class="order-received hide">
			<?php
			  	if(isset($results['order_recieved']) && !empty($results['order_recieved']))
			  	{
			  		foreach ($results['order_recieved'] as $oKey => $oData) 
			  		{
			  		?>
					  <li>
					     <span class="date"><?php echo date('M d',strtotime( $oData['Order']['created'])); ?></span>
						 <div>
						    <b>
							   <span class="fleft">
								   <?php
								   if(isset($oData['OrderDish']) && !empty($oData['OrderDish']))
								   { 
									   	foreach ($oData['OrderDish'] as $ordkey => $ordish) {
									   		echo $ordish['quantity']." ".$ordish['dish_name']."<br/>";
									   	}
								   	} ?>
								    </span>
								   <span class="fright"><?php echo '$'.$oData['Order']['amount']; ?></span>
							</b>
							<label>
							<?php 
								echo $this->Html->link($oData['User']['name'],array('controller' => 'users','action' => 'about_me',$oData['User']['id']),array('escape' => false, 'class' => 'fleft'));
								echo $this->Html->link('view bill',array('controller' => 'orders','action' => 'order_detail',$oData['Order']['id']),array('escape' => false, 'class' => 'fright iframe')); ?>
							</label>	
						 </div>
					  </li>
			  		<?php	
			  		}
			  	}
			  	else
		  		echo "<li>You have not received any order yet.</li>";
			  ?>
		</ul>
	</section>
</section>

<script>
$(document).ready(function(){
	$("a.iframe").fancybox({
	    width : 700,
		height : 575,
		type : 'iframe',
		autoScale : false,
		padding : 0,
		helpers : {
		        overlay : {
		            css : {
		                'background' : 'rgba(58, 42, 45, 0.50)'
		            }
		        }
		    },
	});

	$('ul.order-links li a').click(function(){ 
	    $('ul.order-links li a').removeClass('active-link');
		$(this).addClass('active-link');
		var getatr = $(this).attr('data');

		if(!$('.'+getatr).is(':visible'))
		{
			if(getatr=='order-placed')
			{
				$('ul.order-placed').show();
				$('ul.order-received').hide();
			}
			else
			{
	 			$('ul.order-placed').hide();
				$('ul.order-received').show();
			}
		}
	  });

	<?php
	if(isset($results['order_id']) && !empty($results['order_id']))
	{ ?>
		var orderId = <?php echo $results['order_id'] ?>;
		$('#order'+orderId).click();
	<?php } ?>
});
</script>