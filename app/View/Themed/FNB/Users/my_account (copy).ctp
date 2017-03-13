	 <div class="profile-section">
	     <div class="user-profile-box clearfix">
		   <figure>
			<?php 
			$imgName = 'img1.png';
			if(isset($userDetails['User']['image']) && !empty($userDetails['User']['image'])){
						if(FILE_EXISTS(PROFILE_IMAGE_URL.$userDetails['User']['image'])){
							$imgName = PROFILE_IMAGE_FOLDER.$userDetails['User']['image'];
						}
					}
					echo $this->Image->resize($imgName, 150, 150, true); 
					?>
		   </figure>
		   <div class="figure-content">
			 <h3><?php echo $userDetails['User']['name'].' s Kitchen'; ?></h3>
			 <h4><?php echo $userDetails['User']['address']; ?></h4>
			 <?php echo $this->Html->image('rating.png'); ?>
			 <ul class="search-icons clearfix">
				<li><?php echo $this->Html->link($this->Html->image('icon-1.png'),'javascript:void(0);',array('escape'=>false)); ?></li>
				<li><?php echo $this->Html->link($this->Html->image('icon-2.png'),'javascript:void(0);',array('escape'=>false)); ?></li>
			 </ul>
		   </div>
	     </div>
	     <?php echo $this->Html->link('','#',array('class'=>'send-message')); ?>
	 </div>
	 
	 <div class="dishes-section">
	   <div class="container">
	     <div class="tabs-bar clearfix">
		   <ul>
			 <li><?php echo $this->Html->link('Dishes','javascript:void(0);',array('class'=>'active','data'=>'dishes-content')); ?></li>  
		     <li><?php echo $this->Html->link('About','javascript:void(0);',array('data'=>'about-tab')); ?></li>  
		     <li><?php echo $this->Html->link('Photos','javascript:void(0);',array('data'=>'photos-tab')); ?></li>  
		     <li><?php echo $this->Html->link('Reviews','javascript:void(0);',array('data'=>'review-tab')); ?></li>  
		   </ul>
		 </div>
	   </div>
	   <div class="tabs-content-area">
	   <!-- dishes -->
       <div class="dishes-content con-tabs clearfix">
		  <div class="container">
		    <div class="left-sec">
			  <ul class="dishes-list clearfix">
			    <li>
				  <div class="image-con">
					<div class="checkbox-otr"><a href="#" class="checkbox checked">&nbsp;</a></div>
				    <div class="image"><?php echo $this->Html->image('dish.png'); ?></div>
					<div class="image-data">
					  <h3>Israeli Salad</h3>
					  <div class="time"><b>2 pm</b> Last order 1 pm</div>
					  <div class="grade">A</div>
					</div>
				  </div>
				  <div class="dish-portion">
				    <h6>PORTION</h6>
					<div class="clearfix pecies">
					  <div class="smlpc">Small<br/>3pcs</div>
					  <div class="bigpc">Big<br/> 7 oz</div>
					</div>
					<h6>QUANTITY</h6>
					<div class="clearfix pecies quantity">
					  <a href="#" class="bigpc left-ra">&nbsp;</a>
					  <div>0</div>
					  <a href="#" class="bigpc">&nbsp;</a>
					  <div class="dish-price">$ 9</div>
					</div>
				  </div>
				  <div class="clr"></div>
				  <div class="accordian">
				     <div class="acdian-hdg">More</div>
					 <div class="acdian-data" style="display:none;">
					   <h6>CUISINE</h6>
					   <p>International</p>
					   <h6>CUISINE</h6>
					   <p>International</p>
					 </div>
				  </div>
				</li>
				<li class="grey-style">
				  <div class="image-con">
					<div class="checkbox-otr"><a href="#" class="checkbox">&nbsp;</a></div>
				    <div class="image"><img src="images/dish.png" alt="" /></div>
					<div class="image-data">
					  <h3>Israeli Salad</h3>
					  <div class="time"><b>2 pm</b> Last order 1 pm</div>
					</div>
				  </div>
				  <div class="dish-portion">
				    <h6>PORTION</h6>
					<div class="clearfix pecies">
					  <div class="smlpc">Small<br/>3pcs</div>
					  <div class="bigpc">Big<br/> 7 oz</div>
					</div>
					<h6>QUANTITY</h6>
					<div class="clearfix pecies quantity">
					  <a href="#" class="bigpc left-ra">-</a>
					  <div>0</div>
					  <a href="#" class="bigpc">+</a>
					  <div class="dish-price">$ 9</div>
					</div>
				  </div>
				  <div class="clr"></div>
				  <div class="accordian">
				     <div class="acdian-hdg">More</div>
					 <div class="acdian-data" style="display:none;">
					   <h6>CUISINE</h6>
					   <p>International</p>
					 </div>
				  </div>
				</li>
			  </ul>
			</div>
			<div class="right-sec">
			  <div class="order-tbl">
			    <h4>Your Order</h4>
				<table>
				  <tr>
				    <td>
					 <div>Israeli salad - 1</div>
					 <p>Big - 3pcs</p>
					</td>
					<td>
					 <div class="alignright">$9</div>
					</td>
				  </tr>
				  <tr>
				    <td>
					 <div>Israeli salad - 1</div>
					 <p>Big - 250ml</p>
					</td>
					<td>
					 <div class="alignright">$9</div>
					</td>
				  </tr>
				  <tr class="total">
				    <td>
					 <div><b>Total</b></div>
					</td>
					<td>
					 <div class="alignright"><b>$18</b></div>
					</td>
				  </tr>
				</table>
				<div class=""><input type="submit" class="place-btn" value="Place Order" /></div>
			  </div>
			</div>
		  </div>
	   </div>
	   <!-- dishes -->
	   
	   
	   <!-- about -->
	   <div class="about-tab con-tabs" style="display:none;">
	     <div class="container">
		   <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
		   <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
		 </div>
	   </div>
	   <!-- about -->
	   
	   
	   <!-- photos -->
	    <div class="photos-tab con-tabs" style="display:none;">
	     <div class="container">
		   <ul class="photos-list clearfix">
		     <li><a class="fancybox-button" rel="fancybox-button" href="images/img11.png"><img src="images/img11.png" alt="" /></a></li>
			 <li><a class="fancybox-button" rel="fancybox-button" href="images/img12.jpg"><img src="images/img12.jpg" alt="" /></a></li>
			 <li><a class="fancybox-button" rel="fancybox-button" href="images/img13.jpg"><img src="images/img13.jpg" alt="" /></a></li>
			 <li><a class="fancybox-button" rel="fancybox-button" href="images/img14.jpg"><img src="images/img14.jpg" alt="" /></a></li>
			 <li><a class="fancybox-button" rel="fancybox-button" href="images/img11.png"><img src="images/img11.png" alt="" /></a></li>
			 <li><a class="fancybox-button" rel="fancybox-button" href="images/img12.jpg"><img src="images/img12.jpg" alt="" /></a></li>
			 <li><a class="fancybox-button" rel="fancybox-button" href="images/img13.jpg"><img src="images/img13.jpg" alt="" /></a></li>
			 <li><a class="fancybox-button" rel="fancybox-button" href="images/img14.jpg"><img src="images/img14.jpg" alt="" /></a></li>
		   </ul>
		 </div>
	   </div>
	   <!-- photos -->
	   
	   
	   <!-- review -->
	   <div class="review-tab con-tabs" style="display:none;">
	     <div class="container">
		   <ul class="reviw-list">
		   <li>
		    <div class="image"><div class="inr-img"><img src="images/user.png" alt="" /></div></div>
			<h5>Adam Green<span>1 week ago</span></h5>
			<p>The food itself is alright, I found the ingredients to be not so fresh. 
