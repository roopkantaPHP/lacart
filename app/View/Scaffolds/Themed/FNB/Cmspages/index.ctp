<section class="create-dish-sec">
	<section class="create-dish-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft"><?php echo $cmspageDetails['Cmspage']['title']; ?></h3> 
		</main>
	</section>
	<section class="community-sec clearfix">
		<main class="discussion-container invite-frnds">
			<h4 class="comm-title"><?php echo $cmspageDetails['Cmspage']['sub_title']; ?></h4>
			<ul>
				<li>
					<?php echo $cmspageDetails['Cmspage']['description']; ?>
				</li>
			</ul>			
			<div class="clearfix"></div>
		</main>
	</section>
	<?php if($cmspageDetails['Cmspage']['id']==4)
	{ ?>
	<section class="community-sec contactus clearfix">
		<main class="discussion-container">
			<h4 class="comm-title">Having some Trouble?</h4>
			Email us at <a href="mailto:info@lacart.com?subject=Feedback for Lacart.com" class="red-clr">info@lacart.com</a>
			<br/>
			
			<?php
			if(isset($userId))
			{
				echo "<br/><br/><br/><br/>or feel free to say hello!<br/>";
				echo $this->Html->link('<input type="button" class="button message-btn" value="Leave us a Message">',array('controller'=>'conversations','action'=>'new_message',1),array('escape'=>false,'class'=>'iframe'));
			}
			?>
			<div class="clearfix"></div>
		</main>
	</section>
	<?php }
	elseif ($cmspageDetails['Cmspage']['id']==5 && isset($cmsCities) && !empty($cmsCities))
	{ ?>
	<section class="cities-sec clearfix">
		<div class="container">
			<ul class="cities-list">
				<?php foreach ($cmsCities as $key => $value) {
				?>
				<li>
					<?php echo $this->Html->image($value['CmsCity']['image']); ?>
					<div class="city-name"><?php echo $value['CmsCity']['name']?></div>
				</li>
				<?php
				} ?>
			</ul>
			<div class="clearfix"></div>
		</div>
	</section>
	<?php 	
	 } ?>
</section>

<script>
	$(document).ready(function($) {
		$("a.iframe").fancybox({
		    width : 700,
			height : 575,
			type : 'iframe',
			autoScale : false,
			padding : 0,
		});
	});
</script>

	