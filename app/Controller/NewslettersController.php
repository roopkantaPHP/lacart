<?php
App::uses('AppController', 'Controller');
/**
 * Newsletters Controller
 *
 * @property Newsletter $Newsletter
 * @property PaginatorComponent $Paginator
 */
 App::uses('CakeEmail', 'Network/Email');
class NewslettersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function admin_index()
	{
		if ($this->request->is('post'))
		{
			$action = $this->request->data['listingAction'];
			$ids = $this->request->data['ids'];
			if ($action == "Delete" && count($ids)>0)
			{
				foreach ($ids as $id)
				{
					$this->Newsletter->delete($id);
				}	
			}			 
		}		
		$newsletters = $this->Newsletter->find('all');
		$this->set('newsletters', $newsletters);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Newsletter->exists($id)) {
			throw new NotFoundException(__('Invalid newsletter'));
		}
		$options = array('conditions' => array('Newsletter.' . $this->Newsletter->primaryKey => $id));
		$this->set('newsletter', $this->Newsletter->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		$heading = "Add Newsletter";
		if ($this->request->is('post'))
		{		
			if ($this->request->data['Newsletter']['newsletter_title'] && $this->request->data['Newsletter']['subject'] != "")
			{				
				$this->Newsletter->save($this->request->data);
				$this->Session->setFlash("Operation Successful", 'success');				
			}
			else
			{
				$this->Session->setFlash("Please enter newsletter title and subject.", 'error');				
			}
			$this->redirect(array('controller'=>'pages', 'action'=>'manage_newsletters'));
		}
		$this->set('heading', $heading);
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Newsletter->exists($id)) {
			throw new NotFoundException(__('Invalid newsletter'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Newsletter->save($this->request->data)) {
				$this->Session->setFlash(__('The newsletter has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The newsletter could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Newsletter.' . $this->Newsletter->primaryKey => $id));
			$this->request->data = $this->Newsletter->find('first', $options);
		}
		$this->render('admin_add');
	}

/**
 * send method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_send()
	{
		//Configure::write('debug','2');
		$email = "";	
		$query = $this->request->query;	
		if ($this->request->is("post"))
		{
			$request = $this->request->data;
			$emails = $request['ids'];
			$action = $request['listingAction'];
			$newsletter_id = $request['Newsletter']['newsletter_id'];
			if ($action == "send")
			{
				$ninfo = $this->Newsletter->findById($newsletter_id);
				$subject = $ninfo['Newsletter']['subject'];	
				$content = $ninfo['Newsletter']['content'];
				$from_email = ($ninfo['Newsletter']['from_email']) ? $ninfo['Newsletter']['from_email'] : emailFromAddress;
				$reply_to_email = NEWSLETTER_EMAIL_FROM;
				foreach ($emails as $email)
				{
					$content = $ninfo['Newsletter']['content'];
					$Email = new CakeEmail('smtp');
					$Email->from(array($from_email => NEWSLETTER_NAME_FROM));
					$Email->to($email);
					$Email->subject($subject);
					$Email->replyTo($reply_to_email);
					$Email->emailFormat('html');
					$Email->template('default');
					$Email->send($content);
				}				
			}			
			$this->Session->setFlash("Newsletter has been sent successfully.", 'success');
			$this->redirect($this->referer());
		}
		if (count($query)>0 && is_array($query))
		{			
			$email = isset($query['email']) ? $query['email'] : '';			
		}
		$conditions = array('User.group_id' => NORMAL_USER);		
		if ($email != "")
		{
			$conditions[] = array('User.email LIKE'=>'%'.$email.'%');
		}		
		App::import('Model', 'User');
		$user_obj = new User();
		
		$results = $user_obj->find('list', array('fields' => array('id', 'email'), 'conditions' => $conditions));
		
		$newsletter_list = $this->Newsletter->find('list', array('fields'=>array('id','newsletter_title')));		
		// $results = $this->paginate('Subscriber');
		$totalRecords = count($results);
		$this->set(Compact('totalRecords','pagingStr','results','email','newsletter_list'));
	}
}
