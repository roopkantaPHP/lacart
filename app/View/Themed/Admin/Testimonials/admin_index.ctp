<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Testimonials</h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Add Testimonials</span>', array('controller' => 'testimonials', 'action' => 'add','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="10%" align="left" nowrap="nowrap">Name</th>
				<th width="10%" align="left" nowrap="nowrap">Image</th>
				<th width="15%" align="left" nowrap="nowrap">Email</th>
				<th width="30%" align="left" nowrap="nowrap">Message</th>
				<th width="5%" align="left" nowrap="nowrap">Display</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>
			</tr>
			<?php 
			if(count($testimonials))
			{			
				$i=1;
				$bgClass="";
				foreach($testimonials as $row)
				{
					$row = $row['Testimonial'];
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
								if(FILE_EXISTS(PROFILE_IMAGE_URL.$row['image'])){
									$imgName = PROFILE_IMAGE_FOLDER.$row['image'];
								}
							}
							echo $this->Image->resize($imgName, 50, 50, true); 
							?>
					</td>
					<td align="left"><?php echo $row['email'];?></td>
					<td align="left"><?php echo $row['message'];?></td>			
					<td align="left"><?php echo $row['is_display']==1?"Yes":"No";?></td>					
					<td align="center">
						<?php 
							echo $this->Form->postLink(__('Delete'), array(
								'controller' => 'testimonials', 'admin' => true,
								'action' => 'delete', $row['id']), null,
								__('Are you sure you want to delete # %s?', $row['id'])
							);
						?>
						<?php echo $this->Html->link($this->Html->image('/admin_images/edit_icon.gif'), array('controller'=>'testimonials', 'action'=>'edit', 'admin' => true, $row['id']), array('escape' => false)) ?>
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
