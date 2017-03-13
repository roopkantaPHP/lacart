<?php
	$week_days = Configure::Read('weekdays');
	$highlight_days = array();
	$selection = '';
	if(!empty($user_data['SessionFrequency']) && $user_data['SessionFrequency']['frequency'] == WEEKLY_SESSION)
	{
		$highlight_days = Hash::extract($user_data['SessionFrequency']['SessionWeekday'], '{n}.day');
		$selection = WEEKLY_SESSION;
	} else {
		$highlight_days = Hash::extract($user_data['SessionFrequency']['SessionDate'], '{n}.date');
		$selection = DATE_SESSION;
	}
	$tutor_id = $user_data['TutorProfile']['id'];
	$user_tutor_profile_id = $user_data['TutorProfile']['user_id']; 
	$every_30_minutes = $this->Utility->hoursRange( 0, 86400, 60 * 30, 'h:i a' );
?>
<style>
body{overflow: auto !important}	
</style>
<section class="inner-middle-content">
	<div class="container">
		<div class="row tutor-profile no-lmr">
			<div class="tp-main col-md-9">
				<div class="tp col-md-12 no-lmr mb20">
					<div class="tp-left col-md-3">
						<div class="tp-colm">
							<div class="img-box">
								<div class="img-circle">
									<?php 
										echo $this->CustomImage->show_user_avatar($user_data, 'TutorProfilePicture', 200, 200,  array());
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="right-colm col-md-9">
						<h1 class="pull-left"> <?php echo $user_data['TutorProfile']['name']?>  </h1>
						<div class="online col-md-3 btn pull-right"> 
							<div class="circle-online pull-left"> </div> Online Now
						</div>
					<div class="clear"></div>
					<div class="details">
						<p> Design Professional :: <?php echo $user_data['TutorProfile']['title']?> </p>
						<p class="col-md-1 no-lpd"> Tutors </p> 
						<p class="col-md-11"><?php echo implode(', ', Hash::extract($user_data['Subject'], '{n}.title'))?> </p>
						<p class="col-md-1 no-lpd"> Social </p> 
						<div class="pull-left socials col-lg-11">
							<?php
// 							google url
								$parsed = parse_url($user_data['TutorProfile']['google_plus_url']);
								if (empty($parsed['scheme'])) {
									$user_data['TutorProfile']['google_plus_url'] = 'http://' . ltrim($user_data['TutorProfile']['google_plus_url'], '/');
								} 
								echo $this->Html->link('<span class="col-md-1 ggl"> </span>', $user_data['TutorProfile']['google_plus_url'], array('escape' => false));
								
// 							twitter url
								$parsed = parse_url($user_data['TutorProfile']['twitter_url']);
								if (empty($parsed['scheme'])) {
									$user_data['TutorProfile']['twitter_url'] = 'http://' . ltrim($user_data['TutorProfile']['twitter_url'], '/');
								} 
								echo $this->Html->link('<span class="col-md-1 tw"> </span>', $user_data['TutorProfile']['twitter_url'], array('escape' => false));
								
// 							facebook url
								$parsed = parse_url($user_data['TutorProfile']['facebook_url']);
								if (empty($parsed['scheme'])) {
									$user_data['TutorProfile']['facebook_url'] = 'http://' . ltrim($user_data['TutorProfile']['facebook_url'], '/');
								} 
								echo $this->Html->link('<span class="col-md-1 fb"> </span>', $user_data['TutorProfile']['facebook_url'], array('escape' => false));

// 							linkedn url
								$parsed = parse_url($user_data['TutorProfile']['linkedin_url']);
								if (empty($parsed['scheme'])) {
									$user_data['TutorProfile']['linkedin_url'] = 'http://' . ltrim($user_data['TutorProfile']['linkedin_url'], '/');
								} 
								echo $this->Html->link('<span class="col-md-1 ld"> </span>', $user_data['TutorProfile']['linkedin_url'], array('escape' => false));

