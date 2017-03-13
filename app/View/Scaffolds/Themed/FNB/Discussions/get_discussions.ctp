<?php foreach($discussions as $discu) {?>
	<li data-discu-id = "<?php echo $discu['Discussion']['id']?>" style = "width:100%">
		<figure class="img-circle crs-check pull-left">
			<?php echo $this->CustomImage->show_user_avatar($discu['User'], 'ProfilePicture', 20, 20)?>
		</figure>
		<article>
			<h6><strong><?php echo $discu['User']['name']?></strong> Posted a  discussion, <?php echo $this->Time->timeAgoInWords($discu['Discussion']['created']);?></h6>
			<p><span><?php echo $discu['Discussion']['data']?></span></p>
			<p class = "control-disc-class">
				<a class = "show_replies" href="#" >Show replies</a>
		 		| <a class = "<?php echo isset($discu['Vote'][0]['id']) && ($discu['Vote'][0]['vote_type'] == VOTE_UP) ? 'active' : ''?> vote_button" data-vote-type = "<?php echo VOTE_UP?>" data-vote-added = "<?php echo isset($discu['Vote'][0]['id']) && ($discu['Vote'][0]['vote_type'] == VOTE_UP) ? true : false?>" href="#" >
				<i class="fa fa-thumbs-o-up"></i>
				<span class = "<?php echo isset($discu['Vote'][0]['id']) && ($discu['Vote'][0]['vote_type'] == VOTE_UP) ? 'active' : ''?> vote_count">Vote Up(<?php echo $discu['Discussion']['vote_up']?>)</span>
			</a>
			| <a class = "<?php echo isset($discu['Vote'][0]['id']) && ($discu['Vote'][0]['vote_type'] == VOTE_DOWN) ? 'active' : ''?> vote_button" data-vote-type = "<?php echo VOTE_DOWN?>" data-vote-added = "<?php echo isset($discu['Vote'][0]['id']) && ($discu['Vote'][0]['vote_type'] == VOTE_DOWN) ? true:false?>" href="#" >
				<i class="fa fa-thumbs-o-down"></i>
				<span class = "<?php echo isset($discu['Vote'][0]['id']) && ($discu['Vote'][0]['vote_type'] == VOTE_DOWN) ? 'active' : ''?> vote_count">Vote Down(<?php echo $discu['Discussion']['vote_down']?>)</span>
			</a>
		</p>
		<div class = "comments-div">
			<ul class="replys replies_ul col-md-12 mb0">
			</ul> 
		</div>
		<ul class="replys text-area col-md-12 mt0 comment_div_input">
			<!--<label>Type your comments here </label>-->
			<textarea class="textarea"></textarea>
			<button class="green_btn btn col-md-2 pull-right mt20 save_comment_button" type="submit">Send</button>
		</ul>
	</li>
<?php }?>