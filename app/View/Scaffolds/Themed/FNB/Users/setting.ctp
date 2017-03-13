
	  <section class="create-dish-sec">
	   
	      <section class="create-dish-title clearfix">
		    <main class="container wdth100">
				
			    <h3 class="fleft">Settings  </h3> 
			   
			</main>
		  </section>
		  <section class="community-sec clearfix">
		  <main class="container">
		    
		    <ul class="create-dishes-list c-d-l-custom alignleft">
				<?php echo $this->Html->link('<li><span class="flect-comm">Edit Profile</span><span class="fright-icon comm-arw-icon"></span></li>',array('controller'=>'users','action'=>'edit_profile'),array('escape'=>false)); ?>
				<?php echo $this->Html->link('<li><span class="flect-comm">Edit Kitchen</span><span class="fright-icon comm-arw-icon"></span></li>',array('controller'=>'kitchens','action'=>'edit'),array('escape'=>false)); ?>
				<?php echo $this->Html->link('<li><span class="flect-comm">Preferences</span><span class="fright-icon comm-arw-icon"></span></li>',array('controller'=>'kitchens','action'=>'prefrences'),array('escape'=>false)); ?>
				<?php echo $this->Html->link('<li><span class="flect-comm">Saved Payment Methods</span><span class="fright-icon comm-arw-icon"></span></li>',array('controller'=>'users','action'=>'payment_method'),array('escape'=>false)); ?>
				<?php echo $this->Html->link('<li><span class="flect-comm">Password</span><span class="fright-icon comm-arw-icon"></span></li>',array('controller'=>'users','action'=>'change_password'),array('escape'=>false)); ?>
			</ul>
			<div class="clearfix"></div>

       	  </main>
          </section>
		  
		   			
		  
		
	  </section>
	
