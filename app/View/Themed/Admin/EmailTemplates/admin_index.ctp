<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Email Templates</h1></div>
	<div class="floatright"><?php // echo $this->Html->link('<span>Add Email Template</span>', array('controller' => 'emailTemplates', 'action' => 'add','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="25%" align="left" nowrap="nowrap">Subject</th>
				<th width="25%" align="left" nowrap="nowrap">Template Slug</th>
				<th width="15%" align="left" nowrap="nowrap">From Email</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>
			</tr>
			<?php 
			if(count($mails))
			{			
				$i=1;
				$bgClass="";
				foreach($mails as $row)
				{
					$row = $row['EmailTemplate'];
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i;?></td>
					<td align="left"><?php echo $row['subject'];?></td>
					<td align="left"><?php echo $row['slug'];?></td>
					<td align="left"><?php echo $row['from_email'];?></td>					
					<td align="center">
						<?php 
							echo $this->Form->postLink(__('Delete'), array(
								'controller' => 'emailTemplates', 'admin' => true,
								'action' => 'delete', $row['id']), null,
								__('Are you sure you want to delete # %s?', $row['subject'])
							);
						?>
						<?php echo $this->Html->link($this->Html->image('/admin_images/edit_icon.gif'), array('controller'=>'emailTemplates', 'action'=>'edit', 'admin' => true, $row['id']), array('escape' => false)) ?>
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
