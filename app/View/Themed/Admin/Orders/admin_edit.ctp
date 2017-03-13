<script type="text/javascript">
$(document).ready(function (){
$('.validate').each(function(){
$(this).validate({

});});});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1>Edit Order</h1></div>
		<div class="floatright"><?php echo $this->Html->link('<span>Back to Orders</span>', array('controller' => 'Orders', 'action' => 'index','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
    </div>
	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('Order', array('class' =>'validate','type'=>'file'));
		 ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">				
				<tr>
					<td valign="middle"><strong class="upper">Order Href:</strong></td>
					<td><b><?php echo ($this->request->data['Order']['payment_type']==0) ? $this->request->data['Order']['order_href']:$this->request->data['Order']['transaction_id']; ?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Delivery Fee:</strong></td>
					<td><b><?php echo "$".$this->request->data['Order']['service_fee']; ?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Order Value:</strong></td>
					<td><b><?php echo "$".$this->request->data['Order']['order_value']; ?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Sale Tax Percent:</strong></td>
					<td><b><?php echo $this->request->data['Order']['tax_percent']."%"; ?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Sale Tax on Order Value:</strong></td>
					<td><b><?php echo "$".$this->request->data['Order']['sale_tax']; ?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Total amount:</strong></td>
					<td><b><?php echo "$".$this->request->data['Order']['amount']; ?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Payment Type:</strong></td>
					<td><b><?php echo ($this->request->data['Order']['payment_type']==0)?"Stripe Payment":"Paypal"; ?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Order Dashboard:</strong></td>
					<td><b>
						<?php
						if($this->request->data['Order']['payment_type']==0)
						{ 
							echo $this->Html->link('Click here','https://dashboard.stripe.com/test/payments/'.$this->request->data['Order']['order_href'],array('target'=>'blank'));
						}
						else
						{
							echo $this->Html->link('Click here','https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_account',array('target'=>'blank'));
						}
						?>
					</b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Buyer's Address / Location:</strong></td>
					<td><b><?php 
							
							if ( isset($this->request->data['OrderAddress']['delivery_location']) &&
												 !empty($this->request->data['OrderAddress']['delivery_location']) ) {
								echo $this->request->data['OrderAddress']['delivery_location'];

							} else if ( isset($this->request->data['OrderAddress']['address']) &&
												 !empty($this->request->data['OrderAddress']['address']) ) {
								echo $this->request->data['OrderAddress']['address'];

							} else {
								echo $this->request->data['User']['address'];
							}
							?></b></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Merchant Paid:</strong></td>
					<td>
						<?php echo $this->Form->checkbox('Order.merchant_paid', array('div'=>false,'label'=>false)); ?>
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
