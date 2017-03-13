<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
//session_start();
App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
		'Acl',
		'Cookie',
		'Auth' => array(
			'authorize' => array('Controller'),
			'actionPath' => 'controllers/',
			'loginRedirect' => array('controller' => 'users', 'action' => 'profile'),
			'logoutRedirect' => array('controller' => '/'),
			'authError' => 'Not Authorized. Please Login to continue',
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'username', 'password' => 'password'),					
				)
			),
		),
		'Session',
	);
	public $helpers = array(
		'Session', 'Js','Common'
		// 'CustomImage', 'Timthumb.Timthumb', 'Utility'
	);
		// Ideally should be handeled thru CakePHP Error Handler (http://book.cakephp.org/2.0/en/development/errors.html)
	// For the time being, doing it as this.
	function beforeRender() {
		if($this->name == 'CakeError')
		{
			if($this->request->params['controller'] == 'api')
			{
				$this->layout = false;
				$this->autoRender = false;
				$this->response->type('json');
				$json = json_encode(
					array(
						'message' => $this->viewVars['name'],
						'status' => isset($this->viewVars['code']) ? $this->viewVars['code'] : 405
					)
				);
				header('Content-Type: application/json');  
				echo $json;
				exit;
			}
		}
	}
	public function beforeFilter()
	{
		$this->setRedirect();

		if ($this->Auth->user('id') && $this->Auth->user('group_id') == NORMAL_USER && $this->params['prefix'] == 'admin')
	    {
	    	$this->redirect(array('controller' => 'users', 'action' => 'restricted', 'admin' => false, 'plugin' => false));
	    }

		if(!$this->Auth->user('id'))
		{
			if($this->params['prefix'] == 'admin')
			{
				$this->theme = BACK_END;
			}else
			{
				$this->theme = FRONT_END;
			}
		}
		else if($this->Auth->user('group_id') == SUPER_ADMIN && $this->params['prefix'] == 'admin')
		{
			$this->theme = BACK_END;
			$this->Auth->loginRedirect = array('controller' => 'users', 'action' => 'profile');
		}
		else
		{
			$this->theme = FRONT_END;
			$this->Auth->loginRedirect = array('controller' => 'users', 'action' => 'dashboard', 'admin' => true);
		}

		if($this->Auth->User('id') && $this->Session->check('remember_me') && $this->Session->read('remember_me'))
		{
			$cookie = array();
			$cookie['id'] = $this->Auth->User('id');
			$cookie['email'] = $this->Auth->User('email');
			$this->Cookie->write(LOGIN_COOKIE_NAME, $cookie, true, '+2 weeks');
			$this->Session->delete('remember_me');
		}

		//IF THE USER IS NOT LOGGED IN THEN CHECKING IF THE COOKIE EXIST ON THE SYSTEM THEN MAKING THE USER LOGGED IN
		if(!$this->Auth->user('id') && $this->request->params['controller'] != 'api')
		{
			$cookie_val = $this->Cookie->read(LOGIN_COOKIE_NAME);
			
			if($cookie_val)
			{
				$this->loadModel('User');
				$row_user = $this->User->find(
					'first', array(
						'conditions' => array(
							'User.email' => $cookie_val['email'],
							'User.id' => $cookie_val['id'],
							'User.is_active' => '1',
						)
					)
				);
				if($row_user)
				{
					$this->Auth->fields = array('email' => 'email', 'password' => 'password');
					$this->Auth->login($row_user['User']);
					if(!$this->Auth->user())
					{
						$this->Cookie->delete(LOGIN_COOKIE_NAME);
					}
				}
				else
				{
					$this->Cookie->delete(LOGIN_COOKIE_NAME);
				}
			}
		}

		if($this->Auth->User())
		{
			$userinfo = $this->Auth->User();
		}
		else
		{
			$userinfo = array();
		}
		$this->set('userinfo', $userinfo);
	}
	/**
	 * This function is called by $this->Auth->authorize('controller') 
	 * and only fires when the user is logged in.
	 */
	public function isAuthorized($user) {
		return true;
		# check guest access
		/*$aro = $this->_guestsAro($user['group_id']); // guest aro model and foreign_key
		$aco = $this->_getAcoPath(); // get aco
		if ($this->Acl->check($aro, $aco))
		{
			return true;
		} else {
			# check user access
			$aro = $this->_userAro($user['id']); // user aro model and foreign_key
			$aco = $this->_getAcoPath(); // get aco
			if ($this->Acl->check($aro, $aco)){
				return true;
			} else {
				$requestor = $user['username']; #$aro['model'] . ' ' . $aro['foreign_key'];
				$requested = is_array($aco) ? $aco['model'] . ' ' . $aco['foreign_key'] : str_replace('/', ' ', $aco);
				$message = 'does not have access to';
				$this->Session->setFlash(__('%s %s %s.', $requestor, $message, $requested));
				$this->redirect(array('controller' => 'users', 'action' => 'restricted', 'admin' => false, 'plugin' => false));
			}
		}*/
	}

	/**
	 * Gets the variables used for the lookup of the aro id
	 */
	private function _userAro($userId) {
		$guestsAro = array('model' => 'User', 'foreign_key' => $userId);
		return $guestsAro;
	}

	/**
	 * Gets the variables used for the lookup of the guest aro id
	 */
	private function _guestsAro($groupId) {
		$guestsAro = array('model' => 'Group', 'foreign_key' => $groupId);
		return $guestsAro;
	}

	/**
	 * Gets the variables used to lookup the aco id for the action type of lookup
	 * VERY IMPORTANT : If the aco is a record level type of aco (ie. model and foreign_key lookup) that means that all groups and users who have access rights must be defined.  You cannot have negative values for access permissions, and thats okay, because we deny everything by default.
	 *
	 * return {array || string}		The path to the aco to look up.
	 */
	private function _getAcoPath() {

		if (!empty($this->request->params['pass'][0])) {
			# check if the record level aco exists first
			$aco = $this->Acl->Aco->find('first', array(
				'conditions' => array(
					'model' => $this->modelClass,
					'foreign_key' => $this->request->params['pass'][0]
					)
				));
		}
		if(!empty($aco)) {
			return array('model' => $this->modelClass, 'foreign_key' => $this->request->params['pass'][0]);
		} else {
			$controller = Inflector::camelize($this->request->params['controller']);
			$action = $this->request->params['action'];
			# $aco = 'controllers/Webpages/Webpages/view'; // you could do the full path, but the shorter path is slightly faster. But it does not allow name collisions. (the full path would allow name collisions, and be slightly slower).
			return $controller.'/'.$action;
		}
	}
	public function _cleanTitle($title)
	{		
		$str = substr(strtolower(trim($title)), 0 , 300);
		$str = preg_replace('/[^a-z0-9-]/', '-', $str);
		$str = preg_replace('/-+/', "-", $str);	
		return $str;		
	}
	
	function _send_email($to, $token, $token_value, $template, $subjectParams  )
	{
		if(!filter_var($to, FILTER_VALIDATE_EMAIL))
		{
			return false;
		}
		
		$this->loadModel('EmailTemplate');
		$template = $this->EmailTemplate->findBySlug($template);
		if (empty($template))
		{
			return false;
		}
		$template = $template['EmailTemplate'];
		
		$subject = str_replace('{username}',$subjectParams ,$template['subject']);
		
		$msg = $template['content'];
		
		$msg = str_replace($token, $token_value, $msg);
		
		//echo $to_email ;
		App::uses('CakeEmail', 'Network/Email');
		$email = new CakeEmail('smtp');		
		$email->to($to);
		$email->from($template['from_email']);
		$email->subject($subject);
		$email->emailFormat('html');
		$email->sendAs = 'both';
		$email->config('smtp');		
		$email->send($msg);
	}
	
	function _base64_to_jpeg($base64_string, $output_file) {
		
	    $ifp = fopen($output_file, "wb"); 
	    $data = explode(',', $base64_string);
	    fwrite($ifp, base64_decode($data[1])); 
	    fclose($ifp); 
	    return $output_file; 
	}
	
	/**
	 * Method	: _isValidApiRequest()
	 */
	 public function _isValidApiRequest()
	 {
		//Configure::write('debug', 2);
		$c_url= '';
		$post_params = $this->request->data;
		
		$token = '';
		$timestamp = '';
		//Get all passed parameters, seprate timtstamp and token for security purpose
		
		// Sort the array keys except token and timestamp
		$resp_token = $post_params['token'];
		$resp_timestamp = $post_params['timestamp'];
		unset($post_params['token']);
		unset($post_params['timestamp']);
		$post_params['token'] = $resp_token;
		$post_params['timestamp'] = $resp_timestamp;
		$c_url = $this->params->params['action'].':';
		foreach($post_params as $key => $value)
		{
			if($key == 'timestamp')
			{
				$c_url .= $key.'='.$value.':';
			}					
			if($key == 'timestamp')
			{
				$timestamp = $value;
			}
			else
			{
				$token = $value;
			}
		}
		
		//Error : If token not appended with request
		if(empty($token))				
		{
			echo $this->_status_faliure("Token not found"); die;
		}
		//Error if timestamp is not appended with request
		if(empty($timestamp))				
		{
			echo $this->_status_faliure("Invalid request"); die;
		}				
		//Check expiry of request
		if(($this->_get_utc()-$timestamp) > API_REQUEST_EXPIRED)				
		{
			echo $this->_status_faliure("Token expired, please try again"); die;
		}
		
		$c_url.= API_SECURITY_KEY;
		$check_string = $this->_hash_ssa($c_url);
		if($token == $check_string)				
		{
			return true;
		}
		else
		{
			echo $this->_status_faliure("Token not valid"); die;
		}
			
	}
	public function _hash_ssa($string) 
    {
		$salt = API_SECURITY_KEY; 
       	$hash = base64_encode( base64_encode($string) . $salt );
        return $hash; 
    }
	public function _get_utc()
	{
		$utc_str = gmdate("M d Y H:i:s", time());	
		$utc = strtotime($utc_str);
		return $utc;
	}
	public function _status_faliure($data)
	{
		return json_encode(array('status' => 400, 'data' => $data));
	}
	public function setRedirect ()
	{
	    if ($this->request->params['controller'] != 'users' && $this->request->params['action'] != 'login') {
	        $this->Session->write('redir', array('controller'=>$this->request->params['controller'], 'action'=>$this->request->params['action'], implode($this->request->params['pass'], '/')));
	    }
	}
}
?>