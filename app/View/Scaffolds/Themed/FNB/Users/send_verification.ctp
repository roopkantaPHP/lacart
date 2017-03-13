<section class="create-dish-sec">
    <section class="createdish-mid-sec clearfix">
	    <?php  echo $this->Form->create('User'); ?>
			<main>
				<div style="height:45px;">
					<?php echo $this->Session->flash(); ?>
				</div>

				<span class="user-image">
					<span class="user-name"><?php echo $this->request->data['User']['name']; ?></span>
					<?php echo $this->Form->hidden('User.name'); ?>
					<?php echo $this->Form->hidden('User.send_code',array('value'=>0)); ?>
					<span class="user-address"><?php echo "Verify your contact number, so that you can able to place an order."; ?></span>
				</span>
				
				<li class="textbox marginleftAuto">
					<?php 
					echo $this->Form->input('User.country_code',array('class'=>'disc-field numeric', 'style'=>'width:10%; margin-bottom:20px;','value'=>'+1','required'=>true,'label'=>false,'div'=>false,'readonly'=>'readonly'));
					echo $this->Form->input('User.phone',array('class'=>'disc-field numeric', 'style'=>'width:45%; margin-left:5%; margin-bottom:20px;','placeholder'=>'Contact number','required'=>true,'label'=>false,'div'=>false));
					echo $this->Form->input('User.code',array('class'=>'disc-field numeric', 'style'=>'width:35%; margin-left:5%; margin-bottom:20px;','placeholder'=>'Verification Code','label'=>false,'div'=>false,'autocomplete'=>'off'));

					echo $this->Form->textarea('User.address',array('class'=>'disc-field', 'style'=>'height:70px; width:100%;margin-bottom:20px;','required'=>true,'placeholder'=>'Address','label'=>false,'div'=>false));

					echo $this->Form->input('User.state_id',array('class'=>'community-select', 'style'=>'width:30%; margin-bottom:20px;','label'=>false,'div'=>false,'empty'=>'Select State'));
					echo $this->Form->input('User.city_id',array('class'=>'community-select', 'style'=>'width:30%; margin-left:5%; margin-bottom:20px;','empty'=>'Select City','label'=>false,'div'=>false));
					echo $this->Form->input('User.zipcode',array('class'=>'disc-field', 'style'=>'width:30%; margin-left:5%; margin-bottom:20px;','placeholder'=>'Zipcode','label'=>false,'div'=>false));
					?>
				</li>
				
				<input type="button" value="Send Verification Code" style="width:50%; float:left;" id="sendCodeAgain" class="place-btn">

				<input type="submit" value="Verify" style="width:20%; margin-left:30%;" class="place-btn">
			</main>
		<?php echo $this->Form->end(); ?>
	</section>		  
</section>
<script>
$(document).ready(function(){
	$('#sendCodeAgain').click(function(){
		$('#UserSendCode').val(1);
		$('#UserSendVerificationForm').submit();
	});

	<?php if(isset($closeFancy) && $closeFancy==1)
	{ ?>
		parent.location.reload(true); 
	<?php } ?>
	
	<?php if(isset($this->request->data['User']['state_id']) && !empty($this->request->data['User']['state_id'])){ ?>
			setCityOption($("#UserStateId").val());
	<?php } ?>


    $("#UserStateId").change(function(){
		var stateId = $(this).val();
		if(stateId != ''){
			setCityOption(stateId);
		}
	});
});

function setCityOption(id){
		$.ajax({
				'url':'<?php echo $this->Html->url(array('controller'=>'users','action'=>'getCityOptions')); ?>/'+id,
				'success': function(output) {
					$('#UserCityId').html(output);
					<?php if(isset($this->request->data['User']['city_id']) && !empty($this->request->data['User']['city_id'])){  ?>
						$("#UserCityId").val('<?php echo $this->request->data['User']['city_id']; ?>');
					<?php 	} ?>
				}
			});
}
</script>

