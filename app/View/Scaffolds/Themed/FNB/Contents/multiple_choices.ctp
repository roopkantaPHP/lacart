<?php 
$quiz_ques_id = '';
$anser = '';
$content_desc = '';
$question = '';
$options = array();
if(!empty($content_data))
{
	$question = $content_data['QuizQuestion']['question'];
	$anser = $content_data['QuizQuestion']['answer'];
	$options = json_decode($content_data['QuizQuestion']['options'], 1);
	$quiz_ques_id = $content_data['QuizQuestion']['id'];
}
?>
<div class="upload-asset-container quiz_true_false show">
	Question
	<textarea rows = "2" class = "summernote"><?php echo $question?></textarea>
	Answers :
	<span class="small">
		<a href="#" class="AddMoreFileBox btn btn-primary btn-sm pull-right">Add More Field</a>
	</span>
	<div class="InputsWrapper col-md-12 mt20 mb20">
		<?php
			if(!empty($options)) {
				foreach($options as $option){
		?>
				<div class = "input-group-element" style="float: left;width: 100%;">
					<input type="radio" name="radio" class = "pull-left radio_button" <?php echo ($option == $anser) ? 'checked' : ''?>>
					<input type="text" name="mytext[]" class = "multiple_answers form-control col-md-8" value = "<?php echo $option?>">
					<a href="#" class="removeclass pull-right mt10">×</a>
				</div>
		<?php
				} 
			} else {
		?>
				<div class = "input-group-element" style="float: left;width: 100%;">
					<input type="radio" name="radio" class = "radio_button pull-left">
					<input type="text" name="mytext[]" class = "multiple_answers form-control col-md-8">
					<a href="#" class="removeclass pull-right mt10">×</a>
				</div>
		<?php }?>
	</div>
	<div class="clear"></div>
	<div class="submit-row">
		<?php 
			echo $this->Html->link('Save', '#',array('class' => 'submit_quiz_multiple_choices_false btn btn-success'));
			echo $this->Html->link('Cancel', '#',array('class' => 'cancel_quiz_multiple_choices_false btn btn-error'));
		?>
	</div>
</div>
<script type="text/javascript">
$(function()
{
	var InputsWrapper   = $(".InputsWrapper"); //Input boxes wrapper ID
	$('body').on('click', '.AddMoreFileBox', function (e)  //on add input button click
	{
		e.preventDefault();
		var x = $(this).closest('.quiz_true_false').find(InputsWrapper).find('.input-group-element').length;
		if(x < 6)
		{
			$(this).closest('.quiz_true_false').find(InputsWrapper).append('<div class = "input-group-element" style="float: left;width: 100%;"><input type="radio" name="radio" class = "radio_button pull-left"><input type="text" name="mytext[]" class = "multiple_answers form-control col-md-8"  /><a href="#" class="removeclass pull-right mt10">&times;</a></div>');
		} else
		{
			alert('You can only enter upto six options')
		}
	});
	
	$("body").on("click", ".removeclass" , function(e){ //user click on remove text
		e.preventDefault();
		var x = $(this).closest('.quiz_true_false').find(InputsWrapper).find('.input-group-element').length;
        if( x > 1 ) {
		    $(this).parent('div').remove(); //remove text box
	    	x--; //decrement textbox
	    }
	});
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
    $('body').on('click', '.cancel_quiz_multiple_choices_false', function(e)
	{
		e.preventDefault();
		$(this).closest('.accordion-content').find('.content_show_div').show();
		$(this).closest('.quiz-types-selector').remove();
	});
    
    $('.submit_quiz_multiple_choices_false').click(function(e)
	{
		var this_ele_div = $(this);
		var upload_container = $(this).closest('.upload-asset-container');
		e.preventDefault();
		var quiz_question = upload_container.find('.summernote').code();
		var quiz_options = new Array();
		$('.multiple_answers').each(function(e)
		{
			quiz_options.push($(this).val());
		});
		var quiz_answer = upload_container.find('.radio_button:checked').closest('.input-group-element').find('.multiple_answers').val();
		if(!quiz_question || !quiz_answer || quiz_options.length < 2)
		{
			alert('Please Enter the Complete details');
			return false;
		}
		$.ajax({
			url: "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'add_quiz_content'))?>",
			type : "POST",
			data : 'data[quiz_id]=' + '<?php echo $quiz_id; ?>' + '&data[quiz_type]=' + 'multiple-choice' + '&data[quiz_ques]=' + encodeURIComponent(quiz_question) + '&data[quiz_options]=' + JSON.stringify(quiz_options) + '&data[quiz_answer]=' + quiz_answer + '&data[quiz_ques_id]=' + '<?php echo $quiz_ques_id?>',
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