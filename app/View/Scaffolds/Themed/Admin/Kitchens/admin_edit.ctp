<div class="row">
	<div class="floatleft mtop10"><h1><?php echo __("Edit Kitchen"); ?></h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Kitchen List</span>', array('controller' => 'kitchens', 'action' => 'index'), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('Kitchen', array('novalidate' => true)); ?>
	<?php echo $this->Form->hidden('id');
	echo $this->Form->hidden('user_id');
		  echo $this->Form->hidden('User.id',array('value'=>$this->request->data['Kitchen']['user_id'])); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Kitchen Name:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.name', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Allergy:</strong></td>
				<td><?php echo $this->Form->input('User.allergy', array('options'=> $allergy,'multiple' =>true,'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Diet:</strong></td>
				<td><?php echo $this->Form->input('User.diet', array('options'=> array('Vegetarian'=>'Vegetarian','Non-Vegetarian'=>'Non Vegetarian','Vegan'=>'Vegan'),'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Cuisine:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.cuisine', array('options'=> $cuisine,'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Address:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.address', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<!--
			<tr>
				<td valign="middle"><strong class="upper">Dining Dine In:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.dining_dine_in', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Dining Take Out:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.dining_take_out', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			-->
			<tr>
				<td valign="middle"><strong class="upper">Sales Tax:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.sales_tax', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Status:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.status', array('options'=> array('On'=>'On','Off'=>'Off'),'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Featured:</strong></td>
				<td><?php echo $this->Form->input('Kitchen.is_featured', array('div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
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
	$('#UserAdminEditUserForm').validate();
</script>
