<?php //pr($this->request->data); ?>
 <section class="create-dish-sec">
		  <section class="create-dish-title clearfix">
		    <main class="container wdth100">
			    <h3 class="fleft"><?php echo "Edit Profile"; ?></h3> 
			</main>
		  </section>
		  <section class="community-sec clearfix">
		  <?php echo $this->Form->create('User', array('type' => 'file')); ?>
			<main class="discussion-container">
				<ul>
					<li>
						<?php echo $this->Form->input('name',array('class'=>'disc-field'));  ?>
					</li>
					<li>
						<?php echo $this->Form->input('state_id',array('class'=>'community-select','empty'=>'Select State','options'=>$states));  ?>
					</li>
					<li>
						<?php echo $this->Form->input('city_id',array('class'=>'community-select','empty'=>'Select City'));  ?>
					</li>
					<li>
						<lable><?php echo "Profile photo"; ?></lable>
						<div class="uploaded-img">
							<?php
							$imgName = $this->Common->getProfileImage($this->request->data);
							echo $this->Image->resize($imgName, 150, 150, true); 
							?>
						</div>
						<div class="select-file">
								<?php echo $this->Form->file('image',array('escape'=>false));
									  echo "upload/change image";
								?>
						</div>
					</li>
					<li class="textbox">
						<?php echo $this->Form->textarea('description',array('class'=>'community-select count200'));
						$descLen = 0;
						if(isset($this->request->data['User']['description']) && !empty($this->request->data['User']['description']))
						$descLen = strlen($this->request->data['User']['description']);
						
						$descLenShow = 200-$descLen;  ?>
						
						 <span class="char-info"><?php echo $descLenShow." characters"; ?></span>
						<?php echo $this->Form->submit('Save',array('class'=>'comment-sub-btn'));  ?>
					</li>
				</ul>			
		    	<div class="clearfix"></div>
			</main>
          <?php echo $this->Form->end(); ?>
	      </section>
      </section>
<script>
$(document).ready(function () {
	
	<?php if(isset($this->request->data['User']['state_id']) && !empty($this->request->data['User']['state_id'])){ ?>
			setCityOption($("#UserStateId").val());
	<?php } ?>
	
    $("#UserImage").change(function () {
        $(".uploaded-img").html("");
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
        if (regex.test($(this).val().toLowerCase())) {
            if (typeof (FileReader) != "undefined") {
                    $(".uploaded-img").show();
                    $(".uploaded-img").append("<img height='100'/>");
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $(".uploaded-img img").attr("src", e.target.result);
                    }
                    reader.readAsDataURL($(this)[0].files[0]);
                } else {
                    alert("This browser does not support FileReader.");
                }
        } else {
            alert("Please upload a valid image file.");
        }
    });
    
    
    $("#UserStateId").change(function(){
		var stateId = $(this).val();
		if(stateId != ''){
			setCityOption(stateId);
		}
	});
});

function setCityOption(id){
		$.ajax({
				'url':'<?php echo $this->Html->url(array('controller'=>'users','action'=>'getCityOptions')); ?>/'+id,
				'success': function(output) {
					$('#UserCityId').html(output);
					<?php if(isset($this->request->data['User']['city_id']) && !empty($this->request->data['User']['city_id'])){  ?>
						$("#UserCityId").val(<?php echo $this->request->data['User']['city_id']; ?>);
					<?php 	} ?>
				}
			});
}
</script>
