<?php echo $this->Html->script('common'); ?>
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
<div class="row">
		<div class="floatleft mtop10"><h1><?php echo h("Portion Price Manager"); ?></h1></div>
		<div class="floatright">
			<?php
				echo $this->Html->link(
					"<span>Add Prices</span>", array(
						'controller' => 'dishes', 'action' => 'add_portion',
						'admin' => true, 'plugin' => false
					),array(
						'class' => 'black_btn', 'escape' => false
					)
				); 
			?>
		</div>
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
				
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('Dish.name','Dish Name') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('Kitchen.name','Kitchen Name') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('User.name','User Name') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('p_custom_price','Custom Price') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('p_custom_quantity','Quantity') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('p_custom_desc','Custom Price Description') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('Dish.created','Created') ?></th>
				<th width="10%" align="left" nowrap="nowrap">Actions</th>
			</tr>
			<?php
			if(count($rows)>0)
			{
				$i=$this->Paginator->counter(array('format' => '%start%'));
				$bgClass="";
				foreach($rows as $row)
				{		
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">
					<td align="left"><?php echo $i;?>.</td>
					
					<td align="left"><?php echo $row['Dish']['name'];?></td>
					<td align="left"><?php echo $row['Kitchen']['name'];?></td>
					<td align="left"><?php echo $row['Kitchen']['User']['name'];?></td>
					<td align="left"><?php echo $row['Dish']['p_custom_price'];?></td>
					<td align="left"><?php echo $row['Dish']['p_custom_quantity'];?></td>
					<td align="left"><?php echo $row['Dish']['p_custom_desc'];?></td>			
					<td align="left"><?php echo $row['Dish']['created'];?></td>			
					<td align="left"><?php echo $this->Html->link('Approve', array('controller' => 'dishes', 'action' => 'price_approve', $row['Dish']['id']), array('class'=>'blue','confirm'=>'Are you sure want to approve?'))?></td>
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
