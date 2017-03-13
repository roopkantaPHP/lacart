<script type="text/javascript">
$(document).ready(function (){
$('.validate').each(function(){
$(this).validate({
rules:{
	"data[Setting][max_users_chat]":{
		required:true,		
	},
	"data[Setting][token_cost]":{
		required:true,		
	}
}
});});});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1>Manage Site Settings</h1></div>
		<div class="floatright"></div>
    </div>

	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('', array('enctype'=>'multipart/form-data', 'class'=>'validate')); ?>
			<?php echo $this->Form->hidden('Setting.id'); ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">
				<tr>
					<td valign="middle"><strong class="upper">Upload CSS file for Admin:</strong></td>
					<td><?php echo $this->Form->text('Setting.css_file_admin', array('type'=>'file', 'div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Upload CSS file for Front:</strong></td>
					<td><?php echo $this->Form->text('Setting.css_file_front', array('type'=>'file', 'div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Upload CSS file for Wins:</strong></td>
					<td><?php echo $this->Form->text('Setting.css_file_wins', array('type'=>'file', 'div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Maximum count of users in Chat:</strong></td>
					<td><?php echo $this->Form->text('Setting.max_users_chat', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Token Cost:</strong></td>
					<td><?php echo $this->Form->text('Setting.token_cost', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'70')); ?></td>
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
		</form>
	</div>
