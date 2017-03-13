<?php if($inform['Lecture']['info']) {?>
		<p><?php echo $inform['Lecture']['info']?></p>
		<a class = "add_info" href = "#">Edit INFO</a>
<?php } else {?>
	<a class = "add_info" href = "#">ADD INFO</a>
<?php }?>