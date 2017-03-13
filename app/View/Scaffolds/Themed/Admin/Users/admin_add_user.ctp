<div class="row">
	<div class="floatleft mtop10"><h1><?php echo __("Add Subadmin"); ?></h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Back to Users List</span>', array('controller' => 'users', 'action' => 'manage_users'), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div align="center" class="greybox mtop15">
	<?php echo $this->Form->create('User', array('novalidate' => true,'type'=>'file')); ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Name:</strong></td>
				<td><?php echo $this->Form->input('name', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Address:</strong></td>
				<td><?php echo $this->Form->input('address', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">About me:</strong></td>
				<td><?php echo $this->Form->textarea('description', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Username:</strong></td>
				<td><?php echo $this->Form->input('username', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Password:</strong></td>
				<td><?php echo $this->Form->input('password', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Email:</strong></td>
				<td><?php echo $this->Form->input('email', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<?php if($this->Session->read('Auth.User.id') == SUPER_ADMIN) {?>
				<tr>
					<td valign="middle"><strong class="upper">Group:</strong></td>
					<td><?php echo $this->Form->input('group_id', array('div'=>false, 'label'=>false, 'class'=>'input')); ?></td>
				</tr>
			<?php }?>
			<tr> 
				<td valign="middle"><strong class="upper">Profile Photo:</strong></td>
				<td>
					<div class="uploaded-img">
						<?php 
						$imgName = $this->Common->getProfileImage($this->request->data);
						
						echo $this->Image->resize($imgName, 150, 150, true); 
						?>
					</div>
					<div class="select-file">
							<?php echo $this->Form->file('image',array('escape'=>false));
								  echo "upload/change image";
							?>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">State:</strong></td>
				<td><?php echo $this->Form->input('state_id', array('div'=>false, 'label'=>false, 'class'=>'input','empty'=>'Select State','options'=>$states)); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">City:</strong></td>
				<td><?php echo $this->Form->input('city_id', array('div'=>false, 'label'=>false, 'class'=>'input','empty'=>'Select City')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Is Active:</strong></td>
				<td><?php echo $this->Form->input('is_active', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
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
	$('#UserAdminAddSubadminForm').validate();
	$(document).ready(function(){
		<?php if(isset($this->request->data['User']['state_id']) && !empty($this->request->data['User']['state_id'])){ ?>
			setCityOption($("#UserStateId").val());
		<?php } ?>

	    $("#UserStateId").change(function(){
			var stateId = $(this).val();
			if(stateId != ''){
				setCityOption(stateId);
			}
		});
	});
	function setCityOption(id){
		$.ajax({
				'url':'<?php echo $this->Html->url(array('controller'=>'users','action'=>'getCityOptions','admin'=>false)); ?>/'+id,
				'success': function(output) {
					$('#UserCityId').html(output);
					<?php if(isset($this->request->data['User']['city_id']) && !empty($this->request->data['User']['city_id'])){  ?>
						$("#UserCityId").val('<?php echo $this->request->data['User']['city_id']; ?>');
					<?php 	} ?>
				}
			});
	}
</script>