<div class="row">
	<div class="floatleft mtop10"><h1><?php echo $heading; ?></h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Keywords</span>', array('controller' => 'keywords', 'action' => 'index','admin'=>true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
	<?php echo $this->Form->create('Keyword'); ?>
	<?php echo $this->Form->hidden('Keyword.id'); ?>
	<div id='inline_content' style='padding:10px; background:#fff;'>
		<div align="center" id='inline_content' class="mtop15" >
			<table cellspacing="0" cellpadding="7" border="0" align="center">
				<tr>
					<td valign="middle"><strong class="upper">Name:</strong></td>
					<td><?php echo $this->Form->input('Keyword.name', array('div'=>false, 'label'=>false, 'class'=>'input required', 'style'=>'width:500px;','required'=>true)); ?></td>
				</tr>	
				<tr>
					<td valign="middle"><strong class="upper">Active :</strong></td>
					<td><?php echo $this->Form->input('Keyword.is_active', array('div'=>false, 'label'=>false, 'class'=>'input','required'=>false)); ?></td>
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