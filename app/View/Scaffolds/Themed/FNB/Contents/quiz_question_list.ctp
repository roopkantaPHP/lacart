<ul>
	<?php foreach($all_questions as $quws) { ?>
	    <li data-id="<?php echo $quws['QuizQuestion']['id']?>" data-asses-quiz-id = "<?php echo $quws['QuizQuestion']['quiz_id']?>" data-assessment-type="<?php echo $quws['QuizQuestion']['type']?>">
			<div class="col-md-1 no-pd pull-right gry">
				<button class="quiz-question-edit-btn edit-btn"></button>
			</div>
	 		<span style="float: left;"><?php echo $quws['QuizQuestion']['question']?></span> : <?php echo $quws['QuizQuestion']['type']?>
		</li>
    <?php }?>
</ul>