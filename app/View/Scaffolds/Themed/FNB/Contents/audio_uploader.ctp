<?php 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
	$content_id = '';
	if(!empty($content_data))
	{
		$content_id =  $content_data['Content']['id'];
	}
?>
<div class="col-md-12 right-section-content no-brdr <?php echo $randomString?>">
	<h3> Add Audio</h3>
	<div class="col-md-12 no-pd">
		<span class="btn black-btn col-sm-3 fileinput-button audio-upload-button">
			<input class="form-control col-md-4 wide50" placeholder="Use file no larger than 1.0 GB." type="text">
	        <i class="glyphicon glyphicon-plus"></i>
	        <span>Upload File</span>
	        <!-- The file input field used as target for the file upload widget -->
	        <input id="fileupload" type="file" name="files" accept="audio/*">
	    </span>	
		<div id="progress" class="progress">
	        <div class="progress-bar progress-bar-success"></div>
	    </div>
	    <div class="clear"></div>
	    <div id="files" class="files"></div>
	    <div class="clear"></div>	
		<p class="mt20"> <strong> Tip : </strong> A presentation means slides (e.g. PowerPoint, Keynote). Slides are a great way to combine text and visuals to explain concepts in an effective and efficient way. Use meaningful graphics and clearly legible text!</p>
	</div>
</div>
<style>
	.progress{display:none}
</style>
<script>
$(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'upload_content'))?>';
     var random_class = '<?php echo $randomString?>';
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        autoUpload: true,
        maxNumberOfFiles: 1,
        acceptFileTypes: /(\.|\/)(mp3|wav|aiff)$/i,
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: true
    }).on('fileuploaddestroy', function (e, data) {
		$('.' + random_class + '').closest('.lectur_li_item').find('.audio-upload-button').show();
    }).on('fileuploadadd', function (e, data) {
		$('.' + random_class + '').closest('.lectur_li_item').find('.audio-upload-button').hide();
    	$('.' + random_class + '').closest('.lectur_li_item').find('.add-content').hide();
    	$('.' + random_class + '').closest('.lectur_li_item').find('.progress').show();
        data.context = $('<div/>').appendTo('#files');
        $.each(data.files, function (index, file) {
            var node = $('<p/>')
                    .append($('<span/>').text(file.name));
            if (!index) {
                node
                    .append('<br>');
            }
            node.appendTo(data.context);
        });
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                .prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Upload')
                .prop('disabled', !!data.files.error);
        }
    }).on('fileuploadsubmit', function (e, data) {
    	var $this = $(this);
    	var audioElement = new Audio($(data['files'][0]['preview']).attr('src'));
    	audioElement.load();
	    audioElement.addEventListener("loadedmetadata", function(_event) {
	    	$('.' + random_class + '').closest('.lectur_li_item').find('.audio-upload-button').hide();
    		$('.' + random_class + '').closest('.lectur_li_item').find('.add-content').hide();
	    	data.formData = {FILE_DURATION:audioElement.duration, CONTENT_ID:'<?php echo $content_id?>', TYPE:'<?php echo AUDIO_TYPE ?>',COURSE_ID:COURSE_ID, LECTURE_ID:$('#fileupload').closest('.lectur_li_item').attr('data-lecture')};
	    	data.jqXHR = $this.fileupload('send', data);
		});
		 return false;
	}).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploadprocessfail', function (e, data) {
    	$('.' + random_class + '').closest('.lectur_li_item').find('.audio-upload-button').show();
    	$('.' + random_class + '').closest('.lectur_li_item').find('.progress').hide();
	}).on('fileuploaddone', function (e, data) {
    	var html_div = '<div class="col-md-1 no-pd pull-right gry"><button class="content-edit-btn edit-btn" data-attr-id="' + data.result['content_data']['Content']['id'] + '" data-attr-type="2"></button></div><p> ' + data.result['files'][0]['name'] + '</p><audio src="' + data.result['files'][0]['url'] + '" controls=""></audio>';
    	if(data.result['content_data']['Lecture']['info'] == null)
		{
			html_div += '<div class="clear"></div><div class = "information_div"><a class = "add_info" href = "#">ADD INFO</a></div>';
		} else
		{
			html_div += '<div class="clear"></div><div class = "information_div"><p>' + data.result['content_data']['Lecture']['info'] + '</p><a class = "add_info" href = "#">Edit INFO</a></div>';
		}
    	$('.' + random_class + '').closest('.content_div_complete').find('.content_show_div').html(html_div).show();
		$('.' + random_class + '').closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
    }).on('fileuploadfail', function (e, data) {
    	$('.' + random_class + '').closest('.lectur_li_item').find('.audio-upload-button').show();
    	$('.' + random_class + '').closest('.lectur_li_item').find('.progress').hide();
        $.each(data.files, function (index, file) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
</script>