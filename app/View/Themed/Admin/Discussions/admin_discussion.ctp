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
		<div class="floatleft mtop10"><h1><?php echo h("Discussion Manager"); ?></h1></div>
		<div class="floatright">
			<?php
				echo $this->Html->link(
					"<span>Add Discussion</span>", array(
						'controller' => 'discussions', 'action' => 'add_discussion',
						'admin' => true, 'plugin' => false
					),array(
						'class' => 'black_btn', 'escape' => false
					)
				); 
				
				echo $this->Html->link(
					"<span>Comments</span>", array(
						'controller' => 'discussions', 'action' => 'discussion_comments',
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
				
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('title','Discussion Title') ?></th>
				<th width="25%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('title','Message') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('title','Community Title') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('is_active','Status') ?></th>
				<th width="10%" align="left" nowrap="nowrap">Actions</th>
				<th width="10%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>
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
					<td align="left"><?php echo $row['Discussion']['title'];?></td>
					<td align="left"><?php echo $row['Discussion']['description'];?></td>
					<td align="left"><?php echo $row['Community']['title'];?></td>
					<td align="left"><?php echo ($row['Discussion']['is_active']) ? 'Active' : 'Inactive';?></td>			
					<td align="left"><?php echo $this->Html->link('Edit', array('controller' => 'discussions', 'action' => 'add_discussion', $row['Discussion']['id']))?></td>
					<td align="center"><input type="checkbox" name="ids[]" value="<?php print $row['Discussion']['id']; ?>"></td>
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
							<option value='Delete'>Delete</option>
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
