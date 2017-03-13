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
<section class="inner-middle-content no-pd brdr-tp mt5 video_upload_screen <?php echo $randomString?>">
	<div class="video-section">
		<div class="col-xs-3 no-pd"> <!-- required for floating -->
		    <!-- Nav tabs -->
		    <ul class="nav nav-tabs tabs-left no-brdr" >
		      <li></li>
		      <li class = "active"><a href="#upload-video" data-toggle="tab">Upload Video</a></li>
		      <li><a href="#vimeo_upload" data-toggle="tab">Imbed Youtube/Vimeo Video</a></li>
		    </ul>
		</div>
		<div class="col-xs-9 brdr-left">
		    <!-- Tab panes -->
		    <div class="tab-content">
		      <div class="tab-pane"></div>
		      <div class="tab-pane active" id="upload-video">
		      	<h3> Add Video </h3>
		      	<div class="col-md-12 no-pd">
					<span class="btn black-btn col-sm-4 fileinput-button video-upload-button">
				        <i class="glyphicon glyphicon-plus"></i>
				        <span>Upload File</span>
				        <!-- The file input field used as target for the file upload widget -->
				        <input id="fileupload" type="file" name="file_data" accept="video/*">
				    </span>	
					<div id="progress" class="progress">
				        <div class="progress-bar progress-bar-success"></div>
				    </div>
				    <div class="clear"></div>
				    <div id="files" class="files"></div>
				    <div class="clear"></div>	
					<p class="mt20"> <strong> Tip : </strong> Video is Udemy's preferred delivery type. At least 60% of your course 
								content should be high resolution video (720p or HD) with excellent audio and lighting. Please note that the average video length is within 2-10 minutes, and videos above 20 minutes long will not be approved.</p>
				</div>
		      </div>
		      <div class="tab-pane" id="vimeo_upload">
		      	<div class="col-md-12 no-pd mt40 mb40">
					<input class="form-control col-md-8" placeholder="Paste the link here" type="text">	
					<button class="btn black-btn col-sm-3 vimeo_video_upload" type="button">Upload Video</button>	
					<div class="clear"></div>
				</div>
		      	
		      </div>
		    </div>
		</div>  
		<div class="clear"></div>
	</div>
