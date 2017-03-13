<?php
App::uses('AppController', 'Controller');

/** 
*  Project : FNB 
*  Author : Agam 
*  Creation Date : 22-Sep-2014 
*  Description : This is Api controller file which will be called by the mobile devices
*/

class ApiController extends AppController {

	var $name = 'Api';
	var $uses = array('Api');
	var $layout = false;
	var $autoRender = false;
    var $limit = 15;
    
   	function beforeFilter()
	{
		Configure::write('debug', 2);
		$this->layout = false;
		$this->autoRender = false;
		if($this->Auth->User('id'))
		{
			$this->Auth->logout();
		}
		parent::beforeFilter();
		$this->Auth->allow();
	}
	
	public $components = array('SuiteTest','Paypal','Push','Email','Stripe');
   
	/*
	 * User Registeration API 
	 * Author:- Agam
	 */
	public function register()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		App::import('Model', 'User');
		$user = new User();
		if(!isset($data['name']) || empty($data['name']))
		{
			$error .= 'Name is Required. ';
		}
		if(!isset($data['email']) || empty($data['email']))
		{
			$error .= 'Email is Required. ';
		}
		else
		{
			$email = $data['email']; 
			$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
			if (!preg_match($regex, $email))
			{
				$error .= 'Email Format is not valid. ';
			}
		}
		
		if(!isset($data['password']) || empty($data['password']))
		{
			$error .= 'Password is Required. ';
		}
		if(!isset($data['confirm_password']) || empty($data['confirm_password']))
		{
			$error .= 'Confirm Password is Required. ';
		}
		if(isset($data['password']) && !empty($data['password']) && isset($data['confirm_password']) && !empty($data['confirm_password']))
		{
			if($data['password'] != $data['confirm_password'])
			{
				$error .= 'Passwords does not match. ';
			}
		}

		if(!isset($data['phone']) || empty($data['phone']))
		{
			$error .= 'Phone is Required.';
		}

		if(empty($error))
		{
			$already_user = $user->find(
					'first', array(
						'fields' => array('id','email','phone'),
						'recursive' => -1,
						'conditions' => array(
							'OR'=>array(array('User.email' => $data['email']),array('User.phone' => $data['phone']))
						)
					)
				);
				if(!empty($already_user))
				{
					if($already_user['User']['email']==$data['email'])
						$error .= 'Email Id already in use. Please Specify another email id.';

					if($already_user['User']['phone']==$data['phone'])
						$error .= 'Phone number already in use. Please enter another phone number.';
				}
		}

		if(!empty($error))
		{
			$this->response = array(
				'status' => 400,
				'message' => $error
			);
		}else
	 	{
			$user_data['User']['name'] = $data['name'];
			$user_data['User']['email'] = $data['email'];
			$user_data['User']['password'] = $data['password'];
			$user_data['User']['group_id'] = NORMAL_USER;
			$user_data['User']['phone'] = $data['phone'];
			
			$emValue = trim($data['email']);

			$Email = new CakeEmail('smtp');

			$this->loadModel('EmailTemplate');
			$randString = $this->Paypal->generateRandomString(25);
			$linkTag = BASE_URL.'users/verify_user?v_vode='.$randString;

			$arr = array();
			$arr['{{name}}'] = $data['email'];
			$arr['{{activation_url}}'] = $linkTag;
			
			$email_content = $this->EmailTemplate->findBySlug('verify-email');


			$subject = $email_content['EmailTemplate']['subject'];
			
			$content = $email_content['EmailTemplate']['content'];
			$content = str_replace(array_keys($arr), array_values($arr), $content);

			$reply_to_email = REPLYTO_EMAIL;

			$Email->from(FROM_EMAIL);
			$Email->to($emValue);
			$Email->subject($subject);
			$Email->replyTo($reply_to_email);
			$Email->emailFormat('html');
			
			if($Email->send($content))
			{
				$user_data['User']['email_verification_code'] = $randString;
				$user->create();
				if($user->save($user_data))
				{
					$user_data = $user->find(
						'first', array(
							'fields' => array('id', 'name', 'phone', 'email', 'created', 'modified'),
							'recursive' => -1,
							'conditions' => array(
								'User.id' => $user->getLastInsertId()
							)
						)
					);
					$user_data['User']['is_new_user'] = 1;
					$user_data['User']['isRegistrationConfirmed'] = 0;
					
					if(isset($data['device_id']) && !empty($data['device_id']) && isset($data['device_type']) && !empty($data['device_type']))
					{
						$this->loadModel('UserApp');
						if(!$this->UserApp->find('count',array('conditions'=>array('UserApp.user_id'=>$user_data['User']['id'],'UserApp.device_id'=>$data['device_id']))))
						{
							$this->UserApp->deleteAll(array('UserApp.user_id' => $user_data['User']['id']), false);
							$this->UserApp->create();
							$this->UserApp->set('user_id',$user_data['User']['id']);
							$this->UserApp->set('device_type',$data['device_type']);
							$this->UserApp->set('device_id',$data['device_id']);
							$this->UserApp->save(null);
						}
					}
				
					$this->response = array(
						'status' => 200,
						'message' => 'User Successfully saved',
						'data' => $user_data 
					);
				}
				else
				{
					$this->response = array(
						'status' => 400,
						'message' => 'User cannot be saved right now. Please try again.'
					);
				}
			}
			else
			{
				$this->response = array(
						'status' => 400,
						'message' => 'Please enter valid email address.'
					);
			}
		}
		echo json_encode($this->response);
	}


