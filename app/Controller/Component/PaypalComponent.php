<?php
/** 
*  Project : Lacart 
*  Author : Xicom Technologies 
*  Creation Date : 23-Jan-2015 
*  Description : This is paypal component which creates payment processes.
*/
	App::uses('Component', 'Controller');
	class PaypalComponent extends Component
	{
		/*
		 * Purpose :- this function will create paypal method data 
		 * 
		 * Inputs : $data - Paypal and order details   
		 * 
		 * Outputs : Data Array which is to be passed in paypal curl
		 * 
		 * Returns : It will return array of data
		*/
    	public function pay_me($paramArray=array(),$urlHit)
    	{
    		$nvpString = "USER=".PAYPAL_USER;  # User ID of the PayPal caller account
    		$nvpString .= "&PWD=".PAYPAL_PWD;	 # Password of the caller account
    		$nvpString .= "&SIGNATURE=".PAYPAL_SIGNATURE; # Signature of the caller account
    		
    		$headers = array(
		        "X-PAYPAL-SECURITY-USERID: ".PAYPAL_USER,
		        "X-PAYPAL-SECURITY-PASSWORD: ".PAYPAL_PWD,
		        "X-PAYPAL-SECURITY-SIGNATURE: ".PAYPAL_SIGNATURE,
		        "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
		        "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
		        "X-PAYPAL-APPLICATION-ID: ".PAYPAL_APPID,
		    );
    		
    		if(!empty($paramArray))
    		{
    			foreach ($paramArray as $key => $value)
    			{
    				$nvpString .= "&".$key."=".$value;
    			}

    		}

    		$curlRespo = $this->hit_me($nvpString,$headers,$urlHit);

			return $curlRespo;
	    }

	    /*
		 * Purpose :- this function will run curl for paypal payment 
		 * 
		 * Inputs : $data - Paypal data   
		 * 
		 * Returns : It will return array of response
		*/
    	public function hit_me($paramString,$hData,$urlHit)
    	{
    		$ch = curl_init();  
 		
		    curl_setopt($ch,CURLOPT_URL,$urlHit);
		    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		    curl_setopt($ch,CURLOPT_HTTPHEADER, $hData); 
		    curl_setopt($ch, CURLOPT_POST, count($paramString));
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		    $output=json_decode(curl_exec($ch));

		    curl_close($ch);
		    return $output;
	    }

	    public function ismobile()
	    {
		    $is_mobile = '0';

		    if(preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		        $is_mobile=1;
		    }

		    if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		        $is_mobile=1;
		    }

		    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		    $mobile_agents = array('w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-');

		    if(in_array($mobile_ua,$mobile_agents)) {
		        $is_mobile=1;
		    }

		    if (isset($_SERVER['ALL_HTTP'])) {
		        if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
		            $is_mobile=1;
		        }
		    }

		    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
		        $is_mobile=0;
		    }

		    return $is_mobile;
		}

		public function sendMail($orderData=array())
		{
			if(!empty($orderData))
			{
				if(isset($orderData['User']['email']) && !empty($orderData['User']['email']))
				{
					$email = $orderData['User']['email'];
					if(!empty($orderData['User']['name']))
						$name = $orderData['User']['name'];
					else
						$name = $orderData['User']['email'];

					$tax_percent = $orderData['Order']['tax_percent'];
					$salesTax = $orderData['Order']['sale_tax'];
					$serviceFee = $orderData['Order']['service_fee'];
					$orderValue = $serviceFee + $orderData['Order']['order_value'];
					
					if($orderData['Order']['payment_type']==0)
					{
						$transactionId = str_replace('/orders/', '', $orderData['Order']['order_href']);
						$paymentType = "Stripe Payment";
					}
					else
					{
						$transactionId = $orderData['Order']['transaction_id'];
						$paymentType = "Paypal";
					}

					if($orderData['Order']['dine_type'] == 1)
					{ 
						$dineType = 'Dine-in';	
					}
					else
					{
						$dineType = 'Take-out';	
					}


				$html = '<table cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse;line-height:100%!important;margin:0;padding:0;width:100%!important">
				  <tbody><tr>
				    <td>
				      <table style="border-collapse:collapse;margin:auto;max-width:600px;min-width:320px">
				        <tbody>
				        <tr>
				          <td valign="top" style="padding:0 20px">
				              <table cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse;border-radius:3px;color:#545454;font-family:'."'".'Helvetica Neue'."'".',Arial,sans-serif;font-size:13px;line-height:20px;margin:0 auto;width:100%">
				    <tbody><tr>
				      <td valign="top">
				        <table cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;border-radius:3px 3px 0 0;border:1px solid #113d65;width:100%">
				          <tbody><tr>
				            <td valign="top" style="background:#CA0202;border-top-style:solid;border-top-width:1px;color:#fff;font-family:'."'".'Helvetica Neue'."'".',Arial,sans-serif;font-size:24px;font-weight:bold;line-height:40px;padding:13px 0 12px;text-align:center" align="center" bgcolor="#CA0202">
				              <a href="'.BASE_URL.'"><img src="'.BASE_URL.'images/logo.png" alt=""></a>
				            </td>
				          </tr>
				        </tbody></table>
				        <table cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;border-color:#8c8c8c;border-radius:0 0 3px 3px;border-style:solid;border-width:0 1px 1px;width:100%">
				          <tbody><tr>
				            <td style="border-radius:0 0 3px 3px;color:#545454;font-family:'."'".'Helvetica Neue'."'".',Arial,sans-serif;font-size:13px;line-height:20px;overflow:hidden;padding:3px 30px 15px">
				              <p style="margin:20px 0">Hey '.$name.',</p>
				              <p style="margin:20px 0">Congratulations!! Your order has been placed successfully.</p>
				              <div>
				                <table style="background: none repeat scroll 0 0 rgb(242, 242, 242);">
				                  <tbody>
				                    <tr style="display: block; padding: 5px 20px;">
				                      <td colspan="2" style="float: left; font-weight: normal; text-transform: uppercase; line-height: 38px; font-family: '."'".'Roboto Slab'."'".', serif; font-weight: 700; font-size: 18px; color: #363636;">Order Details</td> 
				                    </tr>
				                    <tr style="padding: 5px 0px;float: left;line-height: 25px;color: #333;border-top: 2px solid rgb(204, 204, 204); width:100%">
				                      <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:13px; width:50%; padding-left:20px;">
				                         TRANSACTION NUMBER
				                      </td>
				                      <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:17px; width:50%; padding-right:20px;">
				                        '.$transactionId.'
				                      </td>
				                    </tr>
				                    <tr style="padding: 5px 0px;float: left;line-height: 25px;color: #333;border-top: 2px solid rgb(204, 204, 204); width:100%">
				                      <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:13px; width:50%; padding-left:20px;">
				                       	PAYMENT METHOD
				                      </td>
				                      <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:17px; width:50%; padding-right:20px;">
				                        '.$paymentType.'
				                      </td>
				                    </tr>
				                    <tr style="padding: 5px 0px;float: left;line-height: 25px;color: #333;border-top: 2px solid rgb(204, 204, 204); width:100%">
				                      
				                    </tr>
				                    <tr style="padding: 5px 20px;float: left;line-height: 25px;color: #333; width:100%;">
				                      <td colspan="2" style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:13px; ">
				                        ORDER SUMMERY
				                      </td>
				                    </tr>
				                    <tr style="padding: 5px 20px; text-align:center;">
				                      <td colspan="2">
				                        <table  style="display: inline-block;width: 60%;">
				                          <tbody>';
											if(isset($orderData['OrderDish']) && !empty($orderData['OrderDish']))
											{
												$kitchenName = $orderData['OrderDish'][0]['Kitchen']['name'];
												$html .=		'<tr>
									                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:17px;">
									                                <b> '.$kitchenName.' </b>
									                              </td>  
									                            </tr>';
									                foreach ($orderData['OrderDish'] as $key => $value)
									                {
									                	$html .=	'<tr style="background: none repeat scroll 0 0 #FFECCB;">
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px;">'.$value['dish_name'].'</td>
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px;">$'.$value['price'].'</td>
											                         </tr>';
									                } 

									                $html .=   '		 <tr style="background: none repeat scroll 0 0 #FFECCB;">
																            <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
																border-top: 2px dotted #e0d9c7;padding: 14px 5px;">Delivery Fee</td>
																            <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
																border-top: 2px dotted #e0d9c7;padding: 14px 5px;">$'.$serviceFee.'</td>
											                            </tr>
									                					<tr style="background: none repeat scroll 0 0 #FFECCB;">
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px; font-weight:bold;">Order Value</td>
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px; font-weight:bold;">$'.$orderValue.'</td>
											                            </tr>
											                            <tr style="background: none repeat scroll 0 0 #FFECCB;">
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px;">Sales Tax('.$tax_percent.'%)</td>
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px;">$'.$salesTax.'</td>
											                            </tr>
											                            <tr style="background: none repeat scroll 0 0 #FFECCB;">
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px; font-weight:bold;">Total</td>
											                              <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:14px;border-bottom: 2px dotted #e0d9c7;
											border-top: 2px dotted #e0d9c7;padding: 14px 5px; font-weight:bold;">$'.$orderData['Order']['amount'].'</td>
											                            </tr>';        
											}                          
											
											$html .=	'</tbody>
									                        </table>
									                      </td>
									                    </tr>
									                     <tr style="padding: 5px 0px;float: left;line-height: 25px;color: #333;border-top: 2px solid rgb(204, 204, 204); width:100%">
									                      <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:13px; width:50%; padding-left:20px;">
									                        DATE
									                      </td>
									                      <td style="font-family: '."'".'Raleway'."'".', sans-serif; font-size:17px; width:50%; padding-right:20px;">
									                        '.date('M d, Y',strtotime($orderData['Order']['created'])).'
									                      </td>
									                    </tr>
									                  </tbody>
									                </table>
									              </div>
									            </td>
									          </tr>
									        </tbody></table>
									      </td>
									    </tr>
									  </tbody></table>
									          </td>
									        </tr>
									        <tr>
									          <td valign="top" height="20">&nbsp;</td>
											        </tr>
											      </tbody></table>
											    </td>
											  </tr>
											</tbody></table>';

					$content = $html;
					
					$subject = 'Your order has been successfully placed on Lacart.';
					
					$Email = new CakeEmail('smtp');
					$Email->to($email);
					$Email->emailFormat('html');
					$Email->from(FROM_EMAIL);
					$Email->replyTo(REPLYTO_EMAIL);
					$Email->subject($subject);

					 if($Email->send($content))
					 {
					 	return true;
					 }
					 else
					 {
					 	return false;
					 }						
				}
			}
		}

		
		public function encrypt($id)
		{
		    $id = base_convert($id, 10, 36); // Save some space
		    $data = mcrypt_encrypt(MCRYPT_BLOWFISH, Configure::read('Security.cipherSeed'), $id, 'ecb');
		    $data = bin2hex($data);

		    return $data;
		}

		public function decrypt($encrypted_id)
		{
		    $data = pack('H*', $encrypted_id); // Translate back to binary
		    $data = mcrypt_decrypt(MCRYPT_BLOWFISH, Configure::read('Security.cipherSeed'), $data, 'ecb');
		    $data = base_convert($data, 36, 10);

		    return $data;
		}

		public function generateRandomString($length = 10) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}

    }
?>