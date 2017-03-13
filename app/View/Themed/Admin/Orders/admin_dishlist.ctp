<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Ordered Dishes</h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Orders</span>', array('controller' => 'Orders', 'action' => 'index','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="20%" align="left" nowrap="nowrap">Dish name</th>
				<th width="10%" align="left" nowrap="nowrap">Portion</th>
				<th width="10%" align="left" nowrap="nowrap">Quantity</th>
				<th width="10%" align="left" nowrap="nowrap">Kitchen name</th>
				<th width="10%" align="left" nowrap="nowrap">Order Status</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>
			</tr>
			<?php 
			if(count($Orders))
			{			
				$i=1;
				$bgClass="";
				foreach($Orders as $row)
				{ 	
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i;?></td>
					<td align="left"><?php echo $row['OrderDish']['dish_name']; ?></td>
					<td align="left"><?php echo $row['OrderDish']['portion']; ?></td>
					<td align="left"><?php echo $row['OrderDish']['quantity']; ?></td>
					<td align="left"><?php echo $row['Kitchen']['name']; ?></td>
					<td align="left"><?php 
						if($row['Order']['is_verified']==0)
							echo "Not verified";
						else if($row['Order']['is_verified']==1)
							echo "Confirmed";
						else if($row['Order']['is_verified']==2)
							echo "Completed";  ?></td>			
					<td align="center">
						<?php echo $this->Html->link('Dish Details', array('controller'=>'Dishes', 'action'=>'view', 'admin' => true, $row['OrderDish']['dish_id'],$row['OrderDish']['order_id']), array('escape' => false)); ?>
					</td>
				</tr> 
			<?php
					$i++; 
				}
			?>
			<?php
			}
			else
			{
			?>
				<tr class="redtext">
					<td align="center" colspan="6">No records found</td>
				</tr>
			<?php
			}
			?>
			
			<tr>
                <td colspan="10" align="left" class="bordernone">
					<div class="floatleft mtop7">
						
					</div>
					
				</td>
			</tr>
			
		</table>
</div>
