<div class="col-md-1 no-pd pull-right gry">
	<button class="content-edit-btn edit-btn" data-attr-id="<?php echo $save_content['Content']['id'];?>" data-attr-type="5">  </button>
</div>
<p>Note : - <?php echo $save_content['Content']['data'];?></p>
<div class = "clear"></div>
<div class = "information_div">
	<?php if($save_content['Lecture']['info']) {?>
			<p><?php echo $save_content['Lecture']['info']?></p>
			<a class = "add_info" href = "#">Edit INFO</a>
	<?php } else {?>
		<a class = "add_info" href = "#">ADD INFO</a>
	<?php }?>
</div>