<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Cms City</h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Add Cms City</span>', array('controller' => 'cms_cities', 'action' => 'add','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="10%" align="left" nowrap="nowrap">Name</th>
				<th width="10%" align="left" nowrap="nowrap">Image</th>
				<th width="30%" align="left" nowrap="nowrap">Description</th>
				<th width="5%" align="left" nowrap="nowrap">Display</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>
			</tr>
			<?php 
			if(count($CmsCitys))
			{			
				$i=1;
				$bgClass="";
				foreach($CmsCitys as $row)
				{
					$row = $row['CmsCity'];
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i;?></td>
					<td align="left"><?php echo $row['name'];?></td>
					<td align="left">
						<?php
						$imgName = 'img1.png';
						 if(isset($row['image']) && !empty($row['image'])){
								if(FILE_EXISTS(CMS_IMAGE_URL.$row['image'])){
									$imgName = CMS_IMAGE_FOLDER.$row['image'];
								}
							}
							echo $this->Image->resize($imgName, 50, 50, true); 
							?>
					</td>
					<td align="left"><?php echo $row['description'];?></td>			
					<td align="left"><?php echo $row['is_display']==1?"Yes":"No";?></td>					
					<td align="center">
						<?php 
							echo $this->Form->postLink(__('Delete'), array(
								'controller' => 'cms_cities', 'admin' => true,
								'action' => 'delete', $row['id']), null,
								__('Are you sure you want to delete # %s?', $row['id'])
							);
						?>
						<?php echo $this->Html->link($this->Html->image('/admin_images/edit_icon.gif'), array('controller'=>'cms_cities', 'action'=>'edit', 'admin' => true, $row['id']), array('escape' => false)) ?>
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
