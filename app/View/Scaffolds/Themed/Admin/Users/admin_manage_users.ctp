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
		<div class="floatleft mtop10"><h1><?php echo h("$group_name Manager"); ?></h1></div>
		<div class="floatright">
			<?php
				echo $this->Html->link(
					"<span>Add $group_name</span>", array(
						'controller' => 'users', 'action' => 'add_user',
						'admin' => true, 'plugin' => false
					),array(
						'class' => 'black_btn', 'escape' => false
					)
				); 
			?>
		</div>
</div>

<div class="row searchbox floatleft mtop30" style="background-color: white; padding: 10px;">
	<p><?php echo $this->Form->create('', array('id'=>'AS', 'type'=>'GET')); ?>
  <table width="90%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"><table width="90%" border="0" cellpadding="4" cellspacing="1" >
        <tr>
          <td colspan="4" align="left" class="header_bg">Advance User Search</td>
        </tr>        
        <tr class="oddRow">
          <td align="right"><strong>Username</strong>:</td>
          <td align="left"><input name="login" type="text" class="input" id="ScreenName" value="<?php echo $login; ?>"></td>
          <td align="right"><strong>Email</strong>:</td>
          <td align="left"><input name="email" type="text" class="input" id="Email" value="<?php echo $email; ?>"></td>         
           <td colspan="4" align="center" class="oddRow"><input type="submit" class="submit_btn" value=" " name="Search">&nbsp;<input name="Submit2" type="button" class="buttons" value="Reset" id="reset"></td>
        </tr>     
       
        <tr>
         
        </tr> 
      </table></td>
    </tr>
  </table>
<?php echo $this->form->end(); ?></p>
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
				<th width="15%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('User.email','Email') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('User.username','Username') ?></th>			
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('User.registrationdate','Registration Date') ?></th>
				<th width="10%" align="left" nowrap="nowrap">Kitchan</th>
				<th width="10%" align="left" nowrap="nowrap">Status</th>			
				<th width="10%" align="left" nowrap="nowrap">Action</th>
				<th width="10%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>
			</tr>
			<?php
			if(count($results)>0)
			{
			//pr($result_arr);

				$i=$this->Paginator->counter(array('format' => '%start%'));
				$bgClass="";
				foreach($results as $row)
				{					
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";
		 

			?>
				<tr class="<?=$bgClass?>">
					<td align="left"><?php echo $i;?>.</td>
					<td align="left"><?php echo $row['User']['email'];?></td>
					<td align="left"><?php echo $row['User']['username'];?></td>					
					<td align="left"><?php echo $row['User']['created'];?></td>					
					<td><?php echo (!empty($row['Kitchen']['id'])) ? 'Yes' : 'No';?></td>
					<td><?php print ($row['User']['is_active'] == "1") ? "Active (". $this->Html->link('Deactivate', array('controller' => 'users', 'action' => 'users_operations', 'status',$row['User']['id'], 0, 'admin' => true)).')' : "Deactivate (". $this->Html->link('Activate', array('Controller' => 'users', 'action' => 'users_operations', 'status',$row['User']['id'], 1, 'admin' => true)).')'; ?></td>
					<td align="left"><?php echo $this->Html->link('Edit', array('controller' => 'users', 'action' => 'admin_edit_user', $row['User']['id']))?></td>
					<td align="center"><input type="checkbox" name="ids[]" value="<?php print $row['User']['id']; ?>"></td>
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
							<option value='Activate'>Activate</option>	
							<option value='Deactivate'>Deactivate</option>					
							<option value='IPBlock'>Block</option>																
							<option value='Unblock'>Unblock</option>																								
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
