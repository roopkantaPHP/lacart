<script type="text/javascript">
$(document).ready(function (){
$('.validate').each(function(){
$(this).validate({
rules:{
	"data[Video][title]":{
		required:true,		
	},
}
});});});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1>Edit Video</h1></div>
		<div class="floatright"><?php echo $this->Html->link('<span>Back to Videos</span>', array('controller' => 'Videos', 'action' => 'index','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
    </div>
	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('Video', array('class' =>'validate','type'=>'file'));
		 ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">				
				<tr>
					<td valign="middle"><strong class="upper">Title:</strong></td>
					<td><?php echo $this->Form->text('Video.title', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Description:</strong></td>
					<td>
						<?php echo $this->Form->textarea('Video.description', array('div'=>false,'label'=>false, 'class'=>'invalidateput', 'cols'=>'100', 'rows'=>'5')); ?>
					</td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Uplaod Video:</strong></td>
					<td><?php echo $this->Form->text('Video.url', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Is Display:</strong></td>
					<td>
						<?php echo $this->Form->checkbox('Video.is_display', array('div'=>false,'label'=>false)); ?>
					</td>
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
