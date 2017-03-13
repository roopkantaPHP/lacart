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
	<div class="floatleft mtop10"><h1><?php echo h("Conversations"); ?></h1></div>
	<div class="floatright">
		<?php
			
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
				<th width="20%" align="left" nowrap="nowrap"><?php echo "Sender Image" ?></th>
				<th width="15%" align="left" nowrap="nowrap"><?php echo  "Sender Name"; ?></th>
				<th width="20%" align="left" nowrap="nowrap"><?php echo  "Last Message"; ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo "Last Sent"; ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo "Message Count"; ?></th>
				<th align="left" nowrap="nowrap">Actions</th>
				<th width="5%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>
			</tr>
			<?php
			if(count($rows)>0)
			{
				$i=$this->Paginator->counter(array('format' => '%start%'));
				$bgClass="";
				foreach($rows as $row)
				{	
					if($userId == $row['Conversation']['sender_id'])
					{
						$other_person = $row['Reciever'];
					}
					else 
					{
						$other_person = $row['Sender'];
					}

					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">
					<td align="left"><?php echo $i;?>.</td>
					<td align="left">
						<?php 
						$userDetails['User']['image'] = $other_person['image']; 
						$imgName = $this->Common->getProfileImage($userDetails);
						echo $this->Image->resize($imgName, 150, 150, true); 
						?>
					</td>
					<td align="left"><?php echo $other_person['name']; ?></td>
					<td align="left"><p><?php echo $row['ConversationReply']['0']['reply']; ?></p></td>
					<td align="left"><?php echo date('M dS, g:ia', strtotime($row['ConversationReply']['0']['created'])); ?></td>
					<td align="left"><?php echo $row['Conversation']['conversation_reply_count']; ?></td>
					<td align="left">
						<?php
							echo $this->Html->link('View Conversation', array('controller' => 'conversations', 'action' => 'detail', $row['Conversation']['id'],'admin'=>false),array('target'=>'blank'));
						?>
					</td>
					<td align="center"><input type="checkbox" name="ids[]" value="<?php print $row['Conversation']['id']; ?>"></td>
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