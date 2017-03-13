<?php foreach($comments as $comment) {?>
<li>
	<div class="top-section col-md-12 no-pd mb10">
		<div class="col-md-1 no-pd name-section">
		<div class="circle short pull-left">
			<?php 
				echo $this->CustomImage->show_user_avatar($comment['User'], 'ProfilePicture', 40, 40, array('class' => 'user_imagePreview circular'));
			?>
		</div>
		<a href="courses-checkout.html">
				<?php echo $comment['User']['name']?>
		</a>
		</div>
		<div class="name start-course col-md-7">
			<p><?php echo $comment['Comment']['name']?></p>
		</div>
		<div class="date col-md-3"><?php echo $this->Time->timeAgoInWords($comment['Comment']['created']);?> </div>
	</div>
</li>
<?php }?>
<?php 
$hasPages = ($this->params['paging']['Comment']['nextPage']);
if ($hasPages)
{
    echo $this->Paginator->next("Load More");
}
?>