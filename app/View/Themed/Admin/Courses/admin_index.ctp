<?php echo $this->Html->script('common'); 
?>
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
<div  id = "resultsDiv">
<div class="row searchbox floatleft mtop30" style="background-color: white; padding: 10px;">
	<p><?php echo $this->Form->create('', array('id'=>'AS', 'type'=>'GET')); ?>
	<table width="90%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="center">
		      	<table width="90%" border="0" cellpadding="4" cellspacing="1" >
				    <tr>
				      <td colspan="4" align="left" class="header_bg">Advance Course Search</td>
				    </tr>        
					<tr class="oddRow">
						<td align="right">
							<strong>Name</strong>:
						</td>
						<td align="left">
							<?php 
								echo $this->Form->input(
									'keyword', array(
										'class' => 'input', 'label' => false, 'div' => false,
										'value' => isset($this->params->query['keyword']) ? $this->params->query['keyword'] : ''
									)
								);
							?>
						</td>         
						<td colspan="4" align="center" class="oddRow">
							<input type="submit" class="submit_btn" value=" ">&nbsp;<input name="Submit2" type="button" class="buttons" value="Reset" id="reset">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?php echo $this->Form->end(); ?></p>
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
				<th width="25%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('name') ?></th>
				<th width="15%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('course_type') ?></th>
				<th width="15%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('course_fee') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('status') ?></th>
				<th width="10%" align="left" nowrap="nowrap"><?php echo $this->Paginator->sort('User.name', 'Created By') ?></th>
				<th width="10%" align="left" nowrap="nowrap">Action</th>
				<th width="10%" align="left" nowrap="nowrap">Link</th>
				<th width="10%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>
			</tr>
			<?php
			if(count($courses)>0)
			{
			//pr($result_arr);

				$i=$this->Paginator->counter(array('format' => '%start%'));
				$bgClass="";
				foreach($courses as $row)
				{					
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">
					<td align="left"><?php echo $i;?>.</td>
					<td align="left"><?php echo $row['Course']['name'];?></td>
					<td align="left"><?php echo Configure::read('paid_type.' . $row['Course']['course_type']);?></td>
					<td align="left"><?php echo $row['Course']['course_fee'];?></td>
					<td align="left"><?php echo Configure::read('course_status.' . $row['Course']['course_status'] . '.name');?></td>					
					<td align="left"><?php echo $row['User']['name'];?></td>					
					<td>
						<?php
							if($row['Course']['course_status'] == COURSE_DRAFT || $row['Course']['course_status'] == COURSE_REVIEW)
							{
								echo $this->Html->link('Approve', array('controller' => 'courses', 'action' => 'course_operations', $row['Course']['id'], COURSE_PUBLISHED, 'admin' => true));
								echo ' | ';
								echo $this->Html->link('Deny', array('controller' => 'courses', 'action' => 'course_operations', $row['Course']['id'], COURSE_DENIED, 'admin' => true));
							} else if($row['Course']['course_status'] == COURSE_DENIED)
							{
								echo $this->Html->link('Approve', array('controller' => 'courses', 'action' => 'course_operations', $row['Course']['id'], COURSE_PUBLISHED, 'admin' => true));
							} else
							{
								echo $this->Html->link('Deny', array('controller' => 'courses', 'action' => 'course_operations', $row['Course']['id'], COURSE_DENIED, 'admin' => true));
							}
						?>
					</td>
					<td align="left">
						<?php
							echo $this->Html->link(
								'View', array(
									'controller' => 'courses',
									'action' => 'start_course',
									$row['Course']['slug'],
									'admin' => false,
								), array(
									'target' => '_blank'
								)
							)
						?>
					</td>
					<td align="center"><input type="checkbox" name="ids[]" value="<?php echo $row['Course']['id']; ?>"></td>
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
							<?php 
								echo $this->Form->input(
									'action', array(
										'options' => Configure::read('admin_course_status'), 'style' => 'width:150px',
										'class' => 'select-small', 'id' => 'action', 'empty' => 'Select', 'div' => false, 'label' => false
									)
								);
							
							?>
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
	<?php
		echo $this->Form->end();
		echo $this->Js->writeBuffer();
	?>
</div>
</div>
