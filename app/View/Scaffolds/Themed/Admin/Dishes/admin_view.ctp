<div class="row">
	<div class="floatleft mtop10"><h1><?php echo __("View Dish"); ?></h1></div>
	<?php 
	if(isset($orderId) && !empty($orderId))
	{ ?>
		<div class="floatright">
			<?php echo $this->Html->link('<span>Back to Ordered Dish List</span>', array('controller' => 'orders', 'action' => 'dishlist',$orderId), array('class' => 'black_btn', 'escape' => false)) ?>
		</div>
	<?php 
	}
	else
	{ ?>
		<div class="floatright">
			<?php echo $this->Html->link('<span>Back to Kitchen</span>', array('controller' => 'dishes', 'action' => 'dish_list',$this->request->data['Kitchen']['id']), array('class' => 'black_btn', 'escape' => false)) ?>
		</div>
	<?php
	} ?>
	
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('Kitchen', array('novalidate' => true)); ?>
	<?php echo $this->Form->hidden('id'); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Dish Name:</strong></td>
				<td><?php echo $this->request->data['Dish']['name']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Allergy:</strong></td>
				<td><?php echo str_replace('::::::::', ',', $this->request->data['Dish']['allergens']); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Diet:</strong></td>
				<td><?php echo $this->request->data['Dish']['diet']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Cuisine:</strong></td>
				<td><?php echo $this->request->data['Dish']['cuisine']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Portion:</strong></td>
				<td>
				<?php
					if($this->request->data['Dish']['p_custom'])
				 		echo "<br>Custom: ".$this->request->data['Dish']['p_custom_price'];
				 	if ($this->request->data['Dish']['p_big']) 
				 		echo "<br>Premium: ".$this->request->data['Dish']['p_big_price'];
				 	if ($this->request->data['Dish']['p_small'])
				 		echo "<br>Budget: ".$this->request->data['Dish']['p_small_price'];
				?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Serving Time:</strong></td>
				<td><?php echo $this->request->data['Dish']['serve_start_time']." - ".$this->request->data['Dish']['serve_end_time']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Lead Time:</strong></td>
				<td><?php echo $this->request->data['Dish']['lead_time']." hours"; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Repeat:</strong></td>
				<td><?php echo (empty($this->request->data['Kitchen']['repeat']))?"No":$this->request->data['Kitchen']['repeat']; ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Status:</strong></td>
				<td><?php echo ($this->request->data['Kitchen']['status']=='on')?"On":"Off"; ?></td>
			</tr>
		
		</table>
	<?php echo $this->Form->end();?>
</div>
<script>
	$('#UserAdminEditUserForm').validate();
</script>