/*
 * User Login API 
 * Author:- Agam
 */
	public function login()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['email']) || empty($data['email']))
		{
			$error .= 'Email is Required. ';
		} 
		else
		{
			$email = $data['email']; 
			$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
			if (!preg_match($regex, $email))
			{
				$error .= 'Email Format is not valid. ';
			}
		}
		if(!isset($data['password']) || empty($data['password']))
		{
			$error .= 'Password is Required. ';
		}

		if(!empty($error))
		{
			$this->response = array(
				'status' => 400,
				'message' => $error
			);
		}else
	 	{
	 		App::import('Model', 'User');
			$user = new User();
			$user_data = $user->find(
				'first', array(
					'fields' => array('id', 'name', 'phone', 'email_verified', 'is_new_user', 'email','image','facebook_id', 'google_id','is_complete_wizard', 'is_verified', 'is_active', 'address', 'state_id', 'city_id', 'zipcode', 'created', 'modified','password'),
					'recursive' => -1,
					'conditions' => array(
						'User.email' => $data['email'],
						'User.password' => $this->Auth->password($data['password']),
						'User.group_id' => NORMAL_USER
					)
				)
			);
			if(!empty($user_data))
			{				
				if(isset($user_data['User']['email_verified']) && $user_data['User']['email_verified']==1 && isset($user_data['User']['is_active']) && $user_data['User']['is_active']==1)
				{
					// Encrypt some data.
					$encryptedUid =  $this->Paypal->encrypt($user_data['User']['id']);
					$user_data['User']['rajnikant'] = $encryptedUid;

					$user_data['User']['image'] = (!empty($user_data['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$user_data['User']['image'],true) : "";
					$this->loadModel('Kitchen');
					$user_data['User']['Kitchen']['status'] = 0;
					if($this->Kitchen->find('count', array('conditions'=> array('Kitchen.status'=>'On','Kitchen.user_id'=>$user_data['User']['id']))))
					{
						$user_data['User']['Kitchen']['status'] = 1;
						$user_data['User']['is_complete_wizard'] = 1;	
					}

					if($user_data['User']['is_new_user']==1)
					{
						$user->updateAll(array('User.is_new_user'=>0),array('User.id'=>$user_data['User']['id']));
					}

					$user_data['User']['hide_password'] = 0;
					if($user_data['User']['password'] == AuthComponent::password('$$##||&||##$$'))
						$user_data['User']['hide_password'] = 1;

					if($user_data['User']['is_verified'])
						$user_data['User']['isRegistrationConfirmed'] = 1;
					else
						$user_data['User']['isRegistrationConfirmed'] = 0;

					$user_data['User']['state_name'] = '';
					if(isset($user_data['User']['state_id']) && !empty($user_data['User']['state_id']))
						$user_data['User']['state_name'] = $user_data['User']['state_id'];

					$user_data['User']['city_name'] = '';
					if(isset($user_data['User']['city_id']) && !empty($user_data['User']['city_id']))
						$user_data['User']['city_name'] = $user_data['User']['city_id'];
					
					if(isset($data['device_id']) && !empty($data['device_id']) && isset($data['device_type']) && !empty($data['device_type']))
					{
						$this->loadModel('UserApp');
						if(!$this->UserApp->find('count',array('conditions'=>array('UserApp.user_id'=>$user_data['User']['id'],'UserApp.device_id'=>$data['device_id']))))
						{
							$this->UserApp->deleteAll(array('UserApp.user_id' => $user_data['User']['id']), false);
							$this->UserApp->create();
							$this->UserApp->set('user_id',$user_data['User']['id']);
							$this->UserApp->set('device_type',$data['device_type']);
							$this->UserApp->set('device_id',$data['device_id']);
							$this->UserApp->save(null);
						}
					}
					//$user_data['STRIPE_PUBLISHABLE_KEY'] = STRIPE_PUBLISHABLE_KEY;
					$this->response = array(
						'status' => 200,
						'message' => 'User Successfully Logged in',
						'data' => $user_data['User']
					);	
				}
				else if(isset($user_data['User']['email_verified']) && $user_data['User']['email_verified']==0)
				{
					$this->response = array(
						'status' => 400,
						'message' => 'Please check your email account and verify your email address first.',
					);
				}
				else
				{
					$this->response = array(
						'status' => 400,
						'message' => 'Your account is status is deactivated. Please contact administrator.',
					);
				}
				
			} else
			{
				$this->response = array(
					'status' => 400,
					'message' => 'Invalid Credentials',
				);
			}
	 	}
	 	
		echo json_encode($this->response);
	}

/*
 * Facebook Login
 * Author : Praveen Pandey
 */
 	function facebook_login()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		$this->loadModel('User');
		
		if(!isset($data['facebook_id']) || empty($data['facebook_id']))
		{
			$error .= 'Facebook user id is Required. ';
		}
		if(!isset($data['name']) || empty($data['name']))
		{
			$error .= 'Name is Required. ';
		}
		if(!isset($data['email']) || empty($data['email']))
		{
			$error .= 'Email is Required. ';
		}
	
		if(!empty($error))
		{
			$this->response = array(
				'status' => 400,
				'message' => $error
			);
		}
		else
	 	{
		
	 		$user_data = $this->User->find('first',array(
	 											'fields' => array('id', 'name', 'phone', 'is_new_user', 'email','is_verified' ,'image','facebook_id','address', 'state_id', 'city_id', 'zipcode', 'google_id','is_complete_wizard', 'created', 'modified','password'),
												'conditions' => array(
														'User.facebook_id' => $data['facebook_id'],
														'User.group_id' => NORMAL_USER
												)
											)
									);
			if(!empty($user_data))
			{
				// Encrypt some data.
				$encryptedUid =  $this->Paypal->encrypt($user_data['User']['id']);
				$user_data['User']['rajnikant'] = $encryptedUid;

				if($user_data['User']['is_new_user']==1)
				{
					$this->User->updateAll(array('User.is_new_user'=>0),array('User.id'=>$user_data['User']['id']));
				}

				if(isset($data['Imageurl']) && !empty($data['Imageurl']) && empty($user_data['User']['image'])){
						$imageData = file_get_contents($data['Imageurl']);
						$FileName = mt_rand().'-'.time().'.jpg';
						$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
						fputs($file, $imageData);
						fclose($file);
						$this->User->set('id', $user_data['User']['id']);
						$this->User->saveField('image', $FileName);
						$user_data['User']['image'] = $FileName;
					}
				$user_data['User']['image'] = (!empty($user_data['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$user_data['User']['image'],true) : "";
				
				$this->loadModel('Kitchen');
				$user_data['User']['Kitchen']['status'] = 0;
				if($this->Kitchen->find('count', array('conditions'=> array('Kitchen.status'=>'On','Kitchen.user_id'=>$user_data['User']['id']))))
				{
					$user_data['User']['Kitchen']['status'] = 1;	
					$user_data['User']['is_complete_wizard'] = 1;	
				}

				$user_data['User']['hide_password'] = 0;
						if($user_data['User']['password'] == AuthComponent::password('$$##||&||##$$'))
							$user_data['User']['hide_password'] = 1;


				if($user_data['User']['is_verified'])
					$user_data['User']['isRegistrationConfirmed'] = 1;
				else
					$user_data['User']['isRegistrationConfirmed'] = 0;
				
				$user_data['User']['state_name'] = '';
				if(isset($user_data['User']['state_id']) && !empty($user_data['User']['state_id']))
					$user_data['User']['state_name'] = $user_data['User']['state_id'];

				$user_data['User']['city_name'] = '';
				if(isset($user_data['User']['city_id']) && !empty($user_data['User']['city_id']))
					$user_data['User']['city_name'] = $user_data['User']['city_id'];
				
				//$user_data['STRIPE_PUBLISHABLE_KEY'] = STRIPE_PUBLISHABLE_KEY;
				$this->response = array(
					'status' => 200,
					'message' => 'User Successfully Logged in',
					'data' => $user_data['User']
				);
			}
			else
			{
				$user_data = $this->User->find('first',array(
	 											'fields' => array('id', 'name', 'phone', 'image', 'is_new_user', 'email', 'is_verified','address', 'state_id', 'city_id', 'zipcode', 'facebook_id', 'google_id','is_complete_wizard', 'created', 'modified','password'),
												'conditions' => array(
														'User.email' => $data['email'],
												)
											)
									);

				if(!empty($user_data))
				{
					if($user_data['User']['is_new_user']==1)
					{
						$this->User->updateAll(array('User.is_new_user'=>0),array('User.id'=>$user_data['User']['id']));
					}
					// Encrypt some data.
					$encryptedUid =  $this->Paypal->encrypt($user_data['User']['id']);
					$user_data['User']['rajnikant'] = $encryptedUid;
					$user_data['User']['email_verified'] = 1;
					if(isset($data['Imageurl']) && !empty($data['Imageurl']) && empty($user_data['User']['image'])){
						$imageData = file_get_contents($data['Imageurl']);
						$FileName = mt_rand().'-'.time().'.jpg';
						$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
						fputs($file, $imageData);
						fclose($file);
						$this->User->set('image', $FileName);
					}
					
					$fresh_data['id'] = $user_data['User']['id'];
					$fresh_data['group_id'] = NORMAL_USER;
					$fresh_data['facebook_id'] = $data['facebook_id'];
					$this->User->save($fresh_data);
					$this->loadModel('Kitchen');
					$user_data['User']['Kitchen']['status'] = 0;
					$user_data['User']['image'] = (!empty($user_data['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$user_data['User']['image'],true) : "";
					
					$user_data['User']['hide_password'] = 0;
						if($user_data['User']['password'] == AuthComponent::password('$$##||&||##$$'))
							$user_data['User']['hide_password'] = 1;

					if($user_data['User']['is_verified'])
						$user_data['User']['isRegistrationConfirmed'] = 1;
					else
						$user_data['User']['isRegistrationConfirmed'] = 0;
					
					$user_data['User']['state_name'] = '';
					if(isset($user_data['User']['state_id']) && !empty($user_data['User']['state_id']))
						$user_data['User']['state_name'] = $user_data['User']['state_id'];

					$user_data['User']['city_name'] = '';
					if(isset($user_data['User']['city_id']) && !empty($user_data['User']['city_id']))
						$user_data['User']['city_name'] = $user_data['User']['city_id'];
					
					if($this->Kitchen->find('count', array('conditions'=> array('Kitchen.status'=>'On','Kitchen.user_id'=>$user_data['User']['id']))))
					{
						$user_data['User']['Kitchen']['status'] = 1;	
						$user_data['User']['is_complete_wizard'] = 1;	
					}

					//$user_data['STRIPE_PUBLISHABLE_KEY'] = STRIPE_PUBLISHABLE_KEY;
					$this->response = array(
						'status' => 200,
						'message' => 'User Successfully Logged in',
						'data' => $user_data['User']
					);
				}
				else
				{
					$user_data['User']['email_verified'] = 1;
					$user_data['User']['name'] = $data['name'];
					$user_data['User']['email'] = $data['email'];
					$user_data['User']['group_id'] = NORMAL_USER;
					$user_data['User']['phone'] = (!empty($data['phone'])) ? $data['phone'] : '';
					$user_data['User']['facebook_id'] = $data['facebook_id'];
					$user_data['User']['password'] = AuthComponent::password('$$##||&||##$$');
					if(isset($data['Imageurl']) && !empty($data['Imageurl']) && empty($user_data['User']['image'])){
						$imageData = file_get_contents($data['Imageurl']);
						$FileName = mt_rand().'-'.time().'.jpg';
						$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
						fputs($file, $imageData);
						fclose($file);
						$user_data['User']['image'] = $FileName;
					}
					$this->User->set($user_data);
					
					$this->User->create();
					if($this->User->save($user_data, array('validate' => false)))
					{
						$user_data = $this->User->find(
							'first', array(
								'fields' => array('id', 'name', 'phone', 'is_new_user', 'email','image','facebook_id', 'is_verified', 'address', 'state_id', 'city_id', 'zipcode','google_id','is_complete_wizard', 'created', 'modified','password'),
								'recursive' => -1,
								'conditions' => array(
									'User.id' => $this->User->getLastInsertId()
								)
							)
						);
						// Encrypt some data.
						$encryptedUid =  $this->Paypal->encrypt($user_data['User']['id']);
						$user_data['User']['rajnikant'] = $encryptedUid;
						
						$user_data['User']['hide_password'] = 0;
						if($user_data['User']['password'] == AuthComponent::password('$$##||&||##$$'))
							$user_data['User']['hide_password'] = 1;

						if($user_data['User']['is_verified'])
							$user_data['User']['isRegistrationConfirmed'] = 1;
						else
							$user_data['User']['isRegistrationConfirmed'] = 0;
						
						$user_data['User']['state_name'] = '';
						if(isset($user_data['User']['state_id']) && !empty($user_data['User']['state_id']))
							$user_data['User']['state_name'] = $user_data['User']['state_id'];

						$user_data['User']['city_name'] = '';
						if(isset($user_data['User']['city_id']) && !empty($user_data['User']['city_id']))
							$user_data['User']['city_name'] = $user_data['User']['city_id'];

						$user_data['User']['image'] = (!empty($user_data['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$user_data['User']['image'],true) : "";
					
						$this->loadModel('Kitchen');
						$user_data['User']['Kitchen']['status'] = 0;
						if($this->Kitchen->find('count', array('conditions'=> array('Kitchen.status'=>'On','Kitchen.user_id'=>$user_data['User']['id']))))
						{
							$user_data['User']['Kitchen']['status'] = 1;	
							$user_data['User']['is_complete_wizard'] = 1;	
						}

						//$user_data['STRIPE_PUBLISHABLE_KEY'] = STRIPE_PUBLISHABLE_KEY;
						$this->response = array(
							'status' => 200,
							'message' => 'Successfully Registered!',
							'data' => $user_data['User']
						);
					} else
					{
						$this->response = array(
							'status' => 400,
							'message' => 'User cannot be saved right now. Please try again.'
						);
					}
				}

			}
			if($this->response['status']==200)
				{
					if(isset($data['device_id']) && !empty($data['device_id']) && isset($data['device_type']) && !empty($data['device_type']))
					{
						$this->loadModel('UserApp');
						if(!$this->UserApp->find('count',array('conditions'=>array('UserApp.user_id'=>$user_data['User']['id'],'UserApp.device_id'=>$data['device_id']))))
						{
							$this->UserApp->deleteAll(array('UserApp.user_id' => $user_data['User']['id']), false);
							$this->UserApp->create();
							$this->UserApp->set('user_id',$user_data['User']['id']);
							$this->UserApp->set('device_type',$data['device_type']);
							$this->UserApp->set('device_id',$data['device_id']);
							$this->UserApp->save(null);
						}
					}
				}
	 	}
		echo json_encode($this->response);
	}

/*
 * Google Login
 * Author : Praveen Pandey
 */
 	function google_login()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		App::import('Model', 'User');
		$user = new User();
		if(!isset($data['google_id']) || empty($data['google_id']))
		{
			$error .= 'Google user id is Required. ';
		}
		if(!isset($data['name']) || empty($data['name']))
		{
			$error .= 'Name is Required. ';
		}
		if(!isset($data['email']) || empty($data['email']))
		{
			$error .= 'Email is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
				'status' => 400,
				'message' => $error
			);
		}
		else
	 	{
	 		$user_data = $user->find('first',array(
	 											'fields' => array('id', 'name', 'phone', 'is_new_user', 'email','image','facebook_id','address', 'state_id', 'city_id', 'zipcode', 'is_verified', 'google_id','is_complete_wizard', 'created', 'modified','password'),
												'conditions' => array(
														'User.google_id' => $data['google_id'],
														'User.group_id' => NORMAL_USER
												)
											)
									);
	 		
			if(!empty($user_data))
			{
				// Encrypt some data.
				$encryptedUid =  $this->Paypal->encrypt($user_data['User']['id']);
				$user_data['User']['rajnikant'] = $encryptedUid;
				if($user_data['User']['is_new_user']==1)
				{
					$user->updateAll(array('User.is_new_user'=>0),array('User.id'=>$user_data['User']['id']));
				}
				if(isset($data['Imageurl']) && !empty($data['Imageurl']) && empty($user_data['User']['image'])){
					$imageData = file_get_contents($data['Imageurl']);
					$FileName = mt_rand().'-'.time().'.jpg';
					$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
					fputs($file, $imageData);
					fclose($file);
					$this->User->set('id', $user_data['User']['id']);
					$this->User->saveField('image', $FileName);
					$user_data['User']['image'] = $FileName;
				}
				$user_data['User']['image'] = (!empty($user_data['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$user_data['User']['image'],true) : "";
				$this->loadModel('Kitchen');
				

				$user_data['User']['hide_password'] = 0;
						if($user_data['User']['password'] == AuthComponent::password('$$##||&||##$$'))
							$user_data['User']['hide_password'] = 1;


				if($user_data['User']['is_verified'])
					$user_data['User']['isRegistrationConfirmed'] = 1;
				else
					$user_data['User']['isRegistrationConfirmed'] = 0;
				
				$user_data['User']['state_name'] = '';
				if(isset($user_data['User']['state_id']) && !empty($user_data['User']['state_id']))
					$user_data['User']['state_name'] = $user_data['User']['state_id'];

				$user_data['User']['city_name'] = '';
				if(isset($user_data['User']['city_id']) && !empty($user_data['User']['city_id']))
					$user_data['User']['city_name'] = $user_data['User']['city_id'];
				
				$user_data['User']['Kitchen']['status'] = 0;
				if($this->Kitchen->find('count', array('conditions'=> array('Kitchen.status'=>'On','Kitchen.user_id'=>$user_data['User']['id']))))
				{
					$user_data['User']['Kitchen']['status'] = 1;	
					$user_data['User']['is_complete_wizard'] = 1;	
				}

				//$user_data['STRIPE_PUBLISHABLE_KEY'] = STRIPE_PUBLISHABLE_KEY;

				$this->response = array(
					'status' => 200,
					'message' => 'User Successfully Logged in',
					'data' => $user_data['User']
				);
			}
			else
			{
				$user_data = $user->find('first',array(
	 											'fields' => array('id', 'name', 'phone', 'is_new_user', 'email','image','facebook_id','address', 'state_id', 'city_id', 'zipcode', 'is_verified', 'google_id','is_complete_wizard', 'created', 'modified','password'),
												'conditions' => array(
														'User.email' => $data['email'],
												)
											)
									);
				if(!empty($user_data))
				{
					// Encrypt some data.
					$encryptedUid =  $this->Paypal->encrypt($user_data['User']['id']);
					$user_data['User']['rajnikant'] = $encryptedUid;
					$user_data['User']['email_verified'] = 1;
					if($user_data['User']['is_new_user']==1)
					{
						$user->updateAll(array('User.is_new_user'=>0),array('User.id'=>$user_data['User']['id']));
					}
					if(isset($data['Imageurl']) && !empty($data['Imageurl']) && empty($user_data['User']['image'])){
						$imageData = file_get_contents($data['Imageurl']);
						$FileName = mt_rand().'-'.time().'.jpg';
						$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
						fputs($file, $imageData);
						fclose($file);
						$this->User->set('image', $FileName);
						$user_data['User']['image'] = $FileName;
					}
					$fresh_data['id'] = $user_data['User']['id'];
					$fresh_data['group_id'] = NORMAL_USER;
					$fresh_data['google_id'] = $data['google_id'];
					$user->save($fresh_data);
					
					$this->loadModel('Kitchen');
					$user_data['User']['Kitchen']['status'] = 0;
					if($this->Kitchen->find('count', array('conditions'=> array('Kitchen.status'=>'On','Kitchen.user_id'=>$user_data['User']['id']))))
					{
						$user_data['User']['Kitchen']['status'] = 1;	
						$user_data['User']['is_complete_wizard'] = 1;	
					}
					
					$user_data['User']['hide_password'] = 0;
						if($user_data['User']['password'] == AuthComponent::password('$$##||&||##$$'))
							$user_data['User']['hide_password'] = 1;

					if($user_data['User']['is_verified'])
						$user_data['User']['isRegistrationConfirmed'] = 1;
					else
						$user_data['User']['isRegistrationConfirmed'] = 0;
					
					$user_data['User']['state_name'] = '';
					if(isset($user_data['User']['state_id']) && !empty($user_data['User']['state_id']))
						$user_data['User']['state_name'] = $user_data['User']['state_id'];

					$user_data['User']['city_name'] = '';
					if(isset($user_data['User']['city_id']) && !empty($user_data['User']['city_id']))
						$user_data['User']['city_name'] = $user_data['User']['city_id'];
					
					$user_data['User']['image'] = (!empty($user_data['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$user_data['User']['image'],true) : "";
					
					//$user_data['STRIPE_PUBLISHABLE_KEY'] = STRIPE_PUBLISHABLE_KEY;

					$this->response = array(
						'status' => 200,
						'message' => 'User Successfully Logged in',
						'data' => $user_data['User']
					);
				}
				else
				{
					$user_data['User']['email_verified'] = 1;
					$user_data['User']['name'] = $data['name'];
					$user_data['User']['email'] = $data['email'];
					$user_data['User']['password'] = AuthComponent::password('$$##||&||##$$');
					
					if(isset($data['Imageurl']) && !empty($data['Imageurl']) && empty($user_data['User']['image'])){
						$imageData = file_get_contents($data['Imageurl']);
						$FileName = mt_rand().'-'.time().'.jpg';
						$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
						fputs($file, $imageData);
						fclose($file);
						$user_data['User']['image'] = $FileName;
					}
					$user_data['User']['group_id'] = NORMAL_USER;
					$user_data['User']['phone'] = (!empty($data['phone'])) ? $data['phone'] : '';
					$user_data['User']['google_id'] = $data['google_id'];
					$user->create();
					if($user->save($user_data, array('validate' => false)))
					{
						$user_data = $user->find(
							'first', array(
								'fields' => array('id', 'name', 'phone', 'email', 'is_new_user', 'image','facebook_id','address', 'state_id', 'city_id', 'zipcode', 'is_verified', 'google_id','is_complete_wizard', 'created', 'modified','password'),
								'recursive' => -1,
								'conditions' => array(
									'User.id' => $user->getLastInsertId()
								)
							)
						);
						// Encrypt some data.
						$encryptedUid =  $this->Paypal->encrypt($user_data['User']['id']);
						$user_data['User']['rajnikant'] = $encryptedUid;
						
						$user_data['User']['hide_password'] = 0;
						if($user_data['User']['password'] == AuthComponent::password('$$##||&||##$$'))
							$user_data['User']['hide_password'] = 1;

						if($user_data['User']['is_verified'])
							$user_data['User']['isRegistrationConfirmed'] = 1;
						else
							$user_data['User']['isRegistrationConfirmed'] = 0;
						
						$user_data['User']['state_name'] = '';
						if(isset($user_data['User']['state_id']) && !empty($user_data['User']['state_id']))
							$user_data['User']['state_name'] = $user_data['User']['state_id'];

						$user_data['User']['city_name'] = '';
						if(isset($user_data['User']['city_id']) && !empty($user_data['User']['city_id']))
							$user_data['User']['city_name'] = $user_data['User']['city_id'];
						
						$user_data['User']['image'] = (!empty($user_data['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$user_data['User']['image'],true) : "";
					
						$this->loadModel('Kitchen');
						$user_data['User']['Kitchen']['status'] = 0;
						if($this->Kitchen->find('count', array('conditions'=> array('Kitchen.status'=>'On','Kitchen.user_id'=>$user_data['User']['id']))))
						{
							$user_data['User']['Kitchen']['status'] = 1;
							$user_data['User']['is_complete_wizard'] = 1;		
						}

						//$user_data['STRIPE_PUBLISHABLE_KEY'] = STRIPE_PUBLISHABLE_KEY;

						$this->response = array(
							'status' => 200,
							'message' => 'Successfully Registered!',
							'data' => $user_data['User']
						);
					} else
					{
						$this->response = array(
							'status' => 400,
							'message' => 'User cannot be saved right now. Please try again.'
						);
					}
				}
			}
			if($this->response['status']==200)
			{
				if(isset($data['device_id']) && !empty($data['device_id']) && isset($data['device_type']) && !empty($data['device_type']))
				{
					$this->loadModel('UserApp');
					if(!$this->UserApp->find('count',array('conditions'=>array('UserApp.user_id'=>$user_data['User']['id'],'UserApp.device_id'=>$data['device_id']))))
					{
						$this->UserApp->deleteAll(array('UserApp.user_id' => $user_data['User']['id']), false);
						$this->UserApp->create();
						$this->UserApp->set('user_id',$user_data['User']['id']);
						$this->UserApp->set('device_type',$data['device_type']);
						$this->UserApp->set('device_id',$data['device_id']);
						$this->UserApp->save(null);
					}
				}
			}

			
	 	}
		echo json_encode($this->response);
	}

/*
 * Forgot Password
 * Author : Praveen Pandey
 */
	function forgot_password()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		App::import('Model', 'User');
		$user = new User();
		if(!isset($data['email']) || empty($data['email']))
		{
			$error .= 'Email is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 400,
				'message' => $error
			);
		}else
	 	{
	 		$user_data = $user->findByEmail($data['email']);
			if($user_data)
			{
				$template = "forgot-password";
				$forgot_token = md5(uniqid(mt_rand(), true));
				$user->set('id', $user_data['User']['id']);
				$user->saveField('token', $forgot_token);
				$token = array('{{name}}','{{reset_password_link}}');
				$reset_link = Router::url(array('controller'=>'users','action'=>'reset_password',$user_data['User']['id'],$forgot_token),true);
				
				if(empty($user_data['User']['name']))
					$name = 'User';
				else
					$name = $user_data['User']['name'];

				$token_value = array($name,$reset_link);
				$this->_send_email($user_data['User']['email'], $token, $token_value, $template, '');
				$this->response = array(
							'status' => 1,
							'message' => "Password reset link has been sent to your email id. Please check"
				);
			}
			else
			{
				$this->response = array(
							'status' => 2,
							'message' => "User does not exists with given email!"
				);
			}
		}
		echo json_encode($this->response);
	}

/*
 * Method	: personal_info
 * Author 	: Praveen Pandey
 * Created	: 13 Oct, 2014
 * @Add Personal inforamtion and kitchen data
 */
	public function personal_info()
	{
		//$this->_isValidApiRequest();
		////header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		$this->loadModel('User');
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['name']) || empty($data['name']))
		{
			$error .= 'Name is Required. ';
		}
		if(!isset($data['city']) || empty($data['city']))
		{
			//$error .= 'City is Required. ';
		}
		if(!isset($data['image']) || empty($data['image']))
		{
			//$error .= 'Image is Required. ';
		}
	
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,	
				'message' => $error
			);
		}
		else
	 	{
	 		$userData['id'] = $data['user_id'];
			if(isset($data['name']))
			{
				$userData['name'] = $data['name'];
			}
			if(!empty($data['image']))
			{
				if(filter_var($data['image'], FILTER_VALIDATE_URL))
				{
					
				}
				else if($data['image'] != 'undefined' && $data['image'] != '' && substr( $data['image'], 0, 10 ) === "data:image")
				{
					$FileName = mt_rand().'-'.time().'.jpeg';
					$NewSaveImageLoc = PROFILE_IMAGE_PATH . $FileName;
					$this->_base64_to_jpeg($data['image'],$NewSaveImageLoc);
					if(file_exists($NewSaveImageLoc)){
						$userData['image'] = $FileName;
					}
				}
			}
			//$userData['city'] = $data['city'];
			if(isset($data['community']))
			{
				$userData['community'] = $data['community'];
			}
			$userData['is_complete_wizard'] = 1;
			if(isset($data['description'])) {	$userData['description'] = $data['description']; }
			
			if(isset($data['stripe_user_id']) && !empty($data['stripe_user_id'])){ $userData['stripe_user_id'] = $data['stripe_user_id'];}
			if(isset($data['stripe_publish_key']) && !empty($data['stripe_publish_key'])) { $userData['stripe_publish_key'] = $data['stripe_publish_key'];}
			
			$kitchenData['user_id'] = $data['user_id'];
			if(isset($data['kitchen_name'])) { $kitchenData['name'] = $data['kitchen_name'];}
			if(isset($data['kitchen_description'])) {$kitchenData['description'] = $data['kitchen_description'];}
			if(isset($data['allergy'])) {$userData['allergy'] = $data['allergy'];}
			if(isset($data['diet'])) {	$userData['diet'] = $data['diet']; }
			if(isset($data['kitchen_status'])) {$kitchenData['status'] = $data['kitchen_status'];}
			if(isset($data['ssn_no'])) {$kitchenData['ssn_no'] = $data['ssn_no'];}
			if(isset($data['kitchen_address']))
			{
				$kitchenData['address'] = $data['kitchen_address'];
				$geoAddress = $this->SuiteTest->_getAddressFormGeoode($data['kitchen_address']);
				$kitchenData['city'] = (!empty($geoAddress['locality'])) ? $geoAddress['locality'] : "";
				$kitchenData['state'] = (!empty($geoAddress['administrative_area_level_1'])) ? $geoAddress['administrative_area_level_1'] : "";
				$kitchenData['country'] = (!empty($geoAddress['country'])) ? $geoAddress['country'] : "";
			}
			if(!empty($data['kitchen_lat_long']))
			{
				$latlong = explode(',', $data['kitchen_lat_long']);
				$kitchenData['lat'] = $latlong[0];
				$kitchenData['lng'] = $latlong[1];	
			}
			
			//if(isset($data['dining_dine_in'])) {$kitchenData['dining_dine_in'] = $data['dining_dine_in']; }
			//if(isset($data['dining_take_out'])) {$kitchenData['dining_take_out'] = $data['dining_take_out']; }
			
			if(!empty($data['cover_photo']) && substr( $data['cover_photo'], 0, 10 ) === "data:image")
			{
				$FileName = mt_rand().'-'.time().'.jpeg';
				$NewSaveImageLoc = KITCHEN_IMAGE_PATH . $FileName;
				$this->_base64_to_jpeg($data['cover_photo'],$NewSaveImageLoc);
				if(file_exists($NewSaveImageLoc)){
					$kitchenData['cover_photo'] = $FileName;
				}
			}
			$userNamenEmail = $this->User->findById($data['user_id']);
			$AccountStatus = 0;
			
			if(isset($data['paypal_id']) && !empty($data['paypal_id']))
			{
				$userData['paypal_id'] = $data['paypal_id'];
				$userData['paypal_name'] = $data['paypal_name'];
				$userData['paypal_lname'] = $data['paypal_lname'];
				$userData['is_paypal_verified'] = 1;
				$AccountStatus = 1;
			}

			if(isset($data['stripe_user_id']) && !empty($data['stripe_user_id']))
			{
				$userData['stripe_user_id'] = $data['stripe_user_id'];
				$userData['stripe_publish_id'] = $data['stripe_publish_id'];
				$AccountStatus = 1;
			}
			
			if((isset($data['kitchen_status']) && $data['kitchen_status']==0) || (isset($data['kitchen_status']) && $data['kitchen_status']==1 && $AccountStatus == 1) || (!isset($data['kitchen_status']) && empty($userNamenEmail['Kitchen']['id'])) || (isset($userNamenEmail['User']['stripe_user_id']) && !empty($userNamenEmail['User']['stripe_user_id'])) || (isset($userNamenEmail['User']['is_paypal_verified']) && $userNamenEmail['User']['is_paypal_verified'] == 1)){
				
				$this->User->set($userData);
				if($this->User->validates()){
					if(isset($userData['id']) && !empty($userData))
						$this->User->create();

					if($this->User->save($userData)){
						$this->response = array(
							'status' => 1,
							'message' => "Data saved successfully"
						);
					}
					else{
						$this->response = array(
							'status' => 2,
							'message' => "Data Not Saved."
						);
					}
				}
				else{
					$errors = $this->User->validationErrors;
					$err = '';
					foreach ($errors as $key => $value) {
						$err .= $value[0].','; 
					}
					$this->response = array(
							'status' => 2,
							'message' => trim($err,',')
						);
				}
				if(isset($data['kitchen_status']) && !empty($data['kitchen_status'])){
					if(!empty($data['kitchen_images']))
					{
						$data['kitchen_images'] = json_decode($data['kitchen_images'],true);
						$i = 1;
						$existing_image_ids = array();
						foreach ($data['kitchen_images'] as $key => $base64image)
						{
							if(filter_var($base64image, FILTER_VALIDATE_INT))
							{
								$existing_image_ids[] = $base64image;
							}
							else if($base64image != 'undefined' && $base64image != '' && substr( $base64image, 0, 10 ) === "data:image")
							{
								$FileName = str_replace(' ', '-', $kitchenData['name']).mt_rand().'-'.time().'.jpeg';
								$NewSaveImageLoc = KITCHEN_IMAGE_PATH . $FileName;
								$this->_base64_to_jpeg($base64image,$NewSaveImageLoc);
								if(file_exists($NewSaveImageLoc)){
									$kitchenData['UploadImage'][$i]['name'] = $FileName;
									$kitchenData['UploadImage'][$i]['type'] = 'kitchen';
									$i++;
								}
							}	
						}
					}
					$this->loadModel('Kitchen');
					$kitchen = $this->Kitchen->find('first',array('conditions'=>array('Kitchen.user_id'=>$data['user_id'])));
					if(!empty($kitchen))
					{
						$kitchenData['id'] = $kitchen['Kitchen']['id'];
						if(!empty($kitchenData['UploadImage']))
						{
							$this->loadModel('UploadImage');
							$this->UploadImage->deleteAll(array('UploadImage.type' => 'kitchen', 'UploadImage.related_id' => $kitchenData['id'],'UploadImage.id !=' => $existing_image_ids), false);
						}
					}

					$this->Kitchen->create();
					if($this->Kitchen->saveAll($kitchenData))
					{
						$this->response = array(
								'status' => 1,
								'message' => "Data saved successfully"
						);
					}
					else
					{
						$this->response = array(
							'status' => 2,
							'message' => "Data not saved."
						);
					}
				}
			}
			else{
				$this->response = array(
							'status' => 2,
							'message' => 'Please update your stripe account details.'
					);
			}
		}
		echo json_encode($this->response);
	}


	//
	function edit_kitchen()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		$this->loadModel('User');
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		else
		{
			if(!$this->User->find('count', array('conditions'=>array('User.id'=>$data['user_id']))))
			{
				$error .= 'User not exists. ';
			}
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$userData['id'] = $data['user_id'];
			
			if(isset($data['stripe_user_id']) && !empty($data['stripe_user_id'])){ $userData['stripe_user_id'] = $data['stripe_user_id'];}
			if(isset($data['stripe_publish_key']) && !empty($data['stripe_publish_key'])) { $userData['stripe_publish_key'] = $data['stripe_publish_key'];}
			
			$kitchenData['user_id'] = $data['user_id'];
			if(isset($data['kitchen_name'])) {
			$kitchenData['name'] = $data['kitchen_name'];
			}
			if(isset($data['kitchen_description'])) {$kitchenData['description'] = $data['kitchen_description'];}
			
			if(isset($data['diet'])) {
			$userData['diet'] = $data['diet'];
			}
			if(isset($data['kitchen_status'])) {
			$kitchenData['status'] = $data['kitchen_status'];
			}
			if(isset($data['kitchen_address']))
			{
				$kitchenData['address'] = $data['kitchen_address'];
				$geoAddress = $this->SuiteTest->_getAddressFormGeoode($data['kitchen_address']);
				$kitchenData['city'] = (!empty($geoAddress['locality'])) ? $geoAddress['locality'] : "";
				$kitchenData['state'] = (!empty($geoAddress['administrative_area_level_1'])) ? $geoAddress['administrative_area_level_1'] : "";
				$kitchenData['country'] = (!empty($geoAddress['country'])) ? $geoAddress['country'] : "";
			}
			if(!empty($data['kitchen_lat_long']))
			{
				$latlong = explode(',', $data['kitchen_lat_long']);
				$kitchenData['lat'] = $latlong[0];
				$kitchenData['lng'] = $latlong[1];	
			}
			
			/*if(isset($data['dining_dine_in'])) {
				$kitchenData['dining_dine_in'] = $data['dining_dine_in'];
			}

			if(isset($data['dining_take_out'])) {
				$kitchenData['dining_take_out'] = $data['dining_take_out'];
			}*/

			if(isset($data['ssn_no'])) {
				$kitchenData['ssn_no'] = $data['ssn_no'];
			}
			
			if(!empty($data['cover_photo']) && substr( $data['cover_photo'], 0, 10 ) === "data:image")
			{
				$FileName = mt_rand().'-'.time().'.jpeg';
				$NewSaveImageLoc = KITCHEN_IMAGE_PATH . $FileName;
				$this->_base64_to_jpeg($data['cover_photo'],$NewSaveImageLoc);
				if(file_exists($NewSaveImageLoc)){
					$kitchenData['cover_photo'] = $FileName;
				}
			}
			$userNamenEmail = $this->User->findById($data['user_id']);
			
			
			$AccountStatus = 0;
			
			$paypalData['id'] = $data['user_id'];
			if(isset($data['paypal_id']) && !empty($data['paypal_id']))
			{
				$paypalData['paypal_id'] = $data['paypal_id'];
				$paypalData['paypal_name'] = $data['paypal_name'];
				$paypalData['paypal_lname'] = $data['paypal_lname'];
				if(!empty($data['paypal_id']))
				{
					$paypalData['is_paypal_verified'] = 1;
					$AccountStatus=1;
				}
				else
				{
					$paypalData['is_paypal_verified'] = 0;
				}

				$this->User->save($paypalData);
			}
			
			if(isset($data['stripe_user_id']) && !empty($data['stripe_user_id']))
			{
				$paypalData['stripe_user_id'] = $data['stripe_user_id'];
				$paypalData['stripe_publish_id'] = $data['stripe_publish_id'];
				$AccountStatus=1;

				$this->User->save($paypalData);
			}

			if($AccountStatus==1 || (isset($userNamenEmail['User']['stripe_user_id']) && !empty($userNamenEmail['User']['stripe_user_id'])) || (isset($userNamenEmail['User']['is_paypal_verified']) && $userNamenEmail['User']['is_paypal_verified']==1))
			{
				$existing_image_ids = array();
				if(!empty($data['kitchen_images']))
				{
					$data['kitchen_images'] = json_decode($data['kitchen_images'],true);
					$i = 1;
					foreach ($data['kitchen_images'] as $key => $base64image)
					{
						if(filter_var($base64image, FILTER_VALIDATE_INT))
						{
							$existing_image_ids[] = $base64image;
						}
						else if($base64image != 'undefined' && $base64image != '' && substr( $base64image, 0, 10 ) === "data:image")
						{
							$FileName = str_replace(' ', '-', $kitchenData['name']).mt_rand().'-'.time().'.jpeg';
							$NewSaveImageLoc = KITCHEN_IMAGE_PATH . $FileName;
							$this->_base64_to_jpeg($base64image,$NewSaveImageLoc);
							if(file_exists($NewSaveImageLoc)){
								$kitchenData['UploadImage'][$i]['name'] = $FileName;
								$kitchenData['UploadImage'][$i]['type'] = 'kitchen';
								$i++;
							}
						}	
					}
				}
				$this->loadModel('Kitchen');
				$kitchen = $this->Kitchen->find('first',array('conditions'=>array('Kitchen.user_id'=>$data['user_id'])));
				if(!empty($kitchen))
				{
					$kitchenData['id'] = $kitchen['Kitchen']['id'];
					
					if(!empty($kitchen['UploadImage']))
					{
						$this->loadModel('UploadImage');
						$this->UploadImage->deleteAll(array('UploadImage.type' => 'kitchen', 'UploadImage.related_id' => $kitchenData['id'],'UploadImage.id !=' => $existing_image_ids), false);
					}
				}
				$this->Kitchen->create();
				if($this->Kitchen->saveAll($kitchenData))
				{
					$this->response = array(
						'status' => 1,
						'message' => "Data saved successfully"
					);
				}
				else
				{
					$this->response = array(
						'status' => 2,
						'message' => "Data not saved"
					);
				}
			}
			else{
				$this->response = array(
							'status' => 2,
							'message' => 'Please update your stripe account details.'
					);
			}
		}
		echo json_encode($this->response);
	}


/*
 * Method	: search_kitchen
 * Author	: Praveen Pandey
 * Created	: 14 Oct, 2014
 * @Search kitchen and dishes
 */
	public function search_kitchen()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['address']) || empty($data['address']))
		{
			//$error .= 'Address is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('Kitchen');
			$results = $this->Kitchen->searchKitchenForApi($data, $this);
			$total_page = $this->params['paging']["Kitchen"]['pageCount'];
			$total_count = $this->params['paging']["Kitchen"]['count'];
			$this->response = array(
				'status' => 1,
				'value'	=> $results,
				'total_page' => $total_page,
				'total_count' => $total_count,
				'message' => 'success'
			);
		}
		echo json_encode($this->response);
	}
	
/*
 * Method	: add_dish
 * Author	: Praveen Pandey
 * Created	: 14 Oct, 2014
 * @Add/Edit dishes
 */
	public function add_dish()
	{
		//$this->_isValidApiRequest();
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		$this->loadModel('Kitchen');
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['dish_name']) || empty($data['dish_name']))
		{
			$error .= 'Dish Name is Required. ';
		}
		$this->Kitchen->recursive = -1;
		$kitchen = $this->Kitchen->findByUserId($data['user_id']);
		if(empty($kitchen['Kitchen']))
		{
			$error .= 'User has no kitchen added.';
		}
		if(!isset($data['timestamp']) || empty($data['timestamp']))
		{
			$error .= 'Please send time stamp for this activity. ';
		}
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else 
		{
			$dishData['id'] = (!empty($data['dish_id'])) ? $data['dish_id'] : '';
			$dishData['name'] = $data['dish_name'];
			$dishData['kitchen_id'] = $kitchen['Kitchen']['id'];
			if(isset($data['portion_small'])) {	$dishData['p_small'] = $data['portion_small']; }
			if(isset($data['portion_small_unit'])) { $dishData['p_small_unit'] = $data['portion_small_unit']; };
			if(isset($data['portion_small_price'])) { $dishData['p_small_price'] = $data['portion_small_price']; }
			if(isset($data['portion_small_quantity'])) { $dishData['p_small_quantity'] = $data['portion_small_quantity'];}
			if(isset($data['portion_big'])) { $dishData['p_big'] = $data['portion_big']; }
			if(isset($data['portion_big_unit'])) { $dishData['p_big_unit'] = $data['portion_big_unit']; }
			if(isset($data['portion_big_price'])) { $dishData['p_big_price'] = $data['portion_big_price'];}
			if(isset($data['portion_big_quantity'])) { $dishData['p_big_quantity'] = $data['portion_big_quantity'];}
			if(!empty($data['portion_custom_price']))
			{
				if(isset($data['portion_custom'])) { $dishData['p_custom'] = $data['portion_custom']; }
				if(isset($data['portion_custom_unit'])) { $dishData['p_custom_unit'] = $data['portion_custom_unit']; }
				if(isset($data['portion_custom_price'])) { $dishData['p_custom_price'] = $data['portion_custom_price']; }
				if(isset($data['portion_custom_quantity'])) { $dishData['p_custom_quantity'] = $data['portion_custom_quantity'];}
				if(isset($data['portion_custom_description'])) { $dishData['p_custom_desc'] = $data['portion_custom_description'];}
				if(empty($dishData['id']))
				{
					$dishData['is_custom_price_active'] = 0;	
				}
			}
			if(isset($data['dish_status'])) { $dishData['status'] = $data['dish_status'];}
			if(isset($data['repeat_dish'])) { $dishData['repeat'] = $data['repeat_dish'];}
			if(isset($data['diet'])) { $dishData['diet'] = $data['diet'];}
			if(isset($data['allergens'])) { $dishData['allergens'] = $data['allergens']; }
			if(isset($data['cuisine'])) {
				$cuisine = explode(',', $data['cuisine']);
				$dishData['cuisine'] = implode(' , ', $cuisine);
			}
			if(isset($data['serving_time_start'])) { $dishData['serve_start_time'] = $data['serving_time_start'];}
			if(isset($data['serving_time_end'])) { $dishData['serve_end_time'] = $data['serving_time_end'];}
			if(isset($data['lead_time'])) { $dishData['lead_time'] = $data['lead_time']; }
			if(!empty($data['dish_images']))
			{
				$existing_image_ids = array();
				$data['dish_images'] = json_decode($data['dish_images'],true);
				$i = 1;
				foreach ($data['dish_images'] as $key => $base64image)
				{
					if(filter_var($base64image, FILTER_VALIDATE_INT))
					{
						$existing_image_ids[] = $base64image;
					}
					else if($base64image != 'undefined' && $base64image != '' && substr( $base64image, 0, 10 ) === "data:image")
					{
						$FileName = str_replace(' ', '-', $dishData['name']).mt_rand().'-'.time().'.jpeg';
						$NewSaveImageLoc = DISH_IMAGE_PATH . $FileName;
						$this->_base64_to_jpeg($base64image,$NewSaveImageLoc);
						if(file_exists($NewSaveImageLoc)){
							$dishData['UploadImage'][$i]['name'] = $FileName;
							$dishData['UploadImage'][$i]['type'] = 'dish';
							$i++;
						}
					}
				}
			}
			$this->loadModel('Dish');
			if(!empty($dishData['id']))
			{
				$dishDetails = $this->Dish->findById($dishData['id']);
				if(!empty($dishDetails['UploadImage']))
				{
					$this->loadModel('UploadImage');
					$this->UploadImage->deleteAll(array('UploadImage.type' => 'dish', 'UploadImage.related_id' => $dishData['id'],'UploadImage.id !='=>$existing_image_ids), false);
				}
			}
			$this->Dish->create();
			if($this->Dish->saveAll($dishData))
			{
				$this->loadModel('ActivityLog');
				//Update Activity Log For Create Dish Activity
				if(empty($dishData['id']))
				{
					$this->ActivityLog->updateLog($data['user_id'],3,$this->Dish->id,$data['timestamp']);
				}
				else if(isset($dishDetails['Dish']['status']) && $dishDetails['Dish']['status'] != $dishData['status'])
				{
					if($dishData['status'] == 'off')
						$this->ActivityLog->updateLog($data['user_id'],5,$dishData['id'],$data['timestamp']);
					else
						$this->ActivityLog->updateLog($data['user_id'],4,$dishData['id'],$data['timestamp']);
				}
				
				$this->response = array(
						'status' => 1,
						'message' => (!empty($dishData['id'])) ? "Dish updated successfully" : "Dish saved successfully"
				);
			}
			else
			{
				$this->response = array(
						'status' => 2,
						'message' => "Dish not saved"
				);
			}
		}
		echo json_encode($this->response);
	}

	/**
	 * Method	: mydishes_list
	 * Author 	: Praveen Pandey
	 * Created	: 17 Oct,2014
	 */
	 public function mydishes_list()
	 {
		//$this->_isValidApiRequest();
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('Kitchen');
			$kitchen = $this->Kitchen->find('first',array('conditions' => array('Kitchen.user_id'=> $data['user_id']),'recursive'=>-1));
			if(isset($kitchen['Kitchen']) && !empty($kitchen))
			{
				$kitchen_id = $kitchen['Kitchen']['id'];
				$this->loadModel('Dish');
				$dishes = $this->Dish->find('all',array(
											'conditions'=> array('Dish.kitchen_id'=>$kitchen_id),
											'fields' => array('Dish.id','Dish.name','Dish.status','Dish.lead_time','Dish.serve_start_time','Dish.serve_end_time')
											));
				$resultArray= array();
				if(!empty($dishes))
				{
					foreach ($dishes as $key => $value) {
						$i = 0;
						foreach ($value['UploadImage'] as $image) {
							$imageId = $image['id'];
							$value['Dish']['images'][$i]['id'] = $imageId;
							$value['Dish']['images'][$i]['url'] = Router::url('/'.DISH_IMAGE_URL.$image['name'],true);
							$i++;
						}
						$resultArray['Dishes'][$key] = $value['Dish'];				
					}
				}
				$this->response = array(
						'status' => 1,
						'value'	=> $resultArray,
						'message' => 'success'
				);
			}
			else
			{
				$this->response = array(
					'status' => 2,
					'message' => 'Kitchen does not exists.'
				);
			}
			
		}
		echo json_encode($this->response);
	 }

/**
 * Method	: activate_dish
 * Author	: Praveen Pandey
 * Created	: 17 Oct, 2014
 */
 	public function active_dish()
	{
		//$this->_isValidApiRequest();
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['dish_id']) || empty($data['dish_id']))
		{
			$error .= 'Dish id is Required. ';
		}
		if(!isset($data['timestamp']) || empty($data['timestamp']))
		{
			$error .= 'Please send time stamp for this activity. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('Dish');
			$this->Dish->bindModel(array('belongsTo'=>array('Kitchen')));
			$this->Dish->unbindModel(
							        array('hasMany' => array('UploadImage'))
							    );
			$dish = $this->Dish->find('first', array(
											'conditions' => array('Dish.id'=>$data['dish_id']),
											'fields' => array('Dish.id','Kitchen.id','Kitchen.user_id')
												)
									);
			if(empty($dish))
			{
				$this->response = array(
						'status' => 2,
						'message' => 'Dish not found'
					);
			}
			else if($dish['Kitchen']['user_id'] != $data['user_id'])
			{
				$this->response = array(
						'status' => 2,
						'message' => 'You are not authorized!'
					);
			}
			else
			{
				$dishData['id'] = $dish['Dish']['id'];
				if(isset($data['dish_status']))	{ $dishData['status'] = $data['dish_status'];}
				if(isset($data['serving_time_start']))	{ $dishData['serve_start_time'] = $data['serving_time_start'];}
				if(isset($data['serving_time_end'])) { $dishData['serve_end_time'] = $data['serving_time_end'];	}
				if(isset($data['lead_time'])) {	$dishData['lead_time'] = $data['lead_time']; }
				if(isset($data['repeat_dish'])) { $dishData['repeat'] = $data['repeat_dish']; }
				
				if($this->Dish->save($dishData))
				{
					//Update Activity Log
					$this->loadModel('ActivityLog');

					if($dishData['status'] == 'on')
					{
						$msg = "Successfully activated";
						//Update Activity Log For Dish Activation Activity
						$this->ActivityLog->updateLog($data['user_id'],4,$dish['Dish']['id'],$data['timestamp']);
					}else{
						$msg = "Successfully deactivated";
						//Update Activity Log For Dish Offline Activity
						$this->ActivityLog->updateLog($data['user_id'],5,$dish['Dish']['id'],$data['timestamp']);
					}
					$this->response = array(
						'status' => 1,
						'message' => $msg
					);
				}
				else
				{
					$this->response = array(
							'status' => 2,
							'message' => "Error Please try again"
					);
				}				
			}
		}
		echo json_encode($this->response);
	}

/**
 * Method	: mydish_info
 * Author	: Praveen Pandey
 * Created	: 17 Oct, 2014
 */
 	public function dish_info()
	{
		//$this->_isValidApiRequest();
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['dish_id']) || empty($data['dish_id']))
		{
			$error .= 'Dish id is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('Dish');
			$this->Dish->bindModel(array('belongsTo'=>array('Kitchen')));
			$dish = $this->Dish->find('first', array(
											'conditions'=>array('Dish.id'=>$data['dish_id']),
											'fields'=> array()
											));
		   $result = array();
		   if(!empty($dish))
		   {
		   		$result['Dish'] = $dish['Dish'];
				/*if($result['Dish']['is_custom_price_active'] == 0)
				{
					$result['Dish']['p_custom'] = '';
					$result['Dish']['p_custom_price'] = '';
					$result['Dish']['p_custom_quantity'] = '';
					$result['Dish']['p_custom_desc'] = '';
					$result['Dish']['p_custom_unit'] = '';
				}*/
				$result['Dish']['allergens'] = explode('::::::::', $result['Dish']['allergens']);
				if(!empty($result['Dish']['repeat']))
					$result['Dish']['repeat'] = explode(',', $result['Dish']['repeat']);
				else
					$result['Dish']['repeat'] = array();

				if(!empty($dish['UploadImage']))
				{
					$i = 0;
					foreach ($dish['UploadImage'] as $image) {
						$imageId = $image['id'];
						$result['Dish']['images'][$i]['id'] = $imageId;
						$result['Dish']['images'][$i]['url'] = Router::url('/'.DISH_IMAGE_URL.$image['name'],true);
						
						$i++;
					}
				}
		   }
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	}

/**
 * Method	: profile_info
 * Author	: Praveen Pandey
 * Created	: 18 Oct, 2014
 */
 	public function profile_info()
	{
		//$this->_isValidApiRequest();
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
			$user = $this->User->findByid($data['user_id']);
			$userDetails = $this->User->getUserCountData($data['user_id']);
              
            	
			$result['OrderPlaced']['Count'] = 0;
			if(isset($userDetails['Order']) && !empty($userDetails['Order'])){
				$result['OrderPlaced']['Count'] = $userDetails['Order'][0]['Order'][0]['noOfPlacedOrders'];
			}
			
			$this->loadModel('Kitchen');
		    $ordersArray = $this->Kitchen->getKitchenDataForDashboard($data['user_id']);
			    $result['OrderReceived']['Count'] = 0;
			if(isset($ordersArray['oIds']) && !empty($ordersArray['oIds'])){
				$result['OrderReceived']['Count'] = count(explode(',',$ordersArray['oIds']));
			}
			$result['User'] = $user['User'];
			$result['Kitchen']['id'] = $user['Kitchen']['id'];
			$result['User']['image'] = (!empty($result['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$result['User']['image'],true) : "";
			$this->response = array(
					'status' => 1,
					'value'	=> $result,
					'message' => 'success'
			);
		}
		echo json_encode($this->response);
	}
	
/**
 * Method	: kitchen_info
 * Author	: Praveen Pandey
 * Created	: 18 Oct, 2014
 */
 	public function kitchen_info()
	{
		//$this->_isValidApiRequest();
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('Kitchen');
			$this->Kitchen->bindModel(array('belongsTo'=>array("User")));
			$kitchen = $this->Kitchen->find('first', array(
													'conditions' => array('Kitchen.user_id'=>$data['user_id']),
													'fields' => array('Kitchen.*','User.id','User.paypal_id','User.paypal_name','User.paypal_lname','User.is_paypal_verified','User.bank_acc_no','User.bank_routing_no','User.bank_acc_holdername','User.bank_acc_type','User.stripe_user_id','User.stripe_publish_id','User.diet','User.allergy')
				));

			$result = array();
			if(!empty($kitchen))
			{				
				$result['Kitchen'] = $kitchen['Kitchen'];
				$result['Kitchen']['diet'] = $kitchen['User']['diet'];
				$result['Kitchen']['allergy'] = $kitchen['User']['allergy'];
				$result['Kitchen']['cover_photo'] = (!empty($kitchen['Kitchen']['cover_photo'])) ? Router::url('/'.KITCHEN_IMAGE_URL.$kitchen['Kitchen']['cover_photo'],true) : "";
				if(!empty($kitchen['UploadImage']))
				{
					$i = 0;
					foreach ($kitchen['UploadImage'] as $image)
					{
						$imageId = $image['id'];
						$result['Kitchen']['images'][$i]['id'] = $imageId;
						$result['Kitchen']['images'][$i]['url'] = Router::url('/'.KITCHEN_IMAGE_URL.$image['name'],true);
						$i++;
					}
				}
				$result['bank_info'] = $kitchen['User'];
			}
			else
			{
				$this->loadModel('User');
				$user = $this->User->findById($data['user_id']);
				$result['bank_info'] = $user['User'];
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	}

/**
 * Method	: load_data
 * Author 	: Praveen Pandey
 * Created	: 18 Oct, 2014
 */
 	public function load_data($type)
	{
		//$this->_isValidApiRequest();
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($type) || empty($type))
		{
			$error .= 'Type is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$result = array();
			switch ($type) {
				case 'allergy':
					$this->loadModel('Allergy');
					$result['Allergy'] = array_values($this->Allergy->find('list', array('conditions'=>array('Allergy.is_active'=>1),'fields'=>array('Allergy.id','Allergy.name'),'order'=>'name ASC')));
					$this->loadModel('Portion');
					$portions = $this->Portion->find('all');					
					$prices = array();
					$i=0;
					foreach ($portions as $key => $value) {
						$k = $value['Portion']['type'];
						$v['unit'] = $value['Portion']['unit'];
						$v['price'] = $value['Portion']['price'];
						$prices[$k][] = $v;
						$i++;
					}
					$result['Price'] = $prices;
					$result['Unit'] = array_values(Configure::read('UNIT'));
					break;
					
				case 'community':
					$this->loadModel('Community');
					$resultData = $this->Community->find('list', array('conditions'=>array('Community.is_active'=>1),'fields'=>array('Community.id','Community.title'),'order'=>'title ASC'));
					$result = array();
					foreach($resultData as $com_id => $com_titile) {
						$result[] = array('id' => $com_id, 'title' => $com_titile);
					}	
					break;
					
				case 'cuisine':
					$this->loadModel('Cuisine');
					$resultData = $this->Cuisine->find('list', array('conditions'=>array('Cuisine.is_active'=>1),'fields'=>array('Cuisine.id','Cuisine.name'),'order'=>'name ASC'));
					foreach($resultData as $key => $value) {
						$result[] = array('id' => $key,  'name' => $value);
					}
					break;

				case 'tax':
					if(isset($data['kitchen_id']) && !empty($data['kitchen_id']))
					{
						$this->loadModel('PaymentSetting');
						$serviceData = $this->PaymentSetting->find('first');
					
						$this->loadModel('Kitchen');
						$resultData = $this->Kitchen->findById($data['kitchen_id']);
					
						$result = array('sale_tax' => $resultData['Kitchen']['sales_tax'], 'service_fee' => $serviceData['PaymentSetting']['service_fee']);
					}
					else
					{
						$this->loadModel('PaymentSetting');
						$resultData = $this->PaymentSetting->find('first');
						$result = array('sale_tax' => $resultData['PaymentSetting']['sales_tax'], 'service_fee' => $resultData['PaymentSetting']['service_fee']);
					}
					break;	
				
				default:
					
					break;
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	}
	
	public function preferences_info()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
			
			$User = $this->User->findById($data['user_id']);
				
			$result = array();
			if(!empty($User))
			{				
				$result['Preferences']['allergens'] = explode('::::::::', $User['User']['allergy']);
				$result['Preferences']['diet'] = explode(',', $User['User']['diet']);
				
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	}
	
	/**
	 * Method	: update_preferences
	 * Author 	: Praveen Pandey
	 * Created	: 20 Oct, 2014
	 */
	 public function update_preferences()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
			$user = $this->User->findById($data['user_id']);
			if($user)
			{
				$userData['id'] = $user['User']['id'];
				if(isset($data['allergy']))
				{
					$userData['allergy'] = $data['allergy'];
				}
				if(isset($data['diet']))
				{
					$userData['diet'] = $data['diet'];
				}

				$this->User->save($userData);
				$this->response = array(
									'status' => 1,
									'message' => 'Preferences has updated successfully.'
				);
			}
		}
		echo json_encode($this->response); 
	 }
	
	/**
	 * Method	: change_password
	 * Author	: Praveen Pandey
	 * Created	: 20 Oct, 2014
	 */
	 public function change_password()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(isset($data['social_login']) && $data['social_login'] == 1)
		{
			$data['old_pwd'] = '$$##||&||##$$';
		}
		if(!isset($data['old_pwd']) || empty($data['old_pwd']))
		{
			$error .= 'Old Password is Required. ';
		}
		if(!isset($data['new_pwd']) || empty($data['new_pwd']))
		{
			$error .= 'New Password is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
				
			
			$user = $this->User->find('first',array('conditions'=>array('User.id'=>$data['user_id'],'User.password'=>AuthComponent::Password($data['old_pwd'])),'recursive'=>-1));
			if(!empty($user))
			{
				$this->User->set('id', $user['User']['id']);
				if($this->User->saveField('password', $data['new_pwd']))
				{
					$this->response = array(
									'status' => 1,
									'message' => 'Password changed successfully'
							);
				}
				else {
					$this->response = array(
									'status' => 2,
									'message' => 'Error occured, Please try again'
								);
				}
			}
			else {
				$this->response = array(
									'status' => 2,
									'message' => 'User id and password is not correct'
								);
			}
		}
		echo json_encode($this->response);
	 }

/**
 * Method	: new_discussion
 * Author 	: Praveen Pandey
 * Created	: 20 Oct, 2014
 */
 	public function new_discussion()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['community_id']) || empty($data['community_id']))
		{
			$error .= 'Community id is Required. ';
		}
		if(!isset($data['title']) || empty($data['title']))
		{
			$error .= 'Title is Required. ';
		}
		if(!isset($data['message']) || empty($data['message']))
		{
			$error .= 'Message is Required. ';
		}
		
				
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('Discussion');
			$data['description'] = $data['message'];
			$this->Discussion->create();
			if($this->Discussion->save($data))
			{
				$this->response = array(
							'status' => 1,
							'message' => 'success'
						);
			}
			else {
				$this->response = array(
							'status' => 2,
							'message' => 'Error, Please try again'
						);
			}
		}
		echo json_encode($this->response);
	}

