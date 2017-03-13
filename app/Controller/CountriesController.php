<?php
App::uses('AppController', 'Controller');
/**
 * Countries Controller
 *
 * @property Country $Country
 * @property PaginatorComponent $Paginator
 */
class CountriesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Search');

/**
 * index method
 *
 * @return void
 */
	public function admin_index() {
		if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if (!empty($ids))
			{
				foreach ($ids as $id)
				{
					if ($action == "Delete")
					{
						$this->Country->id = $id;
						$this->Country->delete();
					}
					
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		$this->Country->recursive = 0;
		$this->Paginator->paginate = array('order' => 'Country.country ASC');
		$this->set('countries', $this->Paginator->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Country->create();
			if ($this->Country->save($this->request->data)) {
				$this->Session->setFlash(__('The country has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The country could not be saved. Please, try again.'));
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
		if (!$this->Country->exists($id)) {
			throw new NotFoundException(__('Invalid country'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Country->save($this->request->data)) {
				$this->Session->setFlash(__('The country has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The country could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Country.' . $this->Country->primaryKey => $id));
			$this->request->data = $this->Country->find('first', $options);
		}
	}
	
	/**
	 * Method	: admin_state
	 * Author	: Praveen Pandey
	 * Created	: 13 Oct 2014
	 */
	 public function admin_state()
	 {
	 	$this->loadModel('State');
	 	if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if (!empty($ids))
			{
				foreach ($ids as $id)
				{
					if ($action == "Delete")
					{
						$this->State->id = $id;
						$this->State->delete();
					}
					
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		$this->State->bindModel(array('belongsTo'=>array('Country')));
		$conditions = array();
	
		if(!empty($this->params->query['name']))
		{
			//$conditions['State.name LIKE'] = $this->params->query['name'].'%';
			$conditions[] = $this->Search->create_conditions_by_name($this->params->query['name'],'State','name');
		}
		if(!empty($this->params->query['country']))
		{
			$conditions[] = $this->Search->create_conditions_by_name($this->params->query['country'],'Country','country');
		}
		
		$this->paginate = array(				
				'limit' => 25,
				'fields' => array('State.*','Country.country'),
				'conditions' => $conditions,
				'order' => array('State.name' => 'ASC'),
		);			
		$this->set('rows', $this->paginate('State'));
	 }
	 
	 /**
	  * Method	: admin_add_state
	  * Author 	: Praveen Pandey
	  * Created	: 13 Oct, 2014
	  */
	  public function admin_add_state($id = NULL)
	  {
	  	$this->loadModel('State');
	  	if($this->request->data)
		{
			$this->State->create();
			if($this->State->save($this->request->data))
			{
				$this->Session->setFlash('State saved successfully');
				$this->redirect(array('action'=>'state'));
			}
			else {
				$this->Session->setFlash('Please try again');
			}
		}
		if(!empty($id))
		{
			$this->request->data = $this->State->findById($id);
		}
	  	$countries = $this->Country->find('list',array('fields'=>array('Country.id','Country.country'),'order'=>'Country.country ASC'));
		$this->set(compact('countries'));
	  }

	/**
	 * Method	: admin_city
	 * Author	: Praveen Pandey
	 * Created	: 13 Oct 2014
	 */
	 public function admin_city()
	 {
	 	$this->loadModel('City');
	 	if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if (!empty($ids))
			{
				foreach ($ids as $id)
				{
					if ($action == "Delete")
					{
						$this->City->id = $id;
						$this->City->delete();
					}
					
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		
		$conditions = array();
	
		if(!empty($this->params->query['name']))
		{
			$conditions[] = $this->Search->create_conditions_by_name($this->params->query['name'],'City','name');
		}
		
		$this->paginate = array(				
				'limit' => 25,
				'fields' => array('City.*'),
				'conditions' => $conditions,
				'order' => array('City.name' => 'ASC'),
		);			
		$this->set('rows', $this->paginate('City'));
	 }
	 
	 /**
	  * Method	: admin_add_state
	  * Author 	: Praveen Pandey
	  * Created	: 13 Oct, 2014
	  */
	  public function admin_add_city($id = NULL)
	  {
	  	$this->loadModel('City');
	  	if($this->request->data)
		{
			$this->City->create();
			if($this->City->save($this->request->data))
			{
				$this->Session->setFlash('City saved successfully');
				$this->redirect(array('action'=>'city'));
			}
			else {
				$this->Session->setFlash('Please try again');
			}
		}
		if(!empty($id))
		{
			$this->request->data = $this->City->findById($id);
		}
	  }
}
