<?php

App::uses('AppController', 'Controller');

class TestimonialsController extends AppController {

	public $uses = array();
	/*
	 * Method	: admin_index page
	 * Author 	: Bharat Borana
	 * Created	: 30 Dec, 2014
	 * @List Testimonial data from backend admin side.
	 */
    public function admin_index(){
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->Testimonial->id = $id;
	        if (!$this->Testimonial->exists()) {
	            throw new NotFoundException(__('Invalid Testemonial'));
	        }
	        if ($this->Testimonial->delete())
	        {
	        	$this->Session->setFlash("Testemonials has been deleted successfully", 'success');
	        } else
	        {
				$this->Session->setFlash("Testemonials has been deleted successfully", 'error');
			}
	        return $this->redirect(array('action' => 'manage_testemonials'));
        }
		$this->theme = "Admin";
		$testimonials = $this->Testimonial->find('all');
		$this->set('testimonials', $testimonials);
	}
	
	/*
	 * Method	: admin_add page
	 * Author 	: Bharat Borana
	 * Created	: 30 Dec, 2014
	 * @Add Testimonial data from backend admin side.
	 */
	public function admin_add() {
		if ($this->request->is(array('post', 'put'))) {
			$testiData =  $this->request->data;
			unset($this->request->data['Testimonial']['image']);
			$this->Testimonial->set($this->request->data);
			if ($this->Testimonial->validates()) {
				if ($this->Testimonial->save()) {
					if (isset($testiData['Testimonial']['image']['tmp_name']) && is_uploaded_file($testiData['Testimonial']['image']['tmp_name']))
					{
						$ext = substr(strtolower(strrchr($testiData['Testimonial']['image']['name'], '.')), 1);
						$FileName = mt_rand().'-'.time().'.'.$ext;
						
						move_uploaded_file($testiData['Testimonial']['image']['tmp_name'],PROFILE_IMAGE_PATH.$FileName);
						
						// store the filename in the array to be saved to the db
						$this->Testimonial->set('id',$this->Testimonial->getLastInsertID());
						$this->Testimonial->saveField('image',$FileName);
					}
					$this->Session->setFlash(__('The Testimonial has been saved.'));
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The Testimonial could not be saved. Please, try again.'));
				}
			} else {
				$errors = $this->Testimonial->validationErrors;
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
		if (!$this->Testimonial->exists($id)) {
			throw new NotFoundException(__('Invalid email template'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$testiData =  $this->request->data;
			unset($this->request->data['Testimonial']['image']);
			$this->request->data['Testimonial']['id'] = $id;
			$this->Testimonial->set($this->request->data);
			if ($this->Testimonial->validates()) { 
				if ($this->Testimonial->save($this->request->data)) {
					if (isset($testiData['Testimonial']['image']['tmp_name']) && is_uploaded_file($testiData['Testimonial']['image']['tmp_name']))
					{
						$ext = substr(strtolower(strrchr($testiData['Testimonial']['image']['name'], '.')), 1);
						$FileName = mt_rand().'-'.time().'.'.$ext;
						
						move_uploaded_file($testiData['Testimonial']['image']['tmp_name'],PROFILE_IMAGE_PATH.$FileName);
						
						// store the filename in the array to be saved to the db
						$this->Testimonial->set('id',$id);
						$this->Testimonial->saveField('image',$FileName);
					}
					$this->Session->setFlash(__('The Testimonial has been saved.'));
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The Testimonial could not be saved. Please, try again.'));
				}
			} else {
				$errors = $this->Testimonial->validationErrors;
				$this->set('errors',$errors);
				// handle errors
			}
		}  else {
			$options = array('conditions' => array('Testimonial.' . $this->Testimonial->primaryKey => $id));
			$this->request->data = $this->Testimonial->find('first', $options);
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
		$this->Testimonial->id = $id;
		if (!$this->Testimonial->exists()) {
			throw new NotFoundException(__('Invalid email template'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Testimonial->delete()) {
			$this->Session->setFlash(__('The email template has been deleted.'));
		} else {
			$this->Session->setFlash(__('The email template could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
}	
 ?>