/**
 * Method	: explore_community
 * Author 	: Praveen Pandey
 * Created	: 20 Oct, 2014
 */
 	public function explore_community()
	{
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		$this->loadModel('Community');		
		$communities = $this->Community->find('all',array('conditions'=> array('Community.is_active'=>1),'fields'=>array('id','title','discussion_count')));
		$communities= Set::classicExtract($communities, '{n}.Community');
		$result['Community'] = $communities;
		$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		echo json_encode($this->response);
	}
	
	/**
	 * Method	: discussion_list
	 * Author 	: Praveen Pandey
	 * Created	: 20 Oct, 2014
	 */
	 public function discussion_list()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['community_id']) || empty($data['community_id']))
		{
			$error .= 'Community id is Required. ';
		}
				
				
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('Discussion');
			$list = $this->Discussion->find('all',array(
												'conditions' => array('Discussion.community_id' => $data['community_id'])
												)
										);
			$discussions = Set::classicExtract($list, '{n}.Discussion');
			
			$result['Discussion'] = $discussions;
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	 }
	 
	 /**
	  * Method	: discussion_detail
	  * Author	: Praveen Pandey
	  * Created	: 20 Oct, 2014
	  */
	  public function discussion_detail()
	  {
	  	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['discussion_id']) || empty($data['discussion_id']))
		{
			$error .= 'Discussion id is Required. ';
		}
							
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
	  	else
		{
			$this->loadModel('Discussion');
			$this->loadModel('Comment');
			$this->Discussion->bindModel(array(
									'hasMany'=>array(
													'Comment'=> array(
																'className'=> 'Comment',
																'conditions' => array('Comment.is_publish'=>1),
																'fields'=> array('Comment.comment','Comment.date_time','Comment.user_id','Comment.created')
													)),
                                                                        'belongsTo' => array('User')
                            ));
			$this->Discussion->unbindModel(array('belongsTo'=>array('Community')));
			$this->Comment->bindModel(array(
			'belongsTo'=>array(
						'User'=>array(
									'className' => 'User',
									'fields'=>	array('User.name','User.image')
						))));
			$discussion = $this->Discussion->find('first', array('conditions'=>array('Discussion.id'=>$data['discussion_id']),'recursive'=>2));
			$result = array();
			
             if(!empty($discussion['Discussion'])) {
				if(empty($discussion['Discussion']['date_time']))
					$discussion['Discussion']['date_time'] = strtotime($discussion['Discussion']['created']);

			$result['Discussion'] = $discussion['Discussion'];
			}

             if(!empty($discussion['User'])) {
			$result['Discussion']['User'] = array('name'=>$discussion['User']['name'], 'image'=>(!empty($discussion['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$discussion['User']['image'],true) : "");
			}
                        //pr($result); exit;
			if(!empty($discussion['Comment']))
			{
				foreach ($discussion['Comment'] as $key => $comment) {
					$comment['replied_by'] = $comment['User']['name'];
					
					if(empty($comment['date_time']))
						$comment['date_time'] = strtotime($comment['created']);
					
					$comment['image'] = (!empty($comment['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$comment['User']['image'],true) : "";
					unset($comment['User']);
					unset($comment['Discussion']);
					$result['replies'][] = $comment;
				}
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	  }
	  
	 /**
	  * 
	  */
	public function post_discussion_message()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['discussion_id']) || empty($data['discussion_id']))
		{
			$error .= 'Community id is Required. ';
		}
		if(!isset($data['comment']) || empty($data['comment']))
		{
			$error .= 'Message is Required. ';
		}
		
		
		if(!empty($error))
		{
			$this->response = array(
					'status' => 2,
					'message' => $error
			);
		}
		else
		{
			$this->loadModel('Comment');
			if($this->Comment->save($data))
			{
					$this->response = array(
							'status' => 1,
							'message' => 'success'
					);
			}
			else {
							$this->response = array(
									'status' => 2,
									'message' => 'Error, Please try again'
						);
			}
		}
		echo json_encode($this->response);
	}
	  
/**
 * Method	: new_message
 * Author 	: Praveen Pandey
 * Created	: 21 Oct, 2014
 */
 	public function new_messageold()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['sender_id']) || empty($data['sender_id']))
		{
			$error .= 'Sender id is Required. ';
		}
		if(!isset($data['receiver_id']) || empty($data['receiver_id']))
		{
			$error .= 'Receiver id is Required. ';
		}
		if(!isset($data['message']) || empty($data['message']))
		{
			$error .= 'Message is Required. ';
		}
							
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('Message');
			$this->Message->create();
			if($this->Message->save($data))
			{
				$this->response = array(
							'status' => 1,
							'message' => "Message send successfully"
						);
			}
			else
			{
				$this->response = array(
							'status' => 2,
							'message' => "Error, Please try again"
						);
			}
		}
		echo json_encode($this->response);
	}
	
	public function new_message()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['sender_id']) || empty($data['sender_id']))
		{
			$error .= 'Sender id is Required. ';
		}
		if(!isset($data['receiver_id']) || empty($data['receiver_id']))
		{
			$error .= 'Receiver id is Required. ';
		}
		if(!isset($data['message']) || empty($data['message']))
		{
			$error .= 'Message is Required. ';
		}
		
		if((isset($data['sender_id']) && isset($data['receiver_id'])) &&($data['sender_id'] == $data['receiver_id']))
		{
			$error .= 'Request not valid. ';
		}
							
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('Conversation');
			$this->loadModel('ConversationReply');
			
			//Update Activity Log
			$this->loadModel('ActivityLog');
			
			$converstion = $this->Conversation->find('first',array(
				'conditions'=>array('OR'=>array(array('Conversation.sender_id'=>$data['sender_id'],'Conversation.receiver_id'=>$data['receiver_id']),array('Conversation.sender_id'=>$data['receiver_id'],'Conversation.receiver_id'=>$data['sender_id']))),
			));
			//$log = $this->Conversation->getDataSource()->getLog(false, false);
			
				
			$replyData['date_time'] = $data['date_time'];
			$replyData['reply'] = $data['message'];
			$replyData['user_id'] = $data['sender_id'];
			if($converstion)
			{
				$replyData['conversation_id'] = $converstion['Conversation']['id'];
				$this->Conversation->id = $converstion['Conversation']['id'];
				$this->Conversation->save();
				
				//Check this combination in activity log if found then just update that record with current timestamp
				$checkActivity = $this->ActivityLog->find('first',array('conditions'=>array( 'ActivityLog.user_id'=>$data['sender_id'],
																			'ActivityLog.activity_id'=>6,
																			'ActivityLog.conversation_id'=>$converstion['Conversation']['id'])));
				if(empty($checkActivity)){
					//Update Activity Log For New Sender Conversation Activity
					$this->ActivityLog->updateLog($data['sender_id'],6,$converstion['Conversation']['id'],$data['date_time']);
				}else{
					//Update Activity Log For Existing Sender Conversation Activity
					$this->ActivityLog->updateLog($data['sender_id'],6,$converstion['Conversation']['id'],$data['date_time'],$checkActivity['ActivityLog']['id']);
				}
			}
			else {
				$conData['date_time'] = $data['date_time'];
				$conData['sender_id'] = $data['sender_id'];
				$conData['receiver_id'] = $data['receiver_id'];
				$this->Conversation->create();
				if($this->Conversation->save($conData))
				{
					$replyData['conversation_id'] = $this->Conversation->getLastInsertID();
					
					//Update Activity Log For Sender Conversation Activity
					$this->ActivityLog->updateLog($data['sender_id'],6,$this->Conversation->getLastInsertID(),$data['date_time']);
					//Update Activity Log For Receiver Conversation Activity
					$this->ActivityLog->updateLog($data['receiver_id'],6,$this->Conversation->getLastInsertID(),$data['date_time']);
				}
			}
			if(!empty($replyData['conversation_id']))
			{
				$this->ConversationReply->create();
				if($this->ConversationReply->save($replyData))
				{
				$this->response = array(
							'status' => 1,
							'message' => "Message send successfully"
						);
				}
				else
				{
					$this->response = array(
								'status' => 2,
								'message' => "Error, Please try again"
							);
				}
			}
			else
			{
				$this->response = array(
						'status' => 2,
						'message' => "Error, Please try again"
				);
			}
		}
		echo json_encode($this->response);
	}
	
	/**
	 * Method	: message_list
	 * Author 	: Praveen Pandey
	 * Created	: 21 Oct, 2014
	 */
	 public function message_list()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('Conversation');
			/* $this->Conversation->bindModel(array('hasMany'=>array(
									'ConversationReply'=>array(
									'className'=>'ConversationReply',
									 'order'=> array('ConversationReply.created'=> 'DESC'),
									 'limit'=> 1
									)))); */
			$this->Conversation->hasMany['ConversationReply']['order'] = array('ConversationReply.created'=> 'DESC');
			$this->Conversation->hasMany['ConversationReply']['limit'] = 1;
			
			$messages = $this->Conversation->find('all',array(
						'contain' => array('Sender'=> array('id','name','image'),
										   'Reciever'=> array('id','name','image'),
											'ConversationReply'),
						'conditions'=>array('OR'=>array('Conversation.sender_id'=>$data['user_id'],'Conversation.receiver_id'=>$data['user_id'])),
						'order' => array('Conversation.modified'=>'DESC'),
			
			));
			
			$result = array();
			if($messages)
			{
				$i=0;
				foreach ($messages as $key => $msg) {
					if($data['user_id'] == $msg['Conversation']['sender_id'])
					{
						$other_person = $msg['Reciever'];
					}
					else 
					{
						$other_person = $msg['Sender'];
					}
					$result['Messages'][$i]['person_name'] = $other_person['name'];
					$result['Messages'][$i]['person_id'] = $other_person['id'];
					$result['Messages'][$i]['last_message_content'] = $msg['ConversationReply']['0']['reply'];
					$result['Messages'][$i]['last_message_time'] = $msg['ConversationReply']['0']['date_time'];
					$result['Messages'][$i]['conversation_id'] = $msg['Conversation']['id'];
					$result['Messages'][$i]['person_image_url_thumb'] = (!empty($other_person['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$other_person['image'],true) : '';
					$result['Messages'][$i]['total_message_count'] = $msg['Conversation']['conversation_reply_count'];
					$i++;
				}
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
			
		}
		echo json_encode($this->response);
	 }

/**
 * Method	: message_detail
 * Author 	: Praveen Pandey
 * Created	: 22 Oct, 2014
 */
	 public function message_detail()
	 {
	 	//header('Content-Type: application/json');
	 	$this->request->onlyAllow('POST');
	 	$data = $this->request->data;
	 	$error = '';
	 	if(!isset($data['sender_id']) || empty($data['sender_id']))
	 	{
	 		$error .= 'Sender id is Required. ';
	 	}
	 	if(!isset($data['receiver_id']) || empty($data['receiver_id']))
	 	{
	 		$error .= 'Receiver id is Required. ';
	 	}
	 	if(!empty($error))
	 	{
	 		$this->response = array(
	 				'status' => 2,
	 				'message' => $error
	 		);
	 	}
	 	else
	 	{
	 		$this->loadModel('Conversation');
	 		$converstion = $this->Conversation->find('first',array(
	 				'conditions'=>array('OR'=>array(array('Conversation.sender_id'=>$data['sender_id'],'Conversation.receiver_id'=>$data['receiver_id']),array('Conversation.sender_id'=>$data['receiver_id'],'Conversation.receiver_id'=>$data['sender_id']))),
	 		));
	 		
	 		$converstion_id = 0;
	 		if($converstion)
	 		{
	 			$converstion_id = $converstion['Conversation']['id'];
	 		}
	 		
	 		$this->loadModel('ConversationReply');
	 		$messages = $this->ConversationReply->find('all', array(
	 				'conditions' => array(
	 						'Conversation.id'=>$converstion_id,
	 						'OR'=>array(
	 								'Conversation.sender_id'=>$data['sender_id'],
	 								'Conversation.receiver_id'=>$data['sender_id']
	 						)
	 				),
	 				'fields' => array('ConversationReply.*','User.name','User.id','User.image')
	 		)
	 		);
	 			
	 			
	 		$result = array();
	 		if($messages)
	 		{
	 			$i=0;
	 			foreach ($messages as $key => $msg) {
	 				$result['Messages'][$i]['person_name'] = $msg['User']['name'];
	 				$result['Messages'][$i]['message_content'] = $msg['ConversationReply']['reply'];
	 				$result['Messages'][$i]['message_time'] = $msg['ConversationReply']['date_time'];
	 				$result['Messages'][$i]['person_image_url_thumb'] = (!empty($msg['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$msg['User']['image'],true) : '';
	 				$result['Messages'][$i]['user_id'] = $msg['User']['id'];
	 				$i++;
	 			}
	 		}
	 		$this->response = array(
	 				'status' => 1,
	 				'value'	=> $result,
	 				'message' => 'success'
	 		);
	 	}
	 	echo json_encode($this->response);
	 }
 	public function message_detaila()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['message_id']) || empty($data['message_id']))
		{
			$error .= 'Message id is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('ConversationReply');
			$messages = $this->ConversationReply->find('all', array(
											'conditions' => array(
																'Conversation.id'=>$data['message_id'],
																'OR'=>array(
																			'Conversation.sender_id'=>$data['user_id'],
																			'Conversation.receiver_id'=>$data['user_id']
																			)
																	),
											'fields' => array('ConversationReply.*','User.name','User.image')																		
														)
											);
			
			
			$result = array();
			if($messages)
			{
				$i=0;
				foreach ($messages as $key => $msg) {
					$result['Messages'][$i]['person_name'] = $msg['User']['name'];
					$result['Messages'][$i]['message_content'] = $msg['ConversationReply']['reply'];
					$result['Messages'][$i]['message_time'] = $msg['ConversationReply']['created'];
					$result['Messages'][$i]['person_image_url_thumb'] = (!empty($msg['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$msg['User']['image'],true) : '';
					$i++;
				}
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	}

/**
 * Method	: add_payment_method
 * Author 	: Praveen Pandey
 * Created	: 22 Oct, 2014 * 
 */
 	public function add_payment_method()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['type']) || empty($data['type']))
		{
			$error .= 'Type is Required. ';
		}
		if(!isset($data['card_no']) || empty($data['card_no']))
		{
			$error .= 'Card No is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('PaymentMethod');
			$this->PaymentMethod->create();
			if($this->PaymentMethod->save($this->request->data))
			{
				$this->response = array(
							'status' => 1,
							'message' => "Payment method saved successfully"
						);
			}
			else
			{
				$this->response = array(
							'status' => 2,
							'message' => "Error, Please try again"
						);
			}
		}
		echo json_encode($this->response);
	}
	
	/**
	 * Method	: get_payment_detail
	 * Author 	: Praveen Pandey
	 * Created	: 22 Oct, 2014
	 */
	 public function get_payment_detail()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('PaymentMethod');
			$this->loadModel('User');
			$methods = $this->PaymentMethod->find('all',array(
												'conditions' => array('PaymentMethod.user_id' => $data['user_id'])
													)
										  );
			$receipt = $this->User->find('first',array(
												'conditions'=> array('User.id' => $data['user_id']),
												'fields'=> array('User.bank_acc_no','User.bank_routing_no','User.bank_acc_holdername','User.bank_acc_type','User.bank_acc_type','User.paypal_id','User.is_paypal_verified','User.stripe_user_id','User.stripe_publish_id')
												)
										);
			$result = array();
			if($methods)
			{
				$i=1;
				foreach ($methods as $key => $pay) {
					$result['Payment'][$i]['id'] = $pay['PaymentMethod']['id'];
					$result['Payment'][$i]['card_no'] = $pay['PaymentMethod']['card_no'];
					$result['Payment'][$i]['type'] = $pay['PaymentMethod']['type'];
					$result['Payment'][$i]['card_no'] = $pay['PaymentMethod']['exp_month'];
					$result['Payment'][$i]['exp_year'] = $pay['PaymentMethod']['exp_year'];
					$result['Payment'][$i]['card_name'] = $pay['PaymentMethod']['card_name'];
					$i++;
				}
			}
			if($receipt)
			{
				$result['Receipt'] = $receipt['User'];
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	 }

	/**
	 * Method	: update_payment_detail
	 * Author	: Praveen Pandey
	 * Created	: 30 Oct, 2014
	 */
	 public function update_payment_detail()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$userData['id'] = $data['user_id'];
			
			/*if(isset($data['bank_account_number'])) { $userData['bank_acc_no'] = $data['bank_account_number']; }
			if(isset($data['routing_number'])) { $userData['bank_routing_no'] = $data['routing_number']; }
			if(isset($data['account_holder_name'])) { $userData['bank_acc_holdername'] =  $data['account_holder_name']; }
			if(isset($data['bank_acc_type'])) { $userData['bank_acc_type'] =  $data['bank_acc_type']; }
			*/

			if(isset($data['stripe_user_id'])) { $userData['stripe_user_id'] = $data['stripe_user_id']; }
			if(isset($data['stripe_publish_id'])) { $userData['stripe_publish_id'] =  $data['stripe_publish_id']; }
			
			$this->loadModel('User');
			if($this->User->save($userData))
			{
				$this->loadModel('PaymentMethod');
				$methods = $this->PaymentMethod->find('all',array(
													'conditions' => array('PaymentMethod.user_id' => $data['user_id'])
														)
											  );
				$receipt = $this->User->find('first',array(
													'conditions'=> array('User.id' => $data['user_id']),
													'fields'=> array('User.bank_acc_no','User.bank_routing_no','User.bank_acc_holdername','User.bank_acc_type','User.stripe_user_id','User.stripe_publish_id')
													)
											);
				$result = array();
				if($methods)
				{
					$i=1;
					foreach ($methods as $key => $pay) {
						$result['Payment'][$i]['id'] = $pay['PaymentMethod']['id'];
						$result['Payment'][$i]['card_no'] = $pay['PaymentMethod']['card_no'];
						$result['Payment'][$i]['type'] = $pay['PaymentMethod']['type'];
						$result['Payment'][$i]['card_no'] = $pay['PaymentMethod']['exp_month'];
						$result['Payment'][$i]['exp_year'] = $pay['PaymentMethod']['exp_year'];
						$result['Payment'][$i]['card_name'] = $pay['PaymentMethod']['card_name'];
						$i++;
					}
				}
				if($receipt)
				{
					$result['Receipt'] = $receipt['User'];
				}
				$this->response = array(
							'status' => 1,
							'value'	=> $result,
							'message' => 'success'
					);
			}
			else
			{
				$this->response = array(
							'status' => 2,
							'message' => "Error, Please try again"
						);
			}
		}
		echo json_encode($this->response);
	 }

	/**
	 * Method	: delete_payment_method
	 * Author	: Praveen Pandey
	 * Created	: 30 Oct, 2014
	 */
 	public function delete_payment_method()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['paymethod_id']) || empty($data['paymethod_id']))
		{
			$error .= 'Payment Method id is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('User');
			$this->loadModel('PaymentMethod');
			$this->PaymentMethod->deleteAll(array('PaymentMethod.user_id'=>$data['user_id'],'PaymentMethod.id'=>$data['paymethod_id']));
			
			$methods = $this->PaymentMethod->find('all',array(
													'conditions' => array('PaymentMethod.user_id' => $data['user_id'])
														)
											  );
			$receipt = $this->User->find('first',array(
													'conditions'=> array('User.id' => $data['user_id']),
													'fields'=> array('User.bank_acc_no','User.bank_routing_no','User.bank_acc_holdername','User.bank_acc_type','User.stripe_user_id','User.stripe_publish_id')
													)
											);
			$result = array();
			if($methods)
			{
				$i=1;
				foreach ($methods as $key => $pay) {
					$result['Payment'][$i]['id'] = $pay['PaymentMethod']['id'];
					$result['Payment'][$i]['card_no'] = $pay['PaymentMethod']['card_no'];
					$result['Payment'][$i]['type'] = $pay['PaymentMethod']['type'];
					$i++;
				}
			}
			if($receipt)
			{
				$result['Receipt'] = $receipt['User'];
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	}

	/**
	 * Method	: add_to_wishlist
	 * Author	: Praveen Pandey
	 * Created  : 30 Oct, 2014
	 */
	 public function add_to_wishlist()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(!isset($data['dish_id']) || empty($data['dish_id']))
		{
			$error .= 'Dish id is Required. ';
		}
		if(!isset($data['kitchen_id']) || empty($data['kitchen_id']))
		{
			$error .= 'Kitchen id is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$error = 0;
			$this->loadModel('Wishlist');
			$checkExisting = $this->Wishlist->find('first',array('conditions'=>array('Wishlist.user_id' => $data['user_id'], 'Wishlist.dish_id' => $data['dish_id'])));
			if(isset($checkExisting['Wishlist']) && !empty($checkExisting['Wishlist']))
			{
				$error = 1;
			}
		    else if(isset($checkExisting['Wishlist']['status']) && $checkExisting['Wishlist']['status']==0)
		    {
		    	$this->Wishlist->set('id', $checkExisting['Wishlist']['id']);
				$this->Wishlist->saveField('stauts', 1);
		    }
		    else
		    {
		    	$this->Wishlist->save($data);
		    }
			
			$wishlists = $this->Wishlist->find('all',array(
													'conditions' => array('Wishlist.user_id'=>$data['user_id']),
													'recursive' => 2,
													'contain' => array('Dish'=>array('name'),'Dish.UploadImage','Dish.Kitchen'),
													)
											);
			$result = array();
			if($wishlists)
			{
				$i= 0;
				foreach ($wishlists as $key => $value) {
					if(isset($value['Dish']['Kitchen']['name']) && !empty($value['Dish']['Kitchen']['name']))
					{
						$result['Wishlist'][$i]['id'] = $value['Wishlist']['id'];
						$result['Wishlist'][$i]['kitchen_name'] = $value['Dish']['Kitchen']['name'];
						$result['Wishlist'][$i]['dish_name'] = $value['Dish']['name'];
						$i++;
					}
				}
			}
			if($error)
			{
				$this->response = array(
					'status' => 2,
					'value'	=> $result,
					'message' => 'This dish has been already added in your wishlist.'
				);
			}
			else
			{
				$this->response = array(
					'status' => 1,
					'value'	=> $result,
					'message' => 'success'
				);
			}
		}
		echo json_encode($this->response);
	 }

	/**
	 * 
	 */
	 public function kitchen_dishes()
	 {
	 	//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
		if(empty($data['kitchen_id']))
		{
			$error .= 'Kitchen id is Required. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('Dish');
			$this->loadModel('Kitchen');
			$this->loadModel('Review');
			$kitchen = $this->Kitchen->find('first', array(
													'conditions'=> array('Kitchen.id'=>$data['kitchen_id']),
													'fields' => array('Kitchen.*','User.image','User.is_paypal_verified','User.paypal_id')
													));
			
			$result = array();
			$result['is_order_placed_for_kitchen'] = 0;
			if($kitchen)
			{
				if(isset($data['user_id']) && !empty($data['user_id']))
				{
					$this->loadModel('OrderDish');
					$countCheckForUser = $this->OrderDish->find('count',array('conditions'=>array('OrderDish.kitchen_id'=>$data['kitchen_id'], 'Order.user_id'=> $data['user_id'])));
					if($countCheckForUser)
					{
						$result['is_order_placed_for_kitchen'] = 1;
					}
				}
				$result['Kitchen'] = $kitchen['Kitchen'];
				$result['Kitchen']['cover_photo'] = (!empty($kitchen['Kitchen']['cover_photo'])) ? Router::url('/'.KITCHEN_IMAGE_URL.$kitchen['Kitchen']['cover_photo'],true) : "";
				$result['User']['image'] = (!empty($kitchen['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$kitchen['User']['image'],true) : "";
				$result['User']['is_paypal_verified'] = $kitchen['User']['is_paypal_verified'];
				if(empty($kitchen['User']['paypal_id']))
					$result['User']['paypal_id'] = '';
				else	
					$result['User']['paypal_id'] = $kitchen['User']['paypal_id'];
				
				if(!empty($kitchen['UploadImage']))
				{
					$i = 0;
					foreach ($kitchen['UploadImage'] as $image) {
						$imageId = $image['id'];
						$result['Kitchen']['images'][$i]['id'] = $imageId;
						$result['Kitchen']['images'][$i]['url'] = Router::url('/'.KITCHEN_IMAGE_URL.$image['name'],true);
						$i++;
					}
				}
				
				$dishes = $this->Dish->find('all', array(
													'conditions' => array('Dish.kitchen_id' => $data['kitchen_id'], 'Dish.status' => 'on'),
													'contain' => array('UploadImage')
												));
												
				
				if(!empty($dishes))
				{
					$i = 0;
					foreach ($dishes as $key => $value) {
						if(!empty($value['Dish']['p_small']) || !empty($value['Dish']['p_big']) || (!empty($value['Dish']['p_custom']) && $value['Dish']['is_custom_price_active']==1))
						{
							if($value['Dish']['is_custom_price_active'] == 0)
							{
								$value['Dish']['p_custom'] = 0;
								$value['Dish']['p_custom_price'] = '';
								$value['Dish']['p_custom_quantity'] = '';
								$value['Dish']['p_custom_desc'] = '';
								$value['Dish']['p_custom_unit'] = '';
							}	
							$value['Dish']['image']  = (!empty($value['UploadImage'][0]['name'])) ? Router::url('/'.DISH_IMAGE_URL.$value['UploadImage'][0]['name'],true) : "";
							$result['Dish'][] = $value['Dish'];
						}
					}
				}

				$reviews = $this->Review->find('all', array(
														'conditions' => array('Review.kitchen_id' => $data['kitchen_id']),
														'fields' => array('Review.*','User.name')
														)
											);
				if(!empty($reviews))
				{
					foreach ($reviews as $key => $value) {
						$r = $value['Review'];
						$r['User'] = $value['User'];
						$result['Review'][] = $r;
                        if(isset($value['Review']['timestamp']) && !empty($value['Review']['timestamp'])) 
                        	$result['Timestamp'][] = $value['Review']['timestamp'];
                    	else
                    		$result['Timestamp'][] = strtotime($value['Review']['created']);
					}
				}
			}
			$this->response = array(
						'status' => 1,
						'value'	=> $result,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	 }
	
	/**
	 * Method	: post_review
	 * Author	: Praveen Pandey
	 * Created	: 14 Nov, 2014
	 * Purpose	: post review
	 */
	public function post_review()
	{
		//header('Content-Type: application/json');  
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';		
                
		if(empty($data['kitchen_id']))
		{
			$error .= 'Kitchen id is Required. ';
		}
		if(empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
		if(empty($data['feedback']))
		{
			$error .= 'Feedback is Required. ';
		}
		if(empty($data['rating']))
		{
			$error .= 'Rating is Required. ';
		}
                if(empty($data['timestamp']))
		{
			$error .= 'Timestamp is Required. ';
		}
		if(!empty($error))
		{
			$this->response = array(
							'status' => 2,
							'message' => $error
						);
		}
		else
		{
			$this->loadModel('Review');
			$review = $this->Review->find('first', array('conditions'=>array('Review.user_id' => $data['user_id'],'Review.kitchen_id'=>$data['kitchen_id'])));
			if(!empty($review))
			{
				$review['Review']['feedback'] = $data['feedback'];
				$review['Review']['rating'] = $data['rating'];
				$this->Review->set($review['Review']);
				if($this->Review->save($review))
				{
					$this->response = array(
							'status' => 1,
							'message' => "Your review edited successfully"
						);
				} else {
					$this->response = array(
							'status' => 2,
							'message' => 'Error while editing your review, Please try again'
						);
				}
			}
			else {
				$this->Review->create();
				if($this->Review->save($data))
				{
					$this->response = array(
							'status' => 1,
							'message' => "Your review post successfully"
						);
				} else {
					$this->response = array(
							'status' => 2,
							'message' => 'Error, Please try again'
						);
				}	
			}
		}
		echo json_encode($this->response);
	}
	/**
	 * Method	: send_verification
	 * Author	: Praveen pandey
	 * Created	: 28 Nov, 2014
	 */
	public function send_verification()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(empty($data['name']))
		{
			$error .= 'Name is Required. ';
		}
		if(empty($data['mobile']))
		{
			$error .= 'Mobile number is Required. ';
		}
		if(empty($data['user_id']))
		{
			$error .= 'User id Required. ';
		}
		if(empty($data['country_code']))
		{
			$error .= 'Please select your country. ';
		}
		
		if(!empty($error))
		{
			$this->response = array(
					'status' => 2,
					'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
			$already_user = $this->User->find(
					'first', array(
						'fields' => array('id','phone'),
						'recursive' => -1,
						'conditions' => array('User.phone' => $data['mobile'],'User.id !='=>$data['user_id'])
					)
				);
				if(isset($already_user['User']['phone']) && !empty($already_user['User']['phone']))
				{
					$error_msg = 'Phone number already in use. Please enter another phone number.';
					$this->response = array(
							'status' => 2,
							'message' => $error_msg 
					);
				}
				else
				{
					try
					{
						$code = mt_rand(100000, 999999);
						$name = ucfirst($data['name']);
						$text = "Dear $name, Enter $code on confirmation code to verify your account on Lacart.";
						App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
						$client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);
						$message = $client->account->messages->sendMessage(
								TWILIO_FROM_NUMBER, // From a valid Twilio number
								'+'.$data['country_code'].$data['mobile'], // Text this number
								//'+91'.$data['mobile'], // Text this number
								$text
						);
						if($message->sid)
						{
							$this->User->id = $data['user_id'];
							$this->User->set('verification_code', $code);
							$this->User->set('phone', $data['mobile']);
							if(isset($data['address']) && !empty($data['address'])) {
							$this->User->set('address', $data['address']);
							}
							if(isset($data['state_id']) && !empty($data['state_id'])) {
							$this->User->set('state_id', $data['state_id']);
							}
							if(isset($data['city_id']) && !empty($data['city_id'])) {
							$this->User->set('city_id', $data['city_id']);
							}
							if(isset($data['zipcode']) && !empty($data['zipcode'])) {
							$this->User->set('zipcode', $data['zipcode']);
							}
							$this->User->set('name', $data['name']);
							$this->User->save();
							$this->response = array(
									'status' => 1,
									'message' => "Verification code sent successfully",
									'value' => array('code'=> $code)
							);
						}
						else
						{
							$this->response = array(
									'status' => 2,
									'message' => 'Error, Please try again'
							);
						}
					
					}
					catch (Exception $e)
					{
						$error_msg = str_replace('To number:', '', $e->getMessage());
		                $error_msg = str_replace('The '."'".'To'."'".' number', '', $error_msg);
						$error_msg = str_replace('From number:', '', $error_msg);
						$this->response = array(
								'status' => 2,
								'message' => $error_msg 
						);
					}
				}

		}
		echo json_encode($this->response);
	}
	
	/**
	 * Method	: validate_verification
	 * Author	: Praveen pandey
	 * Created	: 28 Nov, 2014
	 */
	public function validate_verification()
	{
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		if(empty($data['mobile']))
		{
			$error .= 'Mobile number is Required. ';
		}
		if(empty($data['code']))
		{
			$error .= 'Code is Required. ';
		}
		if(empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}
	
		if(!empty($error))
		{
			$this->response = array(
					'status' => 2,
					'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
			$user = $this->User->find('first', array('conditions'=>array(
															'User.id' => $data['user_id'],
															'User.phone' => $data['mobile'],
															'User.verification_code' => $data['code'],
			)));
			if(!empty($user))
			{
				$this->User->id = $data['user_id'];
				$this->User->set('is_verified', 1);
				if($this->User->save())
				{
					$Email = new CakeEmail('smtp');

					$this->loadModel('EmailTemplate');
					
					$arr = array();
					$arr['{{user}}'] = $user['User']['name'];
					
					$email_content = $this->EmailTemplate->findBySlug('account-verify');

					$content = $email_content['EmailTemplate']['content'];
					$content = str_replace(array_keys($arr), array_values($arr), $content);

					$subject = $email_content['EmailTemplate']['subject'];
					$reply_to_email = REPLYTO_EMAIL;

					$Email->from(FROM_EMAIL);
					$Email->to($user['User']['email']);
					$Email->subject($subject);
					$Email->replyTo($reply_to_email);
					$Email->emailFormat('html');
					$Email->send($content);

					$this->response = array(
							'status' => 1,
							'message' => "You have successfully verified"
					);
				}
				else 
				{
					$this->response = array(
							'status' => 2,
							'message' => 'Error, Please try again'
					);
				}
			}
			else 
			{
				$this->response = array(
										'status' => 2,
										'message' => "Verification code is not valid"
								);
			}
		}
		echo json_encode($this->response);//592746
	}
        
        /*
         * Purpose: To get wishlist of a user
         * @Created: Sandeep Jain
         * @Date: 01 Dec 14
         * @Parameters: user_id (int) 
         * @Response: Wishlist data in json format
         */
        
        function wishlist()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                    $error .= 'User id is Required. ';
            }            
            if(!empty($error))
            {
                $this->response = array(
                                        'status' => 2,
                                        'message' => $error
                                );
            }
            else
            {
                $this->loadModel('Wishlist');                
                $wishlists = $this->Wishlist->find('all',array(
                                                    'conditions' => array('Wishlist.user_id'=>$data['user_id']),
                                                    'contain' => array('Dish'=>array('name', 'serve_start_time', 'serve_end_time', 'lead_time', 'status'),'Dish.UploadImage','Dish.Kitchen'=>array('name')),
                                                    )
                                    			);
                $result = array();
                //pr($wishlists); exit;
                if($wishlists)
                {
                    $i= 0;
                    foreach ($wishlists as $key => $value) {
                    	if(isset($value['Dish']['Kitchen']['name']) && !empty($value['Dish']['Kitchen']['name']))
                    	{
                    		$result['Wishlist'][$i]['wish_id'] = $value['Wishlist']['id'];
                            $result['Wishlist'][$i]['kitchen_name'] = $value['Dish']['Kitchen']['name'];
                            $result['Wishlist'][$i]['dish_name'] = $value['Dish']['name'];
                            $result['Wishlist'][$i]['photo_url'] = (!empty($value['Dish']['UploadImage']['0']['name'])) ? Router::url('/'.DISH_IMAGE_URL.$value['Dish']['UploadImage']['0']['name'],true) : "";
                            $result['Wishlist'][$i]['availability'] = $value['Dish']['status'];
                            $result['Wishlist'][$i]['kitchen_id'] = $value['Dish']['kitchen_id'];
                            $result['Wishlist'][$i]['last_order'] = $value['Dish']['lead_time'];
                            $result['Wishlist'][$i]['start_time'] = $value['Dish']['serve_start_time'];
                            $result['Wishlist'][$i]['end_time'] = $value['Dish']['serve_end_time'];
                            $result['Wishlist'][$i]['time'] = '';
                            $i++;
                    	}
                    }
                }
                $this->response = array(
                                    'status' => 1,
                                    'value'	=> $result,
                                    'message' => 'success'
                );
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: To remove wishlist ofa user according to the id
         * @Created: Sandeep Jain
         * @Date: 01 Dec 14
         * @Parameters: user_id (int) and wish_id (int)
         * @Response: request data in json format
         */
        
        function removewish()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!isset($data['wish_id']) || empty($data['wish_id']))
            {
                $error .= 'wish id is Required. ';
            }            
            if(!empty($error))
            {
                $this->response = array(
                                        'status' => 2,
                                        'message' => $error
                                );
            }
            else
            {
               $this->loadModel('Wishlist');               
               $chk = $this->Wishlist->find('first', array('conditions' => array('Wishlist.user_id'=>$data['user_id'], 'Wishlist.id'=>$data['wish_id'])));
               if (!empty($chk))
               {
                   $this->Wishlist->id = $data['wish_id'];
                   $this->Wishlist->delete();
                   $this->response = array(
                            'status' => 200,
                            'message' => 'Wish has been deleted successfully',                            
                    );
               }
               else
               {
                   $this->response = array(
                            'status' => 2,
                            'message' => 'Permission denide'
                    );
               }
                       
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: New request API for dishes
         * @Created: Sandeep Jain
         * @Date: 01 Dec 14
         * @Parameters: user_id (int) and dish_name (varchar), message (text) and allergies_id (command separated integer values)
         * @Response: success or failed
         */
        
        function newrequest()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!isset($data['dish_name']) || empty($data['dish_name']))
            {
                $error .= 'Dish name is Required. ';
            }             
            if(!isset($data['message']) || empty($data['message']))
            {
                $error .= 'Message is Required. ';
            } 
            if(!isset($data['cuisine_id']) || empty($data['cuisine_id']))
            {
                $error .= 'Cuisine is Required. ';
            } 
            if(!isset($data['timestamp']) || empty($data['timestamp']))
            {
                $error .= 'Timestamp is Required. ';
            }
            if(!isset($data['lat']) || empty($data['lat']))
            {
                $error .= 'Location latitude is Required. ';
            }
            if(!isset($data['lng']) || empty($data['lng']))
            {
                $error .= 'Location longitude is Required. ';
            } 
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {
               $this->loadModel('Request');               
               $ins_data = array();
               $ins_data['Request']['user_id'] = $data['user_id'];               
               $ins_data['Request']['dish_name'] = $data['dish_name'];
               $ins_data['Request']['message'] = $data['message'];
               $ins_data['Request']['allergies'] = $data['allergies_id'];
               $ins_data['Request']['cuisine_id'] = $data['cuisine_id'];
               $ins_data['Request']['timestamp'] = $data['timestamp'];
               $ins_data['Request']['lat'] = $data['lat'];
               $ins_data['Request']['lng'] = $data['lng'];
               $this->Request->create();
               if ($this->Request->save($ins_data))
               {
                   $this->response = array(
                            'status' => 1,
                            'message' => "Request has been submitted successauly",                           
                    );
               }
               else
               {
                   $this->response = array(
                            'status' => 2,
                            'message' => "Oops.. server busy this time",                           
                    );
               }
                       
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: Myrequest API, It will return requests of a user
         * @Created: Sandeep Jain
         * @Date: 04 Dec 14
         * @Parameters: user_id (int)
         * @Response: users request in json fomrat
         */
        
        function myrequest()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {
               if (isset($data['offset']) && ($data['offset'] > 1) )
               {
                   $offset = (($data['offset'] - 1) * ($this->limit));
               }
               else
               {
                   $offset = 0;
               }

               $this->loadModel('Request');               
               $req = $this->Request->find('all', array('conditions'=>array('Request.user_id '=>$data['user_id']), 'order'=>'Request.id desc','limit'=>$this->limit, 'offset'=>$offset, 'recursive'=>2, 'contain'=>array('User'=>array('id','name','image'), 'RequestAnswer.Dish'=>array('name','kitchen_id','id','status','created'), 'RequestAnswer.Dish.Kitchen'=>array('id','name'), 'RequestAnswer.Dish.UploadImage'=>array('name')) ));
               //pr($req); exit;
               $req2 = $this->Request->find('all', array('conditions'=>array('Request.user_id '=>$data['user_id']),  'recursive'=>2, 'contain'=>array('User'=>array('id','name','image'), 'RequestAnswer.Dish'=>array('name','kitchen_id','id','status','created'), 'RequestAnswer.Dish.Kitchen'=>array('id','name'), 'RequestAnswer.Dish.UploadImage'=>array('name')) ));
               $ret = array('total'=>count($req2));
               if (!empty($req))
               {
                   $i=0;
                   foreach ($req as $rq)
                   {                         
                        $ret['Request'][$i]['dish_name'] = $rq['Request']['id'];
                        $ret['Request'][$i]['dish_name'] = $rq['Request']['dish_name'];
                        $ret['Request'][$i]['message'] = $rq['Request']['message'];
                        $ret['Request'][$i]['allergies'] = $rq['Request']['allergies'];
                        $ret['Request'][$i]['cuisine_id'] = $rq['Request']['cuisine_id'];
                       
                        if(isset($rq['Request']['timestamp']) && !empty($rq['Request']['timestamp'])) 
		                	$ret['Request'][$i]['timestamp'] = $rq['Request']['timestamp'];
		            	else
		            		$ret['Request'][$i]['timestamp'] = strtotime($rq['Request']['timestamp']);
				
                        ///$ret['Request'][$i]['serve_start_time'] = $rq['RequestAnswer']['Dish']['serve_start_time'];
                        //$ret['Request'][$i]['serve_start'] = $rq['RequestAnswer']['Dish']['serve_start'];
                        $ret['Request'][$i]['request_id'] = $rq['Request']['id'];  
                        $ret['Request'][$i]['user_name'] = $rq['User']['name'];
                        $ret['Request'][$i]['user_id'] = $rq['User']['id'];
                        $ret['Request'][$i]['image'] = ($rq['User']['image'] != "") ? Router::url('/'.PROFILE_IMAGE_URL.$rq['User']['image'],true) : '';
                        if (count($rq['RequestAnswer'])>0)
                        {
                            foreach ($rq['RequestAnswer'] as $ra)
                            {
                                //pr($ra); exit;
                                $ret['Request'][$i]['Answer'][] = array('dish_id'=>$ra['dish_id'], 'answer_id'=>$ra['id'], 'kitchen_id'=>$ra['Dish']['kitchen_id'], 'serve_start_time'=>$ra['Dish']['serve_start_time'], 'serve_start'=>$ra['Dish']['serve_start'], 'kitchen_name'=>$ra['Dish']['Kitchen']['name'], 'dish_name'=>$ra['Dish']['name'], 'serve_end_time'=>$ra['Dish']['serve_end_time'], 'serve_end'=>$ra['Dish']['serve_end'], 'lead_time'=>$ra['Dish']['lead_time'], 'availability'=>$ra['Dish']['status'], 'servertime'=>$ra['Dish']['created'], 'photo' => isset($ra['Dish']['UploadImage']['0']['name']) ? Router::url('/'.$ra['Dish']['UploadImage']['0']['name'],true) : ''); 
                            }
                        }
                        $i++;
                   }
               }
                $this->response = array(
                        'status' => 1,
                        'message' => "user's request found",
                        'value' => $ret,
                );       
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: Request list API will send all requests of all users except current one
         * @Created: Sandeep Jain
         * @Date: 04 Dec 14
         * @Parameters: user_id (int)
         * @Response: requests array in json formatted
         */
        
        function requestlist()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {
            	$this->limit = 15;
               if (isset($data['offset']) && ($data['offset'] > 1) )
               {
                   $offset = (($data['offset'] - 1) * ($this->limit));
               }
               else
               {
                   $offset = 0;
               }
               $this->loadModel('Request');               
		       $this->loadModel('User');
		       $userDetails = $this->User->findById($data['user_id']);
		      
		       $waitingForAns = '';
		       $ret = array('total'=>0);
		       if(isset($userDetails['Kitchen']['id']) && !empty($userDetails['Kitchen']['id']))
		       {
		        $latKitchen = $userDetails['Kitchen']['lat'];
		        $lngKitchen = $userDetails['Kitchen']['lng'];
		        $expression = "( 3959 * acos( cos( radians($latKitchen) ) * cos( radians( Request.lat ) ) * cos( radians( Request.lng ) - radians($lngKitchen) ) + sin( radians($latKitchen) ) * sin( radians( Request.lat ) ) ) )";
		        $radius = 40;
		        $waitingForAns = $this->Request->find('all',
		                                         array('conditions'=>array(
		                                                                  'Request.user_id !='=>$data['user_id'],
		                                                                  'Request.created >='=>date('Y-m-d',strtotime('-7 Days')),
		                                                                   $expression . ' < '.$radius
                                                                          ),
		                                                                  'order'=>'Request.id desc',
		                                                                  'recursive'=>2,
		                                                                  'limit'=>$this->limit,
		                                                                  'offset'=>$offset,
		                                                                  'joins'=>array(
		                                                                    ),
		                                                                  'contain'=>array(
		                                                                    'Cuisine',
		                                                                    'User'=>array('id','name','image'),
		                                                                    'RequestAnswer.Dish'=>array('name','kitchen_id','id','status','created'),
		                                                                    'RequestAnswer.Dish.Kitchen'=>array('id','name'),
		                                                                    'RequestAnswer.Dish.UploadImage'=>array('name'))
		                                                                  )
		                                            );
				
		       }

               $i=0;
               if (!empty($waitingForAns))
               {
                   foreach ($waitingForAns as $rq)
                   {                         
                        $ret['Request'][$i]['dish_name'] = $rq['Request']['dish_name'];
                        $ret['Request'][$i]['message'] = $rq['Request']['message'];
                        $ret['Request'][$i]['allergies'] = $rq['Request']['allergies'];
                        $ret['Request'][$i]['cuisine_id'] = $rq['Request']['cuisine_id'];
                        $ret['Request'][$i]['timestamp'] = $rq['Request']['timestamp'];
                        $ret['Request'][$i]['request_id'] = $rq['Request']['id'];  
                        $ret['Request'][$i]['user_name'] = $rq['User']['name'];
                        $ret['Request'][$i]['user_id'] = $rq['User']['id'];
                        $ret['Request'][$i]['image'] = ($rq['User']['image'] != "") ? Router::url('/'.PROFILE_IMAGE_URL.$rq['User']['image'],true) : '';
                        $ret['Request'][$i]['total_answer'] = count($rq['RequestAnswer']);
                        if (count($rq['RequestAnswer'])>0)
                        {
                            foreach ($rq['RequestAnswer'] as $ra)
                            {
                                $ret['Request'][$i]['Answer'][] = array('dish_id'=>$ra['dish_id'], 'timestamp'=>$ra['timestamp'],'answer_id'=>$ra['id'], 'kitchen_id'=>$ra['Dish']['kitchen_id'], 'kitchen_name'=>$ra['Dish']['Kitchen']['name'], 'dish_name'=>$ra['Dish']['name'], 'availability'=>$ra['Dish']['status'], 'servertime'=>$ra['Dish']['created'], 'photo' => isset($ra['Dish']['UploadImage']['0']['name']) ? Router::url('/'.$ra['Dish']['UploadImage']['0']['name'],true) : ''); 
                            }
                        }
                        $i++;
                   }
               }
               $ret['total'] = $i;
                $this->response = array(
                        'status' => 1,
                        'message' => "user's request found",
                        'value' => $ret,
                );       
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: Myrequest API,delete request
         * @Created: Sandeep Jain
         * @Date: 04 Dec 14
         * @Parameters: user_id (int) and request_id (int)
         * @Response: success or failed message
         */
        
        function deleterequest()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!isset($data['request_id']) || empty($data['request_id']))
            {
                $error .= 'Request id is Required. ';
            }
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {
               $this->loadModel('Request');
               $chk = $this->Request->find('first', array('conditions'=>array('Request.user_id'=>$data['user_id'], 'Request.id'=>$data['request_id'])));               
               if (!empty($chk))
               {
                   $this->Request->id = $data['request_id'];
                   if ($this->Request->delete())
                   {
                       $this->response = array(
                        'status' => 1,
                        'message' => "Request has been deleted successfully"
                       );
                   }
                   else
                   {
                       $this->response = array(
                        'status' => 2,
                        'message' => "Error occured.. Please try after some time"
                       );
                   }
              
                }
                else
                {
                    $this->response = array(
                     'status' => 2,
                     'message' => "Unauthorize access. Permission denide"
                    );
                }
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: To answer of a request
         * @Created: Sandeep Jain
         * @Date: 04 Dec 14
         * @Parameters: user_id (int) and dish_id (int), request_id (int)
         * @Response: success or failed
         */
        
        function answer_request()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!isset($data['timestamp']) || empty($data['timestamp']))
            {
                $error .= 'Timestamp is Required. ';
            }
            if(!isset($data['request_id']) || empty($data['request_id']))
            {
                $error .= 'Request id is Required. ';
            }   
            else
            {
                $this->loadModel('Request');
                $chk = $this->Request->findById($data['request_id']);
                if (empty($chk))
                {
                    $error .= 'Invalid Request';
                }
            }
            if(!isset($data['dish_id']) || empty($data['dish_id']))
            {
                $error .= 'Dish id is Required. ';
            } 
            else
            {
                $this->loadModel('Dish');
                $chkDish = $this->Dish->findById($data['dish_id']);
                if (empty($chkDish))
                {
                    $error .= 'Invalid Answer';
                }
            }

            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {
               $this->loadModel('RequestAnswer');               
               $checkAnswer = $this->RequestAnswer->find('count',array('conditions'=>array('RequestAnswer.user_id'=>$data['user_id'],'RequestAnswer.request_id'=>$data['request_id'],'RequestAnswer.dish_id'=>$data['dish_id'])));
               if(!$checkAnswer)
               {
	               $ins_data = array();
	               $ins_data['RequestAnswer']['user_id'] = $data['user_id'];               
	               $ins_data['RequestAnswer']['request_id'] = $data['request_id'];
	               $ins_data['RequestAnswer']['dish_id'] = $data['dish_id']; 
	               $ins_data['RequestAnswer']['timestamp'] = $data['timestamp'];        
	               $this->RequestAnswer->create();
	               

	               if ($this->RequestAnswer->save($ins_data))
	               {	
	               		$this->loadModel('ActivityLog');
						//Update Activity Log For Answer Request Activity
						$this->ActivityLog->updateLog($chk['Request']['user_id'],7,$this->RequestAnswer->getLastInsertID(),$data['timestamp']);
						
						$message['_message']['m'] = "Your dish request ".$chk['Request']['dish_name']." has been answered by ".$chkDish['Kitchen']['name'];
	               		$pushNoti = $this->Push->send($chk['Request']['user_id'],$message,$chkDish['Kitchen']['id'],1);
	                   	$this->response = array(
	                            'status' => 1,
	                            'message' => "Answer has been submitted successauly",                           
	                    );
	               }
	               else
	               {
	                   $this->response = array(
	                            'status' => 2,
	                            'message' => "Oops.. server busy this time",                           
	                    );
	               }
	           }
	           else
	           {
	           		$this->response = array(
	                            'status' => 2,
	                            'message' => "This answer has been already added.",                           
	                    );
	           }
	                       
            }
            echo json_encode($this->response);
        }
        
        function testMy()
        {
        	//$message['_message']['m'] = "Hello neetika this one is for testing";
	               		
        	//$pushNoti = $this->Push->send(111,$message,38);
        }
        /*
         * Purpose: To get payment details or a user
         * @Created: Sandeep Jain
         * @Date: 04 Dec 14
         * @Parameters: user_id (int)
         * @Response: payment detils in json format
         */
        
        function get_payment_details()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {             
               $rec = array();
               $ret = array();
               $pay = array();
               
               $this->loadModel('User');
               $det = $this->User->findById($data['user_id'], array('User.bank_acc_no', 'User.bank_routing_no', 'User.bank_acc_holdername', 'User.bank_acc_type', 'User.paypal_id', 'User.paypal_name','User.paypal_lname','User.is_paypal_verified', 'User.stripe_user_id', 'User.stripe_publish_id'));
               if (!empty($det))
               {
                   $rec = array('Bank_account_number'=>$det['User']['bank_acc_no'], 'Bank_routing_number'=>$det['User']['bank_routing_no'], 'Bank_account_holder_name'=>$det['User']['bank_acc_holdername'] , 'Bank_account_type'=>$det['User']['bank_acc_type'], 'Paypal_id'=>$det['User']['paypal_id'], 'Paypal_name'=>$det['User']['paypal_name'], 'Paypal_lname'=>$det['User']['paypal_lname'], 'is_verified'=>$det['User']['is_paypal_verified'], 'stripe_user_id'=>$det['User']['stripe_user_id'], 'stripe_publish_id'=>$det['User']['stripe_publish_id']);
               }         
               
               $this->loadModel('PaymentMethod');
               $det = $this->PaymentMethod->findAllByUserId($data['user_id']);
               
               if (!empty($det))
               {
                   foreach ($det as $de)
                   { 
                      $pay[] = array('card_id'=>$de['PaymentMethod']['id'], 'card_no'=>$de['PaymentMethod']['card_no'], 'type'=>$de['PaymentMethod']['type'], 'exp_month'=>$de['PaymentMethod']['exp_month'], 'exp_year'=>$de['PaymentMethod']['exp_year'], 'card_name'=>$de['PaymentMethod']['card_name']);
                   }
               }
               
               $ret['Receipt'] = $rec;
               $ret['Payment'] = $pay;             
               
                $this->response = array(
                         'status' => 1,
                         'message' => "Payment details found",
                         'value' => $ret
                 );              
                       
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: update payment detils
         * @Created: Sandeep Jain
         * @Date: 04 Dec 14
         * @Parameters: user_id (int) and back info
         * @Response: payment detils in json format
         */
        
        function update_payment_details()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {             
               $b_ac_no = "";
               $b_routing = "";
               $b_holder = "";
               $stripe_user_id = "";
               $stripe_publish_id = "";
               /*
               if (isset($data['Bank_account_number']))
               {
                   $b_ac_no = $data['Bank_account_number'];
               }
               if (isset($data['Bank_routing_number']))
               {
                   $b_routing = $data['Bank_routing_number'];
               }
               if (isset($data['Bank_account_holder_name']))
               {
                   $b_holder = $data['Bank_account_holder_name'];
               }
               if (isset($data['Bank_account_type']))
               {
                   $b_type = $data['Bank_account_type'];
               } 
               */           
               if (isset($data['stripe_user_id']))
               {
                   $stripe_user_id = $data['stripe_user_id'];
               }
               if (isset($data['stripe_publish_id']))
               {
                   $stripe_publish_id = $data['stripe_publish_id'];
               } 

               $this->loadModel('User');
               $ins = array();
               $ins['User']['id'] = $data['user_id'];
               /*$ins['User']['bank_acc_no'] = $b_ac_no;
               $ins['User']['bank_routing_no'] = $b_routing;
               $ins['User']['bank_acc_holdername'] = $b_holder;
               $ins['User']['bank_acc_type'] = $b_type;
               */
               $ins['User']['stripe_user_id'] = $stripe_user_id;
               $ins['User']['stripe_publish_id'] = $stripe_publish_id;
               
               if ($this->User->save($ins) )
               {
                    $status = 1;
                    $message = "Payment information has been saved successfully";
               }
               else
               {
                    $status = 2;
                    $message = "Payment information has not been saved";
               }                        
               
               $rec = array();
               $ret = array();
               $pay = array();              
               
               $det = $this->User->findById($data['user_id'], array('User.bank_acc_no', 'User.bank_routing_no', 'User.bank_acc_holdername', 'User.bank_acc_type', 'User.stripe_user_id', 'User.stripe_publish_id'));
               if (!empty($det))
               {
                   $rec = array('Bank_account_number'=>$det['User']['bank_acc_no'], 'Bank_routing_number'=>$det['User']['bank_routing_no'], 'Bank_account_holder_name'=>$det['User']['bank_acc_holdername'], 'Bank_account_type'=>$det['User']['bank_acc_type'], 'stripe_user_id'=>$det['User']['stripe_user_id'], 'stripe_publish_id'=>$det['User']['stripe_publish_id']);
               }         
               
               $this->loadModel('PaymentMethod');
               $det = $this->PaymentMethod->findAllByUserId($data['user_id']);
               if (!empty($det))
               {
                   foreach ($det as $de)
                   {
                       $pay[] = array('card_no'=>$de['card_no'], 'type'=>$de['type'],'exp_month'=>$de['exp_month'],'exp_year'=>$de['exp_year']);
                   }
               }
               
               $ret['Receipt'] = $rec;
               $ret['Payment'] = $pay;             
               
              
                $this->response = array(
                     'status' => $status,
                     'message' => $message,
                     'value' => $ret
                 );    
                                      
                       
            }
            echo json_encode($this->response);
        }
        
         /*
         * Purpose: delete payment card details from user's account
         * @Created: Sandeep Jain
         * @Date: 04 Dec 14
         * @Parameters: user_id (int) and card_id in int
         * @Response: payment detils in json format
         */
        
        function delete_card_details()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!isset($data['card_type_id']) || empty($data['card_type_id']))
            {
                $error .= 'Card type id is Required. ';
            }
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {  
               $this->loadModel('PaymentMethod');
               $chk = $this->PaymentMethod->find('first', array('conditions'=>array('PaymentMethod.user_id'=>$data['user_id'], 'PaymentMethod.id'=> 'card_type_id')));
               if (!empty($chk))
               {
                   $this->PaymentMethod->id = $data['card_type_id'];
                   if ($this->PaymentMethod->delete())
                   {
                       $message = "Card details has been deleted successfully";
                       $status = 1;
                   }
                   else
                   {
                       $message = "Error occured. Please try after some time";
                       $status = 2;
                   }
               }
               else
               {
                   $message = "Unauthorize access. Permission denide";
                   $status = 2;
               } 
                              
               $rec = array();
               $ret = array();
               $pay = array();              
               $this->loadModel('User');
               $det = $this->User->findById($data['user_id'], array('User.bank_acc_no', 'User.bank_routing_no', 'User.bank_acc_holdername', 'User.bank_acc_type', 'User.stripe_user_id', 'User.stripe_publish_id'));
               if (!empty($det))
               {
                   $rec = array('Bank_account_number'=>$det['User']['bank_acc_no'], 'Bank_routing_number'=>$det['User']['bank_routing_no'], 'Bank_account_holder_name'=>$det['User']['bank_acc_holdername'], 'Bank_account_type'=>$det['User']['bank_acc_type'], 'stripe_user_id'=>$det['User']['stripe_user_id'], 'stripe_publish_id'=>$det['User']['stripe_publish_id']);
               }         
               
               
               $det = $this->PaymentMethod->findAllByUserId($data['user_id']);
               if (!empty($det))
               {
                   foreach ($det as $de)
                   {
                       $pay[] = array('card_no'=>$de['card_no'], 'type'=>$de['type']);
                   }
               }
               
               $ret['Receipt'] = $rec;
               $ret['Payment'] = $pay;             
               
              
                $this->response = array(
                     'status' => $status,
                     'message' => $message,
                     'value' => $ret
                 );    
            }
            echo json_encode($this->response);
        }
        
        /*
         * Purpose: submit order api
         * @Created: Sandeep Jain
         * @Date: 05 Dec 14
         * @Parameters: user_id (int) and card_id in int
         * @Response: sucess or failed message
         * 
         * Updated on :17 Dec 14
         * Updated by: Bharat Borana
         * Purpose: Order confirmation code on message and save card details
         */
        
        function submit_order()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            
            $data = $this->request->data;
 			$error = '';
 			
 			if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }
            if(!isset($data['amount']) || empty($data['amount']))
            {
                $error .= 'Order amount is required. ';
            }            
            if(!isset($data['phone']) || empty($data['phone']))
            {
                $error .= 'Phone is required. ';
            }
            if(!isset($data['address']) || empty($data['address']))
            {
                $error .= 'Address is required. ';
            }
            if(!isset($data['address_lat']) || empty($data['address_lat']))
            {
                $error .= 'Address Latitude is required. ';
            }
            if(!isset($data['address_lng']) || empty($data['address_lng']))
            {
                $error .= 'Address Longitude is required. ';
            }
            if(!isset($data['delivery_location']) || empty($data['delivery_location']))
            {
                $error .= 'Delivery location is required. ';
            }

            if(!isset($data['kitchen_id']) || empty($data['kitchen_id']))
            {
                $error .= 'Kitchen id is required. ';
            }
            if(!isset($data['country_code']) || empty($data['country_code']))
            {
                $error .= 'Country code is required. ';
            }
            /*if(!isset($data['dine_type']) || $data['dine_type']=='')
            {
                $error .= 'Please select type of dining.';
            }*/
            if(!isset($data['timestamp']) || $data['timestamp']=='')
            {
                $error .= 'Please Send timestamp value for this order.';
            }
            if(!isset($data['dish']) || count($data['dish'])<=0 )
            {
                $error .= 'Dish array is required. ';
            }
            if(!isset($data['payment_type']) || $data['payment_type']=='')
			{
				$error .= 'Please specify mode of payment';
			}
			if(!isset($data['delivery_date']) || empty($data['delivery_date']))
			{
				$error .= 'Please select delevery date.';
			}
			if(!isset($data['delivery_time']) || empty($data['delivery_time']))
			{
				$error .= 'Please select delevery time.';
			}
			
			if(!isset($data['service_fee']) || empty($data['service_fee']))
			{
				$error .= 'Service fee required.';
			}
			//	$data['service_fee'] = 0;

			if(!isset($data['tax_percent']) || empty($data['tax_percent']))
				$data['tax_percent'] = 0;

			if(!isset($data['sale_tax']) || empty($data['sale_tax']))
				$data['sale_tax'] = 0;

			if(!isset($data['order_value']) || empty($data['order_value']))
				$data['order_value'] = $data['amount'];

            if(isset($data['payment_type']) && $data['payment_type']==0){
				if(!isset($data['card_json']) || empty($data['card_json']))
				{
					$error .= 'Card Token is required. ';
				}
				if(!isset($data['card_no']) || empty($data['card_no']))
				{
					$error .= 'Card number is required. ';
				}
				if(!isset($data['type']) || empty($data['type']))
				{
					$error .= 'Card type is required. ';
				}
				if(!isset($data['exp_month']) || empty($data['exp_month']))
				{
					$error .= 'Card Expire month is required. ';
				}
				if(!isset($data['exp_year']) || empty($data['exp_year']))
				{
					$error .= 'Card Expire year is required. ';
				}
				if(!isset($data['card_name']) || empty($data['card_name']))
				{
					$error .= 'Name on card is required. ';
				}
			}

            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {  
               $data['dish'] = json_decode($data['dish']);
               $this->loadModel('Order');
               $dish_res = array();

               foreach ($data['dish'] as $dis)
               {   
                   $dish_res[] = array('dish_id' => $dis->dish_id, 
                                        'kitchen_id' => $dis->kitchen_id, 
                                        'quantity'=>$dis->quantity, 
                                        'price'=>$dis->price,
                                        'type'=>$dis->type, 
                                        'dish_name'=>$dis->dish_name,                                         
                                        'portion'=>$dis->portion
                                       );
               }            
               
               $inst = array(
                    'Order' => array('user_id' => $data['user_id'], 'amount'=>$data['amount'], 'sale_tax'=>$data['sale_tax'], 'service_fee'=>$data['service_fee'], 'tax_percent'=>$data['tax_percent'], 'order_value'=>$data['order_value'], /*'dine_type' => $data['dine_type'],*/ 'delivery_date' => $data['delivery_date'], 'delivery_time' => $data['delivery_time'],'kitchen_id'=>$data['kitchen_id']),
                    'OrderDish' => $dish_res,
                    'OrderAddress' => array('order_address'=>$data['address'] , 'phone'=>$data['phone'],'delivery_location' => $data['delivery_location'], 'address_lat' => $data['address_lat'],'address_lng' => $data['address_lng']),         
			   );
            
               //For balanced payments required details fetch
               $this->loadModel('Kitchen');
			   $supplierData = $this->Kitchen->getKitchenDetails($data['kitchen_id']);
			
			   if(empty($error)){
				
				if($data['payment_type']==0)
				{
					//For Stripe Payments action
					$chargeArray['card'] = $data['card_json'];
					$chargeArray['amount'] = round($data['amount'],2)*100;
					$chargeArray['currency'] = 'usd';
					$chargeArray['description'] = "Order for ".$supplierData['Kitchen']['name']." kitchen";
				   	
				   	$paymentDone = $this->Stripe->charge($chargeArray);
								
					if($paymentDone['status']=='success')
					    $inst['Order']['order_href']=$paymentDone['response']['id'];
				}
				else
				{
					//For paypal payments
					$inst['Order']['transaction_id']=$data['transaction_id'];
					$paymentDone['status']=1;
					$paymentDone['order_id']=$data['transaction_id'];
				}
				$inst['Order']['payment_type']=$data['payment_type'];
				if($paymentDone['status']=='success' || $paymentDone['status']==1)
				{
					
				$this->Order->create();
				if ($this->Order->SaveAssociated($inst))
				{
				$orderId = $this->Order->getLastInsertID();
				//Update Activity Log
				$this->loadModel('ActivityLog');
				//Update Activity Log For Order Place Activity
				$this->ActivityLog->updateLog($data['user_id'],1,$orderId,$data['timestamp']);
				//Update Activity Log For Order Received Activity
				$this->ActivityLog->updateLog($supplierData['Kitchen']['user_id'],2,$orderId,$data['timestamp']);

				//send email with order details
				$this->Order->Behaviors->attach('Containable');
				$conditions[] = array('Order.id'=>$orderId);
				$orderDataEmail = $this->Order->find('first',array('conditions'=>$conditions,
													'contain'=>array('User'=>array('fields'=>array('User.name','User.email','User.id')),
													'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name','Kitchen.sales_tax')))),
													'recursive'=>2));
				$this->Order->Behaviors->detach('Containable');

				$message['_message']['m'] = "You have received an order from ".$orderDataEmail['User']['name'];
				$pushNoti = $this->Push->send($supplierData['Kitchen']['user_id'],$message,$orderId,2);

				$this->Paypal->sendMail($orderDataEmail);
				//////////////*********///////////////

				//For balanced payments card save function
				if($data['payment_type']==0)
				{
					if(isset($data['save_card']) && $data['save_card']==1 )
					{
						$this->loadModel('PaymentMethod');
						$payArray = array('PaymentMethod'=>array( 'card_no'=>$data['card_no'],
						'exp_month'=>$data['exp_month'],
						'exp_year'=>$data['exp_year'],
						'card_name'=>$data['card_name'],
						'type'=>$data['type'],
						'save_card'=>$data['save_card'],
						'user_id'=>$data['user_id']));
						$isCardExists = $this->PaymentMethod->find('first',array('conditions'=>array('PaymentMethod.user_id'=>$data['user_id'],'PaymentMethod.card_no'=>$data['card_no'])));
						if(empty($isCardExists)){
						$this->PaymentMethod->create();
						$this->PaymentMethod->save($payArray);
					}
				}
				}
				$this->response = array(
				'status' => 1,
				'order_id' => $paymentDone['response']['id'],
				'message' => 'Order has been placed successfully',                        
				);    
				}
				else
				{
				$this->response = array(
				'status' => 2,
				'message' => 'Opps.. error found. Please try later',                        
				);    
				}                
				}
				else
				{
					$this->response = $paymentDone;
				}
			   }
			   else
			   {
				   $this->response = array(
							'status' => 2,
							'message' => $error
					);
			   }
            }
            echo json_encode($this->response);        
        }
        
        /*
         * Purpose: Order history
         * @Created: Sandeep Jain
         * @Date: 05 Dec 14
         * @Updated: Bharat Borana
         * @Date: 29 Dec 14
         * @Parameters: user_id (int)
         * @Response: orders in json format
         */
        
        function order_history()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }           
            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {               
               $results = array();
               $this->loadModel('Order');
               $this->loadModel('Kitchen');
               $ordersArray = $this->Kitchen->getallorderIds($data['user_id']);
			   $this->Order->Behaviors->attach('Containable');
               $orders_recieved = array();
               if(!empty($ordersArray)){
				   $orders_recieved = $this->Order->find('all',array('conditions'=>array('Order.id IN('.$ordersArray.')'),
																   'contain'=>array('User'=>array('fields'=>array('User.name','User.id')),
																					'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name')))),
																   'order'=>'Order.created DESC',
																   'recursive'=>2));
			   }
               $orders_placed = $this->Order->find('all',array('conditions'=>array('Order.user_id'=>$data['user_id']),
																 'contain'=>array('OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name')))),
																  'order'=>'Order.created DESC',
																  'recursive'=>2));
               $this->Order->Behaviors->detach('Containable');
                if(!empty($orders_recieved)){
				   foreach ($orders_recieved as $ord) 
				   {
					   $mainArray = array();
					   if(isset($ord['Order']) && isset($ord['OrderDish']) && !empty($ord['OrderDish']))
					   {
						$mainArray['order_id'] = $ord['Order']['id'];
						$mainArray['order_date'] = $ord['Order']['created']; 
						
						if($ord['Order']['payment_type']==0)
						$mainArray['transaction_id'] = str_replace('/orders/','',$ord['Order']['order_href']);
						else
						$mainArray['transaction_id'] = $ord['Order']['transaction_id'];

						$mainArray['dine_type'] = $ord['Order']['dine_type'];  
						$mainArray['order_date'] = $ord['Order']['created']; 
						$mainArray['amount'] = $ord['Order']['amount']; 
						$mainArray['order_value'] = $ord['Order']['order_value']; 
						$mainArray['sale_tax'] = $ord['Order']['sale_tax']; 
						$mainArray['service_fee'] = $ord['Order']['service_fee']; 
						$mainArray['tax_percent'] = $ord['Order']['tax_percent']; 
						
						$mainArray['order_by'] = ''; 
						$mainArray['order_by_id'] = ''; 
						$mainArray['dishes'] = '';
					   
					    if(isset($ord['User']['name'])){
							$mainArray['order_by'] = $ord['User']['name'];
							$mainArray['order_by_id'] = $ord['User']['id']; 
						   }
					   if(isset($ord['OrderDish']))
					   {

							$mainArray['dishes'] = $ord['OrderDish']; 
							$mainArray['kitchen_name'] = ''; 
							if(isset($ord['OrderDish'][0]['Kitchen']['name']))
								$mainArray['kitchen_name'] = $ord['OrderDish'][0]['Kitchen']['name']; 
					   }    

					   $or[] = $mainArray;
					   }
				   }      
			   }
				
               if(!empty($orders_placed)){
				   foreach ($orders_placed as $ord)
				   {
						$mainArray = array();
					   if(isset($ord['Order']) && isset($ord['OrderDish']) && !empty($ord['OrderDish']))
					   {
						$mainArray['order_id'] = $ord['Order']['id'];
						$mainArray['order_date'] = $ord['Order']['created'];
						
						if($ord['Order']['payment_type']==0)
						$mainArray['transaction_id'] = str_replace('/orders/','',$ord['Order']['order_href']);
						else
						$mainArray['transaction_id'] = $ord['Order']['transaction_id'];
						$mainArray['dine_type'] = $ord['Order']['dine_type']; 
						 
						$mainArray['amount'] = $ord['Order']['amount']; 

						$mainArray['order_value'] = $ord['Order']['order_value']; 
						$mainArray['sale_tax'] = $ord['Order']['sale_tax']; 
						$mainArray['service_fee'] = $ord['Order']['service_fee'];
						$mainArray['tax_percent'] = $ord['Order']['tax_percent'];
						
						$mainArray['dishes'] = '';

						if(isset($ord['OrderDish']))	{
						$mainArray['dishes'] = $ord['OrderDish'];  
						$mainArray['kitchen_name'] = ''; 
						if(isset($ord['OrderDish'][0]['Kitchen']['name']))
							$mainArray['kitchen_name'] = $ord['OrderDish'][0]['Kitchen']['name']; 
						}    
					   
					   $op[] = $mainArray;
					   }
				   }      
			   }
			   
			   $results['order_placed'] = array();
			   if(isset($op) && !empty($op))
               $results['order_placed'] = $op;
               
               $results['order_recieved'] = array();
			   if(isset($or) && !empty($or))
               $results['order_recieved'] = $or;
          
                $this->response = array(
                     'status' => 1,
                     'message' => 'Orders found',
                    'value' => $results
                 );
                               
                       
            }
            echo json_encode($this->response);        
        }
        
        /*
         * Purpose: Fetch all Countries list
         * @Created: Bharat Borana
         * @Date: 23 Dec 14
         * @Parameters: 
         * @Response: list of countries in json format
         */
        
        function getCountries()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
     		   $results = array();
			   $this->loadModel('Country');
			   $countries = $this->Country->getAllCountryName();
			  $this->response = array(
					 'status' => 1,
					 'message' => 'Coutries found',
					'value' => $countries
				 );
         echo json_encode($this->response);        
        }
        
         /*
         * Purpose: Fetch all States list
         * @Created: Bharat Borana
         * @Date: 23 Dec 14
         * @Parameters: 
         * @Response: list of states in json format
         */
        
        function getStates()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {            
     		   $this->loadModel('State');
			   $states = $this->State->getAllStateName(223);
			   $states = array_values($states);
			   $this->response = array(
					 'status' => 1,
					 'message' => 'States found',
					'value' => $states
				 );
			}	 
         echo json_encode($this->response);        
        }
        
         /*
         * Purpose: Fetch all Cities list
         * @Created: Bharat Borana
         * @Date: 23 Dec 14
         * @Parameters: 
         * @Response: list of cities in json format
         */
        
        function getCities()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['state_id']) || empty($data['state_id']))
            {
                $error .= 'State id is Required. ';
            }           
            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {            
     		   $this->loadModel('City');
			   $states = $this->City->getAllCityName($data['state_id']);
			   $this->response = array(
					 'status' => 1,
					 'message' => 'Cities found',
					'value' => $states
				 );
			}	 
         echo json_encode($this->response);        
        }
        
         /*
         * Purpose: User's Dashboad Data
         * @Created: Bharat Borana
         * @Date: 31 Dec 14
         * @Parameters: user_id (int)
         * @Response: Dashboard data in json format
         */
        
        function user_dashboard()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            } 
            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {               
               $results = array();
               $this->loadModel('User');
               $userDetails = $this->User->getAllActivities($data);
                
               $this->loadModel('ActivityLog');
               $activityCount = $this->ActivityLog->find('count',array('conditions'=>array('ActivityLog.status'=>1, 'ActivityLog.user_id'=>$data['user_id']))); 
		       
		       $this->response = array(
                     'status' => 1,
                     'message' => 'Data found',
                     'count' => $activityCount,
                    'value' => $userDetails
                 );
                               
                       
            }
            echo json_encode($this->response);        
        }
        
        /*
		 * Method	: searchforbulk
		 * Author	: Bharat Borana
		 * Created	: 02 Jan, 2015
		 * @Search kitchen and dishes with limited output params
		 */
		public function searchforbulk()
		{ 
			//header('Content-Type: application/json');
			$this->request->onlyAllow('POST');
			$data = $this->request->data;
			$error = '';
			
			if(!isset($data['address']) || empty($data['address']))
			{
				$error .= 'Address is Required. ';
			}
			
			if(!empty($error))
			{
				$this->response = array(
					'status' => 2,
					'message' => $error
				);
			}
			else
			{ 
				$this->loadModel('Kitchen');
				$results = $this->Kitchen->searchKitchenForBulk($data, $this);
				$this->response = array(
						'status' => 1,
						'value'	=> $results,
						'message' => 'success'
				);
			}
			echo json_encode($this->response);
		}
	
		/*
		 * Purpose: User's Dashboad Data
		 * @Created: Bharat Borana
		 * @Date: 29 Dec 14
		 * @Parameters: user_id (int)
		 * @Response: Dashboard data in json format
		 */
        
        function user_profile()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['user_id']) || empty($data['user_id']))
            {
                $error .= 'User id is Required. ';
            }           
            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {               
               $results = array();
               $this->loadModel('User');
			   $userDetails = $this->User->getUserCountData($data['user_id']);
              
               $dashArray['User'] = array();
			   if(!empty($userDetails)){
					$dashArray['User']['id'] = $userDetails['User']['id'];
					$dashArray['User']['name'] = $userDetails['User']['name'];
					if(isset($userDetails['User']['image']))
					$dashArray['User']['image'] = (!empty($userDetails['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$userDetails['User']['image'],true) : "";
					$dashArray['User']['description'] = $userDetails['User']['description'];
				}
				
				$dashArray['Kitchen'] = array();
			    if(isset($userDetails['Kitchen']) && !empty($userDetails['Kitchen'])){
					$dashArray['Kitchen'] = $userDetails['Kitchen'];
					$dashArray['Dish']['Count'] = 0;
					if(isset($userDetails['Kitchen']['Dish']) && !empty($userDetails['Kitchen']['Dish'][0]['Dish'][0]['noOfDishes'])){
						$dashArray['Dish']['Count'] = $userDetails['Kitchen']['Dish'][0]['Dish'][0]['noOfDishes'];
						unset($dashArray['Kitchen']['Dish']);
					}
				}
				
				$dashArray['OrderPlaced']['Count'] = 0;
				if(isset($userDetails['Order']) && !empty($userDetails['Order'])){
					$dashArray['OrderPlaced']['Count'] = $userDetails['Order'][0]['Order'][0]['noOfPlacedOrders'];
				}
				
				$this->loadModel('Kitchen');
			    $ordersArray = $this->Kitchen->getKitchenDataForDashboard($data['user_id']);
   			    $dashArray['OrderReceived']['Count'] = 0;
				if(isset($ordersArray['oIds']) && !empty($ordersArray['oIds'])){
					$dashArray['OrderReceived']['Count'] = $ordersArray['dishesServed'];
				}
			    $this->response = array(
                     'status' => 1,
                     'message' => 'Data found',
                    'value' => $dashArray
                 );
                               
                       
            }
            echo json_encode($this->response);        
        }
        
        /*
		 * Purpose: App CMS Data
		 * @Created: Bharat Borana
		 * @Date: 31 Dec 14
		 * @Parameters: 
		 * @Response: App CMS data including Orders, featured kitchens and videos in json format
		 */
        
        function app_cms()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['app_id']) || empty($data['app_id']))
            {
                $error .= 'Application id is Required. ';
            }
            if(isset($data['app_id']) && $data['app_id']!='abc-app'){
			    $error .= 'Incorrect application id, please try again later. ';
            }           
            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {               
               $cmsDetails = array();
               $this->loadModel('Kitchen');
			   $cmsDetails['Kitchen'] = $this->Kitchen->getfeaturedKitchens();
              
			   $this->loadModel('Testimonial');
			   $cmsDetails['Testimonial'] = $this->Testimonial->getfeaturedTestimonials();
              
			   $this->loadModel('Video');
			   $cmsDetails['Video'] = $this->Video->getfeaturedVideos();
              
               $this->response = array(
                     'status' => 1,
                     'message' => 'Data found',
                    'value' => $cmsDetails
                 );
                               
                       
            }
            echo json_encode($this->response);        
        }
     
      /*
		 * Purpose: Order completion api
		 * @Created: Bharat Borana
		 * @Date: 02 Jan 15
		 * @Parameters: order_id, user_id
		 * @Response: success if order_exists
		 */
        
        public function order_completion()
		{
			//header('Content-Type: application/json');
			$this->request->onlyAllow('POST');
			$data = $this->request->data;
			$error = '';
			if(empty($data['order_id']))
			{
				$error .= 'Order id is Required. ';
			}
			if(empty($data['user_id']))
			{
				$error .= 'User id is Required. ';
			}
		
			if(!empty($error))
			{
				$this->response = array(
						'status' => 2,
						'message' => $error
				);
			}
			else
			{
				$this->loadModel('Order');
				$order = $this->Order->find('first', array('conditions'=>array('Order.id' => $data['order_id'],
																				'Order.user_id' => $data['user_id']),
														   'recursive'=>0					
																		
				));
				
				
				if(!empty($order))
				{
					if($order['Order']['is_verified']==1)
					{
						$this->Order->id = $data['order_id'];
						$this->Order->set('is_verified', 2);
						if($this->Order->save())
						{
							$this->response = array(
									'status' => 1,
									'message' => "Your order has been successfully completed."
							);
						}
						else 
						{
							$this->response = array(
									'status' => 2,
									'message' => 'Error, Please try again'
							);
						}
					}
					else if($order['Order']['is_verified']==2)
					{
							$this->response = array(
									'status' => 2,
									'message' => 'Your order already has been completed.'
							);
					}
					else
					{
							$this->response = array(
									'status' => 2,
									'message' => 'Seller has not confirmed this order. Try again later.'
							);
					}
				}
				else 
				{
					$this->response = array(
											'status' => 2,
											'message' => "Order does not exists."
									);
				}
			}
			echo json_encode($this->response);//592746
		}  
	
	/*
		 * Purpose: Order confirmation api
		 * @Created: Bharat Borana
		 * @Date: 02 Jan 15
		 * @Parameters: order_id, user_id
		 * @Response: success if order_exists
		 */
        
        public function order_confirmation()
		{
			//header('Content-Type: application/json');
			$this->request->onlyAllow('POST');
			$data = $this->request->data;
			$error = '';
			if(empty($data['order_id']))
			{
				$error .= 'Order id is Required. ';
			}
			if(empty($data['kitchen_id']))
			{
				$error .= 'Kitchen id is Required. ';
			}
			if(empty($data['user_id']))
			{
				$error .= 'User id is Required. ';
			}
			if(empty($data['timestamp']))
			{
				$error .= 'Timestamp is Required. ';
			}
			if(!isset($data['status']))
			{
				$error .= 'Status is Required. ';
			}
			
			if(!empty($error))
			{
				$this->response = array(
						'status' => 2,
						'message' => $error
				);
			}
			else
			{
				$this->loadModel('Order');
				$order = $this->Order->find('first', array('conditions'=>array('Order.id' => $data['order_id'])));
				if(isset($order['Order']['id']) && $order['OrderDish'][0]['kitchen_id'] == $data['kitchen_id'])
				{
					$checkStatus = 1;
					$this->Order->id = $data['order_id'];
					if($data['status']==1)
					{
						if((strtotime(date('Y-m-d')) < strtotime($order['Order']['delivery_date'])) || (strtotime(date('Y-m-d')) == strtotime($order['Order']['delivery_date']) && time() < strtotime($order['Order']['delivery_time'])))
						{
							$this->Order->set('is_verified', 1);
							$status = 1;
							$message = 'Your order has successfully confirmed.';
						}
						else
						{
							$this->Order->set('is_verified', 3);
							$status = 2;
							$message = 'You are not allowed to accept this order. You order has been expired.';
							$this->loadModel('ActivityLog');
							$activityLog = $this->ActivityLog->updateAll(array('ActivityLog.status'=>0),array('ActivityLog.activity_id'=>2,'ActivityLog.user_id'=>$data['user_id'],'ActivityLog.order_id'=>$order['Order']['id']));
						}
					}
					else
					{
						if($order['Order']['is_verified'] == 0)
						{
							$status = 1;
							$message = 'Your order has successfully declined.';
						
							$this->Order->set('is_verified', 3);
							$this->loadModel('ActivityLog');
							$activityLog = $this->ActivityLog->updateAll(array('ActivityLog.status'=>0),array('ActivityLog.activity_id'=>2,'ActivityLog.user_id'=>$data['user_id'],'ActivityLog.order_id'=>$order['Order']['id']));
						}
						else
						{
							$status = 2;
							$message = 'This order has already confirmed.';
						
						}
					}

					if($this->Order->save())
					{
						$this->response = array(
							'status' => $status,
							'message' => $message
						);
					}
					else 
					{
						$this->response = array(
								'status' => 2,
								'message' => 'Error, Please try again'
						);
					}

				}
				else 
				{
					$this->response = array(
											'status' => 2,
											'message' => "Order does not exists."
									);
				}
			}
			echo json_encode($this->response);//592746
		}

		/*
		 * Purpose: Order completion api
		 * @Created: Bharat Borana
		 * @Date: 02 Jan 15
		 * @Parameters: order_id, user_id
		 * @Response: success if order_exists
		 */
        
        public function order_cancellation()
		{
			//header('Content-Type: application/json');
			$this->request->onlyAllow('POST');
			$data = $this->request->data;
			$error = '';
			if(empty($data['order_id']))
			{
				$error .= 'Order id is Required. ';
			}
			if(empty($data['user_id']))
			{
				$error .= 'User id is Required. ';
			}
			if(empty($data['timestamp']))
			{
				$error .= 'Timestamp is Required. ';
			}
		
			if(!empty($error))
			{
				$this->response = array(
						'status' => 2,
						'message' => $error
				);
			}
			else
			{
				$this->loadModel('Order');
				$this->Order->bindModel(array('belongsTo'=>array('Kitchen')));
				$order = $this->Order->find('first', array('conditions'=>array('Order.id' => $data['order_id'],
																				'Order.user_id' => $data['user_id']),
														   'recursive'=>0					
																		
				));
				$this->Order->unbindModel(array('belongsTo'=>array('Kitchen')));
				
				if(!empty($order))
				{
					if($order['Order']['is_verified']==0)
					{
						$this->Order->id = $data['order_id'];
						$this->Order->set('is_verified', 4);
						if($this->Order->save())
						{
							$this->loadModel('ActivityLog');
							$activityLog = $this->ActivityLog->updateAll(array('ActivityLog.status'=>0),array('ActivityLog.activity_id'=>2,'ActivityLog.user_id'=>$order['Kitchen']['user_id'],'ActivityLog.order_id'=>$order['Order']['id']));
				
							$this->response = array(
									'status' => 1,
									'message' => "Your order has cancelled successfully."
							);
						}
						else 
						{
							$this->response = array(
									'status' => 2,
									'message' => 'Error, Please try again'
							);
						}
					}
					else if($order['Order']['is_verified']==1)
					{
							$this->response = array(
									'status' => 2,
									'message' => 'Your order has already verified by kitchen owner.'
							);
					}
					else
					{
						$this->response = array(
									'status' => 2,
									'message' => 'Your order has already completed.'
							);
					}
				}
				else 
				{
					$this->response = array(
											'status' => 2,
											'message' => "Order does not exists."
									);
				}
			}
			echo json_encode($this->response);//592746
		} 
	
		/*
		 * Purpose: Change activity log status
		 * @Created: Bharat Borana
		 * @Date: 02 Jan 15
		 * @Parameters: activitylog_id, user_id
		 * @Response: success if activity_exists
		 */
        
        public function removeActivity()
		{
			//header('Content-Type: application/json');
			$this->request->onlyAllow('POST');
			$data = $this->request->data;
			$error = '';
			if(empty($data['activitylog_id']))
			{
				$error .= 'Please select an activity. ';
			}
			if(empty($data['user_id']))
			{
				$error .= 'User id should not empty. ';
			}
			
			if(!empty($error))
			{
				$this->response = array(
						'status' => 2,
						'message' => $error
				);
			}
			else
			{
				$this->loadModel('ActivityLog');
				$activityLog = $this->ActivityLog->find('count', array('conditions'=>array('ActivityLog.id' => $data['activitylog_id'],'ActivityLog.user_id' => $data['user_id'])));
				if($activityLog)
				{
					$this->ActivityLog->id = $data['activitylog_id'];
					$this->ActivityLog->set('status', 0);
					if($this->ActivityLog->save())
					{
						$this->response = array(
								'status' => 1,
								'message' => "This activity has been successfully removed from your dashboard."
						);
					}
					else 
					{
						$this->response = array(
								'status' => 2,
								'message' => 'Error, Please try again'
						);
					}
				}
				else 
				{
					$this->response = array(
											'status' => 2,
											'message' => "Activity does not exists."
									);
				}
			}
			echo json_encode($this->response);//592746
		}	  	
    
	function testFnb(){
		
		//$this->SuiteTest->deleteBankAccount();
		//$this->SuiteTest->deleteBankAccount('/cards/CC5VB3hr1ZHRKlCy4tZNxpuT');
		//$this->SuiteTest->deleteBankAccount('/bank_accounts/BA6rqsnvhn85gdLiFx6GTAFq');
		/*$code = mt_rand(100000, 999999);
		$name = ucfirst('Bharat Borana');
		$text = "Dear Bharat Borana, Enter abcd on confirmation code to verify your account on Tuckle.";
		App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
		$client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);
		$message = $client->account->messages->sendMessage(
				TWILIO_FROM_NUMBER, // From a valid Twilio number
				'+91123123123', // Text this number
				$text
		);
		pr($message); exit; 
		$sendsms = '';
		$param['To'] = "919001202858";
		$param['Message'] = "Hello World";
		$param['UserName'] = "xicomtest";
		$param['Password'] = "bGu0lynz";
		$param['Mask'] = "DEMOSG";
		$param['v'] = "1.1";
		$param['Type'] = "Individual";
		foreach($param as $key=>$val){
			$sendsms .= $key."=".urlencode($val);
			$sendsms .= "&"; 
		}
		$sendsms = substr($sendsms,0,strlen($sendsms)-1);
		$url = "http://www.smsgatewaycenter.com/library/send_sms_2.php?".$sendsms;
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		$curl_scraped_page = curl_exec($ch);
		curl_close($ch);
		echo $curl_scraped_page;*/
	}
        
    /*
	 * Method	: getAdminId
	 * Author	: Bharat Borana
	 * Created	: 22 Jan, 2015
	 * @Search for admin email id
	 */
	public function getAdminId()
	{ 
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		
		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User Id is Required. ';
		}
		else
		{	
			$this->loadModel('User');
			$userDetails = $this->User->findById($data['user_id']);
			if(empty($userDetails))
			{
				$error .= 'User does not exists. ';
			}
		}
		

		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{ 
			$adminDetails = $this->User->find('first',array('fields'=>array('User.id'),'conditions'=>array('User.group_id'=>1)));
			if(isset($adminDetails) && !empty($adminDetails))
			{
				$this->response = array(
						'status' => 1,
						'value'	=> $adminDetails['User'],
						'message' => 'success'
				);
			}
			else
			{	
				$this->response = array(
						'status' => 2,
						'message' => 'Admin not found.'
				);
			}
		}
		echo json_encode($this->response);
	}

	/*
	 * Method	: checkPaypalVerification
	 * Author	: Bharat Borana
	 * Created	: 30 Jan, 2015
	 * @Search for user's paypal email verification
	 */
	public function checkPaypalVerification()
	{ 
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		
		if(!isset($data['paypal_id']) || empty($data['paypal_id']))
		{
			$error .= 'Paypal Id is Required. ';
		}

		if(!isset($data['paypal_name']) || empty($data['paypal_name']))
		{
			$error .= 'Paypal First Name is Required. ';
		}

		if(!isset($data['paypal_lname']) || empty($data['paypal_lname']))
		{
			$error .= 'Paypal Last Name is Required. ';
		}

		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User Id is Required. ';
		}
		else
		{	
			$this->loadModel('User');
			$userDetails = $this->User->findById($data['user_id']);
			if(empty($userDetails))
			{
				$error .= 'User does not exists. ';
			}
		}
		

		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{ 
			$detailsAre['emailAddress'] = $data['paypal_id'];
			$detailsAre['firstName'] = $data['paypal_name'];
			$detailsAre['lastName'] = $data['paypal_lname'];
			$detailsAre['matchCriteria'] = "NAME";
			$detailsAre['requestEnvelope.errorLanguage'] = "en_US"; 
			$detailsAre['requestEnvelope.detailLevel'] = "ReturnAll"; 


			$url = 'https://svcs.paypal.com/AdaptiveAccounts/GetVerifiedStatus';
			$getExpData = $this->Paypal->pay_me($detailsAre,$url);

			if(isset($getExpData->responseEnvelope->ack) && $getExpData->responseEnvelope->ack=='Success')
			{
				$this->request->data['User']['id'] = $data['user_id'];
				$this->request->data['User']['is_paypal_verified'] = 1;
				$this->request->data['User']['paypal_id'] = $data['paypal_id'];
				$this->request->data['User']['paypal_name'] = $data['paypal_name'];
				$this->request->data['User']['paypal_lname'] = $data['paypal_lname'];
				
				$this->loadModel('User');
				$this->User->set($this->request->data);
				$this->User->save();
				$this->response = array(
						'status' => 1,
						'message' => 'success'
				);
			}
			else if(isset($getExpData->responseEnvelope->ack) && $getExpData->responseEnvelope->ack=='Failure')
			{
				$this->request->data['User']['id'] = $data['user_id'];
				$this->request->data['User']['is_paypal_verified'] = 0;
				$this->request->data['User']['paypal_id'] = $data['paypal_id'];
				$this->request->data['User']['paypal_name'] = $data['paypal_name'];
				$this->request->data['User']['paypal_lname'] = $data['paypal_lname'];
				
				$this->loadModel('User');
				$this->User->set($this->request->data);
				$this->User->save();
				$this->response = array(
						'status' => 2,
						'message' => $getExpData->error[0]->message
				);
			}
		}
		echo json_encode($this->response);
	}  

	 /*
	 * Method	: paypalValidation
	 * Author	: Bharat Borana
	 * Created	: 2 Fab, 2015
	 * @Paypal data validation
	 */
	public function paypalValidation()
	{ 
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';
		
		if(!isset($data['user_id']) || empty($data['user_id']))
        {
            $error .= 'User id is Required. ';
        }
        if(!isset($data['amount']) || empty($data['amount']))
        {
            $error .= 'Order amount is required. ';
        }            
        if(!isset($data['phone']) || empty($data['phone']))
        {
            $error .= 'Phone is required. ';
        }
        if(!isset($data['address']) || empty($data['address']))
        {
            $error .= 'Address is required. ';
        }
		if(!isset($data['address_lat']) || empty($data['address_lat']))
		{
		    $error .= 'Address Latitude is required. ';
		}
		if(!isset($data['address_lng']) || empty($data['address_lng']))
		{
		    $error .= 'Address Longitude is required. ';
		}
		if(!isset($data['delivery_location']) || empty($data['delivery_location']))
        {
            $error .= 'Delivery location is required. ';
        }
        if(!isset($data['kitchen_id']) || empty($data['kitchen_id']))
        {
            $error .= 'Kitchen id is required. ';
        }
        if(!isset($data['country_code']) || empty($data['country_code']))
        {
            $error .= 'Country code is required. ';
        }
        /*if(!isset($data['dine_type']) || $data['dine_type']=='')
        {
            $error .= 'Please select type of dining.';
        }*/
        if(!isset($data['timestamp']) || $data['timestamp']=='')
        {
            $error .= 'Please Send timestamp value for this order.';
        }
        if(!isset($data['dish']) || count($data['dish'])<=0 )
        {
            $error .= 'Dish array is required. ';
        }
       if(!isset($data['delivery_date']) || empty($data['delivery_date']))
		{
			$error .= 'Please select delevery date.';
		}
		if(!isset($data['delivery_time']) || empty($data['delivery_time']))
		{
			$error .= 'Please select delevery time.';
		}
    	if(!isset($data['paypal_id']) || empty($data['paypal_id']))
		{
			$error .= 'Paypal id required.';
		}
		
		if(!isset($data['service_fee']) || empty($data['service_fee']))
		{
			$error .= 'Service fee required.';
		}

    
		

		if(!empty($error))
		{
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{ 
			$this->response = array(
				'status' => 1,
				'message' => 'success'
			);
		}
		echo json_encode($this->response);
	}

	/**
	 * Method	: Cmspage details page
	 * Author	: Bharat Borana
	 * Created	: 27 Feb, 2015
	 */
	public function cms_detail() { 
		$this->loadModel('Cmspage');
		//header('Content-Type: application/json');
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';

		if(!isset($data['page_id']) || empty($data['page_id']))
        {
            $error .= 'Page id is Required. ';
        }
        else
        {
        	$this->Cmspage->id = $data['page_id'];
        	if (!$this->Cmspage->exists())
        	{
				$error .= 'Cmspage not found.';
			}
		
        }

		if(!empty($error)) {
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$options = array('conditions' => array('Cmspage.' . $this->Cmspage->primaryKey => $data['page_id']));
			$cmspageDetails = $this->Cmspage->find('first', $options);
			$this->response = array(
						'status' => 1,
						'value'	=> $cmspageDetails,
						'message' => 'success'
				);
		}
		echo json_encode($this->response);
	}

	public function getPaypalVerification()
	{
		
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';

		if(!isset($data['paypal_id']) || empty($data['paypal_id']))
        {
            $error .= 'Paypal id is Required. ';
        }
        else if(!isset($data['paypal_name']) || empty($data['paypal_name']))
        {
            $error .= 'First Name is Required. ';
        }
        else if(!isset($data['paypal_lname']) || empty($data['paypal_lname']))
        {
            $error .= 'Last Name is Required. ';
        }
      
		if(!empty($error)) {
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
				$detailsAre['emailAddress'] = $data['paypal_id'];
				$detailsAre['firstName'] = $data['paypal_name'];
				$detailsAre['lastName'] = $data['paypal_lname'];
				$detailsAre['matchCriteria'] = "NAME";
				$detailsAre['requestEnvelope.errorLanguage'] = "en_US"; 
				$detailsAre['requestEnvelope.detailLevel'] = "ReturnAll"; 

				$url = 'https://svcs.paypal.com/AdaptiveAccounts/GetVerifiedStatus';
				$getExpData = $this->Paypal->pay_me($detailsAre,$url);
				if(isset($getExpData->responseEnvelope->ack) && $getExpData->responseEnvelope->ack=='Success')
				{
					$this->response = array(
						'status' => 1,
						'message' => 'Your paypal account is successfully verified.'
					);
				}
				else
				{
					$this->response = array(
						'status' => 2,
						'message' => 'Your paypal id has not verified, Please try again.'
					);
				}
		}	
		echo json_encode($this->response);	
	}

		/**
	* function Name : logout	 
	* author : Bharat Borana
	* 24 Mar 2015
	* Description : User logout
	*/
	public function logout()
	{
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';

		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}

		if(!empty($error)) {
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
			$conditions = array('User.id'=>$data['user_id']);
			$UserInfo = $this->User->find('first',array('conditions'=>array($conditions)));
			if(!empty($UserInfo))
			{
				if(isset($data['device_id']) && !empty($data['device_id']) && isset($data['device_type']) && !empty($data['device_type']))
				{
					$this->loadModel('UserApp');
					$this->UserApp->deleteAll(array('UserApp.user_id' => $data['user_id'],'UserApp.device_id' => $data['device_id']), false);
				}

				$this->response = array(
						'status' => 1,
						'message' => 'Logged-Out Succesfully.'
				);
			}
			else
			{
				$this->response = array(
					'status' => 2,
					'message' => 'User not found.'
				);
			}
		}	
		echo json_encode($this->response);	
	}

	 /*
		 * Purpose: App CMS City Data
		 * @Created: Bharat Borana
		 * @Date: 25 Mar 15
		 * @Parameters: 
		 * @Response: App CMS City data in json format
		 */
        
        function cms_cities()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            $error = '';		
            if(!isset($data['app_id']) || empty($data['app_id']))
            {
                $error .= 'Application id is Required. ';
            }
            if(isset($data['app_id']) && $data['app_id']!='abc-app'){
			    $error .= 'Incorrect application id, please try again later. ';
            }           
            
            if(!empty($error))
            {
                $this->response = array(
                        'status' => 2,
                        'message' => $error
                );
            }
            else
            {               
               $cmsDetails = array();
               
               $this->loadModel('CmsCity');
			   $cmsDetails['CmsCity'] = $this->CmsCity->getfeaturedCities();
               
               $this->response = array(
                     'status' => 1,
                     'message' => 'Data found',
                    'value' => $cmsDetails
                 );
            }
            echo json_encode($this->response);        
        }

    /**
	* function Name : logout	 
	* author : Bharat Borana
	* 24 Mar 2015
	* Description : User logout
	*/
	public function checkStripeStatus()
	{
		$this->request->onlyAllow('POST');
		$data = $this->request->data;
		$error = '';

		if(!isset($data['user_id']) || empty($data['user_id']))
		{
			$error .= 'User id is Required. ';
		}

		if(!empty($error)) {
			$this->response = array(
				'status' => 2,
				'message' => $error
			);
		}
		else
		{
			$this->loadModel('User');
			$conditions = array('User.id'=>$data['user_id']);
			$UserInfo = $this->User->find('first',array('fields'=>array('User.stripe_user_id'), 'conditions'=>array($conditions)));
			if(!empty($UserInfo))
			{
				if(isset($UserInfo['User']['stripe_user_id']) && !empty($UserInfo['User']['stripe_user_id']))
				{
					$stripeData = $this->Stripe->retrieveAccount($UserInfo['User']['stripe_user_id']);

					$this->response = array(
						'status' => 1,
						'stripeStatus' => 0,
						'stripeEmail' => '',
						'message' => 'Success'
					);
				}
				else
				{
					$this->response = array(
						'status' => 1,
						'stripeStatus' => 0,
						'stripeEmail' => '',
						'message' => 'Success'
					);
				}
			}
			else
			{
				$this->response = array(
					'status' => 2,
					'message' => 'User not found.'
				);
			}
		}	
		echo json_encode($this->response);	
	}

	/*
     * Purpose: Order history
     * @Created: Bharat Borana
     * @Date: 21 May 15
     * @Parameters: user_id (int), order_id (int)
     * @Response: orders data
     */
    
    function order_detail()
    {
    	//header('Content-Type: application/json');  
        $this->request->onlyAllow('POST');
        $data = $this->request->data;
        $error = '';		
        if(!isset($data['user_id']) || empty($data['user_id']))
        {
            $error .= 'User id is Required. ';
        }
        if(!isset($data['order_id']) || empty($data['order_id']))
        {
		    $error .= 'Order id is required. ';
        }           
        
        if(!empty($error))
        {
            $this->response = array(
                    'status' => 2,
                    'message' => $error
            );
        }
        else
        {               
           $userId = $data['user_id'];
           $order_id = $data['order_id'];

           $this->loadModel('Order');
	       $this->loadModel('Kitchen');
	       
	       $ordersArray = $this->Kitchen->getallorderIds($userId);
		   
		   $this->Order->Behaviors->attach('Containable');
	       $orders_recieved = array();

	       
	       if(!empty($ordersArray)){
		   	$conditions['OR'][] = array('Order.user_id'=>$userId);
	       	$conditions['OR'][] = array('Order.id IN('.$ordersArray.')');
	        $conditions[] = array('Order.id'=>$order_id);
	       }
	       else
	       {
	       	$conditions[] = array('Order.user_id'=>$userId);
	       	$conditions[] = array('Order.id'=>$order_id);
	       }

		   $order_details = $this->Order->find('first',array('conditions'=>$conditions,
															   'contain'=>array('User'=>array('fields'=>array('User.name','User.id')),
																				'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name','Kitchen.sales_tax')))),
															   'recursive'=>2));
	       $this->Order->Behaviors->detach('Containable');
	    
	       
           $this->response = array(
                 'status' => 1,
                 'message' => 'Data found',
                'value' => $order_details
             );
        }
        echo json_encode($this->response);    
    }


    /*
     * Purpose: Refer friend
     * @Created: Bharat Borana
     * @Date: 25 May 15
     * @Parameters: user_id (int), email_id (varchar), phone_nu(varchar)
     * @Response: orders data
     */
    
    function send_referral()
    {
    	//header('Content-Type: application/json');  
        $this->request->onlyAllow('POST');
        $data = $this->request->data;
        $error = '';		
        if(!isset($data['user_id']) || empty($data['user_id']))
        {
            $error .= 'User id is Required. ';
        }
        if((!isset($data['email']) || empty($data['email'])) && (!isset($data['phone']) || empty($data['phone'])))
        {
		    $error .= 'Email Address or Phone number is required. ';
        }           
        
        if(!empty($error))
        {
            $this->response = array(
                    'status' => 2,
                    'message' => $error
            );
        }
        else
        {

          		$this->loadModel('User');
				$userId = $data['user_id'];
				$userDetails = $this->User->findById($userId);
				if(isset($userDetails) && !empty($userDetails))
					$userName = $userDetails['User']['name'];
				else
					$userName = "Lacart User";
				
				$name = ucfirst($userName);
							
				if(isset($data['phone']) && !empty($data['phone']))
				{
					$phoneArray = explode(",", $data['phone']);
					
					//pr($phoneArray); exit;
					foreach ($phoneArray as $phKey => $phValue)
					{ 
						try
						{
							$text = "Hello, $name has invited you to try Lacart. Download our apps now or visit ";
							$text .= "See: http://lacart.com";

							App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
							$client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);
							$message = $client->account->messages->sendMessage(
									TWILIO_FROM_NUMBER, // From a valid Twilio number
									'+1'.$phValue, // Text this number
									$text
							);

							$this->response = array(
			                    'status' => 1,
			                    'message' => 'Invitaion message has successfully sent.'
			            	);
						}
						catch (Exception $e)
						{
							$this->response = array(
				                    'status' => 2,
				                    'message' => $e->getMessage()
				            );
						}
					}
				}

				if(isset($data['email']) && !empty($data['email']))
				{
					$emailArray = explode(",", $data['email']);
					$this->loadModel('EmailTemplate');
					
					foreach ($emailArray as $emKey => $emValue)
					{

						$emValue = trim($emValue);

						$Email = new CakeEmail('smtp');

						$arr = array();
						$arr['{{sender_name}}'] = $name;
						$arr['{{name}}'] = $emValue;
						
						$email_content = $this->EmailTemplate->findBySlug('invite-email');


						$subject = $email_content['EmailTemplate']['subject'];
						$subject = str_replace(array_keys($arr), array_values($arr), $subject);

						$content = $email_content['EmailTemplate']['content'];
						$content = str_replace(array_keys($arr), array_values($arr), $content);

						$reply_to_email = REPLYTO_EMAIL;

						$Email->from(FROM_EMAIL);
						$Email->to($emValue);
						$Email->subject($subject);
						$Email->replyTo($reply_to_email);
						$Email->emailFormat('html');
						$Email->send($content);

					}
					$this->response = array(
	                    'status' => 1,
	                    'message' => 'Invitaion email has successfully sent.'
	            	);
				}
	    }
        echo json_encode($this->response);    
    }


	 /*
		 * Purpose: Update device id status
		 * @Created: Bharat Borana
		 * @Date: 26 May 15
		 * @Parameters: 
		 * @Response: Success if updated
		 */
        
        function updateDeviceToken()
        {
            //header('Content-Type: application/json');  
            $this->request->onlyAllow('POST');
            $data = $this->request->data;
            
			if(isset($data['user_id']) && !empty($data['user_id']))
			{
				if(!isset($data['device_type']) || empty($data['device_type']))
				{
					$error .= 'Device type is Required. ';
				}

				if(!isset($data['device_id']) || empty($data['device_id']))
				{
					$error .= 'Device id is Required. ';
				}

				$this->loadModel('UserApp');
				if(!$this->UserApp->find('count',array('conditions'=>array('UserApp.user_id'=>$data['user_id'],'UserApp.device_type'=>$data['device_type']))))
				{
					$app_status = 'active';
					if(empty($data['device_id']))
						$app_status = 'killed';

					$this->UserApp->updateAll(array('UserApp.app_status'=>$app_status),array('UserApp.user_id' => $data['user_id'], 'UserApp.device_type' => $data['device_type']));
				}

			}     
            
            echo json_encode($this->response);        
        }

}   
?>