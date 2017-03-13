<?php
App::uses('AppController', 'Controller');
/**
 * Dishes Controller
 *
 * @property Dishes $Dishes
 * @property PaginatorComponent $Paginator
 */
class DishesController extends AppController {
	
	public $name = 'Dishes';
	
	public function beforeFilter()
	{
		$this->Auth->allow(array('search'));
		parent::beforeFilter();
	}
	
	/**
	 * Method	: admin_allergy
	 * Author	: Praveen Pandey
	 * Created	: 16 Oct, 2014
	 */
	 public function admin_allergy()
	 {
	 	$this->loadModel('Allergy');
		if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if(!empty($this->request->data['ids']))
			{
				switch($this->request->data['action'])
				{
					case "delete":
						$this->Allergy->deleteAll(array('id' => $this->request->data['ids']));
					break;
					case "active":
						$this->Allergy->updateAll(array('is_active' => 1), array('id' =>  $this->request->data['ids'] ));
					break;
					case "deactive":
						$this->Allergy->updateAll(array('is_active' => 0), array('id' =>  $this->request->data['ids'] ));
					break;
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		$this->paginate = array(
					'order'=> 'name ASC'
		);
		$this->set('rows', $this->paginate('Allergy'));
	 }
	 
	/**
	 * Method	: admin_add_allergy
	 * Author	: Praveen Pandey
	 * Created	: 16 Oct, 2014
	 */
	 public function admin_add_allergy($id = null)
	 {
	 	$this->loadModel('Allergy');
	  	if($this->request->data)
		{
			$this->Allergy->create();
			if($this->Allergy->save($this->request->data))
			{
				$this->Session->setFlash('Allergy saved successfully');
				$this->redirect(array('action'=>'allergy'));
			}
			else {
				$this->Session->setFlash('Please try again');
			}
		}
		if(!empty($id))
		{
			$this->request->data = $this->Allergy->findById($id);
		}
	 }
	 
	 /**
	 * Method	: admin_cuisine
	 * Author	: Praveen Pandey
	 * Created	: 01 Nov, 2014
	 */
	 public function admin_cuisine()
	 {
	 	$this->loadModel('Cuisine');
		if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if(!empty($this->request->data['ids']))
			{
				switch($this->request->data['action'])
				{
					case "delete":
						$this->Cuisine->deleteAll(array('id' => $this->request->data['ids']));
					break;
					case "active":
						$this->Cuisine->updateAll(array('is_active' => 1), array('id' =>  $this->request->data['ids'] ));
					break;
					case "deactive":
						$this->Cuisine->updateAll(array('is_active' => 0), array('id' =>  $this->request->data['ids'] ));
					break;
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		$this->paginate = array(
					'order'=> 'name ASC'
		);
		$this->set('rows', $this->paginate('Cuisine'));
	 }
	 
	/**
	 * Method	: admin_add_cuisine
	 * Author	: Praveen Pandey
	 * Created	: 01 Nov, 2014
	 */
	 public function admin_add_cuisine($id = null)
	 {
	 	$this->loadModel('Cuisine');
	  	if($this->request->data)
		{
			$this->Cuisine->create();
			if($this->Cuisine->save($this->request->data))
			{
				$this->Session->setFlash('Cuisine saved successfully');
				$this->redirect(array('action'=>'cuisine'));
			}
			else {
				$this->Session->setFlash('Please try again');
			}
		}
		if(!empty($id))
		{
			$this->request->data = $this->Cuisine->findById($id);
		}
	 }
	 
	 	 /**
	 * Method	: admin_portion
	 * Author	: Praveen Pandey
	 * Created	: 03 Nov, 2014
	 */
	 public function admin_portion()
	 {
	 	$this->loadModel('Portion');
		if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if(!empty($this->request->data['ids']))
			{
				switch($this->request->data['action'])
				{
					case "delete":
						$this->Portion->deleteAll(array('id' => $this->request->data['ids']));
					break;
					case "active":
						$this->Portion->updateAll(array('is_active' => 1), array('id' =>  $this->request->data['ids'] ));
					break;
					case "deactive":
						$this->Portion->updateAll(array('is_active' => 0), array('id' =>  $this->request->data['ids'] ));
					break;
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		$this->paginate = array(
					'order'=> 'id ASC'
		);
		$this->set('rows', $this->paginate('Portion'));
	 }
	 
	/**
	 * Method	: admin_add_portion
	 * Author	: Praveen Pandey
	 * Created	: 03 Nov, 2014
	 */
	 public function admin_add_portion($id = null)
	 {
	 	$this->loadModel('Portion');
	  	if($this->request->data)
		{
			$this->Portion->create();
			if($this->Portion->save($this->request->data))
			{
				$this->Session->setFlash('Cuisine saved successfully');
				$this->redirect(array('action'=>'portion'));
			}
			else {
				$this->Session->setFlash('Please try again');
			}
		}
		if(!empty($id))
		{
			$this->request->data = $this->Portion->findById($id);
		}
	 }
	 
	 /**
	  * Method	: admin_manage_custom_price
	  * Author	: Praveen Pandey
	  * Created	: 5 Nov, 2014
	  * Purpose	: approve custom price
	  */
	  public function admin_manage_custom_price()
	  {
	  	$conditions = array('Dish.is_custom_price_active' => 0, 'Dish.p_custom' => 1);
	  	$this->paginate = array(
	  				'conditions' => $conditions,
					'order'=> 'Dish.created DESC',
					'contain' => array('Kitchen.User')
		);
		$rows = $this->paginate('Dish');
		// /pr($rows);
		$this->set(compact('rows'));
	  }

	/**
	 * Method	: admin_price_approve
	 * Author 	: Praveen Pandey
	 * Created	: 5 Nov, 2014
	 */
	 public function admin_price_approve($dish_id) {
	 	if(empty($dish_id))
		{
			$this->redirect($this->referer());
		}
		$this->Dish->id = $dish_id;
		$this->Dish->saveField('is_custom_price_active', 1);
		$this->redirect($this->referer());
	}
	
	/**
	 * Method	: admin_dish_list
	 * Author	: Praveen Pandey
	 * Created	: 10 Nov, 2014
	 * Purpose	: List of all dishes
	 */
	public function admin_dish_list($kitchen_id = NULL)
	{
		$order = "Dish.created DESC";
		$conditions = array();
		if(!empty($kitchen_id))
		{
			$conditions['Dish.kitchen_id'] = $kitchen_id;
		}
		$this->paginate = array(
							'conditions' => $conditions,
							'order' => $order,
							'contain' => array('Kitchen.User')
		);
		$rows = $this->paginate('Dish');
		$this->set(compact('rows'));
	}
	  
	/**
	 * Method	: search
	 * Author	: Praveen Pandey
	 * Created	: 16 Nov, 2014
	 */
	public function search() {
		$search_data = $this->params['named'];

		if(isset($search_data['q']) && !empty($search_data['q']))
		{
			$search_data['keyword'] = $search_data['q'];
			unset($search_data['q']);
		}
		
		if(isset($search_data['t']) && !empty($search_data['t']))
		{
			$search_data['time'] = $search_data['t'];
			unset($search_data['t']);
		}
		
		if(isset($search_data['o_popular']) && $search_data['o_popular'] == 'popular')
		{
			$search_data['is_popular'] = 1;
			unset($search_data['is_popular']);
		}
				
		if(isset($search_data['o_rating']) && $search_data['o_rating'] == 'rating')
		{
			$search_data['is_rated_high'] = 1;
			unset($search_data['is_rated_high']);
		}

		if(isset($search_data['diet']) && !empty($search_data['diet']))
		{
			$search_data['diet'] = str_replace('non-vegetarian', 'Non-Vegetarian', $search_data['diet']);
			$search_data['diet'] = str_replace('vegetarian', 'Vegetarian', $search_data['diet']);
			$search_data['diet'] = str_replace('vegan', 'Vegan', $search_data['diet']);
		}

		if(isset($search_data['dine']) && !empty($search_data['dine']))
		{
			if(strpos($search_data['dine'], 'dinein'))
				$search_data['dining_dine_in'] = 1;
			else
				unset($search_data['dining_dine_in']);

			if(strpos($search_data['dine'], 'takeout'))
				$search_data['dining_take_out'] = 1;
			else
				unset($search_data['dining_take_out']);

			unset($search_data['dine']);
		}

		if(isset($search_data['loc']) && !empty($search_data['loc']))
		{
			$search_data['address'] = $search_data['loc'];
			unset($search_data['loc']);
		}

		if(isset($search_data['lat']) && !empty($search_data['lat']))
		{
			$search_data['latitude'] = $search_data['lat'];
			unset($search_data['lat']);
		}

		if(isset($search_data['lng']) && !empty($search_data['lng']))
		{
			$search_data['longitude'] = $search_data['lng'];
			unset($search_data['lng']);
		}

		if(isset($search_data['cu']) && !empty($search_data['cu']))
		{
			$search_data['cuisine'] = $search_data['cu'];
			unset($search_data['cu']);
		}

		$search_data['radius'] = 0;

		$this->loadModel('Kitchen');
		$search_data['login_user_id'] = '';
		$loginUser = $this->Auth->user('id');
		if(!empty($loginUser))
		$search_data['login_user_id'] = $loginUser;
		
		$this->Session->write('search_data',$search_data);
		
		$results = $this->Kitchen->searchKitchen($this, $search_data);
		
		$this->set(compact('results'));
		
		$this->loadModel('Cuisine');
		$cuisines= $this->Cuisine->find('list', array(
										'conditions' => array('is_active'=>1),
										'fields' => array('name','name')
		));
		$this->set(compact('cuisines'));
	}
	
	/*
	 * Method	: add Dish
	 * Author	: Bharat Borana
	 * Created	: 08 Jan, 2015
	 * @Add/Edit dishes
	 */
		public function add()
		{
			$this->loadModel('Kitchen');
			$this->loadModel('Dish');
			$userId = $this->Auth->user('id'); 

			$this->Kitchen->recursive = -1;
			$kitchen = $this->Kitchen->findByUserId($userId);
			
			if(empty($kitchen['Kitchen']) || (isset($kitchen['Kitchen']['status']) && $kitchen['Kitchen']['status']=='off'))
			{ 
				return $this->redirect(array('controller'=>'kitchens','action'=>'activate_now'));
			}

			$this->loadModel('Allergy');
			$allergies = $this->Allergy->find('list',array('conditions'=>array('Allergy.is_active'=>1)));
			
			$this->loadModel('Portion');
			$portions = $this->Portion->find('all');
			
			$this->loadModel('Cuisine');
			$cuisines = $this->Cuisine->find('list',array('fields'=>array('Cuisine.name','Cuisine.name'),array('conditions'=>array('is_active'=>1))));
			
			if($this->request->is(array('post', 'put'))){
				$errors = array(); 

				$dishData = $this->request->data;
				unset($this->request->data['UploadImage']);
				
				$this->request->data['Dish']['kitchen_id'] = $kitchen['Kitchen']['id'];
				if(!empty($this->request->data['Dish']['p_custom']) && $this->request->data['Dish']['p_custom']==1)
				{
					if(empty($this->request->data['Dish']['p_custom_quantity']))
					{
						$this->request->data['Dish']['p_custom_quantity'] = 1;
					}
					$this->request->data['Dish']['is_custom_price_active'] = 0;	
				}

				if(!isset($this->request->data['Dish']['p_small']) && !isset($this->request->data['Dish']['p_big']) && !isset($this->request->data['Dish']['p_custom']))
					$errors[][0] = "Please select atleast one portion.";
					
				if(isset($this->request->data['Dish']['p_small']) && !empty($this->request->data['Dish']['p_small']) && $this->request->data['Dish']['p_small']==1 && empty($this->request->data['Dish']['p_small_quantity']))
					$this->request->data['Dish']['p_small_quantity'] = 1;
				
				if(isset($this->request->data['Dish']['p_big']) &&!empty($this->request->data['Dish']['p_big']) && $this->request->data['Dish']['p_big']==1 && empty($this->request->data['Dish']['p_big_quantity']))
					$this->request->data['Dish']['p_big_quantity'] = 1;
			
				if(isset($this->request->data['Dish']['diet']) && !empty($this->request->data['Dish']['diet'])){
					$this->request->data['Dish']['diet'] = key($this->request->data['Dish']['diet']);
				}

				$allergy = '';
				if(isset($this->request->data['Dish']['allergens']) && !empty($this->request->data['Dish']['allergens'])){
					foreach ($this->request->data['Dish']['allergens'] as $key => $value) {
						if($key =='other' && $value == 1 && !empty($this->request->data['Dish']['other_allergy_text'])){
							$otherAllergy['is_active'] = 0;
							$otherAllergy['name'] = $this->request->data['Dish']['other_allergy_text']; 
							$this->Allergy->create();
							if($this->Allergy->save($otherAllergy)){
								$allergy .= $this->request->data['Kitchen']['other_allergy_text'].'::::::::';
							}
						}else{
							if($value==1)
								$allergy .= $key.'::::::::';
						}
					}
				}

				
				if(!empty($allergy)){
					$this->request->data['Dish']['allergens'] = trim($allergy,'::::::::');
				}

				$repeat = '';
				if(isset($this->request->data['Dish']['repeat']) && $this->request->data['Dish']['repeat']==1){
					if(!empty($this->request->data['Dish']['Day'])){
						foreach ($this->request->data['Dish']['Day'] as $key => $value) {
							if($value == 1)
								$repeat .= $key.',';
						}
					}
					else
					{
						$errors[][0] = "Please select day for repeat.";
					}
				}
				

				if(!empty($repeat)){
					$this->request->data['Dish']['repeat'] = trim($repeat,',');
				}

				if(isset($this->request->data['Dish']['serve_time'])){
					if(!empty($this->request->data['Dish']['serve_time'])){
						$serviceTimeArray = explode('-',str_replace(' ','',strtolower($this->request->data['Dish']['serve_time'])));
						if(isset($serviceTimeArray[1]) && ($serviceTimeArray[0] == $serviceTimeArray[1]))
							$errors[][0] = "Service start time and end time could not be equal.";
						else{
							$this->request->data['Dish']['serve_start_time'] = $serviceTimeArray[0];
							$this->request->data['Dish']['serve_end_time'] = $serviceTimeArray[1];
						}
					}
					else
					{
						$errors[][0] = "Please select service time span.";
					}
				}

				if(!isset($this->request->data['Dish']['lead_time']) || empty($this->request->data['Dish']['lead_time']))
				{
					$this->request->data['Dish']['lead_time'] = 0;
					/*$errors[][0] = "Please select lead time span.";*/
				}

				$this->Dish->set($this->request->data);
				if($this->Dish->validates($this->request->data) && empty($errors)){
					$this->Dish->create();
					if($this->Dish->saveAll($this->request->data))
					{
						$this->loadModel('ActivityLog');
						//Update Activity Log For Create Dish Activity
						$this->ActivityLog->updateLog($userId,3,$this->Dish->getLastInsertID(),time());
				
						/*
						Upload Kitchen photos and save this to Upload Image database.
						*/
						$uploadData = array();
						if(isset($dishData['UploadImage']['name'][0]['name']) && !empty($dishData['UploadImage']['name'][0]['name']))
						{
							foreach ($dishData['UploadImage']['name'] as $key => $value)
							{
								$ext = substr(strtolower(strrchr($value['name'], '.')), 1);
								$FileName = str_replace(' ', '-', $dishData['Dish']['name']).mt_rand().'-'.time().'.'.$ext;
								
								move_uploaded_file($value['tmp_name'],DISH_IMAGE_PATH.$FileName);
								
								// store the filename in the array to be saved to the db
								$uploadData['UploadImage'][$key]['name'] = $FileName;
								$uploadData['UploadImage'][$key]['type'] = 'dish';
								$uploadData['UploadImage'][$key]['related_id'] = $this->Dish->getLastInsertID();
							}
						}

						/*
						Save all kitchen images to database
						*/
						if(!empty($uploadData)){
							$this->loadModel('UploadImage');
							$this->UploadImage->saveAll($uploadData['UploadImage']);
						}

						$this->Session->setFlash(__('The Dish has been saved.'),'success');
						return $this->redirect(array('action' => 'list_dishes'));
					}
					else
					{
						$this->Session->setFlash(__('The Dish could not be saved. Please, try again.'),'error');
					}
				}
				else{
					$error = $this->Dish->validationErrors;
					if(!empty($error))
						$errors = array_merge($error,$errors);
					
					$this->set('errors',$errors);
				}
			}
			$this->set(compact(array('allergies','portions','cuisines')));
		}

	/*
	 * Method	: add Dish
	 * Author	: Bharat Borana
	 * Created	: 08 Jan, 2015
	 * @Add/Edit dishes
	 */
		public function edit($id=null)
		{
			$this->loadModel('Kitchen');
			$this->loadModel('Dish');
			$userId = $this->Auth->user('id'); 
			if(empty($id))
			{
				return $this->redirect(array('controller'=>'dishes','action'=>'add'));	
			}
			
			$this->Kitchen->recursive = -1;
			$kitchen = $this->Kitchen->findByUserId($userId);
			
			if(empty($kitchen))
			{
				return $this->redirect(array('controller'=>'kitchens','action'=>'activate_now'));
			}


			$this->loadModel('Allergy');
			$allergies = $this->Allergy->find('list',array('conditions'=>array('Allergy.is_active'=>1)));
			
			$this->loadModel('Portion');
			$portions = $this->Portion->find('all');
			
			$this->loadModel('Cuisine');
			$cuisines = $this->Cuisine->find('list',array('fields'=>array('Cuisine.name','Cuisine.name'),array('conditions'=>array('is_active'=>1))));
			
			$options = array('conditions' => array('Dish.id' => $id), 'joins'=>array(array('table'=>'kitchens','alias'=>'UserKitchen','type'=>'inner','conditions'=>array('Dish.kitchen_id = UserKitchen.id','UserKitchen.user_id ='.$userId))));
			$dishDetails = $this->Dish->find('first', $options);
			
			if($this->request->is(array('post', 'put')))
			{
				$errors = array(); 
				$pageError = 0;
				$dishData = $this->request->data;
				$this->request->data['Dish']['id'] = $id;

				unset($this->request->data['UploadImage']);
				
				$this->request->data['Dish']['kitchen_id'] = $kitchen['Kitchen']['id'];
				if(!empty($this->request->data['Dish']['p_custom']) && $this->request->data['Dish']['p_custom']==1)
				{
					if(empty($this->request->data['Dish']['p_custom_quantity']))
					{
						$this->request->data['Dish']['p_custom_quantity'] = 1;
					}
					$this->request->data['Dish']['is_custom_price_active'] = 0;	
				}

				if(!isset($this->request->data['Dish']['p_small']))
				{
					$this->request->data['Dish']['p_small'] = 0;
				}
				if(!isset($this->request->data['Dish']['p_big']))
				{
					$this->request->data['Dish']['p_big'] = 0;
				}
				if(!isset($this->request->data['Dish']['p_small']) && !isset($this->request->data['Dish']['p_big']) && !isset($this->request->data['Dish']['p_custom']))
					$errors[][0] = "Please select atleast one portion.";
					
				if(isset($this->request->data['Dish']['p_small']) && !empty($this->request->data['Dish']['p_small']) && $this->request->data['Dish']['p_small']==1 && empty($this->request->data['Dish']['p_small_quantity']))
					$this->request->data['Dish']['p_small_quantity'] = 1;
				
				if(isset($this->request->data['Dish']['p_big']) &&!empty($this->request->data['Dish']['p_big']) && $this->request->data['Dish']['p_big']==1 && empty($this->request->data['Dish']['p_big_quantity']))
					$this->request->data['Dish']['p_big_quantity'] = 1;
			
				if(isset($this->request->data['Dish']['diet']) && !empty($this->request->data['Dish']['diet'])){
					$this->request->data['Dish']['diet'] = key($this->request->data['Dish']['diet']);
				}

				$allergy = '';
				if(isset($this->request->data['Dish']['allergens']) && !empty($this->request->data['Dish']['allergens'])){
					foreach ($this->request->data['Dish']['allergens'] as $key => $value) {
						if($key =='other' && $value == 1 && !empty($this->request->data['Dish']['other_allergy_text'])){
							$otherAllergy['is_active'] = 0;
							$otherAllergy['name'] = $this->request->data['Dish']['other_allergy_text']; 
							$this->Allergy->create();
							if($this->Allergy->save($otherAllergy)){
								$allergy .= $this->request->data['Kitchen']['other_allergy_text'].'::::::::';
							}
						}else{
							if($value==1)
								$allergy .= $key.'::::::::';
						}
					}
				}

				
				if(!empty($allergy)){
					$this->request->data['Dish']['allergens'] = trim($allergy,'::::::::');
				}

				$repeat = '';
				if(isset($this->request->data['Dish']['repeat']) && $this->request->data['Dish']['repeat']==1){
					if(!empty($this->request->data['Dish']['Day'])){
						foreach ($this->request->data['Dish']['Day'] as $key => $value) {
							if($value == 1)
								$repeat .= $key.',';
						}
					}
					else
					{
						$errors[][0] = "Please select day for repeat.";
						$pageError = 1;
					}
				}
				

				if(!empty($repeat)){
					$this->request->data['Dish']['repeat'] = trim($repeat,',');
				}

				if(isset($this->request->data['Dish']['serve_time'])){
					if(!empty($this->request->data['Dish']['serve_time'])){
						$serviceTimeArray = explode('-',str_replace(' ','',strtolower($this->request->data['Dish']['serve_time'])));
						if(isset($serviceTimeArray[1]) && ($serviceTimeArray[0] == $serviceTimeArray[1]))
						{
							$errors[][0] = "Service start time and end time could not be equal.";
							$pageError = 1;
						}
						else{
							$this->request->data['Dish']['serve_start_time'] = $serviceTimeArray[0];
							$this->request->data['Dish']['serve_end_time'] = $serviceTimeArray[1];
						}
					}
					else
					{
						$errors[][0] = "Please select service time span.";
						$pageError = 1;
					}
				}

				if(!isset($this->request->data['Dish']['lead_time']))
				{
					$errors[][0] = "Please select lead time span.";
					$pageError = 1;
				}

				$this->Dish->set($this->request->data);
				if($this->Dish->validates($this->request->data) && empty($errors)){
					if($this->Dish->saveAll($this->request->data))
					{
						if($dishDetails['Dish']['status'] != $dishData['Dish']['status'])
						{
							$data['timestamp'] = time();
							$this->loadModel('ActivityLog');
							if($dishData['Dish']['status'] == 'off')
								$this->ActivityLog->updateLog($userId,5,$id,$data['timestamp']);
							else
								$this->ActivityLog->updateLog($userId,4,$id,$data['timestamp']);
						}

						/*
						Upload Kitchen photos and save this to Upload Image database.
						*/
						$uploadData = array();
						if(isset($dishData['UploadImage']['name'][0]['name']) && !empty($dishData['UploadImage']['name'][0]['name']))
						{
							foreach ($dishData['UploadImage']['name'] as $key => $value)
							{
								$ext = substr(strtolower(strrchr($value['name'], '.')), 1);
								$FileName = str_replace(' ', '-', $dishData['Dish']['name']).mt_rand().'-'.time().'.'.$ext;
								
								move_uploaded_file($value['tmp_name'],DISH_IMAGE_PATH.$FileName);
								
								// store the filename in the array to be saved to the db
								$uploadData['UploadImage'][$key]['name'] = $FileName;
								$uploadData['UploadImage'][$key]['type'] = 'dish';
								$uploadData['UploadImage'][$key]['related_id'] = $id;
							}
						}

						/*
						Save all kitchen images to database
						*/
						if(!empty($uploadData)){
							$this->loadModel('UploadImage');
							$this->UploadImage->saveAll($uploadData['UploadImage']);
						}

						$this->Session->setFlash(__('The Dish has been saved.'),'success');
						return $this->redirect(array('action' => 'list_dishes'));
					}
					else
					{
						$this->Session->setFlash(__('The Dish could not be saved. Please, try again.'),'success');
					}
				}
				else{
					$error = $this->Dish->validationErrors;
					if(!empty($error))
						$errors = array_merge($error,$errors);
					
					$this->set('errors',$errors);
					$this->set('pageError',$pageError);
				}
			}
			else
			{
				$this->request->data = $dishDetails;
			}
			$this->set(compact(array('allergies','portions','cuisines')));
		}

		/**
		 * Method	: mydishes_list
		 * Author	: Bharat Borana
		 * Created	: 08 Jan, 2015
		 */
		 public function list_dishes()
		 {
		 	$userId = $this->Auth->user('id');
		 	if(empty($userId))
		 		throw new NotFoundException(__('Invalid User Id'));
		 	else
			{
				$this->loadModel('Kitchen');
				$kitchen = $this->Kitchen->find('first',array('conditions' => array('Kitchen.user_id'=> $userId),'recursive'=>-1));
				if(empty($kitchen)){
					$this->redirect(array('controller'=>'kitchens','action'=>'activate_now'));
				}
				$kitchen_id = $kitchen['Kitchen']['id'];
				$this->loadModel('Dish');
				$dishes = $this->Dish->find('all',array(
											'conditions'=> array('Dish.kitchen_id'=>$kitchen_id),
											'fields' => array('Dish.*')
											));
			}
			$this->set('dishes',$dishes);
		 }

	/**
	 * Method	: activate_dish
	 * Author	: Bharat Borana
	 * Created	: 08 Jan, 2015
	 */
	 	public function active_dish()
		{
			$this->autoRender = false;
			$result = 0;
			$userId = $this->Auth->user('id');
			if($this->request->is('ajax'))
			{
				$this->Dish->bindModel(array('belongsTo'=>array('Kitchen')));
			
				$dish = $this->Dish->find('first', array(
												'conditions' => array('Dish.id'=>$this->request->data['id']),
												'fields' => array('Dish.id','Dish.status','Kitchen.id','Kitchen.user_id')
													)
										);
				if(!empty($dish))
				{
					if($dish['Kitchen']['user_id'] == $userId)
					{
						if($dish['Dish']['status'] == 'off')
							$freshData = "on";
						else
							$freshData = "off";
						
						if($this->Dish->updateAll(array('Dish.status' => "'".$freshData."'"),array('Dish.id'=>$this->request->data['id']))){
							$result = 1;
							//Update Activity Log
							$this->loadModel('ActivityLog');
							$data['timestamp'] = time();
							if($freshData == 'on')
							{
								//Update Activity Log For Dish Activation Activity
								$this->ActivityLog->updateLog($userId,4,$dish['Dish']['id'],$data['timestamp']);
							}else{
								//Update Activity Log For Dish Offline Activity
								$this->ActivityLog->updateLog($userId,5,$dish['Dish']['id'],$data['timestamp']);
							}
						}
					}
				}
			}
			echo $result;
		}

	/**
	 * Method	: mydish details
	 * Author	: Bharat Borana
	 * Created	: 08 Jan, 2015
	 */
	 	public function details()
		{
			//$this->_isValidApiRequest();
			header('Content-Type: application/json');  
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
					if($result['Dish']['is_custom_price_active'] == 0)
					{
						$result['Dish']['p_custom'] = '';
						$result['Dish']['p_custom_price'] = '';
						$result['Dish']['p_custom_quantity'] = '';
						$result['Dish']['p_custom_desc'] = '';
						$result['Dish']['p_custom_unit'] = '';
					}
					$result['Dish']['allergens'] = explode('::::::::', $result['Dish']['allergens']);
					$result['Dish']['repeat'] = explode(',', $result['Dish']['repeat']);
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
	 * Method	: dismiss activity log
	 * Author	: Bharat Borana
	 * Created	: 15 Jan, 2015
	 */
 	public function dismiss_alert()
	{
		$this->autoRender = false;
		$result = 0;
		$userId = $this->Auth->user('id');
		if($this->request->is('ajax'))
		{
			$this->loadModel('ActivityLog');
			$activityDetails = $this->ActivityLog->find('first',array('conditions'=>array('ActivityLog.id'=>$this->request->data['id'],'ActivityLog.user_id'=>$userId)));		
			if(!empty($activityDetails) && $activityDetails['ActivityLog']['status']==1)
			{
				if($this->ActivityLog->updateAll(array('ActivityLog.status' => "'0'"),array('ActivityLog.id'=>$this->request->data['id']))){
					$result = 1;
				}
			}
		}
		echo $result;
	}	

	 /**
	 * Method	: admin_view
	 * Author	: Bharat Borana
	 * Created	: 03 Feb, 2015
	 * Purpose	: Details of particular kitchens
	 */ 
	 public function admin_view($id,$orderId=null)
	 {
	 	$this->request->data = $this->Dish->findById($id);	
	 	$this->loadModel('Allergy');
		$this->loadModel('Cuisine');
		$allergy = $this->Allergy->find('list', array('fields'=> array('name','name')));
		$cuisine = $this->Cuisine->find('list', array('fields'=> array('name','name')));
		if(isset($orderId) && !empty($orderId))
		{
			$this->set('orderId',$orderId);
		}
		$this->set(compact('allergy','cuisine'));
	 }
	 
	/**
	 * Method	: admin_edit
	 * Author	: Bharat Borana
	 * Created	: 03 Feb, 2015
	 * Purpose	: edit kitchen 
	 */
	public function admin_edit($id)
	{
		$this->loadModel('Allergy');
		$allergies = $this->Allergy->find('list',array('conditions'=>array('Allergy.is_active'=>1)));
		
		$this->loadModel('Portion');
		$portions = $this->Portion->find('all');
		
		$this->loadModel('Cuisine');
		$cuisines = $this->Cuisine->find('list',array('fields'=>array('Cuisine.name','Cuisine.name'),array('conditions'=>array('is_active'=>1))));
		
		if($this->request->is(array('post', 'put'))){
			$errors = array(); 

			$dishData = $this->request->data;
			
			if(!empty($this->request->data['Dish']['p_custom']) && $this->request->data['Dish']['p_custom']==1)
			{
				if(empty($this->request->data['Dish']['p_custom_quantity']))
				{
					$errors[][0] = "Please enter offered quantity with customised portion.";
				}
				$this->request->data['Dish']['is_custom_price_active'] = 0;	
			}

			if(!isset($this->request->data['Dish']['p_small']) && !isset($this->request->data['Dish']['p_big']) && !isset($this->request->data['Dish']['p_custom']))
				$errors[][0] = "Please select atleast one portion.";
				
			if(isset($this->request->data['Dish']['p_small']) && !empty($this->request->data['Dish']['p_small']) && $this->request->data['Dish']['p_small']==1 && empty($this->request->data['Dish']['p_small_quantity']))
				$errors[][0] = "Please enter offered quantity with small portion.";
			
			if(isset($this->request->data['Dish']['p_big']) &&!empty($this->request->data['Dish']['p_big']) && $this->request->data['Dish']['p_big']==1 && empty($this->request->data['Dish']['p_big_quantity']))
				$errors[][0] = "Please enter offered quantity with big portion.";
		
			if(!empty($this->request->data['Dish']['allergens'])){
				$this->request->data['Dish']['allergens'] = implode('::::::::',$this->request->data['Dish']['allergens']);
			}

			$repeat = '';
			if(isset($this->request->data['Dish']['repeat']) && $this->request->data['Dish']['repeat']==1){
				if(!empty($this->request->data['Dish']['Day'])){
					foreach ($this->request->data['Dish']['Day'] as $key => $value) {
						if($value == 1)
							$repeat .= $key.',';
					}
				}
				else
				{
					$errors[][0] = "Please select day for repeat.";
				}
			}
			

			if(!empty($repeat)){
				$this->request->data['Dish']['repeat'] = trim($repeat,',');
			}

			if(isset($this->request->data['Dish']['serve_time'])){
				if(!empty($this->request->data['Dish']['serve_time'])){
					$serviceTimeArray = explode('-',str_replace(' ','',strtolower($this->request->data['Dish']['serve_time'])));
					if(isset($serviceTimeArray[1]) && ($serviceTimeArray[0] == $serviceTimeArray[1]))
						$errors[][0] = "Service start time and end time could not be equal.";
					else{
						$this->request->data['Dish']['serve_start_time'] = $serviceTimeArray[0];
						$this->request->data['Dish']['serve_end_time'] = $serviceTimeArray[1];
					}
				}
				else
				{
					$errors[][0] = "Please select service time span.";
				}
			}

			if(!isset($this->request->data['Dish']['lead_time']) || empty($this->request->data['Dish']['lead_time']))
			{
				$errors[][0] = "Please select lead time span.";
			}

			$this->Dish->set($this->request->data);
			if($this->Dish->validates($this->request->data) && empty($errors)){
				if($this->Dish->saveAll($this->request->data))
				{
					$this->Session->setFlash(__('The Dish has been saved.'),'success');
					return $this->redirect(array('action' => 'dish_list',$this->request->data['Dish']['kitchen_id']));
				}
				else
				{
					$this->Session->setFlash(__('The Dish could not be saved. Please, try again.'),'error');
				}
			}
			else{
				$error = $this->Dish->validationErrors;
				if(!empty($error))
					$errors = array_merge($error,$errors);
				
				$this->set('errors',$errors);
			}
		}
		else
		{
			$this->request->data = $this->Dish->findById($id);
		}
		$this->set(compact(array('allergies','portions','cuisines')));
	}
	 
}
