<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css"> 
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<?php
echo $this->Html->script('masonry.pkgd.min');
echo $this->Html->script('jquery.ui.timeslider'); ?>
<?php 
$time_selected = 1500;
if(!empty($this->params['named']['t']))
{
    $h = date("H", strtotime($this->params['named']['t']));
    $m = date("i", strtotime($this->params['named']['t']));
    $time_selected = ($h*60)+$m+1110;
}
?>
<script type="text/javascript">
    
    $(document).ready(function (){
    	/*$("#whenslider1").timeslider({
            sliderOptions: {
                orientation: 'horizonatal',
		        min: 1110, 
		        max: 2550, 
                value: <?php echo $time_selected?>,
		        step:1,
		        range: "min",
            },
            timeDisplay: '#whentime1',
            //submitButton: '#schedule-submit1',
            clickSubmit: function (e){
               
            },
            timeInput: '#SearchLat'
	    });*/
    });
</script>
	 <div class="row search-filters-section">
	 	<div class="row filters-header">
		 	<div class="container">
		 		<div class="row filters-group" >
		 		<?php
                $popularCheck = '';
                $ratingCheck = '';
                $cuisineArray = array();
                $dineArray = array();
                $diteArray = array();
                $date = '';
                
                if(!empty($this->params['named'])) { ?>
		 			<ul class="row">
		 				<?php
                        foreach ($this->params['named'] as $k=>$param) {
                        if($k!='lat' && $k!='lng')
                        { ?>
		 				<li> <?php echo $param?><a href="<?php echo $this->Common->getSearchUrl($k,false)?>"> <?php echo $this->Html->image('cancel.png')?> </a></li>
                        <?php
                        }
                        if($k=='date')
                            $date = $param;
                        elseif ($k=='cu') 
                            $cuisineArray = explode(',', $param);
                        elseif ($k=='diet')
                            $diteArray = explode(',', $param);
                        elseif ($k=='dine')
                            $dineArray = explode(',', $param);
                        elseif ($k=='o_popular')
                            $popularCheck = "checked=true";
                        elseif ($k=='o_rating')
                            $ratingCheck = "checked=true";
                        elseif($k=='q')
                            echo $this->Form->hidden('key',array('id'=>'SearchKey','value'=>$param));
                        elseif($k=='loc')  
                            echo $this->Form->hidden('location', array('id'=>'SearchLocation','value'=>$param));
                        elseif($k=='lat')  
                            echo $this->Form->hidden('lat', array('id'=>'SearchLat','value'=>$param));
                        elseif($k=='lng')  
                            echo $this->Form->hidden('lng', array('id'=>'SearchLng','value'=>$param));
                        ?>
                       <?php }?>
		 			</ul>
		 		<?php }?>
		 			<a href="javascript:void(0);" id="show-filters" onClick="commonJs.openClosePopup('#filterpopup')"> <?php echo $this->Html->image('filters.png')?> Filters </a>
		 			<div class="filter-box sl-popup" id="filterpopup">
    					<div class="row sort-by">
    						<div class="filter-header">
    							<h4>Sort By</h4>
    						</div>
                            <div class="filter-body">
                                <ul>
                                    <li class="width45">
                                        <ul>
                                            <li>
                                                <span class="portion-checkbox">
                                                    <?php echo $this->Form->checkbox('popular',array('id'=>'SearchPopular','label'=>false,'div'=>false,'name'=>'popular','hiddenField' => false, $popularCheck)); ?>
                                                </span> 
                                            </li>
                                            <li>
                                                <label for="SearchPopular">
                                                    <a class="filter-icon">
                                                       <?php echo $this->Html->image('popular.png'); ?>
                                                    </a>
                                                    <h4>Popular</h4>
                                                </label>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="width45">
                                        <ul>
                                            <li>
                                                <span class="portion-checkbox">
                                                    <?php echo $this->Form->checkbox('rating',array('id'=>'SearchRating','label'=>false,'div'=>false,'name'=>'rating','hiddenField' => false, $ratingCheck)); ?>
                                                </span> 
                                            </li>
                                            <li>
                                                <label for="SearchRating">
                                                    <a class="filter-icon">
                                                        <?php echo $this->Html->image('ratings.png'); ?>
                                                    </a>
                                                    <h4>Rating</h4>
                                                </label>  
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
    					</div>
    					<div class="row cuisine diet">
    						<div class="filter-header">
    							<h4>cuisine</h4>
    						</div>
    						<div class="filter-body">
    							<a href="javascript:void(0);" class="all-cuisnies" onClick="searchJs.toggleFilter(this);">
    								All Cuisines 
    								<?php echo $this->Html->image('caret-down.png')?>
    							</a>
	    							<ul class="box">
	    								<?php
                                        foreach ($cuisines as $cuisine) 
                                        {
                                            $cuChecked = '';
                                            if(in_array(strtolower($cuisine),$cuisineArray))
                                                $cuChecked = "checked=true";
                                                
                                         ?>
	    								<li>
                                        <span class="portion-checkbox width100">
                                            <?php echo $this->Form->checkbox('rating',array('id'=>'SearchCuisine'.$cuisine,'label'=>false,'div'=>false,'name'=>'cuisine[]','hiddenField' => false, 'class' => 'cuisines', 'value'=>$cuisine, $cuChecked)); ?>
                                            <label for="<?php echo 'SearchCuisine'.$cuisine; ?>">
                                                <h4> <?php echo $cuisine; ?></h4>
                                            </label> 
                                        </span>
                                        </li>
                                       <?php }?>
	    							</ul>
    						</div>
    					</div>
    					
                        <!--
                        <div class="row dining">
    						<div class="filter-header">
    							<h4>Dining Options</h4>
    						</div>
    						<div class="filter-body dining">
    					       <ul>
    								<li>
    									<ul>
                                            <li>
                                                <span class="portion-checkbox">
                                                    <?php
                                                    $dineChecked = '';
                                                    if(in_array('dinein',$dineArray))
                                                        $dineChecked = "checked=true";

                                                    echo $this->Form->checkbox('dining_dine_in',array('id'=>'SearchDineIn','label'=>false,'div'=>false,'name'=>'dining_dine_in','hiddenField' => false,'value' => 'dinein', 'class'=>'dine', $dineChecked)); ?>
                                                </span> 
                                            </li>
                                            <li>
                                                 <label for="SearchDineIn">
                                                    <a class="filter-icon">
                                                        <?php echo $this->Html->image('dine-in.png'); ?>
                                                    </a>
                                                    <h4>Dine in</h4>
                                                </label>
                                            </li>
                                       </ul>
    								</li>
    								<li>
    									<ul>
                                            <li>
                                                <span class="portion-checkbox">
                                                    <?php
                                                   $takeoutChecked = '';
                                                    if(in_array('takeout',$dineArray))
                                                        $takeoutChecked = "checked=true";
                                                    echo $this->Form->checkbox('dining_take_out',array('id'=>'SearchTakeOut','label'=>false,'div'=>false,'name'=>'dining_take_out','hiddenField' => false,'value' => 'takeout', 'class'=>'dine', $takeoutChecked)); ?>
                                                </span> 
                                            </li>
                                            <li>
                                                <label for="SearchTakeOut">
                                                    <a class="filter-icon">
                                                        <?php echo $this->Html->image('take-away.png'); ?>
                                                    </a>
                                                    <h4>Take Out</h4>
                                                </label>  
                                            </li>
                                         <?php
                                            
                                            ?>
    									</ul>
    								</li>
    							</ul>
                            </div>
    					</div>
                        -->
                            
    					<div class="row diet">
    						<div class="filter-header">
    							<h4>Diet</h4>
    						</div>
    						<div class="filter-body">
    							<ul>
                                 <?php
                                    $nonvegChecked = '';
                                    $vegChecked = '';
                                    $veganChecked = '';
                                    if(in_array('non-vegetarian',$diteArray))
                                        $nonvegChecked = "checked=true";
                                    if(in_array('vegetarian',$diteArray))
                                        $vegChecked = "checked=true";
                                    if(in_array('vegan',$diteArray))
                                        $veganChecked = "checked=true";
                                    ?>
    								<li> 
                                        <span class="portion-checkbox width100">
                                            <?php
                                            echo $this->Form->checkbox('diet[]',array('id'=>'SearchDietNonveg','label'=>false,'div'=>false,'name'=>'diet[]','hiddenField' => false,'value' => 'Non-vegetarian', 'class'=>'diets', $nonvegChecked));
                                            ?>
                                            <label for="SearchDietNonveg">
                                                <h4> Non-vegetarian </h4>
                                            </label>
                                        </span> 
                                    </li> 

                                    <li> 
                                        <span class="portion-checkbox width100">
                                            <?php
                                            echo $this->Form->checkbox('diet[]',array('id'=>'SearchDietVeg','label'=>false,'div'=>false,'name'=>'diet[]','hiddenField' => false,'value' => 'Vegetarian', 'class'=>'diets', $vegChecked));
                                            ?>
                                            <label for="SearchDietVeg">
                                                <h4> Vegetarian </h4>
                                            </label> 
                                        </span> 
                                    </li>  


                                    <li> 
                                        <span class="portion-checkbox width100">
                                            <?php
                                            echo $this->Form->checkbox('diet[]',array('id'=>'SearchDietVegan','label'=>false,'div'=>false,'name'=>'diet[]','hiddenField' => false,'value' => 'Vegan', 'class'=>'diets', $veganChecked));
                                            ?>
                                            <label for="SearchDietVegan">
                                                <h4> Vegan </h4>
                                            </label> 
                                        </span> 
                                    </li>
    							</ul>
    						</div>
    					</div>
    					<div class="row date-time">
    						<div class="filter-header">
    							<h4>Date and Time</h4>
    						</div>
    						<div class="filter-body calander">
    							<div class="cal-container"> 
    								<a class="left-arrow prev-day"> <?php echo "<"; ?> </a>
    									<?php 
                                            if(isset($date) && !empty($date))
                                                $showThis = date('F d, Y',strtotime($date));
                                            else
                                                $showThis = date('F d, Y');
                                        ?>
    									<div class="middle-date floatLeft" id="divdatpicker" ><?php echo $showThis; ?></div>
    									<input type="text" disabled="disabled" id="hidedatepicker" value="<?php  echo $showThis; ?>" style="width: 0px !important;">
    								<a class="right-arrow next-day"> <?php echo ">"; ?> </a>
    							</div>
    							<div id="whenslider1"></div>
								<div id="whentime1"></div>
    						</div>
    					</div>
    					<?php echo $this->Form->hidden('',array('id'=>'search_key_url','value'=>$this->Common->getSearchUrl('t','S_SEARCHTIME/date:S_SEARCHDATE/cu:S_SEARCHCUISINE/diet:S_SEARCHDIET/dine:S_SEARCHDINE/o_popular:S_SORTPOPULAR/o_rating:S_SORTRATING',true))); ///cry:S_SEARCHRG ?>
    					<a href="javascript:void(0)" onClick='searchJs.submitSearch();return false;' class="select-filters">Done</a>
    				</div>
		 		</div>
		 	</div>
	 	</div>

	 	<div class="row filter-results">
	 		<div class="container">
	 			<div class="result-count">
	 				<?php echo $this->Paginator->counter('{:count}')?> results
	 			</div>
	 		</div>
	 	</div>

		<div class="row filter-container">
			<div class="container">
				<ul class="row results" id="container">
				<?php foreach ($results as $result) { ?>
                <a href="<?php echo $this->Html->url(array('controller'=>'kitchens','action'=>'index',$result['Kitchen']['id'])); ?>">
				    <li class="pictureItem" rel="<?php echo $result['Kitchen']['id']?>">
						<div class="result-upper">
							<figure>
								<?php
                                $imgName = $this->Common->getKitchenImage($result);
								echo $this->Image->resize($imgName, 100, 100, true); 
                                 ?>
							</figure>
							<div class="figure-content">
								<h3><?php echo $result['Kitchen']['name']?></h3>
								<!--<h4><?php echo $result['Kitchen']['address']?></h4>-->
								<?php echo $this->Common->getRatingIcon($result['Kitchen']['avg_rating']); ?>
                                <!--
                                <ul class="search-icons">
                                <?php if(isset($kitchenDetails['Kitchen']['dining_dine_in']) && $kitchenDetails['Kitchen']['dining_dine_in']==1){ ?>
                                    <li>
                                        <?php 
                                        echo $this->Html->link($this->Html->image('icon-1.png'),'javascript:void(0);',array('escape'=>false, 'title'=>'Dine-in')); 
                                        ?>
                                    </li>
                                <?php } ?>
                                <?php if(isset($kitchenDetails['Kitchen']['dining_take_out']) && $kitchenDetails['Kitchen']['dining_take_out']==1){ ?>
                                    <li>
                                        <?php 
                                        echo $this->Html->link($this->Html->image('icon-2.png'),'javascript:void(0);',array('escape'=>false, 'title'=>'Take-out')); 
                                        ?>
                                    </li>
                                <?php } ?>
								</ul>
                                -->
							</div>
						</div>
						<div class="result-lower">
							<ul class="row">
								<?php 
                                $moreCount = 0;
                                foreach ($result['Dish'] as $k => $dish)
                                {
                                    if(!empty($dish['p_small']) || !empty($dish['p_big']) || (!empty($dish['p_custom']) && $dish['is_custom_price_active']==1))
                                    {
                                        if($k<=1)
                                        {
                                        ?>
                                        <li>
                                            <div class="item-name">
                                                <?php echo $dish['name']?>
                                            </div>
                                            <div class="item-price">
                                                <?php
                                                    if($dish['p_custom'] && $dish['is_custom_price_active']==1)
                                                        echo '$ '.$dish['p_custom_price'];
                                                    else if ($dish['p_big']) 
                                                        echo '$ '.$dish['p_big_price'];
                                                    else if ($dish['p_small']) 
                                                        echo '$ '.$dish['p_small_price'];
                                                ?>
                                            </div>
                                        </li>
                                        <?php
                                        }
                                        else
                                        {
                                            $moreCount++;
                                        ?>   
                                        <li class="hide">
                                            <div class="item-name">
                                                <?php echo $dish['name']?>
                                            </div>
                                            <div class="item-price">
                                                <?php
                                                    if($dish['p_custom'] && $dish['is_custom_price_active']==1)
                                                        echo '$ '.$dish['p_custom_price'];
                                                    else if ($dish['p_big']) 
                                                        echo '$ '.$dish['p_big_price'];
                                                    else if ($dish['p_small']) 
                                                        echo '$ '.$dish['p_small_price'];
                                                ?>
                                            </div>
                                        </li> 
                                        <?php
                                        }  
                                    }
                                }?>
							</ul>
                            <?php if(count($result['Dish']) > 2){ ?>
                                <a href="javascript:void(0);" id="showAllLi" class="more-items"><?php echo '+'.$moreCount.' more'; ?></a>  
                            <?php  }else{ ?>
                                <a href="javascript:void(0);" class="more-items"> view kitchen </a>  
                            <?php } ?>
							
						</div>
                    </li>
                    </a>
            	<?php }?>
				</ul>
			</div>
		</div>

	 </div>
	 	<script>
	jQuery(document).ready(function($) {
         var $container = $("#container");
        $container.masonry({
            itemSelector: ".pictureItem",
            columnWidth: 0,
            gutterWidth: 0,
            isFitWidth: true
        }); 

      $('#showAllLi').click(function(){
            $(this).siblings('a').find('ul li').show();
            $(this).hide();
      });

      $("#hidedatepicker").datepicker({
		  	 dateFormat: "MM dd, yy",
             minDate: 0,
             onSelect: function(date) {
		 		 $('#divdatpicker').html(date);
		     },
        });  
		$('#divdatpicker').click(function(){
			$("#hidedatepicker").datepicker("show");
		});

	  $('.next-day').on("click", function () {
		    var date = $('#hidedatepicker').datepicker('getDate');
		    date.setTime(date.getTime() + (1000*60*60*24))
		    $('#hidedatepicker').datepicker("setDate", date);
		    $('#divdatpicker').html($('#hidedatepicker').val());
		});

		$('.prev-day').on("click", function () {
		    var date = $('#hidedatepicker').datepicker('getDate');
		    date.setTime(date.getTime() - (1000*60*60*24))
		    $('#hidedatepicker').datepicker("setDate", date);
		    $('#divdatpicker').html($('#hidedatepicker').val());
		});
	});
	</script>
