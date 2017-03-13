<h1>Manage Static</h1>
<div class="row mtop30">
	
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>				
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="35%" align="left" nowrap="nowrap">Page Title</th>
				<th width="20%" align="center" nowrap="nowrap">Action</th>				
			</tr>
			<?php 
			if(count($pages))
			{
			//pr($result_arr);
			
				$i=1;
				$bgClass="";
				foreach($pages as $row)
				{
					$row = $row['Page'];
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">			
					<td align="left"><?php echo $i;?></td>
					<td align="left"><?php echo $row['page_title'];?></td>					
					<td align="center"><?php echo $this->Html->link('<img src="'.SITE_URL.'/admin_images/edit_icon.gif" title="Edit Page" border="0"/>', array('controller'=>'pages', 'action'=>'add_static', $row['id']), array('escape' => false)) ?></td>
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
					<td align="center" colspan="4">No record found</td>
				</tr>
			<?php
			}
			?>
		</table>
	
</div>
