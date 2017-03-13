<?php echo $this->Html->script('ckeditor/ckeditor.js'); ?>
<script type="text/javascript">
$(document).ready(function (){
$('.validate').each(function(){
$(this).validate({
rules:{
	"data[Newsletter][newsletter_title]":{
		required:true,		
	},
	"data[Newsletter][subject]":{
		required:true,		
	}
}
});});});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1>Edit Newslettter</h1></div>
		<div class="floatright"><?php echo $this->Html->link('<span>Back to Newsletters</span>', array('controller' => 'pages', 'action' => 'manage_newsletters','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
    </div>
	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('', array('id'=>'frmnewsletter', 'class' =>'validate')); ?>
			<?php echo $this->Form->hidden('Newsletter.id'); ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">				
				<tr>
					<td valign="middle"><strong class="upper">Title:</strong></td>
					<td><?php echo $this->Form->text('Newsletter.newsletter_title', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Email From:</strong></td>
					<td><?php echo $this->Form->text('Newsletter.from_email', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Subject:</strong></td>
					<td><?php echo $this->Form->text('Newsletter.subject', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Content:</strong></td>
					<td>
						<?php echo $this->Form->textarea('Newsletter.content', array('div'=>false,  'id' => 'content', 'label'=>false, 'class'=>'ckeditor invalidateput', 'cols'=>'100', 'rows'=>'5')); ?>
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
//<![CDATA[
	CKEDITOR.replace( 'content',
	    {
		    filebrowserBrowseUrl : '<?php echo SITE_URL ?>/js/ckeditor/ckfinder/ckfinder.html',
		    filebrowserImageBrowseUrl : '<?php echo SITE_URL ?>/js/ckeditor/ckfinder/ckfinder.html?type=Images',
		    filebrowserFlashBrowseUrl : '<?php echo SITE_URL ?>/js/ckeditor/ckfinder/ckfinder.html?type=Flash',
		    filebrowserUploadUrl : '<?php echo SITE_URL ?>/js/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		    filebrowserImageUploadUrl : '<?php echo SITE_URL ?>/js/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		    filebrowserFlashUploadUrl : '<?php echo SITE_URL ?>/js/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	    });
//]]>
</script>
