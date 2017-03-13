<?php
	$con_dta = json_decode($save_content['Content']['data'], 1);
?>
<div class = "content_show_div">
	<div class="col-md-1 no-pd pull-right gry">
		<button class="content-edit-btn edit-btn" data-attr-id = "<?php echo $save_content['Content']['id']?>" data-attr-type = "<?php echo $save_content['Content']['type']?>">  </button>
	</div>
		<div class="cfi-quiz cfi-sortable ud-quizeditor" data-id="<?php echo $save_content['Content']['id']?>">
			<div class="cfi-content opened">
				<span class="content ui-state-default">
					<span class="cfi-item-type">Quiz :- </span>
					<span class="cfi-item-title"><?php echo $con_dta['name']?></span>
					<span class="edit-handle"></span>
						<a href="#" class="add-questions btn btn-primary btn-sm container-switch floating pull-right mr20">
							+ Add Questions					</a>
					<span class="collapse-btn container-switch none"></span>
					<span class="sort-handle outer"></span>
				</span>
			</div>
		</div>
		<div class = "quiz-list-items">
			<ul>
			<?php foreach($save_content['QuizQuestion'] as $single_ques) {?>
					<li data-id="<?php echo $single_ques['id']?>" data-asses-quiz-id = "<?php echo $single_ques['quiz_id']?>" data-assessment-type="<?php echo $single_ques['type']?>">
						<div class="col-md-1 no-pd pull-right gry">
							<button class="quiz-question-edit-btn edit-btn"></button>
						</div>
				 		<span style="float: left;"><?php echo $single_ques['question']?></span> : <?php echo $single_ques['type']?>
			 		</li>
			<?php }?>
			</ul>
		</div>
</div>