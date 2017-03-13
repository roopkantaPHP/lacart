<?php 
$dishServed = 0;
$orderPlaced = 0;

if(isset($userDetails['Kitchen']['Dish'][0]['Dish'][0]['dishServed']))
	$dishServed = $userDetails['Kitchen']['Dish'][0]['Dish'][0]['dishServed'];
	
if(isset($userDetails['Order'][0]['Order'][0]['noOfPlacedOrders']))
	$orderPlaced = $userDetails['Order'][0]['Order'][0]['noOfPlacedOrders'];

?>
 <section class="create-dish-sec">
	     <section class="createdish-mid-sec clearfix">
		     <main class="container">
		     <span class="user-image">
				 <div class="dish-img">
						<?php
						$imgName = $this->Common->getProfileImage($userDetails);
						echo $this->Image->resize($imgName, 150, 150, true); 
						?>
				 </div>
                  <span class="user-name"><?php echo $userDetails['User']['name'].' s Kitchen'; ?></span>
				   <!--<span class="user-address"><?php echo $userDetails['User']['address']; ?></span>-->
			 </span>
			 <br/>
			<div class="clearfix about-sec">
			 <b class="about-me">About me</b><br/>
			 <p><?php echo $userDetails['User']['description']; ?></p>
			</div>
			 <ul class="orders-info">
			   <li>Orders placed <br/> <b><?php echo $orderPlaced; ?></b></li>
			    <li>Dishes Served <br/> <b><?php echo $dishServed; ?></b></li>
			 
			 </ul>
			 <?php echo $this->Html->link('<input type="button" value="View Kitchen" class="view-kithech">',array('controller'=>'kitchens','action'=>'index',$userDetails['Kitchen']['id']),array('escape'=>false)); ?>
			 </main>
          </section>		  
 </section>
