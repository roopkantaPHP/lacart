<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class UsersController extends AppController {


	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(array('index','signup','add','reset_password','reset_me','logout','social_login','social_endpoint','testPaypal','getCityOptions','about_me','_successfulHybridauth','_doSocialLogin','getStripeDetails','checkShell','stripe_success','stripe_error','forgot_password','verify_user'));
	}
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Hybridauth','SuiteTest','Paypal', 'Stripe' => array(
																						      'logFile' => 'stripe',
																						      'logType' => 'error'
																						    )
    );
	var $helpers = array('Image');

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index($isLogin=null)
	{
		$this->layout = 'home';
		$cmsDetails = array();

		//$this->loadModel('Kitchen');
		//$cmsDetails['Kitchen'] = $this->Kitchen->getfeaturedKitchens();

		//$this->loadModel('Testimonial');
		//$cmsDetails['Testimonial'] = $this->Testimonial->getfeaturedTestimonials();

		//$this->loadModel('Video');
		//$cmsDetails['Video'] = $this->Video->getfeaturedVideos();

		if(!empty($isLogin) && $isLogin=='pleaselogin'){
			$this->Session->setFlash(__('You need to log in to use this.'),'error');
		}
		$this->set(compact(array('isLogin','cmsDetails')));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		$suppliers = $this->User->Supplier->find('list');
		$buyers = $this->User->Buyer->find('list');
		$groups = $this->User->Group->find('list');
		$this->set(compact('suppliers', 'buyers', 'groups'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		$suppliers = $this->User->Supplier->find('list');
		$buyers = $this->User->Buyer->find('list');
		$groups = $this->User->Group->find('list');
		$this->set(compact('suppliers', 'buyers', 'groups'));
	}

	/*
	* Created By: Agam Banga
	* Purpose : The function will be used for login purposes.
	* Inputs :  $this->request->data – this data will contain username and password which the user submits after login form
	* Returns : Will login the user if credentials are correct else he will be displayed invalid creds session message
	*/
	public function login()
	{
		$this->autoRender = false;
		if ($this->request->is('post'))
		{
			$user = $this->User->find('first', array(
									'conditions' =>  array(
											'OR' => array(
													'User.username' => $this->request->data['User']['email'],
													'User.email' => $this->request->data['User']['email']
											),
											'User.password' => AuthComponent::password($this->request->data['User']['password'])
									)
								)
							);

			$passwordHasher = new SimplePasswordHasher();

			if(empty($user))
			{
				$response['error'] = 'Invalid username or password, try again.';
			}
			else if($user && isset($user['User']['email_verified']) && $user['User']['email_verified']==1 && isset($user['User']['is_active']) && $user['User']['is_active']==1)
			{
				if($user && $this->Auth->login($user['User']))
				{
					if($user['User']['is_new_user']==1)
					{
						$this->User->updateAll(array('User.is_new_user'=>0),array('User.id'=>$user['User']['id']));
					}
					$redir = $this->Session->read('redir');

		            if(!empty($redir))
		            {
		            	$this->Session->write('redir','');

	                	$redirect = Router::url($redir, true);
		            }
		            else
		            {
		            	if(isset($user['User']['name']) && !empty($user['User']['name']))
		            	{
		            		$redirect = Router::url(array('controller' => 'users','action'=>'index'), true);
		            	}
		            	else
		            	{
		            		$redirect = Router::url(array('controller' => 'users','action'=>'edit_profile'), true);
		            	}
		            }
		            $response['redirect'] = $redirect;
		            $response['success'] = 'Successfully Login.Redirecting...';
				}
				else
				{
					$response['error'] = 'Invalid username or password, try again.';
				}
			}
			else if(isset($user['User']['email_verified']) && $user['User']['email_verified']==0)
			{
				$response['error'] = 'Please check your email account and verify your email address first.';
			}
			else
			{
				$response['error'] = 'Your account is status is deactivated. Please contact administrator.';
			}

			if($this->request->is('ajax'))
			{
				echo json_encode($response);
			}
			else
			{
				$this->redirect($redirect);
			}
		}
		if (!($this->request->is('ajax')) && $this->Auth->login())
		{
			return $this->redirect($this->referer());
		}else if(!($this->request->is('ajax'))){
			return $this->redirect(array('action'=>'index','pleaselogin'));
		}
	}


	/*
	* Created By: Bharat Borana
	* Purpose : The function will be used for verify email addresses.
	* Inputs :  verifycode
	* Returns : Will verify the user's email address.
	*/
	public function verify_user()
	{
	
		echo BASE_URL;die;
		$request_data = $this->request->query;
		if(isset($request_data['v_vode']) && !empty($request_data['v_vode']))
		{
			$user = $this->User->find('first', array(
									'conditions' =>  array(
											'User.email_verification_code' => $request_data['v_vode']
									)
								)
							);

			if(empty($user))
			{
				$this->Session->setFlash(__('Invalid verification code.'),'error');
			}
			else if($user && isset($user['User']['email_verified']) && $user['User']['email_verified']==1)
			{
				$this->Session->setFlash(__('Your email address has already verified.'),'error');
			}
			else if($user && isset($user['User']['email_verified']) && $user['User']['email_verified']==0)
			{
				$this->User->updateAll(array('User.email_verified'=>1),array('User.id'=>$user['User']['id'],'User.email_verification_code'=>$user['User']['email_verification_code']));
				$this->Session->setFlash(__('Your email address has successfully verified.'),'success');
			}
		}
		$this->set('isLogin','pleaselogin');
		$this->render('index');
		//return $this->redirect(array('action'=>'index','pleaselogin'));
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////ADMIN FUNCTIONS START///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/*
 * Created By: Agam Banga
 * Purpose : The function will be used for admin login
 * Inputs :  $this->request->data – this data will contain user associated data which needs to be saved
 * Returns : Will login the user if user is saved
 */
	function admin_login()
	{
		if ($this->request->is('post'))
		{
			$passwordHasher = new SimplePasswordHasher();

			if ($this->Auth->login())
			{

				if($this->Auth->User('is_active') == 1)
				{
					if($this->Auth->User('group_id') != NORMAL_USER)
					{
						$this->redirect(array('controller'=>'users', 'action'=>'dashboard', 'admin' => true));
					} else
					{
						$this->Auth->logout();
						$this->Session->setFlash("Not allowed to access admin section ", 'error');
						$this->redirect(array('controller'=>'users', 'action'=>'login', 'admin' => true));
					}
				} else
				{
					$this->Auth->logout();
					$this->Session->setFlash("Your Account is not active. Please activate it from your email ", 'error');
					$this->redirect(array('controller'=>'users', 'action'=>'login', 'admin' => true));
				}
			}
			else
			{
				$this->Session->setFlash("Email or password is not correct. Please try again", 'error');
				$this->redirect(array('controller'=>'users', 'action'=>'login', 'admin' => true));
			}
		} else if ($this->Auth->User('id') && ($this->Auth->User('group_id') != NORMAL_USER))
		{
			$this->redirect(array('controller'=>'users', 'action'=>'dashboard', 'admin' => true));
		}
	}

/*
 * Created By: Agam Banga
 * Purpose : The function will be used for admin dashboard
 * Inputs :  $this->request->data – this data will contain user associated data which needs to be saved
 * Returns : Will login the user if user is saved
 */
	public function admin_dashboard()
	{
	}

/*
 * Created By: Agam Banga
 * Purpose : The function will be used for admin dashboard
 * Inputs :  $this->request->data – this data will contain user associated data which needs to be saved
 * Returns : Will login the user if user is saved
 */
	public function admin_manage_users()
	{
		$this->_admin_manage_users(NORMAL_USER);
		$group_name = 'Users';
		$this->set(compact('group_name'));
		$this->render('admin_manage_users');
	}

	public function admin_manage_admins()
	{
		$group_name = 'Admin';
		$this->set(compact('group_name'));
		$this->_admin_manage_users(SUPER_ADMIN);
		$this->render('admin_manage_users');
	}

	function _admin_manage_users($group_id)
	{
		$email = "";
		$reg_dt_from = "";
		$reg_dt_to = "";
		$sort = "User.created";
		$order = "desc";
		$query = $this->request->query;
		$qstring = "?action=import";
		$login='';
		if ($this->request->is("post"))
		{
			$request = $this->request->data;
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if (!empty($ids))
			{
				foreach ($ids as $id)
				{
					if ($action == "Activate")
					{
						$this->User->id = $id;
						$this->User->saveField('is_active',1);
					}
					else if ($action == "Deactivate")
					{
						$this->User->id = $id;
						$this->User->saveField('is_active',0);
					}
					else if ($action == "IPBlock")
					{
						$this->User->id = $id;
						$this->User->saveField('is_blocked',1);
					}
					else if ($action == "Unblock")
					{
						$this->User->id = $id;
						$this->User->saveField('is_blocked',0);
					}
					else if ($action == "Delete")
					{
						$this->User->id = $id;
						$this->User->delete();
					}

				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}

		if (count($query)>0 && is_array($query))
		{
			$login = isset($query['login']) ? strtolower($query['login']) : '';
			$email = isset($query['email']) ? $query['email'] : '';
			$reg_dt_from = isset($query['reg_dt_from']) ? $query['reg_dt_from'] : '';
			$reg_dt_to = isset($query['reg_dt_to']) ? $query['reg_dt_to'] : '';
			$sort = isset($query['sort']) ? $query['sort'] : "User.registrationdate";
			$order = isset($query['order']) ? $query['order'] : 'desc';
		}
		$conditions = array();
		if ($login != "")
		{
			$conditions['OR']['User.name LIKE'] = '%'.$login.'%';
			$conditions['OR']['User.username LIKE'] = '%'.$login.'%';
			$qstring .= "&login=".$login;
		}
		if ($email != "")
		{
			$conditions['User.email LIKE'] = '%'.$email.'%';
			$qstring .= "&email=".$email;
		}
		if ($reg_dt_from != "")
		{
			$conditions[] = array('User.created >='=>$reg_dt_from);
			$qstring .= "&reg_dt_from=".$reg_dt_from;
		}
		if ($reg_dt_to != "")
		{
			$conditions[] = array('User.created <='=>$reg_dt_to);
			$qstring .= "&reg_dt_to=".$reg_dt_to;
		}
		$conditions = array_merge($conditions, array('User.group_id' => $group_id));

		$this->paginate = array(
			'limit' => 25,
			'conditions' => $conditions,
			'order' => array($sort => strtoupper($order)),
		);
		$results = $this->paginate('User');
		$totalRecords = count($results);
		$this->set(Compact('totalRecords','pagingStr','results','login','email','reg_dt_from','reg_dt_to','order','sort'));
	}

	public function admin_users_operations($type, $id, $value)
	{
		if ($this->User->exists($id))
		{
			$field = "";
			if ($type == "status")
			{
				$field = "is_active";
			}
			else if ($type == "block")
			{
				$field = "is_blocked";
			}
			if ($field != "")
			{
				$this->User->id = $id;
				$this->User->saveField($field, $value);
				$this->Session->setFlash("User information has been updated successfully", 'success');
			}
			else
			{
				$this->Session->setFlash("Operation doesn't allow", 'error');
			}
		}
		else
		{
			$this->Session->setFlash("The provided user id is not exist.", 'error');
		}
		$this->redirect($this->referer());
	}

	public function admin_change_password()
	{
		if ($this->request->is('post'))
		{
			$oldpassword = AuthComponent::password($this->request->data['User']['oldpass']);
			$user = $this->User->find('first', array(
			'conditions' => array('User.id' => $this->Auth->user('id'),'User.password' => $oldpassword)
			));

			if(empty($user))
			{
			   $this->Session->setFlash("Old password is not correct.",'error');
			   $this->redirect(array('controller'=>'users','action'=>'admin_change_password'));
			}
			else if($this->request->data['User']['password']!= $this->request->data['User']['conpass'])
			{
				 $this->Session->setFlash("New password and confirm password doen't match.",'error');
				 $this->redirect(array('controller'=>'users','action'=>'admin_change_password'));
			}
			else
			{
				$pass = $this->request->data['User']['conpass'];
				$this->User->id = $this->Auth->user('id');
				if ($this->User->saveField('password', AuthComponent::password($pass), array('callbacks' => false)))
				{
					$this->Session->setFlash("Password has been changed successfully",'success');
					$this->redirect(array('controller'=>'users','action'=>'admin_change_password'));
				}
			 }
		}
	}

	public function activate_account()
	{
		$this->layout = false;
		$this->autoRender = false;
		if (isset($this->params->query['ident']) && isset($this->params->query['activate']))
		{
			if (!empty($this->params->query['ident']) && !empty($this->params->query['activate']))
			{
				$userId = $this->params->query['ident'];
				$activateKey = $this->params->query['activate'];
				$user = $this->User->find(
					'first', array(
						'conditions' => array(
							'User.id' => $userId,
						)
					)
				);
				if (!empty($user))
				{
					$thekey = $this->User->getActivationKey($user['User']['email']);
					if ($thekey == $activateKey)
					{
						$this->User->id = $user['User']['id'];
						$this->User->saveField('is_active', true);
						$this->Session->setFlash(__('Your account has been activated successfully'),'success');
						$this->__doAuthLogin($user, true);
						$this->redirect($this->Auth->redirect());
					} else
					{
						$this->Session->setFlash(__('Link Expired. Please send password reset link again'),'error');
						$this->redirect($this->Auth->redirect());
					}
				} else {
					$this->Session->setFlash(__('Something went wrong, please click again on the link in email'),'error');
					$this->redirect($this->Auth->redirect());
				}
			} else
			{
				$this->Session->setFlash(__('Something went wrong, please click again on the link in email'),'error');
				$this->redirect($this->Auth->redirect());
			}
		} else
		{
			$this->Session->setFlash(__('Something went wrong, please contact admin or open link again from in email'),'error');
			$this->redirect($this->Auth->redirect());
		}
	}

	/*
	 * Created By: Agam Banga
	 * Purpose : The function will be used for user profile editing
	 * Inputs :  $this->request->data – this data will contain user associated data which needs to be saved
	 * Returns : Will save the user
	 */
	function admin_add_user()
	{
		$this->loadModel('State');
		$states = $this->State->getAllStateName(223);

		if ($this->request->is('post') || $this->request->is('put'))
		{
			$userData =  $this->request->data;
			unset($this->request->data['User']['image']);

			if ($this->User->save($this->request->data))
			{
				if (isset($userData['User']['image']['tmp_name']) && !empty($userData['User']['image']['tmp_name']))
				{
					$ext = substr(strtolower(strrchr($userData['User']['image']['name'], '.')), 1);
					$FileName = mt_rand().'-'.time().'.'.$ext;

					move_uploaded_file($userData['User']['image']['tmp_name'],PROFILE_IMAGE_PATH.$FileName);

					// store the filename in the array to be saved to the db
					$this->User->set('id',$this->User->id);
					$this->User->saveField('image',$FileName);
				}

				$this->Session->setFlash(__('The user has been saved'));
				if($this->request->data['User']['group_id'] == SUPER_ADMIN)
				{
					$this->redirect(array('action'=>'manage_admins'));
				}
				else
				{
					$this->redirect(array('action'=>'manage_users'));
				}
			} else
			{
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups','states'));
	}
	/*
	 * Created By: Agam Banga
	 * Purpose : The function will be used for user profile editing
	 * Inputs :  $this->request->data – this data will contain user associated data which needs to be saved
	 * Returns : Will save the user
	 */
	function admin_edit_user($id = '')
	{
		$this->theme = "Admin";
		$this->User->id = $id;
		$this->loadModel('State');
		$states = $this->State->getAllStateName(223);

		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$userData =  $this->request->data;
			unset($this->request->data['User']['image']);

			if(isset($this->request->data['User']['password']) && empty($this->request->data['User']['password']))
			{
				unset($this->request->data['User']['password']);
			}
			if ($this->User->save($this->request->data))
			{
				if (isset($userData['User']['image']['tmp_name']) && !empty($userData['User']['image']['tmp_name']))
				{
					$ext = substr(strtolower(strrchr($userData['User']['image']['name'], '.')), 1);
					$FileName = mt_rand().'-'.time().'.'.$ext;

					move_uploaded_file($userData['User']['image']['tmp_name'],PROFILE_IMAGE_PATH.$FileName);

					// store the filename in the array to be saved to the db
					$options = array('fields' => array('image'), 'conditions' => array('User.' . $this->User->primaryKey => $id));
					$userPrevImage = $this->User->find('first', $options);

					$this->User->set('id',$id);
					if($this->User->saveField('image',$FileName)){
						if(!empty($userPrevImage['User']['image']))
						unlink(PROFILE_IMAGE_PATH.$userPrevImage['User']['image']);
					}
				}
				$this->Session->setFlash(__('The user has been saved'));
			} else
			{
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			unset($this->request->data['User']['password']);
		}
		$redirect_array = array(
			SUPER_ADMIN => 'manage_admins',
			NORMAL_USER => 'manage_users',
		);
		$redirect_action = isset($redirect_array[$this->request->data['User']['group_id']]) ? $redirect_array[$this->request->data['User']['group_id']] : 'manage_site_users';
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups', 'redirect_action', 'states'));
	}
	/*
	 * Created By: Agam Banga
	 * Purpose : The function will be used for logout
	 */
	function admin_logout()
	{
		$this->Auth->logout();
		$this->Session->setFlash("You have successfully logged out", 'success');
		$this->redirect(array('controller'=>'users', 'action'=>'login', 'admin' => true));
	}

	public function reset_password($userid=null, $token=null)
	{
		if(!empty($userid) && !empty($token)){
			$this->Session->write('resetme',array('user_id'=>$userid,'token'=>$token));
		}

		$user = $this->User->find('first',array('conditions'=>array('User.id'=>$userid,'User.token'=>$token)));
		if(empty($user))
		{
			//$this->Session->setFlash('Url has been expired');
			//$this->redirect(array('action'=>'login'));
		}
		if($this->request->data)
		{
			if((!empty($this->request->data['User']['password'])) && ($this->request->data['User']['password'] == $this->request->data['User']['confirm_password']))
			{
				$this->User->set('id',$user['User']['id']);
				$this->User->saveField('password',$this->request->data['User']['password']);
				$this->Session->setFlash('Password changed successfully');
			}
			else {
				$this->Session->setFlash('Please check your password!');
			}
			$this->redirect($this->referer());
		}
		$this->set(compact('user'));
		$this->redirect(array('controller'=>'users','action'=>'index','reset_me'));
	}

	public function reset_me()
	{
		$this->layout = 'ajax';
		$userid = $this->Session->read('resetme.user_id');
		$token = $this->Session->read('resetme.token');

		if(!empty($userid) && !empty($token))
		{
			$user = $this->User->find('first',array('conditions'=>array('User.id'=>$userid,'User.token'=>$token)));
			if(empty($user))
			{
				$this->Session->setFlash('Url has been expired','error');
			}
			if($this->request->data)
			{
				if((!empty($this->request->data['User']['password'])) && ($this->request->data['User']['password'] == $this->request->data['User']['confirm_password']))
				{
					$this->User->set('id',$user['User']['id']);
					$this->User->saveField('password',$this->request->data['User']['password']);
					$this->Session->setFlash('Password changed successfully','success');
					$this->Session->write('resetme','');

					$this->set('closeFancy',1);
				}
				else {
					$this->Session->setFlash('Password does not match. Please check your password!','error');
				}
			}
			$this->set(compact('user'));
		}
		else{
			$this->Session->setFlash('Session expired! Please try again.','error');
		}
	}

	/**
	 * Method	: signup
	 * Author	: Praveen Pandey
	 * Created	: 14 Nov, 2014
	 */
	public function signup()
	{
		//$this->set('title_for_layout','Home: Tuckle');
		$this->autoRender = false;
		if($this->request->is('ajax'))
		{	$this->request->data['User']['group_id'] = NORMAL_USER;
			$this->User->set($this->request->data);
			$errors = $this->User->invalidFields();
			if(empty($errors)){

				$emValue = trim($this->request->data['User']['email']);

				$Email = new CakeEmail('smtp');

				$this->loadModel('EmailTemplate');
				$randString = $this->Paypal->generateRandomString(25);
				$linkTag = BASE_URL.'users/verify_user?v_vode='.$randString;

				$arr = array();
				$arr['{{name}}'] = $emValue;
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
					$this->User->clear();
					$this->request->data['User']['email_verification_code'] = $randString;
					$this->User->set($this->request->data);

					$this->User->create();
					if($user = $this->User->save($this->request->data))
					{
						$name = $user['User']['email'];

						try
						{
							$code = mt_rand(100000, 999999);
							$name = ucfirst($name);
							$text = "Dear $name, Enter $code on confirmation code to verify your account on Lacart.";
							App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
							$client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);
							$message = $client->account->messages->sendMessage(
									TWILIO_FROM_NUMBER, // From a valid Twilio number
									'+1'.$user['User']['phone'], // Text this number
									$text
							);
							if($message->sid)
							{
								$this->User->id = $user['User']['id'];
								$this->User->set('verification_code', $code);
								$this->User->save();
							}
						}
						catch (Exception $e)
						{
						}

						/*
						$user = $this->User->find('first', array(
										'conditions' =>  array(
												'OR' => array(
														'User.username' => $this->request->data['User']['email'],
														'User.email' => $this->request->data['User']['email']
												),
												'User.password' => AuthComponent::password($this->request->data['User']['password'])
										)
								)
								);
						if ($user && $this->Auth->login($user['User']))
						{
							$redirect = Router::url(array('controller' => 'users','action'=>'edit_profile'), true);
							$response['redirect'] = $redirect;
							$response['success'] = 'You have successfully registered.';
						}else{
							$redirect = '/';
							$response['redirect'] = $redirect;
							$response['success'] = 'You have successfully registered.';
						} */
						$redirect = Router::url(array('controller' => 'users','action'=>'index'), true);
						$response['redirect'] = $redirect;
						$response['success'] = 'We have sent you a confirmation email to '.$name.', can you please verify it to continue.';
					}
					else
					{
						$response['error'] = 'Error, Please try again.';
					}
				}
				else
				{
					$response['error'] = "Please enter valid email address.";
				}

			}else{
				$response['error'] = '';
				foreach ($errors as $key => $value)
				{
					$response['error'] .= $value[0].'. ';
				}
			}
		}
		echo json_encode($response);
	}

	/**
	 * Method	: User's edit profile page.
	 * Author	: Bharat Borana
	 * Created	: 23 Dec, 2014
	 */
	public function edit_profile() {
		$this->loadModel('State');
		$userId = $this->Auth->user('id');

		$states = $this->State->getAllStateName(223);

		if (!$this->User->exists($userId)) {
			throw new NotFoundException(__('Invalid user'));
		}

		if ($this->request->is(array('post', 'put'))) {
			$userData =  $this->request->data;
			unset($this->request->data['User']['image']);
			$this->request->data['User']['id'] = $userId;

			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				if ($userDetails = $this->User->save()) {

					if (isset($userData['User']['image']['tmp_name']) && !empty($userData['User']['image']['tmp_name']))
					{
						$ext = substr(strtolower(strrchr($userData['User']['image']['name'], '.')), 1);
						$FileName = mt_rand().'-'.time().'.'.$ext;

						move_uploaded_file($userData['User']['image']['tmp_name'],PROFILE_IMAGE_PATH.$FileName);

						// store the filename in the array to be saved to the db
						$options = array('fields' => array('image'), 'conditions' => array('User.' . $this->User->primaryKey => $userId));
						$userPrevImage = $this->User->find('first', $options);

						$this->User->set('id',$userId);
						if($this->User->saveField('image',$FileName)){
							if(!empty($userPrevImage['User']['image']))
							unlink(PROFILE_IMAGE_PATH.$userPrevImage['User']['image']);
						}

					}
					$this->Session->setFlash(__('The user has been saved.'));

					if(isset($this->request->data['User']['add_kitchen']) && $this->request->data['User']['add_kitchen']==1)
						return $this->redirect(array('controller' => 'kitchens','action' => 'edit'));
					else
					{
						$userDetails = $this->User->findById($userDetails['User']['id'],array('is_verified'));
						if($userDetails['User']['is_verified'] == 1)
							return $this->redirect(array('action' => 'index'));
						else
							return $this->redirect(array('action' => 'index','verify_me'));
					}
				} else {
					$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
				}
			} else {
				$errors = $this->User->validationErrors;
				$this->set('errors',$errors);
				// handle errors
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $userId));
			$this->request->data = $this->User->find('first', $options);
		}
		$this->set('states',$states);
	}

	public function logout() {
		$this->autoRender = false;
		$this->Session->destroy();
		$this->Auth->logout();
		$this->redirect('/');
	}

	/**
	 * Method	: Social login action for facebook and google login.
	 * Author	: Bharat Borana
	 * Created	: 15 Dec, 2014
	 */
	public function social_login($provider) {
		if( $this->Hybridauth->connect($provider) ){
			$this->_successfulHybridauth($provider,$this->Hybridauth->user_profile);
        }else{
            // error
			$this->Session->setFlash($this->Hybridauth->error);
			$redirect = Router::url(array('controller' => 'users','action'=>'index'), true);
			$this->redirect($redirect);
        }
	}

	/**
	 * Method	: Return url for social login like facebook and google.
	 * Author	: Bharat Borana
	 * Created	: 15 Dec, 2014
	 */
	public function social_endpoint($provider=null) {
		$this->Hybridauth->processEndpoint();
	}

	/**
	 * Method	: After social authentication check for existing user, if exixts then directly login otherwise add user.
	 * Author	: Bharat Borana
	 * Created	: 16 Dec, 2014
	 */
	private function _successfulHybridauth($provider, $incomingProfile){

		// #1 - check if user already authenticated using this provider before
		$conditions = array();
		if($provider=='Google')
		$conditions = array('User.google_id'=>$incomingProfile['User']['google_id'],'User.group_id' => NORMAL_USER);
		else if($provider=='Facebook')
		$conditions = array('User.facebook_id'=>$incomingProfile['User']['facebook_id'],'User.group_id' => NORMAL_USER);

		$this->loadModel('User');
		$existingProfile = $this->User->find('first', array(
			'conditions' => $conditions
		));

		if ($existingProfile) {
			// #2 - if an existing profile is available, then we set the user as connected and log them in
			$this->_doSocialLogin($existingProfile);
		} else {
			// New profile.
			if ($this->Auth->loggedIn()) {
				// user is already logged-in , attach profile to logged in user.
				// create social profile linked to current user
				$incomingProfile['User']['id'] = $this->Auth->user('id');
				$usersPrevImage = $this->Auth->user('image');
				if(isset($incomingProfile['User']['image']) && !empty($incomingProfile['User']['image']) && (empty($usersPrevImage) || strpos($usersPrevImage,'.com') > 0)){
					$imageData = file_get_contents($incomingProfile['User']['image']);
					$FileName = mt_rand().'-'.time().'.jpg';
					$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
					fputs($file, $imageData);
					fclose($file);
					$incomingProfile['User']['image'] = $FileName;
				}
				$this->User->save($incomingProfile);
				$this->Session->setFlash('Your account is now linked to your social account.');

				$redir = $this->Session->read('redir');
				if (!empty($redir)) {
					$this->Session->delete('redir');
					$redirect = Router::url($redir, true);
				} else {
					$redirect = Router::url(array('controller' => 'users','action'=>'index'), true);
				}
				$this->redirect($redirect);
			}
			else {
				// no-one logged and no profile, must be a registration.
				//$user = $this->User->createFromSocialProfile($incomingProfile);
				$user_data = $this->User->find('first',array(
	 											'fields' => array('id', 'name', 'phone', 'image', 'email','facebook_id', 'google_id','is_complete_wizard', 'created', 'modified'),
												'conditions' => array(
														'User.email' => $incomingProfile['User']['email'],
												)
											)
										);
				if(!empty($user_data))
				{
					$userEdit['id'] = $user_data['User']['id'];

					$usersPrevImage = $user_data['User']['image'];

					$userEdit['email_verified'] = 1;

					if(isset($incomingProfile['User']['image']) && !empty($incomingProfile['User']['image']) && (empty($usersPrevImage) || strpos($usersPrevImage,'.com') > 0)){
						$imageData = file_get_contents($incomingProfile['User']['image']);
						$FileName = mt_rand().'-'.time().'.jpg';
						$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
						fputs($file, $imageData);
						fclose($file);
						$userEdit['image'] = $FileName;
					}
					if(isset($incomingProfile['User']['facebook_id']))
					$userEdit['facebook_id'] = $incomingProfile['User']['facebook_id'];

					if(isset($incomingProfile['User']['google_id']))
					$userEdit['google_id'] = $incomingProfile['User']['google_id'];
					$this->User->save($userEdit);
					$this->_doSocialLogin($incomingProfile);
				}
				else
				{
					$incomingProfile['User']['password'] = AuthComponent::password('$$##||&||##$$');
					$incomingProfile['User']['email_verified'] = 1;
					$this->User->set($incomingProfile);

					if($this->User->save($user_data, array('validate' => false)))
					{
						if(isset($incomingProfile['User']['image']) && !empty($incomingProfile['User']['image'])){
						$imageData = file_get_contents($incomingProfile['User']['image']);
						$FileName = mt_rand().'-'.time().'.jpg';
						$file = fopen(PROFILE_IMAGE_PATH.$FileName, 'w+');
						fputs($file, $imageData);
						fclose($file);
						$incomingProfile['User']['image'] = $FileName;
					}

					$this->User->save($incomingProfile, array('validate' => false));
					$this->_doSocialLogin($incomingProfile,1);
					// log in with the newly created user
					}
				}
			}
		}
	}

	/**
	 * Method	: After social authentication check for existing user, if exixts then directly login.
	 * Author	: Bharat Borana
	 * Created	: 16 Dec, 2014
	 */
	private function _doSocialLogin($user, $returning = false) {
		// #1 - check if user already authenticated using this provider before
			$conditions = array();
			if(isset($user['User']['google_id']))
				$conditions['OR'][] = array('User.google_id'=>$user['User']['google_id'],'User.group_id' => NORMAL_USER);
			if(isset($user['User']['facebook_id']))
				$conditions['OR'][] = array('User.facebook_id'=>$user['User']['facebook_id'],'User.group_id' => NORMAL_USER);
			if(isset($user['User']['email']))
				$conditions['OR'][] = array('User.email'=>$user['User']['email'],'User.group_id' => NORMAL_USER);
			if(!empty($conditions))
			{
				$this->loadModel('User');
				$existingProfile = $this->User->find('first', array(
					'conditions' => $conditions
				));
				if($existingProfile && isset($existingProfile['User']['is_active']) && $existingProfile['User']['is_active']==1)
				{
					if ($existingProfile && $this->Auth->login($existingProfile['User']))
					{
						if($existingProfile['User']['is_new_user']==1)
						{
							$this->User->updateAll(array('User.is_new_user'=>0),array('User.id'=>$existingProfile['User']['id']));
						}

						$redir = $this->Session->read('redir');
						if (!empty($redir))
						{
							$this->Session->delete('redir');
							$redirect = Router::url($redir, true);
						}
						else
						{
							if($returning)
							{
								$redirect = Router::url(array('controller' => 'users','action'=>'edit_profile'), true);
							}
							else
							{
								$redirect = Router::url(array('controller' => 'users','action'=>'index'), true);
							}
						}
					}
					else
					{
						$this->Session->setFlash(__('Unknown Error could not verify the user.'),'error');
						$redirect = Router::url(array('controller' => 'users','action'=>'index','showlogin'), true);
					}
				}
				else
				{
					$this->Session->setFlash(__('Your account is status is deactivated. Please contact administrator.'),'error');
					$redirect = Router::url(array('controller' => 'users','action'=>'index','showlogin'), true);
				}
			}
			else
			{
				$this->Session->setFlash(__('Unknown Error could not verify the user.'),'error');
				$redirect = Router::url(array('controller' => 'users','action'=>'index','showlogin'), true);
			}
			$this->redirect($redirect);
	}

	/**
	 * Method	: When state drop down is changed then this action is used to get relevant cities
	 * Author	: Bharat Borana
	 * Created	: 24 Dec, 2014
	 */
	function getCityOptions($stateName = null){
		$this->autoRender = false;
		$conditions = array();
		$citiesOptions = "<option value=''>Select City</option>";
		if(!empty($stateName)){
			$conditions = array('State.name'=>$stateName);
		}
		if($this->request->is('ajax')){
			$this->loadModel('State');
			$state = $this->State->find('first',array('conditions'=>$conditions, 'recursive'=>1));
			if(!empty($state['City'])){
				foreach($state['City'] as $cId=>$cName){
					$citiesOptions .= "<option value=".$cName['name'].">".$cName['name']."</option>";
				}
			}
		}
		echo $citiesOptions;
	}

	/**
	 * Method	: About me page
	 * Author	: Bharat Borana
	 * Created	: 05 Jan 2015
	 */
	public function about_me($userId = null) {
			$this->loadModel('User');
			if(empty($userId)){
				$userId = $this->Auth->user('id');
			}
			$userDetails = $this->User->getUserCountData($userId);

			if(empty($userDetails)){
				throw new NotFoundException(__('Invalid User'));
			}
			$this->set('userDetails',$userDetails);
		}

	/**
	 * Method	: User's settings page
	 * Author	: Bharat Borana
	 * Created	: 05 Jan 2015
	 * @List out all settings option for a user.
	 */
	public function setting() {
	}

	/**
	 * Method	: saved payment method page
	 * Author	: Bharat Borana
	 * Created	: 06 Jan 2015
	 */
	public function payment_method() {
		$this->loadModel('PaymentMethod');
		$userId = $this->Auth->user('id');
		$paymentDetails = $this->PaymentMethod->findAllByUserId($userId);
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $userId));
		$userDetails = $this->User->find('first', $options);
		$errors = array();

		// Encrypt some data.
		$encrypted =  $this->Paypal->encrypt($this->Auth->user('id'));

		if ($this->request->is(array('post', 'put')))
		{
			$this->request->data['User']['id'] = $userId;
			if(($this->request->data['User']['paypal_id'] != $userDetails['User']['paypal_id']) || $userDetails['User']['is_paypal_verified']==0)
			{
				$detailsAre['emailAddress'] = $this->request->data['User']['paypal_id'];
				$detailsAre['firstName'] = $this->request->data['User']['paypal_name'];
				$detailsAre['lastName'] = $this->request->data['User']['paypal_lname'];
				$detailsAre['matchCriteria'] = "NAME";
				$detailsAre['requestEnvelope.errorLanguage'] = "en_US";
				$detailsAre['requestEnvelope.detailLevel'] = "ReturnAll";

				$url = 'https://svcs.paypal.com/AdaptiveAccounts/GetVerifiedStatus';
				$getExpData = $this->Paypal->pay_me($detailsAre,$url);
				//pr($getExpData); exit;
				if(isset($getExpData->responseEnvelope->ack) && $getExpData->responseEnvelope->ack=='Success')
				{
					$this->request->data['User']['is_paypal_verified'] = 1;
					$this->Session->setFlash(__('Your paypal account is successfully verified.'),'success');
				}
				else if(isset($getExpData->responseEnvelope->ack) && $getExpData->responseEnvelope->ack=='Failure')
				{
					$errors[] = array(0=>$getExpData->error[0]->message);
					$paypalConfirmation['User']['id'] = $userId;
					$paypalConfirmation['User']['is_paypal_verified'] = 0;
					$this->request->data['User']['is_paypal_verified'] = 0;
					$paypalConfirmation['User']['paypal_id'] = $this->request->data['User']['paypal_id'];
					$paypalConfirmation['User']['paypal_name'] = $this->request->data['User']['paypal_name'];
					$paypalConfirmation['User']['paypal_lname'] = $this->request->data['User']['paypal_lname'];
					$this->User->save($paypalConfirmation);
				}
			}

			$this->User->set($this->request->data);
			$AccountStatus = 0;
			$recieveAccountInfo = 0;
			if ($this->User->validates())
			{
				if(empty($errors)){
					if ($this->User->save()) {
						$this->Session->setFlash(__('The payment method has been saved.'),'success');
					} else {
						$this->Session->setFlash(__('The payment method could not be saved. Please, try again.'),'error');
					}
				}
				else{
					$this->set('errors',$errors);
				}
			} else {
				$errors = $this->User->validationErrors;
				$this->set('errors',$errors);
				// handle errors
			}
		}
		else
		{
			$checkSession = $this->Session->read('stripeArray');
			if(isset($checkSession['status']))
			{
				if($checkSession['status']==1)
					$this->Session->setFlash($checkSession['message'],'success');
				else
					$this->Session->setFlash($checkSession['message'],'error');
			}

			$options = array('conditions' => array('User.' . $this->User->primaryKey => $userId));
			$this->request->data = $userDetails;
		}
		$this->set(compact(array('paymentDetails','encrypted')));
	}

	/**
	 * Method	: Change password page
	 * Author	: Bharat Borana
	 * Created	: 06 Jan 2015
	 */
	public function change_password() {
		$userId = $this->Auth->user('id');
		$userDetails = $this->User->findById($userId);

		if(isset($userDetails) && !empty($userDetails) && AuthComponent::password('$$##||&||##$$') == $userDetails['User']['password'])
		{
			$this->set('socialLogin',1);
			$socialLogin = 1;
		}
		else
		{
			$this->set('socialLogin',0);
			$socialLogin = 0;
		}
		if ($this->request->is(array('post','put')))
		{
			if($socialLogin == 1)
				$this->request->data['User']['old_password'] = '$$##||&||##$$';
			$this->User->set($this->request->data);
			if($this->User->validates()){
				$oldpassword = AuthComponent::password($this->request->data['User']['old_password']);
				$user = $this->User->find('first', array(
					'conditions' => array('User.id' => $this->Auth->user('id'),'User.password' => $oldpassword)
				));
				$errors = array();
				if(empty($user))
				{
				   $errors[] = array(0 => "Old password is not correct.");
				}
				else if($this->request->data['User']['password']!= $this->request->data['User']['confirm_password'])
				{
				   $errors[] = array(0 => "New password and confirm password doen't match.");
				}
				else
				{
					$pass = $this->request->data['User']['confirm_password'];
					$this->User->id = $this->Auth->user('id');
					if ($this->User->saveField('password', AuthComponent::password($pass), array('callbacks' => false)))
					{
						$this->Session->setFlash("Password has been changed successfully",'success');
					}
				 }
				 $this->set('errors',$errors);
			}
			else
			{
				$errors = $this->User->validationErrors;
				$this->set('errors',$errors);
			}
		}


	}

	/**
	 * Method	: Saved Card delete function
	 * Author	: Bharat Borana
	 * Created	: 07 Jan 2015
	 */
	public function deletecard(){
		$this->autoRender = false;
		if($this->request->is('ajax')){
			$result = 0;
			$userId = $this->Auth->user('id');
			if(isset($this->request->data['id']) && !empty($this->request->data['id']) && !empty($userId)){
				$this->loadModel('PaymentMethod');
					if($this->PaymentMethod->deleteAll(array('PaymentMethod.user_id' => $userId,'PaymentMethod.id' => $this->request->data['id']), false)){
						$result = 1;
					}
			}
		}
		echo $result;
		exit;
	}

	/*
     * Purpose: User's Dashboad Data
     * @Created: Bharat Borana
     * @Date: 14 Jan 2015
     * @Response: Dashboard data
     */

    function dashboard()
    {
	    $userId = $this->Auth->user('id');
	    if (!$this->User->exists($userId)) {
			throw new NotFoundException(__('Invalid user'));
		}
	    $data['user_id'] = $userId;
        $userDetails = $this->User->getAllActivities($data,0);

        if(isset($userDetails['User']['address']) && !empty($userDetails['User']['address']))
        {
    	    $addressLat = $this->SuiteTest->_getAddressFormGeoode($userDetails['User']['address']);
        	if(isset($addressLat['lat']) && isset($addressLat['lng']))
        	{
        		$userDetails['User']['lat'] = $addressLat['lat'];
 		   		$userDetails['User']['lng'] = $addressLat['lng'];
 			}
        }
    	else if($userDetails['Kitchen']['address'])
    	{
            $addressLat = $this->SuiteTest->_getAddressFormGeoode($userDetails['Kitchen']['address']);
    		if(isset($addressLat['lat']) && isset($addressLat['lng']))
        	{
        		$userDetails['User']['lat'] = $addressLat['lat'];
    			$userDetails['User']['lng'] = $addressLat['lng'];
    		}
    	}


        $this->set('userDetails',$userDetails);
    }

    /*
     * Purpose: User's wishlist Data
     * @Created: Bharat Borana
     * @Date: 14 Jan 2015
     * @Response: Wishlist data
     */
    function wishlist()
    {
        $this->loadModel('Wishlist');
        $userId = $this->Auth->user('id');
        $wishlists = $this->Wishlist->find('all',array(
                                            'conditions' => array('Wishlist.user_id'=>$userId),
                                            'contain' => array('Dish'=>array('UploadImage', 'Kitchen' => array('name'))),
                                            )
                            );
        $this->set('wishlists',$wishlists);
    }

    /**
	 * Method	: add_wishlist
	 * Author	: Bharat Borana
	 * Created	: 14 Jan, 2015
	 * Porpose	: Add dish in wishlist
	 */
 	public function add_wishlist()
	{
		$this->autoRender = false;
		$result = 0;
		$userId = $this->Auth->user('id');
		if($this->request->is('ajax'))
		{
			$this->request->data['user_id'] = $userId;
			if(!empty($this->request->data['kitchen_id']) && !empty($this->request->data['dish_id']) && !empty($userId))
			{
				$this->loadModel('Wishlist');
				$wishlist = $this->Wishlist->find('first',array('conditions'=>array('Wishlist.user_id'=>$userId,'Wishlist.dish_id'=>$this->request->data['dish_id'])));
				if(empty($wishlist))
				{
					if($this->Wishlist->save($this->request->data))
					{
						$result = 1;
					}
				}
				else
				{
					$result = 2;
				}
			}
		}
		echo $result;
	}

	/**
	 * Method	: remove_wishlist
	 * Author	: Bharat Borana
	 * Created	: 14 Jan, 2015
	 * Porpose	: Remove dish from wishlist
	 */
 	public function remove_wishlist()
	{
		$this->autoRender = false;
		$result = 0;
		$userId = $this->Auth->user('id');
		if($this->request->is('ajax'))
		{
			$this->request->data['user_id'] = $userId;
			if(!empty($this->request->data['kitchen_id']) && !empty($this->request->data['dish_id']) && !empty($userId))
			{
				$this->loadModel('Wishlist');
				$wishlist = $this->Wishlist->find('first',array('conditions'=>array('Wishlist.user_id'=>$userId,'Wishlist.dish_id'=>$this->request->data['dish_id'])));
				if(!empty($wishlist))
				{
					$this->Wishlist->id = $wishlist['Wishlist']['id'];
					if($this->Wishlist->delete())
					{
						$result = 1;
					}
				}
				else
				{
					$result = 2;
				}
			}
		}
		echo $result;
	}

	/**
	 * Method	: send_verification
	 * Author	: Bharat Borana
	 * Created	: 05 Feb 2015
	 */
	public function send_verification()
	{
		$this->layout = 'ajax';
		$userId = $this->Auth->user('id');
		$userDetails = $this->User->findById($userId);

		$this->loadModel('State');
		$states = $this->State->getAllStateName(223);

		if ($this->request->is(array('post', 'put')))
		{
			if(isset($this->request->data['User']['send_code']) && $this->request->data['User']['send_code']==1)
			{
				$already_user = $this->User->find(
					'first', array(
						'fields' => array('id','phone'),
						'recursive' => -1,
						'conditions' => array('User.phone' => $this->request->data['User']['phone'], 'User.id !=' => $userId)
					)
				);
				if(isset($already_user['User']['phone']) && !empty($already_user['User']['phone']))
				{
					$this->Session->setFlash('Phone number already in use. Please enter another phone number.','error');
				}
				else
				{
					try
					{
						$code = mt_rand(100000, 999999);
						$name = ucfirst($userDetails['User']['name']);
						$text = "Dear $name, Enter $code on confirmation code to verify your account on Lacart.";
						App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
						$client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);
						$message = $client->account->messages->sendMessage(
								TWILIO_FROM_NUMBER, // From a valid Twilio number
								'+1'.$this->request->data['User']['phone'], // Text this number
								$text
						);
						if($message->sid)
						{
							$this->User->id = $userId;
							$this->request->data['User']['verification_code'] = $code;
							$this->User->set($this->request->data['User']);
							$this->User->save();
							$this->Session->setFlash("Verification code has been sent to ".$this->request->data['User']['phone'].", please check.",'success');
						}
						else
						{
							$this->Session->setFlash("Something went wrong, please try again.",'error');
						}

					}
					catch (Exception $e)
					{
						$this->Session->setFlash($e->getMessage(),'error');
					}
				}
			}
			else
			{
				if(isset($this->request->data['User']['code']) && !empty($this->request->data['User']['code']))
				{
					$user = $this->User->find('count', array('conditions'=>array(
																	'User.id' => $userId,
																	'User.phone' => $this->request->data['User']['phone'],
																	'User.verification_code' => $this->request->data['User']['code'],
					)));
					if($user)
					{
						$this->User->id = $userId;
						$this->User->set('is_verified', 1);
						if($user = $this->User->save())
						{
							$this->set('closeFancy',1);
							$userDetails = $this->User->read(null, $this->Auth->User('id'));
							$this->Session->write('Auth', $userDetails);

							$Email = new CakeEmail('smtp');

							$this->loadModel('EmailTemplate');

							$arr = array();
							$arr['{{user}}'] = $userDetails['User']['name'];

							$email_content = $this->EmailTemplate->findBySlug('account-verify');

							$content = $email_content['EmailTemplate']['content'];
							$content = str_replace(array_keys($arr), array_values($arr), $content);

							$subject = $email_content['EmailTemplate']['subject'];
							$reply_to_email = REPLYTO_EMAIL;

							$Email->from(FROM_EMAIL);
							$Email->to($userDetails['User']['email']);
							$Email->subject($subject);
							$Email->replyTo($reply_to_email);
							$Email->emailFormat('html');
							$Email->send($content);

							$this->Session->setFlash("Your account has been successfully verified.",'success');
						}
						else
						{
							$this->Session->setFlash("Something went wrong, please try again.",'error');
						}
					}
					else
					{
						$this->Session->setFlash("Verification code does not found for this number.",'error');
					}
				}
				else
				{
					$this->Session->setFlash("Please enter verification code.",'error');
				}
			}
		}
		else
		{
			if(isset($userDetails) && !empty($userDetails))
			$this->request->data = $userDetails;
		}

		$this->set('states',$states);
	}

	/**
	 * Method	: addcard
	 * Author	: Bharat Borana
	 * Created	: 15 Apr 2015
	 */
	public function addcard()
	{
		$this->layout = 'ajax';
		$userId = $this->Auth->user('id');

		if ($this->request->is(array('post', 'put')))
		{
			$this->loadModel('PaymentMethod');

			$this->request->data['PaymentMethod']['user_id'] = $userId;
			$isCardExists = $this->PaymentMethod->find('first',array('conditions'=>array('PaymentMethod.user_id'=>$userId,'PaymentMethod.card_no'=>$this->request->data['PaymentMethod']['card_no'])));
			if(empty($isCardExists))
			{
				$this->set('closeFancy',1);
				$this->PaymentMethod->create();
				$this->PaymentMethod->save($this->request->data['PaymentMethod']);
				$this->Session->setFlash("Your card detail has been successfully added.",'success');
			}
			else
			{
				$this->Session->setFlash("This card aleady exists. Please try again.",'error');
			}
		}
	}

	/**
	 * Method	: stripe confirmation
	 * Author	: Bharat Borana
	 * Created	: 11 May 2015
	 */
	public function getStripeDetails()
	{
		$this->autoRender = false;
		$is_mobile = 0;
		if(isset($this->request->query['code']) && !empty($this->request->query['code']))
		{
			// Encrypt some data.
			$state = $this->request->query['state'];
			$pos = strpos($state, 'mobile');
			if ($pos === false) {
			    //echo "The string '$findme' was not found in the string '$mystring'";
			} else {
			    $state =  str_replace('mobile','',$this->request->query['state']);
				$is_mobile = 1;
			}

			$user_id =  $this->Paypal->decrypt($state);

			$authCode = $this->request->query['code'];
			$url = 'https://connect.stripe.com/oauth/token';
			$paramArray = array('client_secret'=>STRIPE_SECRET_KEY,'code'=>$authCode,'grant_type'=>'authorization_code');
			$nvpString = '';
			if(!empty($paramArray))
			{
				foreach ($paramArray as $key => $value)
				{
					$nvpString .= "&".$key."=".$value;
				}
			}

			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_POST, count($paramArray));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArray);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			$output=json_decode(curl_exec($ch));

			if(isset($output->stripe_user_id))
			{
				if(!empty($user_id))
				{
					if($this->User->exists($user_id))
					{
						$userSave['User']['id'] = $user_id;
						$userSave['User']['stripe_user_id'] = $output->stripe_user_id;
						$userSave['User']['stripe_publish_id'] = $output->stripe_publishable_key;
						if($this->User->save($userSave))
						{
							$resultArray['status'] = 1;
							$resultArray['message'] = 'Congratulations! your stripe details has successfully added to your account.';
						}
						else
						{
							$resultArray['status'] = 2;
							$resultArray['message'] = 'Request time out. pleasr try again.';
						}
					}
					else
					{
						$resultArray['status'] = 2;
						$resultArray['message'] = 'User does not exists';
					}
				}
				else
				{
					$resultArray['status'] = 1;
					$resultArray['message'] = $output->stripe_user_id;
				}
			}
			else
			{
				$resultArray['status'] = 2;
				$resultArray['message'] = 'Request time out. pleasr try again.';
			}
			curl_close($ch);
		}
		else if(isset($this->request->query['error']) && !empty($this->request->query['error']))
		{
			$resultArray['status'] = 2;
			$resultArray['message'] = $this->request->query['error_description'];
		}
		$this->Session->write('stripeArray',$resultArray);
		if($is_mobile)
		{
			if($resultArray['status'] == 2)
				return $this->redirect(array('controller'=>'users','action'=>'stripe_error',$resultArray['message']));
			else
				return $this->redirect(array('controller'=>'users','action'=>'stripe_success',$resultArray['message']));
		}
		else
		{
			return $this->redirect(array('controller'=>'users','action'=>'payment_method'));
		}
	}

	public function checkShell() {
        App::uses('PaymentShell', 'Console/Command');
        $bckup = new PaymentShell();
        $bckup->main();
        $datefmt = date('Y-m-d H:i:s');
        return $datefmt . ' rotating of logs finished\r\n';
    }

    public function stripe_error($error_desc=null)
    {
    	$this->autoRender = false;
    	exit;
    }

    public function stripe_success()
    {
    	$this->autoRender = false;
    	exit;
    }

    public function admin_label_list()
	{
		$this->loadModel('SiteSetting');
		$this->paginate = array(
			'limit' => 25,
		);
		$results = $this->paginate('SiteSetting');
		$this->set('results',$results);
	}

	/*
	 * Created By: Bharat Borana
	 * Purpose : The function will be used for site setting like label and default redius edit
	 * Returns : Will save the user
	 */
	function admin_edit_label($id = '')
	{
		$this->theme = "Admin";
		$this->loadModel('SiteSetting');
		$this->SiteSetting->id = $id;

		if (!$this->SiteSetting->exists())
		{
			throw new NotFoundException(__('Invalid Setting'));
		}
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$siteData =  $this->request->data;

			if ($this->SiteSetting->save($this->request->data))
			{
				$this->Session->setFlash(__('The site setting has been saved'));
			}
			else
			{
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->SiteSetting->read(null, $id);
		}
	}

	/*
 * Forgot Password
 * Author : Bharat Borana
 */
	function forgot_password()
	{
		$this->layout = 'ajax';

		if($this->request->is(array('post','put')))
		{
			$user_data = $this->User->findByEmail($this->request->data['User']['email']);
			if(isset($user_data['User']['id']) && !empty($user_data['User']['id']))
			{
				$template = "forgot-password";
				$forgot_token = md5(uniqid(mt_rand(), true));
				$this->User->set('id', $user_data['User']['id']);
				$this->User->saveField('token', $forgot_token);
				$token = array('{{name}}','{{reset_password_link}}');
				$reset_link = Router::url(array('controller'=>'users','action'=>'reset_password',$user_data['User']['id'],$forgot_token),true);
				$token_value = array($user_data['User']['name'],$reset_link);
				$this->_send_email($user_data['User']['email'], $token, $token_value, $template, '');
				$this->Session->setFlash("Password reset link has been sent to your email id. Please check",'success');
			}
			else
			{
				$this->Session->setFlash("User does not exists with given email!",'error');
			}
		}
	}
}
