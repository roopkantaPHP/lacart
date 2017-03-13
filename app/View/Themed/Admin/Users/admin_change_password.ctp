 <div class="row">
    <div class="floatleft mtop10"><h1>Change Password</h1></div>
	<!--<div class="floatright"><a href="#" class="black_btn"><span>Back To Manage Products</span></a></div>-->
    </div>
      
	<div align="center" class="whitebox mtop15">
	<?php echo $this->Form->create('User',array('onsubmit'=>'return changepassword()'));?>
	<div class="error" id="mainerror"><?php echo $this->Session->flash(); ?></div>
            <table cellspacing="0" cellpadding="7" border="0" align="center">
                  <tr>
                    <td align="left"><strong class="upper">Old Password</strong></td>
                        <td align="left">                      
                        <input type="password" name="data[User][oldpass]" value="" id="oldpass" class="input" style="width: 450px;">
                        <div id="error-oldpass" class="error"></div>
                        </td>
					</tr>
					
                      <tr>
                        <td align="left"><strong class="upper">New Password</strong></td>
                        <td align="left"><input type="password" name="data[User][password]" value="" id="newpass" class="input" style="width: 450px;">
                         <div id="error-newpass" class="error"></div>
                        </td>
                      </tr>
                      
                       <tr>
                        <td align="left"><strong class="upper">Confirm Password</strong></td>
                        <td align="left"><input type="password" name="data[User][conpass]" value="" id="conpass" class="input" style="width: 450px;">
                         <div id="error-conpass" class="error"></div>
                        </td>
                      </tr>
                     
                      <tr>
                        <td align="center">&nbsp;</td>
                        <td align="left"><div class="black_btn2"><span class="upper"><?php echo $this->Form->submit('submit'); ?></span></div></td>
                      </tr>
          </table>
          <?php echo $this->Form->end();?>
   
 </div>
