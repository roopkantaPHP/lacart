<?php 	echo $this->Html->script('common');
		$statusType = array(0 => 'Not verified', 1 => 'Confirmed', 2 => 'Completed');
		$payType = array(0 => 'Balanced', 1 => 'Paypal');
 ?>
<script>
function checkall(objForm)
{
	len = objForm.elements.length;
	for( i=0 ; i<len ; i++) 
	{
		if (objForm.elements[i].type=='checkbox') objForm.elements[i].checked = objForm.check_all.checked;
	}
}
</script>

<div class="row searchbox floatleft mtop30" style="background-color: white; padding: 10px;">
	<?php echo $this->Form->create('', array('id'=>'AS', 'type'=>'GET')); ?>
 	<table width="90%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"><table width="90%" border="0" cellpadding="4" cellspacing="1" >
        <tr>
          <td colspan="4" align="left" class="header_bg">Advance Order Search</td>
        </tr>        
		<tr class="oddRow">
			<td align="right"><strong>Order Placed By</strong>:</td>
			<td align="left">
				<input name="email" type="text" class="input" id="ScreenName" value="<?php echo $email; ?>">
			</td>
			<td align="right"><strong>Order Status</strong>:</td>
			<td align="left">
				<?php echo $this->Form->input('status',array('options'=>$statusType, 'value' => $status, 'div'=>false, 'label'=>false, 'name' => 'status', 'class' => 'input', 'id' => 'status', 'empty' => 'All')); ?>
			</td>       
			<td align="right"><strong>Payment Type</strong>:</td>
			<td align="left">
				<?php echo $this->Form->input('pType',array('options'=>$payType, 'value' => $pType, 'div'=>false, 'label'=>false, 'name' => 'pType', 'class' => 'input', 'id' => 'pType', 'empty' => 'Both')); ?>
			</td>     
		</tr>     
		<tr class="oddRow">
			<td align="right"><strong>Order From</strong>:</td>
			<td align="left"><input name="o_from" type="date" class="input" id="O_FROM" value="<?php echo $o_from; ?>"></td>
			<td align="right"><strong>Order To</strong>:</td>
			<td align="left"><input name="o_to" type="date" class="input" id="O_TO" value="<?php echo $o_to; ?>"></td>         
			<td colspan="4" align="center" class="oddRow">
				<input type="submit" class="submit_btn" value=" " name="Search">
				&nbsp;
				<input name="Submit2" type="button" class="buttons" value="Reset" id="reset">
			</td>
		</tr>
        <tr>
         
        </tr> 
      </table></td>
    </tr>
  	</table>
<?php echo $this->form->end(); ?>
</div>

<div class="row mtop15">
	<?php echo $this->Form->create(); ?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>
				<td colspan="14">
					<div class="pagination floatright">
						<?php echo $this->Paginator->counter(array('format' => 'Showing Records: %start% to %end% of %count%'));  ?>
					</div>
				</td>
			</tr>
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('User.email','Order Placed By') ?></th>
				<!--<th width="5%" align="left" nowrap="nowrap">Dine Type</th>-->
				<th width="5%" align="left" nowrap="nowrap">No. of dishes</th>
				<th width="5%" align="left" nowrap="nowrap">Order Value</th>
				<th width="5%" align="left" nowrap="nowrap">Sales Tax</th>
				<th width="5%" align="left" nowrap="nowrap">Total Amount</th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('Order.created','Created Date') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('Order.payment_type','Payment Type') ?></th>
				<th width="5%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('Order.is_verified','Order Status') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('Order.merchant_paid','Merchant Payment') ?></th>
				<th width="10%" align="center" nowrap="nowrap">Action</th>
				<th width="10%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>
			</tr>

			<?php
			if(count($results)>0)
			{
				$i=$this->Paginator->counter(array('format' => '%start%'));
				$bgClass="";
				foreach($results as $row)
				{	
				    $dishCount = count($row['OrderDish']);	

					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";
		 

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i;?></td>
					<td align="left"><?php echo $row['User']['email']; ?></td>
					<!--<td align="left"><?php echo ($row['Order']['dine_type']==0)?"Dine-in":"Take out"; ?></td>-->
					<td align="left"><?php echo $dishCount;?></td>
					<td align="left"><?php echo "$".$row['Order']['order_value'];?></td>
					<td align="left"><?php echo "$".$row['Order']['sale_tax'];?></td>
					<td align="left"><?php echo "$".$row['Order']['amount'];?></td>
					<td align="left"><?php echo date('M d,Y',strtotime($row['Order']['created']));?></td>	
					<td align="left"><?php echo ($row['Order']['payment_type']==0)?"Balanced Payments":"Paypal"; ?></td>
					<td align="left"><?php 
						if($row['Order']['is_verified']==0)
							echo "Not verified";
						else if($row['Order']['is_verified']==1)
							echo "Confirmed";
						else if($row['Order']['is_verified']==2)
							echo "Completed";  ?></td>		
					<td align="left"><?php echo ($row['Order']['merchant_paid']==0)?"Pending":"Transfered"; ?></td>					
					<td align="center">
						<?php 
							echo $this->Form->postLink(__('List Dishes'), array(
								'controller' => 'Orders', 'admin' => true,
								'action' => 'dishlist', $row['Order']['id']), null);
							echo " | ";
							echo $this->Html->link(__('Details'), array(
								'controller' => 'Orders', 'admin' => true,
								'action' => 'edit', $row['Order']['id']), null);	
						?>
					</td>
					<td align="center">
					<?php
					if($row['Order']['payment_type'] == 0)
					{
						if($row['Order']['merchant_paid']==0)
						{
						?>
							<input type="checkbox" name="ids[]" value="<?php print $row['Order']['id']; ?>">
						<?php
						}
						else
						{
						?>	
							<input type="checkbox" name="gold" disabled="disabled" checked="checked" value="<?php print $row['Order']['id']; ?>">
						<?php
						}		
					}
					?>
					</td>
				</tr> 
			<?php
					$i++;
				}
			?>
			<tr>
                <td colspan="14" align="left" class="bordernone">
					<div class="floatleft mtop7">
						<div class="pagination">
							<?php echo $this->Paginator->prev("<< Previous", null, null, array('class' => 'disabled')); ?>
							<?php echo $this->Paginator->numbers(); ?>
							<?php echo $this->Paginator->next("Next >>", null, null, array('class' => 'disabled')); ?>
							<!-- prints X of Y, where X is current page and Y is number of pages -->
						</div>
					</div>
					<div class="floatright">
						<div class="floatleft">
						<span class="redtext top5" id="err_status" style="float:left;"></span> &nbsp;&nbsp;
						<select name='action' id='action' class='select-small' style='width:150px;'>
							<option value=''>Select</option>
							<option value='pay'>Pay Merchant</option>
							<option value='confirm'>Confirmed</option>
							<option value='complete'>Complete</option>	
						</select>
						</div>
						<input type="hidden" name="listingAction" value="" />
						<div class="floatleft mleft10"><input type="button" class="submit_btn" value="" name="Search" onclick="return submit_form(this.form,'')"></div>
                   </div>
				</td>
			</tr>
			<?php
			}
			else
			{
			?>
				<tr class="redtext">
					<td align="center" colspan="14">No record found</td>
				</tr>
			<?php
			}
			?>
		</table>
	<?php echo $this->Form->end(); ?>
</div>
