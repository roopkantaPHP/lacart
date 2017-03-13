<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft discu-title">Discussions </h3>
			<div class="clearfix"></div>
			<div class="comunity-bradcrum">
				<?php echo $this->Html->link('All Communities',
											array('controller'=>'discussions','action'=>'explore_com'),
											array('class'=>'red-clr'));
				if(isset($communityDetails['Community']) && !empty($communityDetails['Community'])){
					echo " . ";
					echo $this->Html->link($communityDetails['Community']['title'],
											array('controller'=>'discussions','action'=>'community',$communityDetails['Community']['id']),
											array('class'=>'red-clr'));
				}
				?>		
			</div>
		</main>
	</section>
	<section class="community-sec clearfix">
		<main class="container">
			<?php 
			 if(isset($communityDetails['Community']) && !empty($communityDetails['Community'])){ ?>
				<h4 class="comm-title"><?php echo $communityDetails['Community']['title']; ?><br/>
					<span><?php echo $communityDetails['Community']['discussion_count']." Discussions"; ?> </span>
				</h4>
				<ul class="create-dishes-list c-d-l-custom alignleft">
				<?php 
					if(isset($communityDetails['Discussion']) && !empty($communityDetails['Discussion'])){
						foreach ($communityDetails['Discussion'] as $key => $value) { 
							$comment_count = 0;
							if(isset($value['Comment']) && !empty($value['Comment'])){
								$comment_count = count($value['Comment']);	
							}
							echo $this->Html->link('<li><span class="flect-comm">'.$value['title'].'</span><span class="fright-comm comm-arw-icon">'.$comment_count.' comments</span></li>',array('controller'=>'discussions','action'=>'discussion',$value['id']),array('escape'=>false));
							}
					}
				 } ?>
			</ul>
			<div class="clearfix"></div>
			<section class="cant-find">
				<span>can't find what you're looking for?</span>
				<div class="discusn-btn">
					<?php echo $this->Html->link('<input type="button" value="Start a discussion">',array('controller'=>'discussions','action'=>'add'),array('escape'=>false)); ?>
				</div>
			</section>
		</main>
	</section>
</section>

