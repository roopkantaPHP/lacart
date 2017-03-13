	  <section class="dish-ingredient-sec">
		<section class="create-dish-title kitchen-title clearfix">
			<main class="container wdth100">
				<h3 class="fleft">Edit Kitchen </h3> 
				<span class="fright">
				<?php echo $this->Html->link('View Kitchen',array('controller'=>'kitchens','action'=>'index'),array('class'=>'view-kitchen-btn')); ?>
				</span>
			</main>
		</section>
		<ul class="ingredient-title-bar clearfix">
			<li class="active-lft">Kitchen details</li>
			<li>Bank details</li>
		</ul>
		<?php if(isset($errors) && !empty($errors)){
				$errr = '';
				foreach ($errors as $key => $value) {
					$errr .= '<div>'.$value[0].'</div>';
				}
				if(!empty($errr)){ ?>
 					<div class="error_popup">
 						<div class="error_title"><strong>Please make proper entries.</strong></div>
 						<div onclick="close_error()" id="close_error">
 							<?php echo $this->Html->image('cross_grey_small.png',array('height'=>10)); ?>
 						</div>
 						<?php echo $errr; ?>
 						<div style="clear:both;"></div>
 					</div>
				<?php }
			} ?>
		<?php echo $this->Form->create('Kitchen',array('type'=>'file')); ?>
		<div class="tabs-content-area">
		   <!-- Create Dish -->
		   <div class="kitchen-content con-tabs clearfix">
		   	<section class="dish-status clearfix">
		   		<h3 class="ingred-title-bar">Status</h3><br/>
			  	<main class="dishes-status-info">
					<span>By turning the kitchen on,you may receive orders on your dishes, </span>
					<div class="on-off-btn">
					  <?php if(isset($this->request->data['Kitchen']['status']) && !empty($this->request->data['Kitchen']['status']) && $this->request->data['Kitchen']['status']=='off')
					  { ?>
					  	<a class="active"> </a>
					  	<a class="off">OFF</a>	
					  <?php echo $this->Form->input('Kitchen.status',array('type'=>'hidden','value'=>'off')); 
					  	}
					  	else{ ?>
					  	<a class="On">ON</a>
					  	<a class="active"> </a>
					  <?php echo $this->Form->input('Kitchen.status',array('type'=>'hidden','value'=>'on')); 
					   } ?>
					</div>
					
			  	</main>
		   </section>

		    <section class="dish-name clearfix">
		   		<h3 class="ingred-title-bar">Name</h3><br/>
		   		<?php echo $this->Form->input('Kitchen.name',
		   					array('class'=>'kitc-infield','placeholder'=>'Example:Rachel'."'".'s Kitchen','label'=>false,'div'=>false,'required'=>false,'error'=>false)); ?>
		   </section>
		   
		   <section class="dish-name clearfix">
		   		<h3 class="ingred-title-bar">SSN Number</h3><br/>
		   		<?php 
		   			echo $this->Form->input('Kitchen.ssn_no',
		   					array('class'=>'kitc-infield','placeholder'=>'000000000','label'=>false,'div'=>false,'required'=>false,'error'=>false, 'maxlength'=>9));
		   		?>	
		   			
		   </section>

		   <section class="portions clearfix">
		   		<h3 class="ingred-title-bar">Address</h3><br/>
   				<?php echo $this->Form->input('Kitchen.address',
   										array(	'class'=>'kitc-infield',
   												'placeholder'=>'Start typing address',
   												'label'=>false,
   												'div'=>false,
   												'required'=>false,
   												'error'=>false
   												));
   					echo $this->Form->input('Kitchen.lat',array('type'=>'hidden'));
   					echo $this->Form->input('Kitchen.lng',array('type'=>'hidden'));
   					echo $this->Html->link('','#',array('class'=>'address-encounter','onclick'=>'lookup_location();return false'));
   				?>
   			</section>

   			<section class="bank-details clearfix">
				<h3 class="ingred-title-bar">About Kitchen</h3><br/>
   				<ul class="bank-account">
					<li>
						<?php echo $this->Form->textarea('Kitchen.description', array('label'=>false,'div'=>false,'class'=>'kitc-infield count500'));
						$descLen = 0;
						if(isset($this->request->data['Kitchen']['description']) && !empty($this->request->data['Kitchen']['description']))
						$descLen = strlen($this->request->data['Kitchen']['description']);
						
						$descLenShow = 500-$descLen;  ?>
						
						<span class="char-info"><?php echo $descLenShow." characters"; ?></span>
					</li>
				</ul>
		   </section>
		   
		   <section class="photos clearfix">
			   <h3 class="ingred-title-bar">Photos</h3><br/>
			   <ul class="upload-image-sec">
					<?php echo $this->Form->input('UploadImage.name.', array('type' => 'file', 'multiple' => true, 'class'=>'hide')); ?>	
						<li class="img-upload" id="img-upload">
							Upload<br>
							Image
						</li>
					<?php
				   		if(isset($this->request->data['UploadImage']) && !empty($this->request->data['UploadImage'])){
				   			foreach($this->request->data['UploadImage'] as $id => $name){
				   					if(isset($name['name']) && !empty($name['name'])){
										if(FILE_EXISTS(KITCHEN_IMAGE_URL.$name['name'])){ ?>
											<li>
												<div class="delete_images" rel="<?php echo $name['id']; ?>">
						 							<?php echo $this->Html->image('cross-thum.png',array('height'=>15)); ?>
						 						</div>
												<?php 
												$imgName = KITCHEN_IMAGE_FOLDER.$name['name'];
												echo $this->Image->resize($imgName, 150, 150, true);
												?>
											</li> 
										<?php }
									}
				   				}
				   		}
				   ?>
			   </ul>
		   </section>
		   
		   <section class="photos clearfix">
		   		<h3 class="ingred-title-bar">Cover Photo</h3><br/>
		   		<?php echo $this->Form->input('Kitchen.cover_photo', array('type' => 'file', 'style'=>'display:none;','label'=>false)); ?>
	       		<div class="cover-img" id="cover-img"> Upload <div class="clear"></div>
							Image</div>
	       		 <?php 
				   		if(isset($this->request->data['Kitchen']['cover_photo']) && !empty($this->request->data['Kitchen']['cover_photo'])){
				   			?>
				   				<li>
				   					<?php
				   					if(isset($this->request->data['Kitchen']['cover_photo']) && !empty($this->request->data['Kitchen']['cover_photo'])){
										if(FILE_EXISTS(KITCHEN_IMAGE_URL.$this->request->data['Kitchen']['cover_photo'])){
											$imgName = KITCHEN_IMAGE_FOLDER.$this->request->data['Kitchen']['cover_photo'];
											echo $this->Image->resize($imgName, 150, 150, true); 
										}
									}
				   					?>
				   				</li>	
				   		<?php	
				   		}
				   ?>
		   </section>
		   
		   <section class="Commmon-allergens clearfix">
		   		<h3 class="ingred-title-bar"><!--Dinning Options--></h3><br/>
		     	<section class="common-alergen-checkbox">
		      		<!--
		      		<span class="portion-checkbox">
		      			<?php 
		      			$checkDine = false;
		      			if(isset($this->request->data['Kitchen']['dining_dine_in']) && $this->request->data['Kitchen']['dining_dine_in']==1)
		      				$checkDine = true;
		      			echo $this->Form->checkbox('Kitchen.dining_dine_in',array('label'=>false,'div'=>false,'checked'=>$checkDine,'id'=>'dine-in','hiddenField' => false)); ?>
   						<label for="dine-in"><span></span><?php echo $this->Html->image('dine-in.png'); ?><br/><b>Dine-in</b></label>
					</span>
					<span class="portion-checkbox">
						<?php 
						$checkTake = false;
		      			if(isset($this->request->data['Kitchen']['dining_take_out']) && $this->request->data['Kitchen']['dining_take_out']==1)
		      				$checkTake = true;
		      			
						echo $this->Form->checkbox('Kitchen.dining_take_out',array('label'=>false,'div'=>false,'checked'=>$checkTake,'id'=>'take-out','hiddenField' => false)); ?>
   					    <label for="take-out"><span></span><?php echo $this->Html->image('icon-2.png'); ?><br/><b>Take-out</b></label>
					</span>
					<br/>
					-->
					<div><?php echo $this->Html->link('Next','#',array('class'=>'btn-next','id'=>'gotoNext')); ?></div>
				</section>
		   	</section>
		   </div>	
		   <div class="bankdetail-tab con-tabs" style="display:none;">
			 <div class="container">
				<section class="dish-ingredient-sec">
				    <section class="bank-details clearfix">
						<div class="clearfix"></div>
						<ul class="bank-account">
							<li>
								<label class="lable"> Bank account number</label>
								<?php echo $this->Form->input('User.bank_acc_no', array('label'=>false,'div'=>false,'class'=>'kitc-infield numeric','autocomplete'=>"off")); ?>
							</li>
							<br/>
							<li>
								<label class="lable">Routing number</label>
								<?php echo $this->Form->input('User.bank_routing_no', array('label'=>false,'div'=>false,'class'=>'kitc-infield','autocomplete'=>"off")); ?>
							</li>
							<br/>
							<li>
								<label class="lable">Account holder's name</label>
								<?php echo $this->Form->input('User.bank_acc_holdername', array('label'=>false,'div'=>false,'class'=>'kitc-infield','autocomplete'=>"off")); ?>
							</li>
							<li>
								<label class="lable">Bank Account type</label>
								<?php echo $this->Form->input('User.bank_acc_type', array('class'=>'community-select','empty'=>'Select Account type','label'=>false,'options'=>array(0=>'Checking',1=>'Saving'))); ?>
							</li>
							<li>
								<?php echo $this->Form->submit('Save',array('class'=>'btn-next')); ?>
							</li>
						</ul>
				   </section>
				</section>
			 </div>
		   </div>
		   <?php echo $this->Form->end(); ?>
       </div>
	  </section>
