<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft">Discussions </h3> 
		</main>
	</section>
	<section class="community-sec clearfix">
		<main class="container">
			<h4 class="comm-title">Explore Communities</h4>
			<ul class="create-dishes-list c-d-l-custom alignleft">
				<?php
					if(isset($communities) && !empty($communities)){
						foreach ($communities as $key => $value) { 
 							echo $this->Html->link('<li><span class="flect-comm">'.$value['Community']['title'].'</span><span class="fright-comm comm-arw-icon">'.$value['Community']['discussion_count'].' discussions</span></li>',array('controller'=>'discussions','action'=>'community',$value['Community']['id']),array('escape'=>false)); 
						}
					}
					else
					{
						echo "Community Does not found.";
					}
				?>
			</ul>
		</main>
	</section>		  
</section>

