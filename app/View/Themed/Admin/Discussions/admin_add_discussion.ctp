<div class="row">
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Discussion List</span>', array('controller' => 'discussions', 'action' => 'discussion', 'admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('Discussion', array('novalidate' => true)); ?>
	<?php echo $this->Form->hidden('id'); ?>
	<?php echo $this->Form->hidden('user_id'); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Select Community:</strong></td>
				<td><?php echo $this->Form->input('community_id', array('options'=>$communities,'empty'=>'Select Community','div'=>false, 'label'=>false, 'class'=>'input required', 'style'=>'width:524px;')); ?></td>
			</tr>
			
			<tr>
				<td valign="middle"><strong class="upper">Discussion Title:</strong></td>
				<td><?php echo $this->Form->input('title', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			
			<tr>
				<td valign="middle"><strong class="upper">Message:</strong></td>
				<td><?php echo $this->Form->textarea('description', array('div'=>false, 'label'=>false, 'class'=>'input required', 'rows'=>'7','cols'=>68)); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Is Active:</strong></td>
				<td><?php echo $this->Form->checkbox('is_active', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70','checked'=>(isset($this->request->data['Community']['is_active'])) ? $this->request->data['Community']['is_active'] : 'checked')); ?></td>
			</tr>
			
			<tr>
            	<td>&nbsp;</td>
				<td>
					<div class="floatleft">
						<input type="submit" class="submit_btn" value="">
					</div>
					<div class="floatleft" id="domain_loader" style="padding-left:5px;"></div>
				</td>
			</tr>
		</table>
	<?php echo $this->Form->end();?>
</div>
<script>
	$('#DiscussionAdminAddDiscussionForm').validate();
</script>