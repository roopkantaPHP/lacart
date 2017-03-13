<?php
/*
*  Project       : Lacart
*  Author 		 : Bharat Borana
*  Creation Date : Mar 23, 2015
*  Description   : Used For Mobile app only. 
*/
//
define('GOOGLE_URL','https://android.googleapis.com/gcm/send');
//define("GOOGLE_API_KEY", "AIzaSyBdM7J2TkZI8q0hen9_sw_snJHtaVcLPFw"); for live
define("GOOGLE_API_KEY", "AIzaSyCg9Owfvjw7IYlDt7TXdihodZHM037r52A");

//for demo
//define("CK_PEM_PATH",WWW_ROOT."ck.pem");
//for live
define("CK_PEM_PATH",WWW_ROOT."ck_Dist_12June.pem");
define('PASS_PHARSE', '1234567890');

class PushComponent extends Component 
{
	public $name = "Push";
	/**
	 * Description : function will send push notification on device
	 * return @mixed
	 * Author  : Bharat Borana
	 * Created : Mar 23, 2015
	 */
	public function send($user_id,$data = array(),$kId,$type)
	{	 
		$res = array('status'=>false,'error_msg'=>'');
		$UserApp =  ClassRegistry::init('UserApp');
		$UserAppFetch = $UserApp->find('all',array(
									'conditions'=>array('UserApp.user_id'=>$user_id, 'UserApp.app_status' => 'active', 'UserApp.device_id !=' => ''),
									'fields'=>array('device_type','device_id'),
									'limit'=>10,
									'order'=>array('UserApp.created'=>'desc')
						));
		if(empty($UserAppFetch))
		{
			$res['error_msg'] = 'User is not register on the app';
			return $res;
		}
		
		$iphone_ids = $android_registration_ids = array();
		foreach($UserAppFetch as $_t)
		{
			if($_t['UserApp']['device_type'] == 'iphone')
			{
				$iphone_ids[] = $_t['UserApp']['device_id'];
			}
			else if($_t['UserApp']['device_type'] == 'android')
			{
				$android_registration_ids[] = $_t['UserApp']['device_id'];
			}
		}
		
		if(!empty($android_registration_ids))
		{
			$data['_registration_ids'] = $android_registration_ids;
			$data['_message']['id'] = $kId;
			$data['_message']['type'] = $type;

			$success = $this->send_message_android($data);
			if(@$success === true)
			{
				$res['status'] = true;
			}
			else
			{
				$res['error_msg'] = @$success['error_msg'];
			}
		}

		if(!empty($iphone_ids))
		{
			$data['_iphone_ids'] = $iphone_ids;
			$data['_message']['id'] = $kId;
			$data['_message']['type'] = $type;
			$success = $this->sendToIphone($data);
			if(@$success === true)
			{
				$res['status'] = true;
			}
			else
			{
				$res['error_msg'] = @$success['error_msg'];
			}
		}
		
		return $res;
	}
	
	/**
	 * Description : function will send push notification on device
	 * return @mixed
	 * Author  :gaurav thakur
	 * Created : Dec 17, 2013
	 */
	public function send_message_android($data = array())
	{
		if(!empty($data['_registration_ids']))
		{
			if(is_array($data['_registration_ids']))
			{
				$registatoin_ids = $data['_registration_ids'];
			}
			else
			{
				$registatoin_ids[] = $data['_registration_ids'];
			}
		}
		else
		{
			$success['error_msg'] = 'Registration id not found';
			return false;
		}
		if(!empty($data['_message']))
		{
			$message = $data['_message'];
		}
		else
		{
			$success['error_msg'] = 'Blank message';
			return false;
		}
		$url = GOOGLE_URL;
		$fields = array('registration_ids' => $registatoin_ids,'data' => array("message" => $message));
		$headers = array( "Authorization: key=".GOOGLE_API_KEY,"Content-Type: application/json");
		$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				$result=curl_exec($ch);

				if($result==FALSE)
				{
					die('Curl Failed');
				}
				else
				{
				}
		 if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
            return false;
            $success['error_msg'] = 'Curl Failed';            
            return $success;
        }
        $success['status'] = true;
		curl_close($ch);
        return $success;
}
	
	
	public function sendToIphone($data = array())
	{
		$success = array('status'=>true,'error_msg'=>'');
		$message = $device_tokens = array();
		if(!empty($data['_iphone_ids']))
		{
			if(is_array($data['_iphone_ids']))
			{
				$device_tokens = $data['_iphone_ids'];
			}
			else
			{
				$device_tokens[] = $data['_iphone_ids'];
			}
		}
		else
		{
			$success['error_msg'] = 'Device Token not found';
			return false;
		}
		if(!empty($data['_message']))
		{
			$message = $data['_message'];
		}
		else
		{
			$success['error_msg'] = 'Blank message';
			return false;
		}
		
		try
		{
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', CK_PEM_PATH);
			stream_context_set_option($ctx, 'ssl', 'passphrase', PASS_PHARSE);	
			$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);		
			
			if($fp)
			{	
				$body['aps'] = array(
					'badge' => 1,
					'alert' => $message['m'],
					'sound' => 'default'
				);
				$body['data'] = $message;

				$payload = json_encode($body);
				// Build the binary notification
				foreach ($device_tokens as $token)
				{
					if (!empty($token))
					{
						$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
				
						// Send it to the server
						$result = fwrite($fp, $msg, strlen($msg));		
						/*if (!$result)
							//echo 'Message not delivered' . PHP_EOL;
						else
							//echo 'Message successfully delivered '.$message['m']. PHP_EOL;
						*/
					}	
				}
				// Close the connection to the server
				fclose($fp);
			}
			else
			{
				$success['error_msg'] = 'Connection not placed.';
				return false;
			}
		}
		catch(exception $e)
		{
			$success['error_msg'] = 'Error Occured';
			return false;
		}

	
	}
}
