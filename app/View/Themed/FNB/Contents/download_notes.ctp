<div id="list2">
	<ol>
		<?php foreach($my_notes as $notes){?>
			<li>	
				<div class="created-on"> <em><?php echo date('d/m/Y H:i', strtotime($notes['CoursesUsersNote']['created']))?></em> </div>
				<span><?php echo $notes['CoursesUsersNote']['notes']?></span>
			</li>
		<?php }?>
   </ol>
</div>