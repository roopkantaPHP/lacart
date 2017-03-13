<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Videos</h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Add Videos</span>', array('controller' => 'Videos', 'action' => 'add','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="10%" align="left" nowrap="nowrap">Title</th>
				<th width="15%" align="left" nowrap="nowrap">Url</th>
				<th width="5%" align="left" nowrap="nowrap">Display</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>
			</tr>
			<?php 
			if(count($Videos))
			{			
				$i=1;
				$bgClass="";
				foreach($Videos as $row)
				{
					$row = $row['Video'];
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i;?></td>
					<td align="left"><?php echo $row['title'];?></td>
					<td align="left"><?php echo $row['url'];?></td>
					<td align="left"><?php echo $row['is_display']==1?"Yes":"No";?></td>					
					<td align="center">
						<?php 
							echo $this->Form->postLink(__('Delete'), array(
								'controller' => 'Videos', 'admin' => true,
								'action' => 'delete', $row['id']), null,
								__('Are you sure you want to delete # %s?', $row['id'])
							);
						?>
						<?php echo $this->Html->link($this->Html->image('/admin_images/edit_icon.gif'), array('controller'=>'Videos', 'action'=>'edit', 'admin' => true, $row['id']), array('escape' => false)) ?>
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