<?php echo $this->Html->script('geoPosition')?>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>

<script>
	// This example displays an address form, using the autocomplete feature
	// of the Google Places API to help users fill in the information.

	var placeSearch, autocomplete;
	
	$(document).ready(function(){
		initializeAutoComplete();

		if($('#KitchenStatus').val()=='off')
		{
			$('#UserBankAccNo').attr('required',false);
			$('#UserBankRoutingNo').attr('required',false);
			$('#UserBankAccHoldername').attr('required',false);
			$('#UserBankAccType').attr('required',false);
		}
	});

	function initializeAutoComplete() {
	  // Create the autocomplete object, restricting the search
	  // to geographical location types.
	  autocomplete = new google.maps.places.Autocomplete(
	      /** @type {HTMLInputElement} */(document.getElementById('KitchenAddress')),
	      { types: ['geocode'] });
	  // When the user selects an address from the dropdown,
	  // populate the address fields in the form.
	  google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    fillInAddress();
	  });
	}

	function fillInAddress() {
	  // Get the place details from the autocomplete object.
	  var place = autocomplete.getPlace();
	}

	// Bias the autocomplete object to the user's geographical location,
	// as supplied by the browser's 'navigator.geolocation' object.
	function geolocate() {
	  if (navigator.geolocation) {
	    navigator.geolocation.getCurrentPosition(function(position) {
	      var geolocation = new google.maps.LatLng(
	          position.coords.latitude, position.coords.longitude);
	      var circle = new google.maps.Circle({
	        center: geolocation,
	        radius: position.coords.accuracy
	      });
	      autocomplete.setBounds(circle.getBounds());
	    });
	  }
	}
	$(document).ready(function(){
		$('.delete_images').click(function(){
			var id = $(this).attr('rel');
			if (confirm('Are you sure you want to delete this image?')) {
				$.ajax({
					'url':'<?php echo $this->Html->url(array('controller'=>'kitchens','action'=>'deleteimage')); ?>',
					'type':'post',
					'data':{'id':id, 'type':'kitchen'},
					'success':function(data){
						if(data==1){
							$('.delete_images[rel="'+id+'"]').parent('li').remove();
						}
						else{
							alert('Sorry, Image deletion failed please try again.');
						}
					}
				});
			}
		});
		$('#gotoNext').click(function(){
				$('.kitchen-content').hide();
				$('.bankdetail-tab').show();
				$('.ingredient-title-bar li:first').removeClass('active-lft').addClass('disactive-lft');
				$('.ingredient-title-bar li:last').removeClass('disactive-lft').addClass('active-lft');
		});

		$('.ingredient-title-bar li:first').click(function(){
			if($('.bankdetail-tab').is(':visible')){
				$('.kitchen-content').show();
				$('.bankdetail-tab').hide();
				$('.ingredient-title-bar li:last').removeClass('active-lft').addClass('disactive-lft');
				$('.ingredient-title-bar li:first').removeClass('disactive-lft').addClass('active-lft');
			}
		});

		$('.ingredient-title-bar li:last').click(function(){
			if($('.kitchen-content').is(':visible')){
				$('.kitchen-content').hide();
				$('.bankdetail-tab').show();
				$('.ingredient-title-bar li:first').removeClass('active-lft').addClass('disactive-lft');
				$('.ingredient-title-bar li:last').removeClass('disactive-lft').addClass('active-lft');
			}
		});
		
		$('#img-upload').click(function(){
			$('#UploadImageName').click();
		});

		$('.cover-img').click(function(){
			$('#KitchenCoverPhoto').click();
		});

		$('.on-off-btn').click(function(){ 
			var len = $(this).find('.On').length;
			
			if(len){
				$('.on-off-btn').find('.On').remove();
				$('.on-off-btn').append("<a class='off'>OFF</a>");
				$('#KitchenStatus').val('off');
				$('#UserBankAccNo').attr('required',false);
				$('#UserBankRoutingNo').attr('required',false);
				$('#UserBankAccHoldername').attr('required',false);
				$('#UserBankAccType').attr('required',false);
			}
			else{
				$('.on-off-btn').find('.off').remove();
				$('.on-off-btn').prepend("<a class='On'>ON</a>");
				$('#KitchenStatus').val('on');
				$('#UserBankAccNo').attr('required',true);
				$('#UserBankRoutingNo').attr('required',true);
				$('#UserBankAccHoldername').attr('required',true);
				$('#UserBankAccType').attr('required',true);
			}
		});

		$("#UploadImageName").change(function (evt) {
			   $("#img-upload").parent('ul').find('img.thumb').parent('li').remove();
	    	   handleFileMultiple(evt);
	    });

	    $("#KitchenCoverPhoto").change(function (evt) {
	    	   $(".cover-img").parent('section').find('img.thumb').remove();
	    	   handleFileSelect(evt);
	    });
	});
	
	function handleFileMultiple(evt) {
		var files = evt.target.files; // FileList object

	    // Loop through the FileList and render image files as thumbnails.
	    for (var i = 0, f; f = files[i]; i++) {

	      // Only process image files.
	      if (!f.type.match('image.*')) {
	        continue;
	      }

	      var reader = new FileReader();

	      // Closure to capture the file information.
	      reader.onload = (function(theFile) {
	        return function(e) {
	          // Render thumbnail.
	          var li = document.createElement('li');
	          li.innerHTML = ['<img class="thumb" src="', e.target.result,
	                            '" title="', escape(theFile.name), '"/>'].join('');
	          $("#img-upload").parent('ul').append(li);
	        };
	      })(f);

	      // Read in the image file as a data URL.
	      reader.readAsDataURL(f);
	    }
	  }

	function handleFileSelect(evt) {
		var files = evt.target.files; // FileList object

	    // Loop through the FileList and render image files as thumbnails.
	    for (var i = 0, f; f = files[i]; i++) {

	      // Only process image files.
	      if (!f.type.match('image.*')) {
	        continue;
	      }

	      var reader = new FileReader();

	      // Closure to capture the file information.
	      reader.onload = (function(theFile) {
	        return function(e) {
	          // Render thumbnail.
	           $(".cover-img").parent('section').find('li').remove();
	          var li = document.createElement('li');
	          li.innerHTML = ['<img height="150" class="thumb" src="', e.target.result,
	                            '" title="', escape(theFile.name), '"/>'].join('');
	          $(".cover-img").parent('section').append(li);
	        };
	      })(f);

	      // Read in the image file as a data URL.
	      reader.readAsDataURL(f);
	    }
	  } 

	function lookup_location() { 
	  geoPosition.getCurrentPosition(show_map, show_map_error);
	}
	
	function show_map(loc) {
		codeLatLng(loc.coords.latitude,loc.coords.longitude);
	}
	
	function show_map_error() {
	  $("#live-geolocation").html('Unable to determine your location.');
	}
	
	$(function() {
	  if (geoPosition.init()) {
	    
	  } else {
	    
	  }
	});
	
	
	var geocoder;
	var map;
	var infowindow = new google.maps.InfoWindow();
	var marker;
	function initialize() {
	  geocoder = new google.maps.Geocoder();
	}
	
	function codeLatLng(lat, lng) {
	  var latlng = new google.maps.LatLng(lat, lng);
	  geocoder.geocode({'latLng': latlng}, function(results, status) {
	    if (status == google.maps.GeocoderStatus.OK) {
	    	if (results[1]) {
		    	$('#KitchenAddress').val(results[0].formatted_address);
		    	$('#KitchenLat').val(lat);
		    	$('#KitchenLng').val(lng);
		    }
	
	    } else {
	      alert('Geocoder failed due to: ' + status);
	    }
	  });
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);

</script>	
