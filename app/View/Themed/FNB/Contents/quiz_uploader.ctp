<?php 
$content_id = '';
$content_name = '';
$content_desc = '';
if(!empty($content_data))
{
	$content = json_decode($content_data['Content']['data'], 1);
	$content_name = $content['name'];
	$content_desc = $content['desc'];
	$content_id =  $content_data['Content']['id'];
}
?>
<div class="upload-asset-container notes show quiz_uploader_div" data-content-type = "<?php echo QUIZ_TYPE ?>">
	<?php echo $this->Form->input('quiz_name', array('id' => 'quiz_name', 'value' => $content_name,'class' => 'form-control'))?>
	<textarea rows = "2" class = "summernote"><?php echo $content_desc?></textarea>
	<div class="submit-row">
		<?php 
			echo $this->Html->link('Save', '#',array('class' => 'submit_quiz_uploader_text btn btn-success'));
			echo $this->Html->link('Cancel', '#',array('class' => 'cancel_uploader_quiz btn btn-error'));
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

    $('.submit_quiz_uploader_text').click(function(e)
	{
		var lecture_id = $(this).closest('.lectur_li_item').attr('data-lecture');
		var upload_div = $(this);
		e.preventDefault();
		var quiz_name = $('#quiz_name').val();
		var quiz_desc = $('.summernote').code();
		if(!quiz_name || !quiz_desc)
		{
			alert('Please Enter the details');
			return false;
		}
		var quiz_data = {};
		quiz_data['name'] = quiz_name;
		quiz_data['desc'] = quiz_desc;
		$.ajax({
			url: "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'add_content'))?>" + '/' + <?php echo QUIZ_TYPE?>,
			type : "POST",
			data : 'data[lecture_id]=' + lecture_id + '&data[data]=' + encodeURIComponent(JSON.stringify(quiz_data)) + '&data[id]=' + '<?php echo $content_id?>',
			success: function(data)
			{
				data = JSON.parse(data);
				if(data['status'])
				{
					upload_div.closest('.lectur_li_item').find('.add-content').hide();
					upload_div.closest('.content_div_complete').find('.content_show_div').show().html(data['html']);
					upload_div.closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
				} else
				{
					alert('Problem Occured. Please refresh and then try again.')
				}
			}
		});
	});
    $('.cancel_uploader_quiz').click(function(e)
	{
		e.preventDefault();
		var this_container = $(this);
		this_container.closest('.content_div_complete').find('.content_show_div').show();
		this_container.closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
	});

});
</script>