</section>
<style>
.tabs-left{
  border-bottom: none !important;
  padding-top: 2px;
}
.tabs-left > li {
margin-left: 5px;
border-bottom: none !important;
}
.tabs-left {
  border-right: 1px solid #ddd;
}
.tabs-left>li {
  float: none;
  margin-bottom: 2px;
}
.tabs-left>li {
  margin-right: -1px;
}
.tabs-left>li.active>a,
.tabs-left>li.active>a:hover,
.tabs-left>li.active>a:focus {
  border-bottom-color: #ddd;
  border-right-color: transparent;
}
.tabs-left>li>a {
  border-radius: 4px 0 0 4px;
  margin-right: 0;
  display:block;
}
.progress{display:none}
</style>
<!--Subscribe section Ends Here-->
<script>
$(function () {
	var upload_url = '';
	var mycars = [];
	var TICKET_DATA = '';
	var FILE_NAME = ''
	$('#fileupload').change(function(e)
	{
		var f;
        f = e.target.files || [{name: this.value}];
        FILE_NAME = f[0]['name'];
        mycars.push(f[0]);
		$.ajax({
				url: "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'upload_video'))?>" + '/' + '1',
				success: function(data)
				{
					data = JSON.parse(data);
					if(data['stat'] == 'ok')
					{
						upload_url = data['ticket']['endpoint'];
						TICKET_DATA = data['ticket']['id'];
						$('#fileupload').fileupload('send', {files: mycars, url: upload_url});	
					} else
					{
						alert('Server failure')
					}
				}
			});
	})
	
	var regYoutube = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
	var regVimeo = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
	function check_url(url) {
	    if(regYoutube.test(url)) {
	        return 'youtube';
	    }else if(regVimeo.test(url)) {
	        return 'vimeo';
	    }else{
	        return false;
	    }
	}
	
	function getEmbeddedPlayer(url, height, width){
		var output = false;
		var yout_reg = new RegExp('(?:https?://)?(?:www\\.)?(?:youtu\\.be/|youtube\\.com(?:/embed/|/v/|/watch\\?v=))([\\w-]{10,12})', 'g');
//get matches found for the regular expression
		var youtubeUrl = yout_reg.exec(url);
		var vimeoUrl = url.match(/^.+vimeo.com\/(.*\/)?([^#\?]*)/);
		if( youtubeUrl ){
			var id =  youtubeUrl ? youtubeUrl[1] : null;
			output = '<iframe src="http://www.youtube.com/embed/'+ id+'?rel=0" height="' + height + '" width="' + width + '" allowfullscreen="" frameborder="0"></iframe>'
		}
		if( vimeoUrl ){
			var id =  vimeoUrl ? vimeoUrl[2] || vimeoUrl[1] : null;
			if(id)
			{
				output = '<iframe src="//player.vimeo.com/video/'+ id+'?rel=0" height="' + height + '" width="' + width + '" allowfullscreen="" frameborder="0"></iframe>'
			}else
			{
				return false;
			}
		}
		return output;
	}
	
	$('.vimeo_video_upload'). click(function(e)
	{
		e.preventDefault();
		var this_upload = $(this);
		var url_value = this_upload.closest('#vimeo_upload').find('input[type="text"]').val();
		var aaaa = encodeURI(url_value)
		var lecture_id = this_upload.closest('.lectur_li_item').attr('data-lecture');
		if(url_value == '')
		{
			alert('Please Enter the url to upload');
		} else
		{
			if(video_source = check_url(url_value))
			{
				$.ajax({
					url: "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'video_upload_url'))?>",
					type:'POST',
					data : 'data[Content][lecture_id]=' + lecture_id + '&data[Content][data]=' + encodeURIComponent(url_value) + '&data[Content][source]=' + video_source + '&data[Content][id]=' + '<?php echo $content_id?>', 
					success: function(data)
					{
						data = JSON.parse(data);
						if(data['status'])
						{
							this_upload.closest('.lectur_li_item').find('.add-content').remove();
							var width = 320;
							var height = 200;
							var url = data['url'];
							var output = getEmbeddedPlayer(url, height, width);
							var content_id = data['data']['Content']['id'];
							output = '<div class="col-md-1 no-pd pull-right gry"><button class="content-edit-btn edit-btn" data-attr-id="' + content_id + '" data-attr-type="' + <?php echo VIDEO_TYPE ?> + '">  </button></div>' + output;
							if(typeof(data['data']['Lecture']['info']) != 'undefined' && (data['data']['Lecture']['info'] == null || data['data']['Lecture']['info'] == ''))
							{
								output += '<div class="clear"></div><div class = "information_div"><a class = "add_info" href = "#">ADD INFO</a></div>';
							} else
							{
								output += '<div class="clear"></div><div class = "information_div"><p>' + data['data']['Lecture']['info'] + '</p><a class = "add_info" href = "#">Edit INFO</a></div>';
							}
							this_upload.closest('.content_div_complete').find('.content_show_div').html(output).show();
							this_upload.closest('.content_div_complete').find('.content_show_div').find('iframe').show();
							this_upload.closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
						} else
						{
							alert('Couldnot save. Please refresh and then try again.')
						}
					}
				});
			} else
			{
				alert('Invalid Url');
			}
		}
	})
	
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'upload_content'))?>';
    var random_class = '<?php echo $randomString?>';
    $('#fileupload').fileupload({
        autoUpload: false,
        maxNumberOfFiles: 1,
        acceptFileTypes: /(\.|\/)(mp4|mov|3gp|avi|mpeg|mpg|ogv|ogm|ogg|webm|mkv|wmv|rm|flv|vob)$/i,
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: true
    }).on('fileuploadadd', function (e, data) {
    	$('.' + random_class + '').closest('.lectur_li_item').find('#files').html('');
    	$('.' + random_class + '').closest('.lectur_li_item').find('.progress').show();
    	$('.' + random_class + '').closest('.lectur_li_item').find('.video-upload-button').hide();
    	$('.' + random_class + '').closest('.lectur_li_item').find('.add-content').hide();
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
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploadprocessfail', function (e, data) {
    	$('.' + random_class + '').closest('.lectur_li_item').find('.video-upload-button').show();
    	$('.' + random_class + '').closest('.lectur_li_item').find('.progress').hide();
	}).on('fileuploaddone', function (e, data) {
		var lecture_id = $('.' + random_class + '').closest('.lectur_li_item').attr('data-lecture');
		$.ajax({
    		type:'POST',
			url: "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'upload_video'))?>" + '/' + 2,
			data:'filename=' + FILE_NAME + '&ticket=' + TICKET_DATA + '&content_id=' + "<?php echo $content_id?>" + '&lecture_id=' + lecture_id + '&duration=' + data.files[0]['preview']['duration'],
			success: function(data)
			{
				data = JSON.parse(data);
				content_id = data['data']['Content']['id'];
				if(data['status'])
				{
					$('.' + random_class + '').closest('.lectur_li_item').find('.add-content').remove();
					var html_div = '<div class="col-md-1 no-pd pull-right gry"><button class="content-edit-btn edit-btn" data-attr-id="' + content_id + '" data-attr-type="1">  </button></div>';
					html_div += '<iframe style = "display:inline-block" src="//player.vimeo.com/video/' +  data['video_id'] + '?rel=0" height="240" width="320" allowfullscreen="" frameborder="0"></iframe>';
					if(typeof(data['data']['Lecture']['info']) != 'undefined' && (data['data']['Lecture']['info'] == null || data['data']['Lecture']['info'] == ''))
					{
						html_div += '<div class="clear"></div><div class = "information_div"><a class = "add_info" href = "#">ADD INFO</a></div>';
					} else
					{
						html_div += '<div class="clear"></div><div class = "information_div"><p>' + data['data']['Lecture']['info'] + '</p><a class = "add_info" href = "#">Edit INFO</a></div>';
					}
					$('.' + random_class + '').closest('.content_div_complete').find('.content_show_div').html(html_div).show();
					$('.' + random_class + '').closest('.content_div_complete').find('.edit_content_blank_div').html('').hide();
				}
			}
		});
    }).on('fileuploadfail', function (e, data) {
		$('.' + random_class + '').closest('.lectur_li_item').find('.video-upload-button').show();
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