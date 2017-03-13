<div class="row">
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Price List</span>', array('controller' => 'dishes', 'action' => 'portion', 'admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('Portion', array('novalidate' => true)); ?>
	<?php echo $this->Form->hidden('id'); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			
			<tr>
				<td valign="middle"><strong class="upper">Type:</strong></td>
				<td><?php echo $this->Form->input('type', array('options'=>array('small'=>'Small','big'=>'Big'),'empty'=>'Select Type','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Unit:</strong></td>
				<td><?php echo $this->Form->input('unit', array('options'=>Configure::read('UNIT'),'empty'=>'Select Unit','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			
			<tr>
				<td valign="middle"><strong class="upper">Price:</strong></td>
				<td><?php echo $this->Form->input('price', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
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
	$('#PortionAdminAddPortionForm').validate();
</script>