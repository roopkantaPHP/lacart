<script type="text/javascript">
$(document).ready(function (){
$('.validate').each(function(){
$(this).validate({
rules:{
	"data[CmsCity][name]":{
		required:true,		
	},
	"data[CmsCity][email]":{
		required:true,		
	},
	"data[CmsCity][message]":{
		required:true,		
	}
}
});});});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1>Add CmsCity</h1></div>
		<div class="floatright"><?php echo $this->Html->link('<span>Back to Cms City</span>', array('controller' => 'cms_cities', 'action' => 'index','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
    </div>
	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('CmsCity', array('class' =>'validate','type'=>'file')); ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">				
				<tr>
					<td valign="middle"><strong class="upper">Name:</strong></td>
					<td><?php echo $this->Form->text('CmsCity.name', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Image:</strong></td>
					<td><?php echo $this->Form->file('CmsCity.image', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Description:</strong></td>
					<td>
						<?php echo $this->Form->textarea('CmsCity.description', array('div'=>false,'label'=>false, 'class'=>'invalidateput count200', 'cols'=>'100', 'rows'=>'5')); 
						$descLen = 0;
						if(isset($this->request->data['CmsCity']['description']) && !empty($this->request->data['CmsCity']['description']))
						$descLen = strlen($this->request->data['CmsCity']['description']);
						
						$descLenShow = 200-$descLen;  ?>
						
						 <span class="char-info"><?php echo $descLenShow." characters"; ?></span>
					</td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Is Display:</strong></td>
					<td>
						<?php echo $this->Form->checkbox('CmsCity.is_display', array('div'=>false,'label'=>false)); ?>
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