// 							website url
								$parsed = parse_url($user_data['TutorProfile']['website_url']);
								if (empty($parsed['scheme'])) {
									$user_data['TutorProfile']['website_url'] = 'http://' . ltrim($user_data['TutorProfile']['website_url'], '/');
								} 
								echo $this->Html->link('<span class="col-md-1 gl"> </span>', $user_data['TutorProfile']['website_url'], array('escape' => false));
							?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<!--tabs content-->
  		<div class="tabs col-md-11 no-lpd"> 
			<!-- Nav tabs -->
			<ul class="nav nav-tabs mb20">
				<li class="active"><a href="#home" data-toggle="tab">  About </a></li>
				<li><a href="#reviews" data-toggle="tab">Reviews </a></li>
				<li><a href="#expertise" data-toggle="tab">Subject Expertise </a></li>
				<?php if($my_courses) {?><li><a href="#courses" data-toggle="tab">Courses </a></li><?php }?>
				<div class="clear"></div>
			</ul>
			<div class="clear"></div>
			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane active" id="home">
					<div class="col-md-12 no-pd mb10">
					<div class="about-heading col-md-12  no-pd"> <i class="fa fa-users"> </i> Bio</div>
					<div class="col-md-12 bio-details"> <p class="mt0"> <?php echo $user_data['TutorProfile']['biography'] ?> </p></div>
					</div>
					
					<div class="col-md-12 no-pd mb10">
					<div class="about-heading inlineBlock col-md-12 no-pd"> <i class="fa fa-trophy"> </i> Accolades</div>
					<ul class="accolades col-md-9">
						<?php foreach($user_data['TutorProfile']['accolades'] as $acc) { ?>
						<li>  <?php echo $acc; ?></li>
						<?php }?>
					</ul>
				</div>
				</div>
				<div class="tab-pane" id="reviews">sdfdf</div>
				<div class="tab-pane" id="expertise">
					<ul class="exeprtise-list">
						<?php
							$i = 1; 
							foreach($user_data['Subject'] as $sub) {
						?>
							<li><strong class="sub"> Subject : -  </strong><span class="orange-text"> <?php echo $sub['title']; ?> </span> </li>
							<li> <strong class="col-md-12 no-pd mb5"> Description : </strong><p> <?php echo $sub['description']; ?></p></li>
							<li><strong class="col-md-12 no-pd mb5">  Category : </strong> <p><?php echo $sub['Category']['name']; ?></p></li>
							<hr>
						<?php }?>
					</ul>
				</div>
				<?php if($my_courses) {?>
				<div class="tab-pane course-result" id="courses">
					<ul class="course-list">
					<?php foreach($my_courses as $single_course) {?>
						<li>
							<figure class="main">
								<?php 
									$url_slug = $this->Html->url(array('controller' => 'courses', 'action' => 'course_checkout', $single_course['Course']['slug']));
								?>
								<a href="<?php echo $url_slug?>">
									<?php 
						        		if(isset($single_course['CoursePicture']) && !empty($single_course['CoursePicture']))
										{
											$image =  $this->CustomImage->show_image($single_course['CoursePicture'], 'CoursePicture', array('class' => 'user_imagePreview', 'width' => 216, 'height' => 149));
										} else
										{
											$dim_array = array('width' => 216, 'height' => 149); 
											$image = $this->CustomImage->show_default_image('default_course.jpg', $dim_array,  array('class' => 'user_imagePreview', 'width' => 216, 'height' => 149));
										}
									?>
								</a>
								<span class="price orange-bg">$<?php echo $single_course['Course']['course_fee']?></span>	
								<span class="rating">
									<i class="fa fa-user"></i>22.5k Students
									<img src="<?php echo SITE_URL?>/images/stars.png" width="81" height="18" alt="Rating"> (15)
								</span>
							</figure>
							<h5><?php echo $single_course['Course']['name']?></h5>
							<div class="detail-box">
								<figure>
									<?php 
										echo $this->CustomImage->show_user_avatar($single_course['User'], 'ProfilePicture', 43, 43, array('class' => 'user_imagePreview circular'));
									?>
								</figure>
						        <div class="detail">
									<div class="name">
										<?php 
											echo $this->Html->link($single_course['User']['name'], '#');
										?>
									</div>
									<div class="designation"><?php echo $single_course['User']['title']?></div>
								</div>
							</div>
						</li>
						<?php }?>
					</ul>
				</div>
				<?php }?>
			</div>
			<div class="clear"></div>
		</div> <!--tabs end-->
		<div class="clear"></div>
	</div> <!-- tp-main end -->
	<div class="col-md-3 no-pd">
		<div class="tp-right-block col-md-12">
			<div class="col-md-12 price-blk">
				<h4> Price</h4>
				<h4>  <strong>$<?php echo $user_data['TutorProfile']['hourly_price']?>/</strong>hour</h4>
			</div>
			<div class="session col-md-11">
				<i class="fa fa-calendar"></i>
				<a data-target="#myModal1" data-toggle="modal" href="#">Schedule Session</a>
			</div>
			<div class="clear"></div>
			<div class="ratings">
				<img src="<?php echo SITE_URL?>/images/stars2.png" alt="" /> <span> (14) </span> 
			</div>
			<div class="cont">
				87 Sessions
			</div>
			<div class="cont">
				120 Hours Tutored
			</div>
			<div class="cont gray">
				Contact Me
			</div>
			<div class="cont avrg">
				Average Response Time : <strong>4 Hours</strong>
			</div> <!--price block end-->
		</div>
		<div class="clear"></div>
				<div class="advertise">
					<div class="ratings"> 
						<div class="right-contnt">
							<div class="icons-img mny">   </div>
							<p> 30 day money back guarantee  </p>
						</div>
						<div class="clear"></div>
						<div class="right-contnt">
							<div class="icons-img hat online">   </div>
							<p> Online workspace to collaborate, take notes, edit documents and much more</p>
						</div>
						<div class="clear"></div>
						<div class="right-contnt">
							<div class="icons-img clck profess">   </div>
							<p> Learn from professionals in the comfort of your own home </p>
						</div>
						<!--price block end-->
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</section>
<!-- Celender window  -->
<div class="modal fade  helpout-sec" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog col-sm-6">
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel1">Select a date and time for this Helpout</h4>
            </div>
            <div class="modal-body">
                <div class="helpout-left col-sm-7">
                    <div class="celender-wrap">
                    </div>                            
                </div>
                <div class="helpout-right col-sm-5">
                    <h5 class = "selected_date"></h5>
                    <ul class= "selected_day_timings">
                      Select Date
                    </ul>
                    <div class="form-group">
                        <button class="btn btn-green schedule_button" type="button"> Schedule</button>
                        <button class="btn btn-black" data-dismiss="modal" type="button"> Cancel</button>
                    </div>
                </div>
                <div class="clear"></div>
            </div>                    
            <div class="modal-footer">
                <p class="text-left footer-text">Don't see a time that works? <a href="#" data-target="#myModal-request" data-toggle="modal" href="javascript:void(0);" >Send a message</a> to request a time</p>         
            </div>
        </div>
    </div>
