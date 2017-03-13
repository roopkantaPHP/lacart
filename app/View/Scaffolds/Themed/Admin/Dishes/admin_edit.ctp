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
if((!empty($this->request->data['Dish']['serve_start_time']) && !empty($this->request->data['Dish']['serve_end_time'])))
{ 
    $h1 = date("H", strtotime($this->request->data['Dish']['serve_start_time']));
    $m1 = date("i", strtotime($this->request->data['Dish']['serve_start_time']));

    $h2 = date("H", strtotime($this->request->data['Dish']['serve_end_time']));
    $m2 = date("i", strtotime($this->request->data['Dish']['serve_end_time']));
    
    $time_selected1 = ($h1*60)+$m1+1110;
    $time_selected2 = ($h2*60)+$m2+1110;
    $time_span = $time_selected1.', '.$time_selected2;
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
<script type="text/javascript">
    
    $(document).ready(function (){

         $("#whenslider").timeslider({
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
                        max: 2553, 
                        values: [<?php echo $time_span; ?>],
                        step:5
                    },
                    timeDisplay: '#limittime',
                    //submitButton: '#schedule-submit1',
                    clickSubmit: function (e){
                    },
                });
    });
</script>
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
<div class="row">
	<div class="floatleft mtop10"><h1><?php echo __("Edit Dish"); ?></h1></div>
	<div class="floatright">
		<?php echo $this->Html->link('<span>Back to Kitchen</span>', array('controller' => 'dishes', 'action' => 'dish_list',$this->request->data['Dish']['kitchen_id']), array('class' => 'black_btn', 'escape' => false)) ?>
	</div>
