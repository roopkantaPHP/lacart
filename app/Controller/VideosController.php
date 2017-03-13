<?php

App::uses('AppController', 'Controller');

class VideosController extends AppController {

	public $uses = array();
	/*
	 * Method	: admin_index page
	 * Author 	: Bharat Borana
	 * Created	: 30 Dec, 2014
	 * @List Video data from backend admin side.
	 */
    public function admin_index(){
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->Video->id = $id;
	        if (!$this->Video->exists()) {
	            throw new NotFoundException(__('Invalid Testemonial'));
	        }
	        if ($this->Video->delete())
	        {
	        	$this->Session->setFlash("Videos has been deleted successfully", 'success');
	        } else
	        {
				$this->Session->setFlash("Videos has been deleted successfully", 'error');
			}
	        return $this->redirect(array('action' => 'manage_Videos'));
        }
		$this->theme = "Admin";
		$Videos = $this->Video->find('all');
		$this->set('Videos', $Videos);
	}
	
	/*
	 * Method	: admin_add page
	 * Author 	: Bharat Borana
	 * Created	: 30 Dec, 2014
	 * @Add Video data from backend admin side.
	 */
	public function admin_add() {
		if ($this->request->is(array('post', 'put'))) {
			$this->Video->set($this->request->data);
			if ($this->Video->validates()) {
				if ($this->Video->save()) {
					$this->Session->setFlash(__('The Video has been saved.'));
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The Video could not be saved. Please, try again.'));
				}
			} else {
				$errors = $this->Video->validationErrors;
				$this->set('errors',$errors);
				// handle errors
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
		if (!$this->Video->exists($id)) {
			throw new NotFoundException(__('Invalid email template'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['Video']['id'] = $id;
			$this->Video->set($this->request->data);
			if ($this->Video->validates()) { 
				if ($this->Video->save($this->request->data)) {
					$this->Session->setFlash(__('The Video has been saved.'));
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The Video could not be saved. Please, try again.'));
				}
			} else {
				$errors = $this->Video->validationErrors;
				$this->set('errors',$errors);
				// handle errors
			}
		}  else {
			$options = array('conditions' => array('Video.' . $this->Video->primaryKey => $id));
			$this->request->data = $this->Video->find('first', $options);
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
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid email template'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Video->delete()) {
			$this->Session->setFlash(__('The email template has been deleted.'));
		} else {
			$this->Session->setFlash(__('The email template could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
}	
 ?>
