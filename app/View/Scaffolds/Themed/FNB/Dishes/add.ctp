<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css"> 
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<?php echo $this->Html->script('jquery.ui.timeslider');

$time_selected = 1170;
if(isset($this->request->data['Dish']['lead_time']) && !empty($this->request->data['Dish']['lead_time']))
{
    $time_selected = ($this->request->data['Dish']['lead_time']*60)+1110;
}

$time_span = "900, 1500";
$serveTimeRange = "12:00 AM - 6:30 AM";
if(!empty($this->request->data['Dish']['serve_time']))
{
    $serviceTimeArray = explode('-',str_replace(' ','',strtolower($this->request->data['Dish']['serve_time'])));

    $h1 = date("H", strtotime($serviceTimeArray[0]));
    $m1 = date("i", strtotime($serviceTimeArray[0]));

    $h2 = date("H", strtotime($serviceTimeArray[1]));
    $m2 = date("i", strtotime($serviceTimeArray[1]));

    $time_selected1 = ($h1*60)+$m1+1110;
    $time_selected2 = ($h2*60)+$m2+1110;
    $time_span = $time_selected1.', '.$time_selected2;
}
?>
<script type="text/javascript">
    
    $(document).ready(function (){

         $("#whenslider1").timeslider({
                    sliderOptions: {
                        orientation: 'horizonatal',
                        min: 1110, 
                        max: 1830, 
                        value: <?php echo $time_selected?>,
                        step:1,
                        range: "min",
                        hours: true
                    },
                    timeDisplay: '#loadtime',
                    //submitButton: '#schedule-submit1',
                    clickSubmit: function (e){
                       
                    },
                });

         $("#whensliderLimit").timeslider({
                    sliderOptions: {
                        range: true, 
                        min: 1110, 
                        max: 2550, 
                        values: [<?php echo $time_span; ?>],
                        step:1
                    },
                    timeDisplay: '#limittime',
                    //submitButton: '#schedule-submit1',
                    clickSubmit: function (e){
                    },
                });
    });
</script>
<?php
    $checkNonVeg = $checkVeg = $checkVegan = false;

    if(isset($this->request->data['Dish']['diet']) && !empty($this->request->data['Dish']['diet'])){
        if($this->request->data['Dish']['diet'] == 'Non-Vegetarian')
            $checkNonVeg = true;
        else if($this->request->data['Dish']['diet'] == 'Vegetarian')
            $checkVeg = true;
        else
            $checkVegan = true;
    }
    else{
        $checkNonVeg = true;
    }    

    $checkSmallPortion = $checkBigPortion = $checkCustomPortion = false;
    $classSmallPortion = $classBigPortion = $classCustomPortion = 'hide';
    if(isset($this->request->data['Dish']['p_small']) && !empty($this->request->data['Dish']['p_small'])){
        $checkSmallPortion = true;
        $classSmallPortion = '';
    }
    
    if(isset($this->request->data['Dish']['p_big']) && !empty($this->request->data['Dish']['p_big'])){
        $checkBigPortion = true;
        $classBigPortion = '';
    }
    
    if(isset($this->request->data['Dish']['p_custom']) && !empty($this->request->data['Dish']['p_custom'])){
        $checkCustomPortion = true;
        $classCustomPortion = '';
    }

    if($checkSmallPortion == false && $checkBigPortion == false && $checkCustomPortion == false){
        $checkSmallPortion = true;
        $classSmallPortion = '';
    }

    $smallOptions = $bigOptions = array();
    $smallPrice = $bigPrice = '';
    foreach ($portions as $pKey => $pValue)
    {
        if($pValue['Portion']['type'] == 'small')
        { 
            if(empty($smallPrice)){
                $smallPrice = "<small priceVal='".$pValue['Portion']['price']."' rel='".$pValue['Portion']['unit']."''>$ ".$pValue['Portion']['price']."</small>";
                $smallPriceValue = $pValue['Portion']['price'];
            }
            else
                $smallPrice .= "<small class='hide' priceVal='".$pValue['Portion']['price']."' rel='".$pValue['Portion']['unit']."''>$ ".$pValue['Portion']['price']."</small>";
            
            $smallOptions[$pValue['Portion']['unit']] = $pValue['Portion']['unit'];            
        }
        else
        {
            if(empty($bigPrice)){
                $bigPrice = "<small priceVal='".$pValue['Portion']['price']."' rel='".$pValue['Portion']['unit']."''>$ ".$pValue['Portion']['price']."</small>";
                $bigPriceValue = $pValue['Portion']['price'];
            }
            else
                $bigPrice .= "<small class='hide' priceVal='".$pValue['Portion']['price']."' rel='".$pValue['Portion']['unit']."''>$ ".$pValue['Portion']['price']."</small>";
            
            $bigOptions[$pValue['Portion']['unit']] = $pValue['Portion']['unit'];                        
        }
    }