</div>
<div align="center" class="greybox mtop15">
	<?php 	echo $this->Form->create('Dish', array('novalidate' => true)); ?>
	<?php 	echo $this->Form->hidden('id');
			echo $this->Form->hidden('kitchen_id');
	 ?>
		<table cellspacing="0" cellpadding="7" border="0" align="center">
			<tr>
				<td valign="middle"><strong class="upper">Dish Name:</strong></td>
				<td><?php echo $this->Form->input('name', array('div'=>false, 'label'=>false, 'class'=>'input required', 'size'=>'70')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Allergy:</strong></td>
				<td><?php echo $this->Form->input('allergens', array('options'=> $allergies,'multiple' =>true,'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Diet:</strong></td>
				<td><?php echo $this->Form->input('diet', array('options'=> array('Vegetarian'=>'Vegetarian','Non Vegetarian'=>'Non Vegetarian','Vegan'=>'Vegan'),'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Cuisine:</strong></td>
				<td><?php echo $this->Form->input('cuisine', array('options'=> $cuisines,'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
				<td valign="top"><strong class="upper">Portion:</strong></td>
				<td class="allPortions">
					<span class="">
                        <?php echo $this->Form->checkbox('Dish.p_small',array('div'=>false,'checked'=>$checkSmallPortion,'hiddenField' => false)); ?>
                       <label for="DishPSmall"><span></span>Make it budget</label>
                    </span>
                    <section class="price-info <?php echo $classSmallPortion; ?>" id="forSmall">
                        <span class="price">price <br/><?php echo $smallPrice; ?></span>
                        <span class="Qty">Quantity <br/>
                            <?php echo $this->Form->input('Dish.p_small_quantity',array('class'=>'qty-field numeric','label'=>false,'div'=>false));
                                echo $this->Form->input('Dish.p_small_price',array('type'=>'hidden','label'=>false,'div'=>false,'value'=>$smallPriceValue)); 
                                echo $this->Form->input('Dish.p_small_unit',array('class'=>'oz','options'=>$smallOptions,'label'=>false,'div'=>false)); ?>
                        </span>
                    </section>
                    <div class="clearfix"></div>
                    <span class="">
                        <?php echo $this->Form->checkbox('Dish.p_big',array('div'=>false,'checked'=>$checkBigPortion,'hiddenField' => false)); ?>
                       <label for="DishPBig"><span></span>Premium</label>
                    </span>
                    <section class="price-info <?php echo $classBigPortion; ?>" id="forBig">
                        <span class="price">price <br/><?php echo $bigPrice; ?></span>
                        <span class="Qty">Quantity <br/>
                            <?php echo $this->Form->input('Dish.p_big_quantity',array('class'=>'qty-field numeric','label'=>false,'div'=>false));
                                echo $this->Form->input('Dish.p_big_price',array('type'=>'hidden','label'=>false,'div'=>false,'value'=>$bigPriceValue));
                             ?>
                            <?php echo $this->Form->input('Dish.p_big_unit',array('class'=>'oz','options'=>$bigOptions,'label'=>false,'div'=>false)); ?>
                        </span>
                    </section>
                    <div class="clearfix"></div>           
                    
                	<span class="">
                	    <?php echo $this->Form->checkbox('Dish.p_custom',array('div'=>false,'checked'=>$checkCustomPortion,'hiddenField' => false)); ?>
                       <label for="DishPCustom"><span></span>Custom</label>
                   </span>
                	<section class="price-info <?php echo $classCustomPortion; ?>"  id="forCustom">
                    	<span class="price">price <br/>
                            <?php echo $this->Form->input('Dish.p_custom_price',array('class'=>'qty-field numeric','label'=>false,'div'=>false)); ?>
                        </span>
                    	<span class="Qty">Quantity <br/>
                            <?php echo $this->Form->input('Dish.p_custom_quantity',array('class'=>'qty-field numeric','label'=>false,'div'=>false)); ?>
                            <?php echo $this->Form->input('Dish.p_custom_unit',array('class'=>'oz','options'=>$bigOptions,'label'=>false,'div'=>false)); ?>
                            <br/>
                    	</span>
                    	<span class="description">Description <br/>
                    	   <input type="text" class="Description-field"> 
                    	</span>
                	   <div class="clearfix"></div>
                	</section>
				</td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Serve Time:</strong></td>
				<td>
					 <main class="dishes-status-info">
                        <b id="limittime"></b>
                         <?php echo $this->Form->input('Dish.serve_time',array('type'=>'hidden','value'=>$serveTimeRange)); ?>
                        <div class="clearfix"></div>
                        <span>
                            <div>
                                <div id="whensliderLimit" class="lightGray"></div>
                            </div>
                        </span>
                    </main>
				</td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Lead Time:</strong></td>
				<td>
					<main class="dishes-status-info">
                        <b id="loadtime"></b>
                         <?php echo $this->Form->input('Dish.lead_time',array('type'=>'hidden','value'=>1));?>
                        <div class="clearfix"></div>
                        <span>
                            <div>
                                <div id="whenslider" class="lightGray"></div>
                            </div>
                        </span>
                    </main>
				</td>
			</tr>
			<tr>
				<td valign="middle"><strong class="upper">Repeat:</strong></td>
				<td>
					<main class="dishes-status-info">
                        <span class="diet-radio-btn">
                            <input type="radio" name="data[Dish][repeat]" id="never" value="0">
                            <label for="never"><span></span>Never</label>
                        </span>
                        <span class="diet-radio-btn">
                            <input type="radio" name="data[Dish][repeat]" id="Repeat-on" value="1" checked >
                            <label for="Repeat-on"><span></span>Repeat on</label>
                        </span>
                        <?php
                            $checkClass = '';
                            if(isset($this->request->data['Dish']['repeat']) && empty($this->request->data['Dish']['repeat'])){
                                $checkClass = 'hide';
                            }
                            else if(!empty($this->request->data['Dish']['repeat'])){
                                $repeatDay = explode(',', $this->request->data['Dish']['repeat']);
                                $sundayCheck = $mondayCheck = $tuesdayCheck = $wednesdayCheck = $thursdayCheck = $fridayCheck = $saturdayCheck = '';
                                foreach ($repeatDay as $dKey => $dayData) {
                                    if($dayData == 'sunday')
                                        $sundayCheck = true;
                                    else if($dayData == 'monday')
                                        $mondayCheck = true;
                                    else if($dayData == 'tuesday')
                                        $tuesdayCheck = true;
                                    else if($dayData == 'wednesday')
                                        $wednesdayCheck = true;
                                    else if($dayData == 'thursday')
                                        $thursdayCheck = true;
                                    else if($dayData == 'friday')
                                        $fridayCheck = true;
                                    else if($dayData == 'saturday')
                                        $saturdayCheck = true;
                                }
                            }
                        ?>    
                        <div class="weekend-info margin_topbottom <?php echo $checkClass; ?>">
                            <span>
                                <?php echo $this->Form->checkbox('Dish.Day.sunday',array('div'=>false,'checked'=>$sundayCheck,'hiddenField' => false)); ?>
                                <label for="DishDaySunday"><span>sun</span></label>
                            </span>
                            <span>
                                <?php echo $this->Form->checkbox('Dish.Day.monday',array('div'=>false,'checked'=>$mondayCheck,'hiddenField' => false)); ?>
                                <label for="DishDayMonday"><span>Mon</span></label>
                            </span>
                            <span>
                                <?php echo $this->Form->checkbox('Dish.Day.tuesday',array('div'=>false,'checked'=>$tuesdayCheck,'hiddenField' => false)); ?>
                                <label for="DishDayTuesday"><span>Tue</span></label>
                            </span>
                            <span>
                                <?php echo $this->Form->checkbox('Dish.Day.wednesday',array('div'=>false,'checked'=>$wednesdayCheck,'hiddenField' => false)); ?>
                                <label for="DishDayWednesday"><span>Wed</span></label>
                            </span>
                            <span>
                                <?php echo $this->Form->checkbox('Dish.Day.thursday',array('div'=>false,'checked'=>$thursdayCheck,'hiddenField' => false)); ?>
                                <label for="DishDayThursday"><span>Thu</span></label>
                            </span>
                            <span>
                                <?php echo $this->Form->checkbox('Dish.Day.friday',array('div'=>false,'checked'=>$fridayCheck,'hiddenField' => false)); ?>
                                <label for="DishDayFriday"><span>Fri</span></label>
                            </span>
                            <span>
                                <?php echo $this->Form->checkbox('Dish.Day.saturday',array('div'=>false,'checked'=>$saturdayCheck,'hiddenField' => false)); ?>
                                <label for="DishDaySaturday"><span>Sat</span></label>
                            </span>
                        </div>
                    </main>
				</td>
			</tr>
			



			<tr>
				<td valign="middle"><strong class="upper">Status:</strong></td>
				<td><?php echo $this->Form->input('status', array('options'=> array('on'=>'On','off'=>'Off'),'empty' =>'','div'=>false, 'label'=>false, 'class'=>'input required')); ?></td>
			</tr>
			<tr>
            	<td>&nbsp;</td>
				<td>
					<div class="floatleft">
						<input type="submit" class="submit_btn" value="">
					</div>
					<div class="floatleft" id="domain_loader" style="padding-left:5px;"></div>
				</td>
			</tr>
		</table>
	<?php echo $this->Form->end();?>
</div>
<script>
	$('#UserAdminEditUserForm').validate();
	$(document).ready(function(){
        $('#whensliderLimit').mouseout(function(){
            $('#DishServeTime').val($('#limittime').html());
        });

        $('#whenslider').mouseout(function(){ 
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
</script>
