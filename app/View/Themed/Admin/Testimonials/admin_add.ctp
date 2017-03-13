<script type="text/javascript">
$(document).ready(function (){
$('.validate').each(function(){
$(this).validate({
rules:{
	"data[Testimonial][name]":{
		required:true,		
	},
	"data[Testimonial][email]":{
		required:true,		
	},
	"data[Testimonial][message]":{
		required:true,		
	}
}
});});});
</script>	
    <div class="row">
		<div class="floatleft mtop10"><h1>Add Testimonial</h1></div>
		<div class="floatright"><?php echo $this->Html->link('<span>Back to Testimonials</span>', array('controller' => 'testimonials', 'action' => 'index','admin' => true), array('class' => 'black_btn', 'escape' => false)) ?></div>
    </div>
	<div align="center" class="greybox mtop15">
		<?php echo $this->Form->create('Testimonial', array('class' =>'validate','type'=>'file')); ?>
			<table cellspacing="0" cellpadding="7" border="0" align="center">				
				<tr>
					<td valign="middle"><strong class="upper">Name:</strong></td>
					<td><?php echo $this->Form->text('Testimonial.name', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Email:</strong></td>
					<td><?php echo $this->Form->text('Testimonial.email', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Image:</strong></td>
					<td><?php echo $this->Form->file('Testimonial.image', array('div'=>false, 'label'=>false, 'class'=>'input', 'size'=>'115')); ?></td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Content:</strong></td>
					<td>
						<?php echo $this->Form->textarea('Testimonial.message', array('div'=>false,'label'=>false, 'class'=>'invalidateput count200', 'cols'=>'100', 'rows'=>'5')); 
						$descLen = 0;
						if(isset($this->request->data['Testimonial']['message']) && !empty($this->request->data['Testimonial']['message']))
						$descLen = strlen($this->request->data['Testimonial']['message']);
						
						$descLenShow = 200-$descLen;  ?>
						
						 <span class="char-info"><?php echo $descLenShow." characters"; ?></span>
					</td>
				</tr>
				<tr>
					<td valign="middle"><strong class="upper">Is Display:</strong></td>
					<td>
						<?php echo $this->Form->checkbox('Testimonial.is_display', array('div'=>false,'label'=>false)); ?>
					</td>
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
		</form>
	</div>
