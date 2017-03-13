 <section class="create-dish-sec">
		<section class="create-dish-title clearfix">
			<main class="container wdth100">
				<h3 class="fleft"><?php echo "Your Kitchen"; ?></h3> 
			</main>
		</section>
        <section class="createdish-mid-sec clearfix">
		     <main class="container">
		     <span class="user-image">
				 <div class="dish-img">
						<?php
						$imgName = 'img2.png';
						echo $this->Image->resize($imgName, 150, 150, true); 
						?>
				 </div>
            </span>
			 <br/>
			<div class="clearfix about-sec">
			 <p><?php echo "You don"."'"."t have an active kitchen, you can setup the kitchen and then edit kitchen."; ?></p>
			</div>
			<?php echo $this->Html->link('<input type="button" value="Activate Kitchen" class="view-kithech">',array("controller"=>"kitchens","action"=>"edit"),array('escape'=>false)); ?>
			</main>
          </section>		  
 </section>
