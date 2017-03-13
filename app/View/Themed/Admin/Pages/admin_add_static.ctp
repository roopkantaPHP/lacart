<?php echo $this->Html->script('ckeditor/ckeditor.js'); ?>	
    <div class="row">
		<div class="floatleft mtop10"><h1><?php echo __("Edit Page"); ?></h1></div>
		<div class="floatright"><?php echo $this->Html->link('<span>Back to Static Pages List</span>', array('controller' => 'pages', 'action' => 'manage_static'), array('class' => 'black_btn', 'escape' => false)) ?></div>
    </div>

	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create(); ?>
			<?php echo $this->Form->hidden('Page.id'); ?>
			<?php echo $this->Form->hidden('Page.identifire'); ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">
				<tr>
					<td valign="middle"><strong class="upper">Title:</strong></td>
					<td><?php echo $this->Form->text('Page.page_title', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
				</tr>								
				<tr>
					<td valign="middle"><strong class="upper">Content:</strong></td>
					<td>
						<?php echo $this->Form->textarea('Page.page_content', array('div'=>false, 'label'=>false, 'class'=>'ckeditor input', 'cols'=>'100', 'rows'=>'5')); ?>
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
<script>
	$('#PageAdminAddStaticForm').validate();
</script>