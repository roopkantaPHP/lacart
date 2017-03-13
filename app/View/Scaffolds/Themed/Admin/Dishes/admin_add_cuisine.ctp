<div class="row">
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Cuisines List</span>', array('controller' => 'dishes', 'action' => 'cuisine', 'admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('Cuisine', array('novalidate' => true)); ?>
	<?php echo $this->Form->hidden('id'); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			
			<tr>
				<td valign="middle"><strong class="upper">Name:</strong></td>
				<td><?php echo $this->Form->input('name', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			
			<tr>
				<td valign="middle"><strong class="upper">Is Active:</strong></td>
				<td><?php echo $this->Form->checkbox('is_active', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70','checked'=>(isset($this->request->data['Cuisine']['is_active'])) ? $this->request->data['Cuisine']['is_active'] : 'checked')); ?></td>
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
	$('#CuisineAdminAddCuisineForm').validate();
</script>