Dining space is beautiful that comes with an adorable dog. Will be ordering from here again. </p>
		   </li>
		   <li>
		    <div class="image"><div class="inr-img"><img src="images/user.png" alt="" /></div></div>
			<h5>Adam Green<span>1 week ago</span></h5>
			<p>The food itself is alright, I found the ingredients to be not so fresh. 
Dining space is beautiful that comes with an adorable dog. Will be ordering from here again. </p>
		   </li>
           <li>
		    <div class="image"><div class="inr-img"><img src="images/user.png" alt="" /></div></div>
			<h5>Adam Green<span>1 week ago</span></h5>
			<p>The food itself is alright, I found the ingredients to be not so fresh. 
Dining space is beautiful that comes with an adorable dog. Will be ordering from here again. </p>
		   </li>
           <li>
		    <div class="image"><div class="inr-img"><img src="images/user.png" alt="" /></div></div>
			<h5>Adam Green<span>1 week ago</span></h5>
			<p>The food itself is alright, I found the ingredients to be not so fresh. 
Dining space is beautiful that comes with an adorable dog. Will be ordering from here again. </p>
		   </li>
           <li>
		    <div class="image"><div class="inr-img"><img src="images/user.png" alt="" /></div></div>
			<h5>Adam Green<span>1 week ago</span></h5>
			<p>The food itself is alright, I found the ingredients to be not so fresh. 
Dining space is beautiful that comes with an adorable dog. Will be ordering from here again. </p>
		   </li>		   
		   </ul>
		 
		 </div>
	   </div>
	   <!-- review -->
	   </div>
	   
	 </div>
 
  <script>
	jQuery(document).ready(function($) {
		jQuery('#show-filters').click(function(){
			jQuery('.filter-box').slideToggle(500);
		});
	
		 $('.tabs-bar ul li a').click(function(){
		    $('.tabs-bar ul li a').removeClass('active');
			$(this).addClass('active');
			var getatr = $(this).attr('data');
			$('.con-tabs').fadeOut(300);
			$('.'+getatr).fadeIn(300);
		  });
		  
		$(".fancybox-button").fancybox({
			margin: [0, 0, 80, 0],
			prevEffect		: 'none',
			nextEffect		: 'none',
			closeBtn		: false,
			helpers		: {
				title	: { type : 'inside' },
				buttons	: {}
			}
	   });
 
	});
	</script>
   <!-- 
	  <div class="guide-msg">
	    <div class="container">
		  <h3>Tuckle guide</h3>
		  <a href="#" class="cancel">&nbsp;</a>
		</div>
	  </div>
	  
	  <div class="slider">
	    <ul class="bxslider">
		  <li>&nbsp;</li>
		  <li>&nbsp;</li>
		  <li>&nbsp;</li>
		  <li>&nbsp;</li>
		</ul>
	  </div>
	  -->
