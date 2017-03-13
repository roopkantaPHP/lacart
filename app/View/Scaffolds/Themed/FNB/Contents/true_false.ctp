<?php 
$quiz_ques_id = '';
$anser = '';
$content_desc = '';
$question = '';
if(!empty($content_data))
{
	$question = $content_data['QuizQuestion']['question'];
	$anser = $content_data['QuizQuestion']['answer'];
	$quiz_ques_id = $content_data['QuizQuestion']['id'];
}
?>
<div class="upload-asset-container quiz_true_false show">
	<textarea rows = "2" class = "summernote"><?php echo $question?></textarea>
	<?php 
		echo $this->Form->input(
			'answer', array(
				'type' => 'radio',
				'legend' => false,
				'class' => 'true_false_radio mr10',
				'div' => false,
				'value' => $anser,
				'options' => array(
					'false' => 'false', 'true' => 'true'
				)
			)
		)
	?>
	<div class="submit-row">
		<?php 
			echo $this->Html->link('Save', '#',array('class' => 'submit_quiz_text_true_false btn btn-success'));
			echo $this->Html->link('Cancel', '#',array('class' => 'cancel_quiz_true_false btn btn-error'));
		?>
	</div>
</div>
<script type="text/javascript">
$(function()
{
	$('.summernote').summernote({
        height: 200,
        toolbar:
        [
            //['style', ['style']], // no style button
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['picture', 'link']], // no insert buttons
        ]
    });
    $('.cancel_quiz_true_false').click(function(e)
	{
		e.preventDefault();
		$(this).closest('.content_div_complete').find('.content_show_div').show();
		$(this).closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
	});
    
    $('.submit_quiz_text_true_false').click(function(e)
	{
		var this_ele_div = $(this);
		var upload_container = $(this).closest('.upload-asset-container');
		e.preventDefault();
		var quiz_question = upload_container.find('.summernote').code();
		var quiz_options = new Array('true', 'false');
		var quiz_answer = upload_container.find('.true_false_radio:checked').val();
		if(!quiz_question || !quiz_answer)
		{
			alert('Please Enter the Complete details');
			return false;
		}
		$.ajax({
			url: "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'add_quiz_content'))?>",
			type : "POST",
			data : 'data[quiz_id]=' + '<?php echo $quiz_id; ?>' + '&data[quiz_type]=' + 'true-false' + '&data[quiz_ques]=' + encodeURIComponent(quiz_question) + '&data[quiz_options]=' + JSON.stringify(quiz_options) + '&data[quiz_answer]=' + quiz_answer + '&data[quiz_ques_id]=' + '<?php echo $quiz_ques_id?>',
			success: function(data)
			{
				data = JSON.parse(data);
				if(data['status'])
				{
					this_ele_div.closest('.content_div_complete').find('.content_show_div').show();
					this_ele_div.closest('.content_div_complete').find('.content_show_div').find('.quiz-list-items').html(data['html']);
					upload_container.closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
				} else
				{
					alert('Problem Occured. Please refresh and then try again.')
				}
			}
		});
	});
});
</script>