?>
<section class="dish-ingredient-sec">
	<section class="create-dish-title kitchen-title clearfix">
		<main class="container wdth100">
			<h3 class="fleft">Add Dish </h3> 
		</main>
	</section>

	<ul class="ingredient-title-bar clearfix">
		<li class="active-lft">1</li>
		<li>2</li>
	</ul>

	<?php 
     if(isset($errors) && !empty($errors)){
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

	<?php echo $this->Form->create('Dish',array('type'=>'file','novalidate'=>true)); ?>
	<div class="tabs-content-area">
    	<!-- Create Dish -->
    	<div class="kitchen-content con-tabs clearfix">
    	 	<section class="dish-name clearfix">
        	   <h3 class="ingred-title-bar">Dish name</h3><br/>
               <?php echo $this->Form->input('Dish.name',
                            array('class'=>'kitc-infield','label'=>false,'div'=>false,'required'=>false,'error'=>false)); ?>
        	</section>
        	<section class="diet clearfix">
        	    <h3 class="ingred-title-bar">Diet</h3><br/>
            	<section class="common-alergen-checkbox alldiets">
                    <span class="portion-checkbox">
                        <?php echo $this->Form->checkbox('Dish.diet.Non-Vegetarian',array('id'=>'nveg-diet','label'=>false,'div'=>false,'checked'=>$checkNonVeg,'hiddenField' => false)); ?>
                        <label for="nveg-diet"><span></span>Non Vegetarian</label>
                    </span>
                    <span class="portion-checkbox">
                        <?php echo $this->Form->checkbox('Dish.diet.Vegetarian',array('id'=>'veg-diet','label'=>false,'div'=>false,'checked'=>$checkVeg,'hiddenField' => false)); ?>
                        <label for="veg-diet"><span></span>Vegetarian</label>
                    </span>
                    <span class="portion-checkbox">
                        <?php echo $this->Form->checkbox('Dish.diet.Vegan',array('id'=>'vegan-diet','label'=>false,'div'=>false,'checked'=>$checkVegan,'hiddenField' => false)); ?>
                        <label for="vegan-diet"><span></span>Vegan</label>
                    </span>
                </section>

        	</section>

        	<section class="portions clearfix allPortions">
            	<h3 class="ingred-title-bar">Portions</h3>
            	<section class="portion-inner-div">
                    <span class="portion-checkbox">
                        <?php echo $this->Form->checkbox('Dish.p_small',array('div'=>false,'checked'=>$checkSmallPortion,'hiddenField' => false)); ?>
                       <label style="width:60%;" for="DishPSmall"><span></span>Make it budget</label>
                    </span>
                    <section class="price-info <?php echo $classSmallPortion; ?>" id="forSmall">
                        <span class="price">price <br/><?php echo $smallPrice; ?></span>
                        <span class="Qty">Quantity <br/>
                            <?php echo $this->Form->input('Dish.p_small_quantity',array('class'=>'qty-field numeric','type' => 'number',
  'min' => 1,'label'=>false,'div'=>false));
                                echo $this->Form->input('Dish.p_small_price',array('type'=>'hidden','label'=>false,'div'=>false,'value'=>$smallPriceValue)); 
                                echo $this->Form->input('Dish.p_small_unit',array('class'=>'oz','options'=>$smallOptions,'label'=>false,'div'=>false)); ?>
                        </span>
                    </section>
                    <div class="clearfix"></div>
                    <span class="portion-checkbox">
                        <?php echo $this->Form->checkbox('Dish.p_big',array('div'=>false,'checked'=>$checkBigPortion,'hiddenField' => false)); ?>
                       <label for="DishPBig"><span></span>Premium</label>
                    </span>
                    <section class="price-info <?php echo $classBigPortion; ?>" id="forBig">
                        <span class="price">price <br/><?php echo $bigPrice; ?></span>
                        <span class="Qty">Quantity <br/>
                            <?php echo $this->Form->input('Dish.p_big_quantity',array('class'=>'qty-field numeric','type' => 'number',
  'min' => 1, 'label'=>false,'div'=>false));
                                echo $this->Form->input('Dish.p_big_price',array('type'=>'hidden','label'=>false,'div'=>false,'value'=>$bigPriceValue));
                             ?>
                            <?php echo $this->Form->input('Dish.p_big_unit',array('class'=>'oz','options'=>$bigOptions,'label'=>false,'div'=>false)); ?>
                        </span>
                    </section>
                    <div class="clearfix"></div>           
                    
                	<span class="portion-checkbox">
                	    <?php echo $this->Form->checkbox('Dish.p_custom',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                       <label for="DishPCustom"><span></span>Custom</label>
                   </span>
                	<section class="price-info <?php echo $classCustomPortion; ?>"  id="forCustom">
                    	<span class="price">price <br/>
                            <?php echo $this->Form->input('Dish.p_custom_price',array('class'=>'qty-field numeric','label'=>false,'div'=>false)); ?>
                        </span>
                    	<span class="Qty">Quantity <br/>
                            <?php echo $this->Form->input('Dish.p_custom_quantity',array('class'=>'qty-field numeric','type' => 'number',
  'min' => 1, 'label'=>false,'div'=>false)); ?>
                            <?php echo $this->Form->input('Dish.p_custom_unit',array('class'=>'oz','options'=>$bigOptions,'label'=>false,'div'=>false)); ?>
                            <br/>
                    	</span>
                    	<span class="description">Reason <br/>
                    	   <input type="text" class="Description-field"> 
                    	</span>
                	   <div class="clearfix"></div>
                	   <b>The custom pricing will be approved by our management before it become available.this typically take 30 minutes.</b>
                	</section>
            	</section>
        	</section>

            <section class="photos clearfix">
               <h3 class="ingred-title-bar">Photos</h3><br/>
               <ul class="upload-image-sec">
                    <?php echo $this->Form->input('UploadImage.name.', array('type' => 'file', 'multiple' => true, 'class'=>'hide')); ?>    
                   <li class="img-upload" id="img-upload">Upload <br/> Image</li>
                   <?php
                        if(isset($this->request->data['UploadImage']) && !empty($this->request->data['UploadImage'])){
                            foreach($this->request->data['UploadImage'] as $id => $name){
                                    if(isset($name['name']) && !empty($name['name'])){
                                        if(FILE_EXISTS(DISH_IMAGE_URL.$name['name'])){ ?>
                                            <li>
                                            <div class="delete_images" rel="<?php echo $name['id']; ?>">
                                                <?php echo $this->Html->image('cross-thum.png',array('height'=>15)); ?>
                                            </div>
                                            <?php 
                                            $imgName = DISH_IMAGE_FOLDER.$name['name'];
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

            <section class="dish-name clearfix">
                <h3 class="ingred-title-bar">Cuisine</h3><br/>
                <main class="discussion-container">
                    <?php echo $this->Form->input('Dish.cuisine',
                            array('class'=>'community-select','label'=>false,'div'=>false, 'options'=>$cuisines)); ?>
                </main>
            </section>

        	<section class="Commmon-allergens clearfix">
        	   <h3 class="ingred-title-bar">Commmon allergens</h3><br/>
            	<section class="common-alergen-checkbox">
                    <?php
                        $dishAllergy = array();
                        if(isset($this->request->data['Dish']['allergens']) && !empty($this->request->data['Dish']['allergens']))
                            $dishAllergy = explode('::::::::',$this->request->data['Dish']['allergens']);

                        if(isset($allergies) && !empty($allergies)){
                            foreach ($allergies as $aId => $allergyValue) {
                                $checkDine = false;
                                if(in_array($allergyValue, $dishAllergy))
                                    $checkDine = true;
                             ?>
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.allergens.'.$allergyValue,array('id'=>$allergyValue,'label'=>false,'div'=>false,'checked'=>$checkDine,'hiddenField' => false)); ?>
                                    <label for="<?php echo $allergyValue; ?>"><span></span><?php echo $allergyValue; ?></label>
                                </span>         
                            <?php }
                        }
                    ?>
                    
                    <span class="portion-checkbox">
                        <?php echo $this->Form->checkbox('Dish.allergens.other',array('id'=>'other','label'=>false,'div'=>false,'hiddenField' => false)); ?>
                        <label for="other"><span></span>others</label>
                    </span>
                    <br>
                    <?php echo $this->Form->input('Dish.other_allergy_text',array('div'=>false,'label'=>false,'class'=>'kitc-infield hide','placeholder'=>"Type your allergy")); ?>

                <div><?php echo $this->Html->link('Next','#',array('class'=>'btn-next','id'=>'gotoNext')); ?></div>
                </section>
            </section>


        </div>

        <div class="bankdetail-tab con-tabs hide">
            <section class="dish-ingredient-sec">
                <section class="bank-details clearfix">
                    <section class="dish-status clearfix">
                        <h3 class="ingred-title-bar">Status</h3><br/>
                        <main class="dishes-status-info">
                            <span>By turning the dish on,you may receive orders on the dish, </span>
                            <div class="on-off-btn">
                              <?php if(isset($this->request->data['Dish']['status']) && !empty($this->request->data['Dish']['status']) && $this->request->data['Dish']['status']=='off'){ ?>
                                <a class="active"> </a>
                                <a class="off">OFF</a>      
                              <?php }
                                else{ ?>
                                <a class="On">ON</a>
                                <a class="active"> </a>
                              <?php } ?>
                            </div>
                            <?php echo $this->Form->input('Dish.status',array('type'=>'hidden','value'=>'on')); ?>
                        </main>
                    </section>

                    <section class="serving-time clearfix">
                        <h3 class="ingred-title-bar">Serving time</h3><br/>
                        <main class="dishes-status-info">
                            <span>Adjust the slider to set your dish serve start time and serve end time.  </span><br/>
                            <b id="limittime"></b>
                             <?php echo $this->Form->input('Dish.serve_time',array('class'=>'hide','label'=>false,'value'=>$serveTimeRange)); ?>
                            <div class="clearfix"></div>
                            <span>
                                <div>
                                    <div id="whensliderLimit" class="lightGray"></div>
                                </div>
                            </span>
                        </main>
                    </section>

                    <section class="loadtime clearfix">
                        <h3 class="ingred-title-bar">Lead time</h3><br/>
                        <main class="dishes-status-info">
                            <span>Adjust the slider to set your lead time for this dish. </span><br/>
                            <b id="loadtime"></b>
                             <?php echo $this->Form->input('Dish.lead_time',array('class'=>'hide','label'=>false,'value'=>1));?>
                            <div class="clearfix"></div>
                            <span>
                                <div>
                                    <div id="whenslider1" class="lightGray"></div>
                                </div>
                            </span>
                        </main>
                    </section>

                    <section class="Repeat clearfix">
                     <?php
                        $checkClass = 'hide';
                        $checkedNever = "checked";
                        $checkedRepeat = ""; 
                        if(isset($this->request->data['Dish']['repeat']) && $this->request->data['Dish']['repeat']){
                            $checkClass = '';
                            $checkedNever = "";
                            $checkedRepeat = "checked"; 
                        }
                    ?>  
                        <h3 class="ingred-title-bar">Repeat</h3><br/>
                        <main class="dishes-status-info">
                            <span class="diet-radio-btn">
                                <input type="radio" name="data[Dish][repeat]" id="never" value="0" <?php echo $checkedNever; ?> >
                                <label for="never"><span></span>Never</label>
                            </span>
                            <span class="diet-radio-btn">
                                <input type="radio" name="data[Dish][repeat]" id="Repeat-on" value="1" <?php echo $checkedRepeat; ?>>
                                <label for="Repeat-on"><span></span>Repeat on</label>
                            </span>
                             
                            <div class="weekend-info <?php echo $checkClass; ?>">
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.Day.sunday',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                                    <label for="DishDaySunday"><span>sun</span></label>
                                </span>
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.Day.monday',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                                    <label for="DishDayMonday"><span>Mon</span></label>
                                </span>
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.Day.tuesday',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                                    <label for="DishDayTuesday"><span>Tue</span></label>
                                </span>
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.Day.wednesday',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                                    <label for="DishDayWednesday"><span>Wed</span></label>
                                </span>
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.Day.thursday',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                                    <label for="DishDayThursday"><span>Thu</span></label>
                                </span>
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.Day.friday',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                                    <label for="DishDayFriday"><span>Fri</span></label>
                                </span>
                                <span class="portion-checkbox">
                                    <?php echo $this->Form->checkbox('Dish.Day.saturday',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                                    <label for="DishDaySaturday"><span>Sat</span></label>
                                </span>
                            </div>
                            <br/>
                            <?php echo $this->Form->submit('Save',array('class'=>'btn-next')); ?>
                        </main>
                    </section>
                </section>
            </section>    
        </div>
    </div>    
    <?php echo $this->Form->end(); ?>
</section>
<script>
	$(document).ready(function(){
        $('#whensliderLimit').mouseout(function(){
            $('#DishServeTime').val($('#limittime').html());
        });

        $('#whenslider1').mouseout(function(){ 
            var leadTime = parseInt($('#loadtime').html().replace(' hours'));
            
            $('#DishLeadTime').val(leadTime);
        });

        $('.Qty select').change(function(){
            var id = $(this).attr('id');
            var val = $(this).val();
            if(id == 'DishPSmallUnit' || id == 'DishPBigUnit'){
                $(this).parent('span').siblings('.price').find('small').hide();
                var priceVal = $(this).parent('span').siblings('.price').find('small[rel="'+val+'"]').attr('priceVal');
                $(this).parent('span').siblings('.price').find('small[rel="'+val+'"]').show();
                $(this).siblings('input[type="hidden"]').attr('value',priceVal);
            }
        });
        $('#other').change(function(){
            if($(this).is(':checked')){
                $('#KitchenOtherAllergyText').show();
            }else{
                $('#KitchenOtherAllergyText').hide();
            }
        });

        $(".alldiets input:checkbox").change(function() {  
            var currentCheck = this;
            $(".alldiets input:checkbox").each(function() {
                if($(this).is(':checked') && $(currentCheck).attr('id') != $(this).attr('id')){
                    $(this).attr('checked',false);
                    $(this).parent('span').removeClass('selected');
                }
            });
        });

        $(".allPortions input:checkbox").change(function() {  

           var id = $(this).attr('id');
           if(id=='DishPSmall'){
                if($(this).is(':checked'))
                    $('#forSmall').show();
                else
                    $('#forSmall').hide();
           }
           else if(id=='DishPBig'){
                if($(this).is(':checked'))
                    $('#forBig').show();
                else
                    $('#forBig').hide();
           }
           else if(id=='DishPCustom'){
                if($(this).is(':checked'))
                    $('#forCustom').show();
                else
                    $('#forCustom').hide();
           }
        });

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

		$('.on-off-btn').click(function(){ 
			var len = $(this).find('.On').length;
			
			if(len){
				$('.on-off-btn').find('.On').remove();
				$('.on-off-btn').append("<a class='off'>OFF</a>");
				$('#DishStatus').val('off');
			}
			else{
				$('.on-off-btn').find('.off').remove();
				$('.on-off-btn').prepend("<a class='On'>ON</a>");
				$('#DishStatus').val('on');
			}
		});

		$("#UploadImageName").change(function (evt) {
			   $("#img-upload").parent('ul').find('img.thumb').parent('li').remove();
	    	   handleFileMultiple(evt);
	    });

        $('.diet-radio-btn input[type="radio"]').change(function(){
            if($(this).val()==0)
            {
                $('.weekend-info').hide();
            }
            else
            {
                $('.weekend-info').show();   
            }
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
</script>	
