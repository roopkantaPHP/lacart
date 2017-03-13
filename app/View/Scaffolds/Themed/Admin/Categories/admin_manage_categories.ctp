<?php echo $this->Html->script('common'); ?>
<div class="row">
	<div class="floatleft mtop10"><h1>Manage Categories</h1></div>
	<div class="floatright"><?php echo $this->Html->link('<span>Add New Category</span>', array('controller' => 'Categories', 'action' => 'add_category','admin'=>true), array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>
<div class="row mtop30">
	<?php echo $this->Form->create(); ?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
		<tr>
			<th width="5%" align="center">S No.</th>
			<th width="50%" align="left">Name</th>
			<th width="15%" align="left">Status</th>
			<th width="9%" align="left">Created On</th>
			<th width="6%" align="center">Action</th>
			<th width="5%" align="center"><input type="checkbox" class = "select_all_checkbox" value="check_all" id="check_all"></th>
		</tr>
		<?php foreach($categories as $category) : ?>
		<tr>
			<td align="center"><?php echo $category['Category']['id']; ?></td>
			<td align="left"><span class="blue"><?php echo $category['Category']['name']; ?></span></td>
			<td align="left"><?php echo ($category['Category']['is_active']) ? 'Active' : 'Inactive'; ?></td>
			<td align="left"><?php echo $category['Category']['created']; ?></td>
			<td align="center"><?php echo $this->Html->link('<img src="'.SITE_URL.'/admin_images/edit_icon.gif" title="Edit Newsletter" border="0"/>', array('controller'=>'categories', 'action'=>'add_category', $category['Category']['id']), array('escape' => false)) ?></td>
			<td align="center"><input type="checkbox" name="ids[]" value="<?php print $category['Category']['id']; ?>"></td>
		</tr>
		<?php endforeach; ?>
		<tr align="right">
			<td colspan="7" align="left" class="bordernone">
				<div class="floatleft mtop7">
					<div class="pagination">
						<?php
							echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled', 'tag' => 'a'));
							echo $this->Paginator->numbers(array('separator' => ''));
							echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled', 'tag' => 'a'));
						?>
					</div>
				</div>
				<?php if(!empty($categories)) :?>
				<div class="floatright">
						<div class="floatleft">
						<span class="redtext top5" id="err_status" style="float:left;"></span> &nbsp;&nbsp;
						<select name='action' id='action' class='select-small' style='width:150px;'>
							<option value=''>Select</option>
							<option value='Active'>Active</option>
							<option value='Deactive'>Deactive</option>					
							<option value='Delete'>Delete</option>
						</select>
						</div>
						<input type="hidden" name="listingAction" value="" />
						<div class="floatleft mleft10"><input type="button" class="submit_btn" value="" name="Search" onclick="return submit_form(this.form,'')"></div>
                   </div>
				<?php endif;?>			
				</td>
		</tr>
	</table>
	<?php echo $this->Form->end(); ?>
</div>
