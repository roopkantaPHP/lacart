<?php echo $this->Html->script('ckeditor/ckeditor.js'); ?>
<script type="text/javascript">
$(document).ready(function (){
	$('.validate').validate({
		rules:{
			"data[Cmspage][subject]":{
				required:true,		
			},
			"data[Cmspage][from_email]":{
				required:true,
				email:true
			},
			"data[Cmspage][reply_to]":{
				required:true,
				email:true
			},
			"data[Cmspage][content]":{
				required:true,
				email:true
			}
		}
	});
});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1><?php echo ($this->action == 'admin_add') ? 'Add' : 'Edit'?> Cms Page</h1></div>
		<div class="floatright"><?php echo $this->Html->link('<span>Back to Templates</span>', array('controller' => 'Cmspages', 'action' => 'index','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
    </div>
	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('Cmspage', array('id'=>'frmnewsletter', 'class' =>'validate')); ?>
			<?php echo $this->Form->hidden('id'); ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">				
				<tr>
					<td valign="middle"><strong class="upper">Title:</strong></td>
					<td><?php echo $this->Form->text('title', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115', 'maxlength' => 90)); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Subtitle:</strong></td>
					<td><?php echo $this->Form->text('sub_title', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115', 'maxlength' => 200)); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Page Content:</strong></td>
					<td>
						<?php echo $this->Form->textarea('description', array('id' => 'content','div'=>false, 'label'=>false, 'class'=>'ckeditor invalidateput', 'cols'=>'100', 'rows'=>'5')); ?>
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
<script type="text/javascript">
var JS_PATH = '<?php echo $this->Html->url(array('controller' => 'js', 'action' => 'ckeditor', 'admin' => false))?>';
//<![CDATA[
	CKEDITOR.replace('content',
	    {
		    filebrowserBrowseUrl : JS_PATH + '/ckfinder/ckfinder.html',
		    filebrowserImageBrowseUrl : JS_PATH + '/ckfinder/ckfinder.html?type=Images',
		    filebrowserFlashBrowseUrl : JS_PATH + '/ckfinder/ckfinder.html?type=Flash',
		    filebrowserUploadUrl : JS_PATH + '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		    filebrowserImageUploadUrl : JS_PATH + '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		    filebrowserFlashUploadUrl : JS_PATH + '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	    });
//]]>
</script>
