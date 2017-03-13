<div class="row">
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Countries List</span>', array('controller' => 'countries', 'action' => 'index', 'admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('Country', array('novalidate' => true)); ?>
	<?php echo $this->Form->hidden('id'); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Country:</strong></td>
				<td><?php echo $this->Form->input('country', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Country Code:</strong></td>
				<td><?php echo $this->Form->input('country_code', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
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
	$('#CountryAdminAddForm').validate();
</script>