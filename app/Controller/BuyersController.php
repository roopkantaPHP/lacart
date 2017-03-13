<?php
App::uses('AppController', 'Controller');
/**
 * Buyers Controller
 *
 * @property Buyer $Buyer
 * @property PaginatorComponent $Paginator
 */
class BuyersController extends AppController {

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
	public function index() {
		$this->Buyer->recursive = 0;
		$this->set('buyers', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Buyer->exists($id)) {
			throw new NotFoundException(__('Invalid buyer'));
		}
		$options = array('conditions' => array('Buyer.' . $this->Buyer->primaryKey => $id));
		$this->set('buyer', $this->Buyer->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Buyer->create();
			if ($this->Buyer->save($this->request->data)) {
				$this->Session->setFlash(__('The buyer has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The buyer could not be saved. Please, try again.'));
			}
		}
		$countries = $this->Buyer->Country->find('list');
		$this->set(compact('countries'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Buyer->exists($id)) {
			throw new NotFoundException(__('Invalid buyer'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Buyer->save($this->request->data)) {
				$this->Session->setFlash(__('The buyer has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The buyer could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Buyer.' . $this->Buyer->primaryKey => $id));
			$this->request->data = $this->Buyer->find('first', $options);
		}
		$countries = $this->Buyer->Country->find('list');
		$this->set(compact('countries'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Buyer->id = $id;
		if (!$this->Buyer->exists()) {
			throw new NotFoundException(__('Invalid buyer'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Buyer->delete()) {
			$this->Session->setFlash(__('The buyer has been deleted.'));
		} else {
			$this->Session->setFlash(__('The buyer could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
