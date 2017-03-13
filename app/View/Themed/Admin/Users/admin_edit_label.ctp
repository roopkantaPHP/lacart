<div class="row">
	<div class="floatleft mtop10"><h1><?php echo __("Edit Label"); ?></h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Label List</span>', array('controller' => 'users', 'action' => 'admin_label_list'), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('SiteSetting', array('novalidate' => true, 'type'=>'file')); ?>
	<?php echo $this->Form->hidden('id'); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Page Slug:</strong></td>
				<td><?php echo $this->Form->input('page_slug', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70','readonly'=>'readonly')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Slug:</strong></td>
				<td><?php echo $this->Form->input('slug', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70','readonly'=>'readonly')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Label:</strong></td>
				<td><?php echo $this->Form->input('label', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
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