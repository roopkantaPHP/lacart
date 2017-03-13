<div class = "note_upload_screen">
	<?php 
	$content_id = '';
	$content_text = '';
	if(!empty($content_data))
	{
		$content_text = $content_data['Content']['data'];
		$content_id =  $content_data['Content']['id'];
	}
	?>
	<div class="upload-asset-container notes show " data-content-type = "<?php echo NOTE_TYPE ?>">
		<textarea rows = "2" class = "summernote"><?php echo $content_text?></textarea>
		<div class="submit-row">
			<?php 
				echo $this->Html->link('Save', '#',array('class' => 'submit_uploader_note btn btn-success'));
				echo $this->Html->link('Cancel', '#',array('class' => 'cancel_uploader_note btn btn-error'));
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
    $('.submit_uploader_note').click(function(e)
	{
		var lecture_id = $(this).closest('.lectur_li_item').attr('data-lecture');
		var upload_container = $(this).closest('.accordion-content');
		e.preventDefault();
		$.ajax({
			url: "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'add_content'))?>" + '/' + <?php echo NOTE_TYPE?>,
			type : "POST",
			data : 'data[lecture_id]=' + lecture_id + '&data[data]=' + encodeURIComponent($('.summernote').code()) + '&data[id]=' + '<?php echo $content_id?>',
			success: function(data)
			{
				data = JSON.parse(data);
				if(data['status'])
				{
					upload_container.closest('.lectur_li_item').find('.add-content').hide();
					upload_container.closest('.content_div_complete').find('.content_show_div').show().html(data['html']);
					upload_container.closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
				} else
				{
					alert('Problem Occured. Please refresh and then try again.')
				}
			}
		});
	});
    $('.cancel_uploader_note').click(function(e)
	{
		e.preventDefault();
		var this_container = $(this);
		this_container.closest('.content_div_complete').find('.content_show_div').show();
		this_container.closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
	});

});
</script>
</div>