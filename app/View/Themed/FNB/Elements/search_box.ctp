<?php
	$javascriptTime = date('g:00 a',strtotime('+1 Hour'));

	$h1 = date('H',strtotime('+1 Hour'));
	$time_selected = ($h1*60)+1110;

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<?php echo $this->element('timeslider_server'); ?>
<?php
$className = 'hide';
if($this->request->action == 'index' && $this->request->controller == 'users' || $this->request->action == 'verify_user')
{
	$className = '';
}
$location = '';
$searchKeyword = '';
$searchLat = '';
$searchLng = '';
 if(!empty($this->params['named']))
 					{
                        foreach ($this->params['named'] as $k=>$param)
                        {
	                        if($k=='q')
	                            $searchKeyword = $param;
	                        elseif($k=='loc')
	                            $location = $param;
	                        elseif($k=='lat')
	                            $searchLat = $param;
	                        elseif($k=='lng')
	                            $searchLng = $param;
	                        elseif($k=='t')
	                        {
	                        	$h1 = date("H", strtotime($param));
														$time_selected = ($h1*60)+1110;
											   }
                        }
                    }
?>

<div class="search-banner <?php echo $className; ?>">
	<div class="form-inner">
		<h2 style="font-size:21px;">Get the best home made food delivered to your doorstep. Search by your delivery location and what time you want it at</h2>
		<div class="form">
		    <?php echo $this->Form->create('Search', array('url'=>array('controller'=>'dishes','action'=>'search'),'onSubmit'=>'searchJs.submitSearch();return false;', 'id'=>'SearchIndexForm','type'=>'get','inputDefaults'=>array('div'=>false,'label'=>false))); ?>
		    <?php
		    if($this->request->action != 'search')
			{
				echo $this->Form->hidden('',array('id'=>'search_key_url','value'=>$this->Common->getSearchUrl('q','S_SEARCHKEY/loc:S_SEARCHLOC/lat:S_SEARCHLAT/lng:S_SEARCHLNG/t:S_SEARCHTIME',true)));
			}
			?>
		    <?php   ?>
			<div class="field searchfield"><?php echo $this->Form->input('key',array('placeholder'=>'Search for anything you like','id'=>'SearchKey','value'=>$searchKeyword))?></div>
			<div class="field1 clearfix">
				<div class="field-lbl">WHERE</div>
				<div class="field1-input">
				<?php  echo $this->Form->input('location', array('placeholder'=>'Search for location or city','id'=>'SearchLocation','autocomplete'=>'on','runat'=>'server','onFocus'=>"geolocate()",'value'=>$location)); ?> <a
						href="#" onclick="lookup_location();return false" class="find-btn"></a>
				</div>
				 <?php echo $this->Form->hidden('lat', array('id'=>'SearchLat','value'=>$searchLat))?>
				 <?php echo $this->Form->hidden('lng', array('id'=>'SearchLng','value'=>$searchLng))?>
			   </div>
<div class="">
	 <ul class="order">
		 <li>
				 <div class="takeaway">
	 				<input type="" value="Take away"/>
	 			</div>
			</li>
			<li>
				<div class="delivery">
				 <input type="" value="Delivery"/>
			 </div>
		 </li>
		 </ul>
		 </div>
			<!-- <div class="field1 clearfix">
				<div class="field-lbl">WHEN</div>
				<div class="field1-input time">
					<div id="whenslider" class="whenslider"></div>
					<div id="whentime"></div>

				</div>
			</div> -->
			<div class="searchbtn">
				<input type="submit" value="Search" />
			</div>
			 <?php echo $this->Form->end()?>
		   </div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#SearchIndexForm').submit(function(e){
		e.preventDefault();

	});

	if($(".whenslider").length)
	{
		$(".whenslider").timeslider({
		    sliderOptions: {
		        orientation: 'horizonatal',
		        min: 1110,
		        max: 2550,
		        value: '<?php echo $time_selected; ?>',
		        step: 1,
		        range: "min",
		    },
		    timeDisplay: '#whentime',
		    //submitButton: '#schedule-submit1',
		    clickSubmit: function (e){

		    },
		    timeInput: '#SearchLat'
		});

		$('#whenslider').mouseup(function(){
      var starttime =  getHour24($('#whentime').html());

			var time = new Date('<?php echo date("d-m-Y H:i:s"); ?>');
			var endtime = time.getHours(); // 0-24 format
			console.log(endtime);
			var timeDifference = endtime - starttime;
        	if(timeDifference >= 1)
        	{
        		alert('You can not select previous time.');
        		$(".whenslider").slider("value", '<?php echo $time_selected; ?>');
        		$('#whentime').html('<?php echo $javascriptTime; ?>');
  				}
    });
	}

	function getHour24(timeString)
	{
	    time = null;
	    var matches = timeString.match(/^(\d{1,2}):00 (\w{2})/);
	    if (matches != null && matches.length == 3)
	    {
	        time = parseInt(matches[1]);
	        if (matches[2] == 'PM')
	        {
	            time += 12;
	        }
	    }
	    return time;
	}


	$("#SearchLocation").change(function(){
		$("#SearchLat").val("");
		$("#SearchLng").val("");
	});

});
</script>
<?php echo $this->Html->script('geoPosition')?>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>


<script type="text/javascript">
	// This example displays an address form, using the autocomplete feature
	// of the Google Places API to help users fill in the information.

	var placeSearch, autocomplete;

	$(document).ready(function(){
		initializeAutoComplete();
	});

	function initializeAutoComplete() {
	  // Create the autocomplete object, restricting the search
	  // to geographical location types.
	  autocomplete = new google.maps.places.Autocomplete(
	      /** @type {HTMLInputElement} */(document.getElementById('SearchLocation')),
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
	  var lat = place.geometry.location.lat(),
      lng = place.geometry.location.lng();

      $("#SearchLat").val(lat);
	  $("#SearchLng").val(lng);
	}

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
  	if($('#SearchLat').val()=='')
  		$('#SearchLat').val(position.coords.latitude);

  	if($('#SearchLng').val()=='')
  		$('#SearchLng').val(position.coords.longitude);
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

	function lookup_location() {
	  geoPosition.getCurrentPosition(show_map, show_map_error);
	}
	function show_map(loc) {
		//console.log(loc);
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
		    	//console.log(results);
		    	//console.log(results[0].formatted_address);
		    	console.log(lat);
		    	$('#SearchLocation').val(results[0].formatted_address);
		    	$('#SearchLat').val(lat);
		    	$('#SearchLng').val(lng);
		    }

	    } else {
	      alert('Geocoder failed due to: ' + status);
	    }
	  });
	}

	google.maps.event.addDomListener(window, 'load', initialize);
  </script>
