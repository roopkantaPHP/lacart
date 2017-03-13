<style type="text/css">
select.community-select, input.disc-field{ margin-bottom: 25px; }	
</style>

<section class="create-dish-sec">
    <section class="createdish-mid-sec clearfix">
	    <?php  echo $this->Form->create('PaymentMethod'); ?>
			<main>
				<?php echo $this->Session->flash(); ?>
				<section class="clearfix fleft">
				  		<div>
							<ul>
								<li>
									<label> Card number</label>
									<?php echo $this->Form->input('card_no', array('label'=>false,'div'=>false,'class'=>'disc-field numeric','autocomplete'=>"off",'required'=>true)); ?>
								</li>
								<li>
									<label> Card holder name</label>
									<?php echo $this->Form->input('card_name', array('label'=>false,'div'=>false,'class'=>'disc-field','autocomplete'=>"off",'required'=>true)); ?>
								</li>
								<br/>
								<li>
									<div class="width50percent fleft">
										<label>Exp Month</label>
										<?php
										for($i=1;$i<=12;$i++){
									        $expMonth[$i] = $i;
									    }
										echo $this->Form->input('exp_month', array('class'=>'community-select','empty'=>'Exp Month','label'=>false,'options'=>$expMonth,'required'=>true)); ?>
									</div>
									<div class="width50percent fright">
										<label>Exp Year</label>
										<?php
										for($i=0;$i<21;$i++){
									        $expYear[date('Y',strtotime('+'.$i.' year'))] = date('Y',strtotime('+'.$i.' year'));
									    }
										echo $this->Form->input('exp_year', array('class'=>'community-select','empty'=>'Exp Year','label'=>false,'options'=>$expYear,'required'=>true)); ?>
									</div>
								</li>
								<br/>
								<li>
									<div class="width50percent fleft">
										<label>Card type</label>
										<?php echo $this->Form->input('type', array('class'=>'community-select','empty'=>'Card type','label'=>false,'options'=>array('visa'=>'Visa', 'mastercard'=>'Mastercard', 'amex'=>'Amex','discover'=>'Discover'),'required'=>true)); ?>
									</div>
									<div class="width50percent fright">
										
									</div>
								</li>
								<br/>
								<li>
									<input type="submit" value="Save" class="place-btn">
								</li>
							</ul>		
							<div class="clearfix"></div>
						</div>
					</section>		
			</main>
		<?php echo $this->Form->end(); ?>
	</section>		  
</section>

<script>
$(document).ready(function(){
	<?php if(isset($closeFancy) && $closeFancy==1)
	{ ?>
		parent.location.reload(true); 
	<?php } ?>
});
</script>

