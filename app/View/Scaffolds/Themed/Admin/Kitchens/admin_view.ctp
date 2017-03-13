<div class="row">
	<div class="floatleft mtop10"><h1><?php echo __("View Kitchen"); ?></h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Kitchen List</span>', array('controller' => 'kitchens', 'action' => 'index'), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('Kitchen', array('novalidate' => true)); ?>
	<?php echo $this->Form->hidden('id'); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Kitchen Name:</strong></td>
				<td><?php echo $this->request->data['Kitchen']['name']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Allergy:</strong></td>
				<td><?php echo str_replace('::::::::', ',', $this->request->data['User']['allergy']); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Diet:</strong></td>
				<td><?php echo $this->request->data['User']['diet']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Cuisine:</strong></td>
				<td><?php echo $this->request->data['Kitchen']['cuisine']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Address:</strong></td>
				<td><?php echo $this->request->data['Kitchen']['address']; ?></td>
			</tr>
			<!--
			<tr>
				<td valign="middle"><strong class="upper">Dining Dine In:</strong></td>
				<td><?php echo ($this->request->data['Kitchen']['dining_dine_in']==1)?"Yes":"No"; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Dining Take Out:</strong></td>
				<td><?php echo ($this->request->data['Kitchen']['dining_take_out']==1)?"Yes":"No"; ?></td>
			</tr>
			-->
			<tr>
				<td valign="middle"><strong class="upper">Status:</strong></td>
				<td><?php echo ($this->request->data['Kitchen']['status']=='on')?"On":"Off"; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Featured:</strong></td>
				<td><?php echo ($this->request->data['Kitchen']['is_featured']==1)?"Yes":"No"; ?></td>
			</tr>
		</table>
	<?php echo $this->Form->end();?>
</div>
<script>
	$('#UserAdminEditUserForm').validate();
</script>
