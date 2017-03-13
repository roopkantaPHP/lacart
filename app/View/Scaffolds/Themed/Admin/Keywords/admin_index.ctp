<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Keyword</h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Add Keyword</span>', array('controller' => 'keywords', 'action' => 'add','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
		<?php echo $this->Form->create(); ?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="25%" align="left" nowrap="nowrap">Keyword</th>
				<th width="25%" align="left" nowrap="nowrap">Active</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>
				<th width="5%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>			
			</tr>
			<?php 
			if(count($keywords))
			{			
				$i=1;
				$bgClass="";
				foreach($keywords as $keyword)
				{
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i; ?></td>
					<td align="left"><span class="blue"><?php echo $keyword['Keyword']['name']; ?><span class="blue"></td>
					<td align="left"><?php echo ($keyword['Keyword']['is_active']) ? 'Active' : 'Inactive'; ?></td>
					<td align="center"><?php echo $this->Html->link('<img src="'.SITE_URL.'/admin_images/edit_icon.gif" title="Edit Keyword" border="0"/>', array('controller'=>'keywords', 'action'=>'edit', $keyword['Keyword']['id']), array('escape' => false)) ?></td>
					<td align="center"><input type="checkbox" name="ids[]" value="<?php print $keyword['Keyword']['id']; ?>"></td>
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
					<div class="floatright">
						<div class="floatleft">
						<span class="redtext top5" id="err_status" style="float:left;"></span> &nbsp;&nbsp;
						<select name='action' id='action' class='select-small' style='width:150px;'>
							<option value=''>Select</option>							
							<option value='delete'>Delete</option>
							<option value='active'>Activate</option>
							<option value='unactive'>Deactivate</option>
						</select>
						</div>
						<input type="hidden" name="listingAction" value="" />
						<div class="floatleft mleft10"><input type="button" class="submit_btn" value="" name="Search" onclick="return submit_form(this.form,'')"></div>
                   </div>
				</td>
			</tr>
			
		</table>
		<?php echo $this->Form->end(); ?>
	
</div>
