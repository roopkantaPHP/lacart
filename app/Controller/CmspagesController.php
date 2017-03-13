<?php
App::uses('AppController', 'Controller');
/**
 * Cmspages Controller
 *
 * @property Cmspage $Cmspage
 * @property PaginatorComponent $Paginator
 */
class CmspagesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Hybridauth');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(array('index'));
	}

/**
 * index method
 *
 * @return void
 */
	public function admin_index() {
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->Cmspage->id = $id;
	        if (!$this->Cmspage->exists()) {
	            throw new NotFoundException(__('Invalid Cmspage'));
	        }
	        if ($this->Cmspage->delete())
	        {
	        	$this->Session->setFlash("Cms page has been deleted successfully", 'success');
	        } else
	        {
				$this->Session->setFlash("Cms page has been deleted successfully", 'error');
			}
	        return $this->redirect(array('action' => 'manage_cmspages'));
        }
		$this->theme = "Admin";
		$Cmspages = $this->Cmspage->find('all');
		$this->set('Cmspages', $Cmspages);
	}


/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Cmspage->create();
			if ($this->Cmspage->save($this->request->data)) {
				$this->Session->setFlash(__('The Cms page has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Cms page could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Cmspage->exists($id)) {
			throw new NotFoundException(__('Invalid Cms page'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Cmspage->save($this->request->data)) {
				$this->Session->setFlash(__('The Cms page has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Cms page could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Cmspage.' . $this->Cmspage->primaryKey => $id));
			$this->request->data = $this->Cmspage->find('first', $options);
		}
		$this->render('admin_add');
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		$this->Cmspage->id = $id;
		if (!$this->Cmspage->exists()) {
			throw new NotFoundException(__('Invalid Cms page'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Cmspage->delete()) {
			$this->Session->setFlash(__('The Cms page has been deleted.'));
		} else {
			$this->Session->setFlash(__('The Cms page could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * Method	: Cmspage details page
	 * Author	: Bharat Borana
	 * Created	: 27 Jan, 2014
	 */
	public function index($id = null) {
		$this->Cmspage->id = $id;
		$userId = $this->Auth->user('id');

		if (!$this->Cmspage->exists()) {
			throw new NotFoundException(__('Invalid Cms page'));
		}

		$options = array('conditions' => array('Cmspage.' . $this->Cmspage->primaryKey => $id));
		$cmspageDetails = $this->Cmspage->find('first', $options);
		$cmsCities = '';
		if(isset($cmspageDetails['Cmspage']['id']) && $cmspageDetails['Cmspage']['id']==5)
		{
			$this->loadModel('CmsCity');
			$cmsCities = $this->CmsCity->getfeaturedCities();
		}
		$this->set("title_for_layout",$cmspageDetails['Cmspage']['title']); 
		
		$this->set(compact(array("userId","cmspageDetails","cmsCities")));
	}

	/**
	 * Method	: Cmspage invite friends page
	 * Author	: Bharat Borana
	 * Created	: 27 Jan, 2014
	 */
	
	public function invite() { 
		if($this->request->is(array('put','post')))
		{
			$this->loadModel('User');
			$this->loadModel('Newsletter');
			$error = '';
			$userId = $this->Auth->user('id');
			$userDetails = $this->User->findById($userId);
			if(isset($userDetails) && !empty($userDetails))
				$userName = $userDetails['User']['name'];
			else
				$userName = "Lacart User";
			
			$name = ucfirst($userName);
						
			if(isset($this->request->data['Invitation']['phone']) && !empty($this->request->data['Invitation']['phone']))
			{
				$phoneArray = explode(",", $this->request->data['Invitation']['phone']);
				
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
						/*if($message->sid)
						{
							$count++;		
						}*/
					}
					catch (Exception $e)
					{
						$error[][0] = $e->getMessage();
					}
				}
				$this->Session->setFlash(__('Invitaion message has successfully sent.'),'success');
			}

			if(isset($this->request->data['Invitation']['emailaddress']) && !empty($this->request->data['Invitation']['emailaddress']))
			{
				$emailArray = explode(",", $this->request->data['Invitation']['emailaddress']);
				//$ninfo = $this->Newsletter->findById(4);
				foreach ($emailArray as $emKey => $emValue)
				{

					$emValue = trim($emValue);

					$Email = new CakeEmail('smtp');

					$this->loadModel('EmailTemplate');
					
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
				$this->Session->setFlash(__('Invitaion email has successfully sent.'),'success');
			}

			if(empty($this->request->data['Invitation']['emailaddress']) && empty($this->request->data['Invitation']['phone']))
			{
				$this->Session->setFlash(__('Please enter email address or phone number to send invitaion'),'error');
			}
			$this->set('errors', $error);
		
		}
		
		//$this->set("cmspageDetails",$cmspageDetails);
	}

	public function shareme(){
		$this->Hybridauth->shareme('Facebook');
	}

}
