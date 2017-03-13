<div class="row">
	<div class="floatleft mtop10"><h1><?php echo $heading; ?></h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Categories</span>', array('controller' => 'Categories', 'action' => 'manage_categories','admin'=>true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
<?php echo $this->Form->create('Category', array('id' => 'CategoryAdmin')); ?>
<?php echo $this->Form->hidden('Category.id'); ?>
<div id='inline_content' style='padding:10px; background:#fff;'>
	<div align="center" id='inline_content' class="mtop15" >
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
					<td valign="middle"><strong class="upper">Meta Keywords:</strong></td>
					<td><?php echo $this->Form->textarea('Category.keywords', array('div'=>false, 'label'=>false, 'class'=>'input required', 'style'=>'width:500px;', 'rows'=>'3', 'required'=>true)); ?></td>
				</tr>	
				<tr>
					<td valign="middle"><strong class="upper">Meta	Description:</strong></td>
					<td><?php echo $this->Form->textarea('Category.description', array('div'=>false, 'label'=>false, 'class'=>'input required', 'style'=>'width:500px;', 'rows'=>'3', 'required'=>true)); ?></td>
				</tr>	
				
				<tr>
					<td valign="middle"><strong class="upper">Category Name:</strong></td>
					<td><?php echo $this->Form->text('Category.name', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70', 'required'=>true)); ?></td>
				</tr>
			<tr>
				<td align="center">&nbsp;</td>
				<td align="left">
					<div class="black_btn2">
						<span class="upper"><?php echo $this->Form->input('SUBMIT', array('label' => false, 'type' => 'submit', 'div' => false))?></span>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php echo $this->Form->end(); ?>
</div>
