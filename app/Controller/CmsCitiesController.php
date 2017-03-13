<?php

App::uses('AppController', 'Controller');

class CmsCitiesController extends AppController {

	public $uses = array();
	/*
	 * Method	: admin_index page
	 * Author 	: Bharat Borana
	 * Created	: 30 Dec, 2014
	 * @List CmsCity data from backend admin side.
	 */
    public function admin_index(){
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->CmsCity->id = $id;
	        if (!$this->CmsCity->exists()) {
	            throw new NotFoundException(__('Invalid CmsCity'));
	        }
	        if ($this->CmsCity->delete())
	        {
	        	$this->Session->setFlash("CmsCitys has been deleted successfully", 'success');
	        } else
	        {
				$this->Session->setFlash("CmsCitys has been deleted successfully", 'error');
			}
	        return $this->redirect(array('action' => 'manage_CmsCitys'));
        }
		$this->theme = "Admin";
		$CmsCitys = $this->CmsCity->find('all');
		$this->set('CmsCitys', $CmsCitys);
	}
	
	/*
	 * Method	: admin_add page
	 * Author 	: Bharat Borana
	 * Created	: 30 Dec, 2014
	 * @Add CmsCity data from backend admin side.
	 */
	public function admin_add() {
		if ($this->request->is(array('post', 'put'))) {
			$testiData =  $this->request->data;
			unset($this->request->data['CmsCity']['image']);
			$this->CmsCity->set($this->request->data);
			if ($this->CmsCity->validates()) {
				if ($this->CmsCity->save()) {
					if (isset($testiData['CmsCity']['image']['tmp_name']) && is_uploaded_file($testiData['CmsCity']['image']['tmp_name']))
					{
						$ext = substr(strtolower(strrchr($testiData['CmsCity']['image']['name'], '.')), 1);
						$FileName = mt_rand().'-'.time().'.'.$ext;
						
						move_uploaded_file($testiData['CmsCity']['image']['tmp_name'],CMS_IMAGE_PATH.$FileName);
						
						// store the filename in the array to be saved to the db
						$this->CmsCity->set('id',$this->CmsCity->getLastInsertID());
						$this->CmsCity->saveField('image',$FileName);
					}
					$this->Session->setFlash(__('The CmsCity has been saved.'));
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The CmsCity could not be saved. Please, try again.'));
				}
			} else {
				$errors = $this->CmsCity->validationErrors;
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
		if (!$this->CmsCity->exists($id)) {
			throw new NotFoundException(__('Invalid email template'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$testiData =  $this->request->data;
			unset($this->request->data['CmsCity']['image']);
			$this->request->data['CmsCity']['id'] = $id;
			$this->CmsCity->set($this->request->data);
			if ($this->CmsCity->validates()) { 
				if ($this->CmsCity->save($this->request->data)) {
					if (isset($testiData['CmsCity']['image']['tmp_name']) && is_uploaded_file($testiData['CmsCity']['image']['tmp_name']))
					{
						$ext = substr(strtolower(strrchr($testiData['CmsCity']['image']['name'], '.')), 1);
						$FileName = mt_rand().'-'.time().'.'.$ext;
						
						move_uploaded_file($testiData['CmsCity']['image']['tmp_name'],CMS_IMAGE_PATH.$FileName);
						
						// store the filename in the array to be saved to the db
						$this->CmsCity->set('id',$id);
						$this->CmsCity->saveField('image',$FileName);
					}
					$this->Session->setFlash(__('The CmsCity has been saved.'));
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The CmsCity could not be saved. Please, try again.'));
				}
			} else {
				$errors = $this->CmsCity->validationErrors;
				$this->set('errors',$errors);
				// handle errors
			}
		}  else {
			$options = array('conditions' => array('CmsCity.' . $this->CmsCity->primaryKey => $id));
			$this->request->data = $this->CmsCity->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->CmsCity->id = $id;
		if (!$this->CmsCity->exists()) {
			throw new NotFoundException(__('Invalid email template'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->CmsCity->delete()) {
			$this->Session->setFlash(__('The email template has been deleted.'));
		} else {
			$this->Session->setFlash(__('The email template could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
}	
 ?>
