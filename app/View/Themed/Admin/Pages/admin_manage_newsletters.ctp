<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Newslettter</h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Add Newsletter</span>', array('controller' => 'pages', 'action' => 'add_newsletter','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
		<?php echo $this->Form->create(); ?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="25%" align="left" nowrap="nowrap">Newsletter Title</th>
				<th width="25%" align="left" nowrap="nowrap">Subject</th>
				<th width="15%" align="left" nowrap="nowrap">From Email</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>
				<th width="5%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>			
			</tr>
			<?php 
			if(count($newsletters))
			{			
				$i=1;
				$bgClass="";
				foreach($newsletters as $row)
				{
					$row = $row['Newsletter'];
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i;?></td>
					<td align="left"><?php echo $row['newsletter_title'];?></td>
					<td align="left"><?php echo $row['subject'];?></td>
					<td align="left"><?php echo $row['from_email'];?></td>					
					<td align="center"><?php echo $this->Html->link('<img src="'.SITE_URL.'/admin_images/edit_icon.gif" title="Edit Newsletter" border="0"/>', array('controller'=>'pages', 'action'=>'add_newsletter', $row['id']), array('escape' => false)) ?></td>
					<td align="center"><input type="checkbox" name="ids[]" value="<?php print $row['id']; ?>"></td>
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
							<option value='Delete'>Delete</option>
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
