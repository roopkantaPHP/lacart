<script>
	$(document).ready(function(){
		if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(savePosition, positionError, {timeout:10000});
      } else {
          alert("We are unable to fetch your location, Please update your address in profile section.");
      }
	});
 
  // handle the error here
  function positionError(error) {
      var errorCode = error.code;
      var message = error.message;

      alert(message);
  }

  function savePosition(position)
  {
  	$('#RequestLat').val(position.coords.latitude);
  	$('#RequestLng').val(position.coords.longitude);
  }
  </script>
 <section class="create-dish-sec">
		  <section class="create-dish-title clearfix">
		    <main class="container wdth100">
			    <h3 class="fleft"><?php echo "New Request"; ?></h3> 
			</main>
		  </section>
		  <section class="community-sec clearfix">
		  	<?php 
		  		echo $this->Form->create('Request');
		  		echo $this->Form->input('Request.lat',array('type'=>'hidden'));
		  		echo $this->Form->input('Request.lng',array('type'=>'hidden'));
		   	?>
			<main class="discussion-container">
				<ul>
					<li>
						<?php echo $this->Form->input('Request.dish_name',array('class'=>'disc-field'));  ?>
					</li>
					<li>
						<?php echo $this->Form->input('Request.cuisine_id',array('class'=>'community-select','empty'=>'Select Cuisine','options'=>$cuisines));  ?>
					</li>
					<li class="textbox">
						<label>Message</label>
						<?php echo $this->Form->textarea('Request.message',array('class'=>'community-select count200'));
						$descLen = 0;
						if(isset($this->request->data['Request']['message']) && !empty($this->request->data['Request']['message']))
						$descLen = strlen($this->request->data['Request']['message']);
						
						$descLenShow = 200-$descLen;  ?>
						
						 <span class="char-info"><?php echo $descLenShow." characters"; ?></span>
					</li>
					<li>
						<label>Allergies</label>
						<section class="common-alergen-checkbox">
							<?php
								$dishAllergy = array();
								if(isset($kitchenDetails['Request']['allergies']) && !empty($kitchenDetails['Request']['allergies']))
									$dishAllergy = explode(',',$kitchenDetails['Request']['allergies']);

								
								if(isset($allergies) && !empty($allergies)){
									foreach ($allergies as $aId => $allergyValue) {
										$checkDine = false;
										if(in_array($allergyValue, $dishAllergy))
											$checkDine = true;
									 ?>
										<span class="portion-checkbox">
											<?php echo $this->Form->checkbox('Request.allergy.'.$allergyValue,array('id'=>$allergyValue,'label'=>false,'div'=>false,'checked'=>$checkDine,'hiddenField' => false)); ?>
											<label for="<?php echo $allergyValue; ?>"><span></span><?php echo $allergyValue; ?></label>
										</span>			
									<?php }
								}
							?>
							</section>
					</li>
				</ul>			
		    	<div class="clearfix"></div>
		    	<?php echo $this->Form->submit('Save',array('class'=>'btn-next')); ?>
			</main>
          <?php echo $this->Form->end(); ?>
	      </section>
      </section>