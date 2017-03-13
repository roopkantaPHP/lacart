  <section class="create-dish-sec">
	  <section class="create-dish-title clearfix">
	    <main class="container wdth100">
		    <h3 class="fleft"> REQUESTS </h3> 
		</main>
	  </section>
	  <section class="order-history clearfix">
	      <ul class="order-links">
	      	<li><a class="active-link" data="my_req" href="#">My Requests </a></li>
		    <?php
		    if(isset($userDetails['Kitchen']['id']) && !empty($userDetails['Kitchen']['id']))
		    { ?>
		    <li><a href="#" data="your_req">Answer Requests </a></li>
		    <?php } ?>
		  </ul>
	  </section>
	  
	  <section class="Requests clearfix my_req">
	    <div class="Rinr-box">  
		  	<?php echo $this->Session->flash(); ?>
		  	<span class="request-info">
			  	<?php 
			  		$checkCount = (count($req)>1)?'es':'';
			  		echo "You have requested for ".count($req)." dish".$checkCount;
			  		echo $this->Html->link('+ New Request',array('controller'=>'requestdishes','action'=>'newrequest'),array('class'=>'new-requst'));
			  	?> 
		  	</span>
		  	<ul class="reqst-block clearfix">
		  	<?php if(isset($req) && !empty($req))
		     	{
		     		foreach ($req as $reqKey => $reqData)
		     		{ 
		     		?>
					<li>
						<div class="reqst-blk-bk">
							<div class="reqst-blk-hdr">
								<h3><?php echo $reqData['Request']['dish_name']; ?> <a href="#" class="dlt-icn delete_request" rel="<?php echo $reqData['Request']['id'];?>">&nbsp;</a></h3>
								<div class="mi-pnl clearfix"> 
									<div class="left"><?php echo $reqData['Cuisine']['name']; ?></div>
									<div class="right"><?php echo $this->Common->getTimeAgo($reqData['Request']['timestamp']); ?> ago</div>
								</div>
								<p><?php echo $reqData['Request']['message']; ?></p>
							</div>
							<div class="btm-block">
								<div class="bb-hdr clearfix">
									<div class="left"><?php echo count($reqData['RequestAnswer'])." answers"; ?></div>
									<?php if(count($reqData['RequestAnswer']) > 0)
									{ ?>
									<a href="javascript:void(0);" class="right more-less"></a>
									<?php } ?>
								</div>
								<div class="con-tabs-otr" style="display:none;">
									<?php
									if(!empty($reqData['RequestAnswer']))
									{
										foreach ($reqData['RequestAnswer'] as $reqAnsKey => $reqAnsData)
										{ 
											if(isset($reqAnsData['Dish']) && !empty($reqAnsData['Dish']))
											{ 
											?>
												<div class="con-tabs">
													<div class="ctb-inr">
														<figure class="image">
															<?php 
							                                $imgName = $this->Common->getDishImage($reqAnsData['Dish']);
															
															echo $this->Image->resize($imgName, 150, 150, true); 
							                                 ?>
														</figure>
														<h4><?php echo $reqAnsData['Dish']['name']; ?></h4>
														<div class="odr-tm">
														<?php echo $reqAnsData['Dish']['serve_start_time'].'<span>Last order '.$reqAnsData['Dish']['serve_end_time'].'</span>'; ?>
														</div>
														<div class="odr-prc">
															<?php 
																if($reqAnsData['Dish']['p_custom'] && $reqAnsData['Dish']['is_custom_price_active']==1)
																{
				                                                    echo '$ '.$reqAnsData['Dish']['p_custom_price'];
																}
				                                                else if ($reqAnsData['Dish']['p_big']) 
				                                                {
				                                                    echo '$ '.$reqAnsData['Dish']['p_big_price'];
				                                                }
				                                                else if ($reqAnsData['Dish']['p_small']) 
				                                                {
				                                                    echo '$ '.$reqAnsData['Dish']['p_small_price'];
				                                                }
															?>
														</div>
													<?php echo $this->Html->link($reqAnsData['Dish']['Kitchen']['name'],array('controller'=>'kitchens','action'=>'index',$reqAnsData['Dish']['Kitchen']['id']),array('class'=>'rs-nm')); ?>
													</div>
												</div>
											<?php
											}
										}
									}
									?>
								</div>
							</div>
						</div>
					</li>
		     		<?php
		     		}
		     	}
		    ?>
		   	</ul>
		</div>   
	  </section>
	  <section class="Requests clearfix hide your_req">
	    <div class="Rinr-box">  
		  	<ul class="reqst-block clearfix">
		     <?php if(isset($waitingForAns) && !empty($waitingForAns))
		     	{
		     		foreach ($waitingForAns as $toBeAnsKey => $toBeAnsData)
		     		{
		     		?>
					<li>
						<div class="reqst-blk-bk">
							<div class="reqst-blk-hdr">
								<h3><?php echo $toBeAnsData['Request']['dish_name']; ?></h3>
								<div class="mi-pnl clearfix"> 
									<div class="left"><?php echo $toBeAnsData['Cuisine']['name']; ?></div>
									<div class="right"><?php echo $this->Common->getTimeAgo($toBeAnsData['Request']['timestamp']); ?> ago</div>
								</div>
								<p><?php echo $toBeAnsData['Request']['message']; ?></p>
								<div class="usr-info-box">
								  <figure class="u-img">
								  	<?php $imgName = $this->common->getProfileImage($toBeAnsData);
								  		  echo $this->Image->resize($imgName,56,56,true);
								  	 ?>
								  </figure>
								  <h5><?php echo $toBeAnsData['User']['name']; ?></h5>
								  <?php echo $this->Html->link('Message '.$toBeAnsData['User']['name'],array('controller'=>'conversations','action'=>'new_message',$toBeAnsData['User']['id']),array('class'=>'rs-nm iframe')); ?>
								  <a href="#" class="ans-lnk right"><?php echo count($toBeAnsData['RequestAnswer'])." answers"; ?></a>
								</div>
							</div>
							<?php echo $this->Html->link('Answer Request',array('controller'=>'requestdishes','action'=>'answer_request',$toBeAnsData['Request']['id']),array('class'=>'ans-req-btn')); ?>
						</div>
					</li>
		     		<?php
		     		}
		     	} ?>
		   	</ul>
		</div>   
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
	});

	$('.more-less').click(function(){
			$(this).parent().next().slideToggle(300);
			$(this).toggleClass('less');
		  });

	$('.delete_request').click(function(){
				var id = $(this).attr('rel');
				if (confirm('Are you sure you want to delete this request?')) {
					$.ajax({
						'url':'<?php echo $this->Html->url(array('controller'=>'requestdishes','action'=>'deleterequest')); ?>',
						'type':'post',
						'data':{'id':id},
						'success':function(data){
							if(data==1){
								$('.delete_request[rel="'+id+'"]').parents('li').remove();
							}
							else{
								alert('Sorry, Request deletion failed please try again.');
							}
						}
					});
				}
		});

	$('ul.order-links li a').click(function(){ 
	    $('ul.order-links li a').removeClass('active-link');
		$(this).addClass('active-link');
		var getatr = $(this).attr('data');

		if(!$('.'+getatr).is(':visible'))
		{ 
			if(getatr=='my_req')
			{
				$('section.my_req').show();
				$('section.your_req').hide();
			}
			else
			{
	 			$('section.my_req').hide();
				$('section.your_req').show();
			}
		}
	  });
});	
</script>