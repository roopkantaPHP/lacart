<?php echo $this->Html->script('common'); ?>
<script>
function checkall(objForm)
{
	len = objForm.elements.length;
	for( i=0 ; i<len ; i++) 
	{
		if (objForm.elements[i].type=='checkbox') objForm.elements[i].checked = objForm.check_all.checked;
	}
}

function submit_form(frmobj,comb)
{
	var comb = document.getElementById('action').value;
	if(comb=='')
	{
		alert("Please select an action.");
		return false;	
	}
	
	var checked = 0;
	
	if ((frmobj.elements['ids[]'] != null) && (frmobj.elements['ids[]'].length == null))
	{
		if (frmobj.elements['ids[]'].checked)
		{
			checked = 1;
		}
	}
	else
	{
		for (var i=0; i < frmobj.elements['ids[]'].length; i++)
		{
			if (frmobj.elements['ids[]'][i].checked)
			 {
				checked = 1;
				break;
			 }
		}
	}
	
	if (checked == 0)
	{
		alert("Please select checkboxes to do any operation.");
		return false;
	}

	if (document.getElementById('NewsletterNewsletterId').value == "")
	{
		alert("Please select a newsletter to send.");
		return false;
	}
	
	if(comb == 'Delete')
	{
		if(confirm ("Are you sure you want to delete record(s)?"))
		{
			frmobj.listingAction.value = 'Delete';
			frmobj.submit();
		}
		else
		{
			return false;
		}
	}
	else
	{
		frmobj.listingAction.value = comb;
		frmobj.submit();
	}	

}
</script>
<div class="row">
		<div class="floatleft mtop10"><h1>Send Newsletter</h1></div>
		<div class="floatright"><?php //echo $this->Html->link('<span>Create User</span>', '/admin/users/add/', array('class' => 'black_btn', 'escape' => false)) ?></div>
</div>

<div class="row searchbox floatleft mtop30" style="background-color: white; padding: 10px;">
	<p><?php echo $this->Form->create('Newsletter', array('type'=>'GET')); ?>
  <table width="90%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"><table width="90%" border="0" cellpadding="4" cellspacing="1" >
        <tr>
          <td colspan="4" align="left" class="header_bg">Advance User Search</td>
        </tr>        
        <tr class="oddRow">        
          <td align="right"><strong>Email</strong>:</td>
          <td align="left"><input name="email" type="text" class="input" id="Email" value="<?php echo $email; ?>">&nbsp;&nbsp; <input type="submit" class="submit_btn" value="" name="Search"></td>   
          
        </tr>               
      </table></td>
    </tr>
  </table>
<?php echo $this->form->end(); ?></p>
</div>

<div class="row mtop15">
	<?php echo $this->Form->create('', array('method'=>'post')); ?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="listing">
			<tr>			
			<th  align="right" colspan="2">Select Newsletter to Send:</th>
				<th colspan="3" align="left">
					<?php echo $this->Form->input('newsletter_id', array('div'=>false, 'label'=>false, 'class'=>'input', 'options'=>$newsletter_list, 'empty'=>'-Select Newsletter-', 'style'=>'width:550px;')); ?>
				</th>
				<?php /* ?>
				<th colspan="2">
					<?php echo $this->Html->link('<span>Send to All Users</span>', 'javascript:void(0)', array('class' => 'black_btn', 'escape' => false, 'onclick'=>'sendtoall()')) ?>				
				</th>
				<?php */ ?>
			</tr>
			<tr>
				<th width="5%" align="left" nowrap="nowrap">S. No.</th>
				<th width="20%" align="left" nowrap="nowrap">User Email</th>				
				<th width="5%" align="center" nowrap="nowrap"><input type="checkbox" name="checkall" id="checkall" onclick="checkAll(this);" /></th>
			</tr>
			<?php
			if(count($results)>0)
			{
			//pr($result_arr);

				$i=1;
				$bgClass="";
				foreach($results as $row)
				{
					//$page++;
					if($i%2==0)
						$bgClass = "oddRow";
					else
						$bgClass = "evenRow";

			?>
				<tr class="<?=$bgClass?>">
					<td align="left"><?php echo $i;?>.</td>
					<td align="left"><?php echo $row;?></td>					
					<td align="center"><input type="checkbox" name="ids[]" value="<?php print $row; ?>"></td>
				</tr>
			<?php
					$i++;
				}
			?>
			<tr>
                <td colspan="10" align="left" class="bordernone">
					<div class="floatright">
						<div class="floatleft">
						<span class="redtext top5" id="err_status" style="float:left;"></span> &nbsp;&nbsp;
						<select name='action' id='action' class='select-small' style='width:150px;'>
							<option value=''>Select</option>							
							<option value='send'>Send Newsletter</option>							
						</select>
						</div>
						<input type="hidden" name="listingAction" value="" />
						<div class="floatleft mleft10"><input type="button" class="submit_btn" value="" name="Search" onclick="return submit_form(this.form,'')"></div>
                   </div>
				</td>
			</tr>
			<?php
			}
			else
			{
			?>
				<tr class="redtext">
					<td align="center" colspan="7">No record found</td>
				</tr>
			<?php
			}
			?>
		</table>
	<?php echo $this->Form->end(); ?>
</div>
<script>
function sendtoall()
{
	if ($("#UserNewsletterId").val() == "")
	{
		alert("Please select a newsletter");
	}
	else
	{
		var conf = confirm("Are you sure you want to send newsletter to all users?");
		if (conf)
		{
			val = $("#UserNewsletterId").val();
			window.location.href="<?php echo Router::url('/'); ?>cpanel/sendtoall/"+val;
			//alert('This feature is not available for now');
		}
	}
}
</script>
