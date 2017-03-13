<?php echo $this->Html->script('common'); ?>
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
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('SiteSetting.page_slug','Page Slug') ?></th>		
				<th width="15%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('SiteSetting.slug','Slug') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('SiteSetting.label','Label') ?></th>
				<th width="10%" align="left" nowrap="nowrap">Action</th>
			</tr>
			<?php
			if(count($results)>0)
			{
				$i=$this->Paginator->counter(array('format' => '%start%'));
				$bgClass="";
				foreach($results as $row)
				{					
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";
			?>
				<tr class="<?=$bgClass?>">
					<td align="left"><?php echo $i;?>.</td>
					<td align="left"><?php echo $row['SiteSetting']['page_slug'];?></td>					
					<td align="left"><?php echo $row['SiteSetting']['slug'];?></td>
					<td align="left"><?php echo $row['SiteSetting']['label'];?></td>					
					<td align="left"><?php echo $this->Html->link('Edit', array('controller' => 'users', 'action' => 'admin_edit_label', $row['SiteSetting']['id']))?></td>
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
