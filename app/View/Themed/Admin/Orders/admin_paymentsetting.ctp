<script type="text/javascript">
$(document).ready(function (){
$('.validate').each(function(){
$(this).validate({

});});});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1>Payment Setting</h1></div>
    </div>
	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('PaymentSetting', array('class' =>'validate','type'=>'file'));
		 ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">				
				<tr>
					<td valign="middle"><strong class="upper">Processing Fee type:</strong></td>
					<td>
						<?php echo $this->Form->input('PaymentSetting.fee_type', array('options'=>array(0 => 'Fixed Amount',1 => 'Percentage'),'label'=>false,'div'=>false)); ?>
					</td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Processing Fee:</strong></td>
					<td>
						<?php echo $this->Form->text('PaymentSetting.processing_fee', array('class'=>'numeric')); ?>
					</td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Service Fee:</strong></td>
					<td>
						<?php echo $this->Form->text('PaymentSetting.service_fee', array('class'=>'numeric')); ?>
					</td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Paypal Email:</strong></td>
					<td>
						<?php echo $this->Form->text('PaymentSetting.paypal_email'); ?>
					</td>
				</tr>
				<tr>
                	<td>&nbsp;</td>
					<td>
						<div class="floatleft">
							<input type="submit" class="submit_btn" value="">
						</div>
						<div class="floatleft" id="domain_loader" style="padding-left:5px;"></div>
					</td>
				</tr>
			</table>
		</form>
	</div>