</div>
<div class="modal fade given-msg" id="myModal-request" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="profile-pic-line">
                    <figure><img src="<?php echo SITE_URL?>/images/crs-chk5.png" alt="" /></figure>
                    <label>Russell Alleen-Willems</label>
                </div>
            </div>
            <div class="modal-body">
                <h4>Request a time</h4>
                <h5>Free Consultation On Archaeology Databases & Data Collectors</h5>
                <p>Use the box below to tell Russell Alleen-Willems when you would like to take the Helpout.</p>
                <textarea></textarea>
            </div>
            <div class="modal-footer">
                <div class="text-left">
                    <button class="btn btn-green" type="submit"> Send</button>
                    <button class="btn btn-black" data-dismiss="modal" type="button"> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
	Date.prototype.getMonthFormatted = function() {
	    var month = this.getMonth();
	    var inc_month = parseInt(month) + 1;
	    return inc_month < 10 ? '0' + inc_month : inc_month; // ('' + month) for string result
	}
	Date.prototype.getDateFormatted = function() {
	    var date = this.getDate();
	    return date < 10 ? '0' + date : date; // ('' + month) for string result
	}
	var time_ranges = '<?php echo json_encode($every_30_minutes)?>';
	time_ranges = JSON.parse(time_ranges);
	var type_of_selection = '<?php echo $selection;?>';
	var tutor_id = '<?php echo $tutor_id?>'
	var tutor_user_id = '<?php echo $user_tutor_profile_id?>'
	var all_days = '<?php echo json_encode($week_days)?>'
	all_days = JSON.parse(all_days);
	var hightlight_days = '<?php echo json_encode($highlight_days)?>'
	hightlight_days = JSON.parse(hightlight_days);
	// $('.modal').on('hidden.bs.modal', function( event ) {
		// $(this).removeClass( 'fv-modal-stack' );
		// $('body').data( 'fv_open_modals', $('body').data( 'fv_open_modals' ) - 1 );
	// });
	// $( '.modal' ).on( 'shown.bs.modal', function ( event ) {
		// if ( typeof( $('body').data( 'fv_open_modals' ) ) == 'undefined' )
		// {
			// $('body').data( 'fv_open_modals', 0 );
       	// }
        // if ( $(this).hasClass( 'fv-modal-stack' ) )
        // {
	        // return;
        // }
        // $(this).addClass( 'fv-modal-stack' );
        // $('body').data( 'fv_open_modals', $('body').data( 'fv_open_modals' ) + 1 );
        // $(this).css('z-index', 1040 + (10 * $('body').data( 'fv_open_modals' )));
        // $( '.modal-backdrop' ).not( '.fv-modal-stack' ).css( 'z-index', 1039 + (10 * $('body').data( 'fv_open_modals' )));
		// $( '.modal-backdrop' ).not( 'fv-modal-stack' ).addClass( 'fv-modal-stack' ); 
	// });
	$('.celender-wrap').datepicker({
		todayHighlight: true,
		startDate: "today",
	    beforeShowDay: function (date)
	    {
	    	var now = new Date();
	    	if (now < date) {
	    		if(type_of_selection == '<?php echo WEEKLY_SESSION?>')
		    	{
		    		if(typeof(hightlight_days[date.getDay()]) != 'undefined')
			    	{
			    		return {
			              tooltip: 'Avaliable',
			              classes: 'circle-online'
			            };
			    	}
		    	} else if(type_of_selection == '<?php echo DATE_SESSION?>')
		    	{
		    		var date_string = date.getFullYear() + '-' + date.getMonthFormatted() + '-' + date.getDateFormatted();
		    		if($.inArray(date_string, hightlight_days) !== -1)
					{
						return {
			              tooltip: 'Avaliable',
			              classes: 'circle-online'
			            };
					}
		    	}
	    	}
	    }
	}).on('changeDate', function(e)
	{
		var date_string = e.date.getDate() + '-' + parseInt(e.date.getMonth() + 1) + '-' + e.date.getFullYear();
		$('.selected_date').text(e.date.toDateString());
		$.ajax({
			url: "<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'schedule_timings'))?>" + '/' + tutor_id,
			type:'POST',
			data:'data[date]=' + date_string,
			success: function(data)
			{
				data = JSON.parse(data);
				if(data)
				{
					var li_data = '';
					for(i in data)
					{
						li_data += '<li><label><input value = "' + data[i] + '" type="radio"  name = "time_select" />' + time_ranges[data[i]] + '</label></li>';
					}
					$('.selected_day_timings').html(li_data);
				} else
				{
					$('.selected_day_timings').html('All Slots Booked');
				}
			}
		});
    });
    
    $('body').on('click', '.schedule_button', function(e)
    {
    	e.preventDefault();
    	if(typeof($('.celender-wrap').data('datepicker').date) != 'undefined')
    	{
    		if(typeof($('.selected_day_timings').find('input[type="radio"]:checked').val()) != 'undefined')
	    	{
	    		var selected_time_li = $('.selected_day_timings').find('input[type="radio"]:checked');
	    		var selected_time = selected_time_li.val();
		    	var date_obj = $('.celender-wrap').data('datepicker').date;
		    	var date_string = date_obj.getDate() + '-' + parseInt(date_obj.getMonth() + 1) + '-' + date_obj.getFullYear();
		    	$.ajax({
					url: "<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'book_schedule'))?>",
					type:'POST',
					data:'data[date]=' + date_string + '&data[time]=' + selected_time + '&data[tutor_id]=' + tutor_id + '&data[tutor_user_id]=' + tutor_user_id,
					success: function(data)
					{
						data = JSON.parse(data);
						if(data['status'])
						{
							if(data['remove_time'])
							{
								selected_time_li.closest('label').remove()
							}
							var send_array = {};
							send_array['user_id'] = tutor_user_id;
							send_array['message'] = data['tutor_message'];
							send_array['message_title'] = 'Session Booking';
							socket.emit('notify', send_array);
							// alert(data['message']);
						} else
						{
							if(data['remove_time'])
							{
								selected_time_li.closest('label').remove()
							}
							alert(data['message']);
						}
					}
				});
	    	} else
	    	{
	    		alert('Please Select the time')
	    	}
    	} else
    	{
    		alert('Please Select the date')
    	}
    })
});

</script>