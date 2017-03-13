<section class="create-dish-sec">
<?php if(isset($errors) && !empty($errors)){
				$errr = '';
				foreach ($errors as $key => $value) {
					$errr .= '<div>'.$value[0].'</div>';
				}
				if(!empty($errr)){ ?>
 					<div class="error_popup">
 						<div class="error_title"><strong>Please make proper entries.</strong></div>
 						<div onclick="close_error()" id="close_error">
 							<?php echo $this->Html->image('cross_grey_small.png',array('height'=>10)); ?>
 						</div>
 						<?php echo $errr; ?>
 						<div style="clear:both;"></div>
 					</div>
				<?php }
			} ?>
	<?php 	echo $this->Session->flash(); ?>
	<?php 	echo $this->Form->create('PaymentMethod');
			echo $this->Form->hidden('User.is_paypal_verified'); ?>
	<section class="create-dish-title clearfix">
		<div class="container wdth100">
			<h3 class="fleft">Saved Payment Methods </h3> 
		</div>
	</section>
	<div class="dishes-section">
	    <div class="container">
			<div class="tabs-bar clearfix">
				<ul>
				 <li><?php echo $this->Html->link('Cards','javascript:void(0);',array('class'=>'active','data'=>'cards-tab')); ?></li>  
				 <li><?php echo $this->Html->link('Paypal','javascript:void(0);',array('data'=>'paypal-tab')); ?></li>  
				 <li><?php echo $this->Html->link('Receipt','javascript:void(0);',array('data'=>'receipt-tab')); ?></li>  
				</ul>
			</div>
		</div>
		<div class="tabs-content-area">
			<!-- Cards -->
			<div class="cards-tab con-tabs clearfix">
					<section class="community-sec clearfix">
				  		<div class="discussion-container">
							<ul class="cart-info">
								<?php if(isset($paymentDetails) && !empty($paymentDetails)){
									foreach($paymentDetails as $pid => $pValue){
								?>
								<li>
									<figure class="card-img"><?php echo $this->Html->image($pValue['PaymentMethod']['type'].'.png'); ?></figure>
									<span class="card-number"><?php echo $pValue['PaymentMethod']['card_no']; ?></span>
									<span class="delete-card" rel="<?php echo $pValue['PaymentMethod']['id']; ?>">&nbsp;</span>		
								</li>			 
								<?php
									}
								?>
								<li>
									<div style="text-align:center;">
										<?php echo $this->Html->link('<input type="button" value="Add New Card" class="view-kithech">',array("controller"=>"users","action"=>"addcard"),array('escape'=>false,'class'=>'addNewCard')); ?>
									</div>
								</li>
								<?php
								 }else{
								 ?>
								 <div style="text-align:center;">
								 	<?php echo "You don"."'"."t have any saved cards."; ?>
								 	<div class="clearfix" style="height:40px;"></div>
								 	<?php echo $this->Html->link('<input type="button" value="Add New Card" class="view-kithech">',array("controller"=>"users","action"=>"addcard"),array('escape'=>false,'class'=>'addNewCard')); ?>
								 </div>	
								<?php } ?>
							</ul>			
							<div class="clearfix"></div>
						</div>
					</section>		
			</div>

			<!-- Paypal -->
			<div class="paypal-tab con-tabs" style="display:none;">
	   				<section class="community-sec clearfix">
				  		<div class="discussion-container">
							<ul>
								<li>
									<label> Id</label>
									<?php echo $this->Form->text('User.paypal_id', array('label'=>false,'div'=>false,'class'=>'disc-field','autocomplete'=>"off")); ?>
								</li>
								<li>
									<label> Paypal Name</label>
									<?php echo $this->Form->text('User.paypal_name', array('label'=>false,'div'=>false,'class'=>'disc-field','autocomplete'=>"off")); ?>
								</li>
								<br/>
								<li>
									<label><?php echo "Paypal Account Status:"; ?></label>
									<?php echo $this->Form->input('User.paypal_dummy_input', array('label'=>false,'div'=>false,'class'=>'disc-field','readonly'=>'readonly','value'=>($this->request->data['User']['is_paypal_verified']==1)?"Verified":"Not Verified")); ?>
								</li>
								<li>
									<?php echo $this->Form->submit('Save',array('class'=>'btn-next')); ?>
								</li>
							</ul>
						</div>
					</section>
			</div>

			<!-- Reciept -->
			<div class="receipt-tab con-tabs" style="display:none;">
					<section class="community-sec clearfix">
				  		<div class="discussion-container">
							<ul>
								<li>
									<label> Bank account number</label>
									<?php echo $this->Form->input('User.bank_acc_no', array('label'=>false,'div'=>false,'class'=>'disc-field numeric','autocomplete'=>"off")); ?>
								</li>
								<br/>
								<li>
									<label>Routing number</label>
									<?php echo $this->Form->input('User.bank_routing_no', array('label'=>false,'div'=>false,'class'=>'disc-field','autocomplete'=>"off")); ?>
								</li>
								<br/>
								<li>
									<label>Account holder's name</label>
									<?php echo $this->Form->input('User.bank_acc_holdername', array('label'=>false,'div'=>false,'class'=>'disc-field','autocomplete'=>"off")); ?>
								</li>
								<li>
									<label>Bank Account type</label>
									<?php echo $this->Form->input('User.bank_acc_type', array('class'=>'community-select','empty'=>'Select Account type','label'=>false,'options'=>array(0=>'Checking',1=>'Saving'),'autocomplete'=>"off")); ?>
								</li>
								<li>
									<?php echo $this->Form->submit('Save',array('class'=>'btn-next')); ?>
								</li>
							</ul>			
							<div class="clearfix"></div>
						</div>
					</section>		
			</div>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>
</section>
 <script>
	jQuery(document).ready(function($) {
		 $('.tabs-bar ul li a').click(function(){
		    $('.tabs-bar ul li a').removeClass('active');
			$(this).addClass('active');
			var getatr = $(this).attr('data');
			$('.con-tabs').fadeOut(300);
			$('.'+getatr).fadeIn(300);
		  });

		 $('#show_pass').change(function(){
		 	if($(this).is(':checked')){
		 		$('#UserPaypalPass').attr('type','text');
		 	}
		 	else{
		 		$('#UserPaypalPass').attr('type','password');
		 	}
		 });

		 $('.delete-card').click(function(){
			var id = $(this).attr('rel');
			if (confirm('Are you sure you want to delete this card?')) {
				$.ajax({
					'url':'<?php echo $this->Html->url(array('controller'=>'users','action'=>'deletecard')); ?>',
					'type':'post',
					'data':{'id':id},
					'success':function(data){
						if(data==1){
							$('.delete-card[rel="'+id+'"]').parent('li').remove();
						}
						else{
							alert('Sorry, Card deletion failed please try again.');
						}
					}
				});
			}
		});

		$(".addNewCard").fancybox({
		    type : 'iframe',
		    autoSize : false,
			width: 500,
			height: 550,
        	padding : 0,
			helpers : {
		        overlay : {
		            css : {
		                'background' : 'rgba(58, 42, 45, 0.50)'
		            }
		        }
		    },
		});
	});
	</script>