<?php
App::uses('AppController', 'Controller');
/**
 * Kitchens Controller
 *
 * @property Kitchen $Kitchens
 * @property PaginatorComponent $Paginator
 */
class KitchensController extends AppController {


	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(array('index'));
	}
	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator','SuiteTest','Paypal');
	var $helpers = array('Image'); 


	/**
	 * Method	: Kitchen details page
	 * Author	: Bharat Borana
	 * Created	: 30 Dec, 2014
	 */
	public function index($kitchenId=null) {
		$this->loadModel('Kitchen');
		$this->loadModel('User');
		$userId = $this->Auth->user('id');
		$userDetails = array();
		if(!empty($userId))
			$userDetails = 	$this->User->findById($userId);

		$this->loadModel('PaymentSetting');
		$paymentSettings = $this->PaymentSetting->find('first');
		$this->set('api_key',STRIPE_PUBLISHABLE_KEY);		
		if(empty($kitchenId))
		{
			$this->Kitchen->recursive = -1;
		    $kitchen = $this->Kitchen->findByUserId($userId);
			if(empty($kitchen) && !empty($userId))
				return $this->redirect(array('action' => 'activate_now'));
			else if(empty($kitchen))
				throw new NotFoundException(__('Invalid Kitchen Id'));
			else
				$kitchenId = $kitchen['Kitchen']['id'];
		}

		if($this->request->is(array('post','put'))){
			if(!isset($userDetails['User']['id']) || empty($userDetails['User']['id']))
			{
				$this->Session->write('redir',array('controller'=>'kitchens','action'=>'index',$kitchenId));
				$this->set('clickLogin',1);
			}
			else
			{
				$orderDish = array();
				if(isset($this->request->data['OrderDish']) && !empty($this->request->data['OrderDish'])){
					$this->loadModel('Dish');
					$this->Kitchen->recursive = 2;
		   			$kitchenForOrderSummery = $this->Kitchen->findById($kitchenId);
		   			
		   			$this->loadModel('State');
		   			$states = $this->State->find('list',array('conditions'=>array('country_id'=>223), 'fields' => array('State.name', 'State.name')));
		   			
		   			$this->loadModel('PaymentMethod');
					$paymentDetails = $this->PaymentMethod->find('all',array('conditions'=>array('PaymentMethod.user_id'=>$userId)));
					
		   			if(isset($userDetails['User']['address']) && !empty($userDetails['User']['address']))
			            $addressLat = $this->SuiteTest->_getAddressFormGeoode($userDetails['User']['address']);
			        else if(isset($userDetails['Kitchen']['address']) && !empty($userDetails['Kitchen']['address']))
			    	    $addressLat = $this->SuiteTest->_getAddressFormGeoode($userDetails['Kitchen']['address']);
			    	
			    	if(isset($addressLat) && !empty($addressLat))
		        	{
		        		$userDetails['User']['lat'] = $addressLat['lat'];
		    			$userDetails['User']['lng'] = $addressLat['lng'];
		        	}
		        	
			    	$this->set(compact(array('paymentDetails','states','userDetails','kitchenForOrderSummery','paymentSettings')));		
				
					foreach ($this->request->data['OrderDish'] as $key => $value)
					{ 
						if(isset($value['is_checked']) && !empty($value['is_checked']))
						{
							$DishDetails = $this->Dish->findById($value['dish_id']);
							$orderDish['Order'][] = array('OrderDish'=>$value, 'Dish'=>$DishDetails);
						}
					}

					if(isset($orderDish['Order']) && !empty($orderDish['Order']))
					{
						if($userDetails['User']['is_verified']==1)
						{
							$this->request->data = $orderDish;
					    	$this->render('/Orders/summery');
						}
						else
						{
							$this->Session->setFlash(__('Your mobile number is not verified, please verify to continue.'),'error');
							$this->set('openVerify',1);
						}
						
					}
					else
					{
						$errors[0][0] = 'Please select at least one Dish.';
						$this->set("errors",$errors);
					}	
				}
			}
		}
		else
		{
			$previousSearch = $this->Session->read('order_checkout');
			if(isset($previousSearch) && !empty($previousSearch))
			{
				$this->request->data['OrderDish'] = $previousSearch['OrderDish'];
			}
		}
		

		$search_data = $this->Session->read('search_data');
		
		if(isset($userDetails['Kitchen']['id']) && $userDetails['Kitchen']['id']==$kitchenId)
		{
			$this->Kitchen->recursive = 2;
			$this->Kitchen->bindModel(array(
								'hasMany'=>array(
											'Review'=>array(
												'className' => 'Review',
												'belongsTo'=>array(
													'User'=>array(
														'className' => 'User',
														'fields' => array('User.id','User.name','User.image'),
											)		
											)))));

			$kitchenDetails = $this->Kitchen->findById($kitchenId);
		}
		else
		{
			$search_data['kitchen_id'] = $kitchenId;
			$this->Kitchen->recursive = 2;
			$this->Kitchen->bindModel(array(
								'hasMany'=>array(
											'Review'=>array(
												'className' => 'Review',
												'belongsTo'=>array(
													'User'=>array(
														'className' => 'User',
														'fields' => array('User.id','User.name','User.image'),
											)		
											)))));
			$kitchenDetails = $this->Kitchen->kitchenDashboard($this, $search_data);
		}
		
		$kitchenDetails['is_order_placed_for_kitchen'] = 0;
		if(isset($userId) && !empty($userId))
		{
			$this->loadModel('OrderDish');
			$countCheckForUser = $this->OrderDish->find('count',array('conditions'=>array('OrderDish.kitchen_id'=>$kitchenId, 'Order.user_id'=>$userId)));
			if($countCheckForUser)
			{
				$kitchenDetails['is_order_placed_for_kitchen'] = 1;
			}
		}

		$this->set(compact(array('userDetails','kitchenDetails','paymentSettings','search_data')));
	}

/**
 * Method	: Kitchen Activate page
 * Author	: Bharat Borana
 * Created	: 30 Dec, 2014
 */
	public function activate_now() {

	}



	/**
	 * add method
	 *
	 * @return void
	 */
	public function edit() {
		$userId = $this->Auth->user('id');
		// Encrypt some data.
		$this->loadModel('User');
		$encrypted =  $this->Paypal->encrypt($userId);
		$userNamenEmail = $this->User->findById($userId);
		
		if($this->request->is(array('post', 'put', 'ajax'))){ 
			
			if(isset($userNamenEmail['Kitchen']) && !empty($userNamenEmail['Kitchen'])){
				$this->request->data['Kitchen']['id'] = $userNamenEmail['Kitchen']['id'];				
			}
			
			/*
			if(!isset($this->request->data['Kitchen']['dining_dine_in']))
				$this->request->data['Kitchen']['dining_dine_in'] = 0;

			if(!isset($this->request->data['Kitchen']['dining_take_out']))
				$this->request->data['Kitchen']['dining_take_out'] = 0;
			*/
			
			$this->request->data['Kitchen']['user_id'] = $userId;
			$this->request->data['User']['id'] = $userId;

			$kitchenData = $this->request->data;
			unset($this->request->data['Kitchen']['cover_photo']);
			unset($this->request->data['UploadImage']);
			$this->Kitchen->set($this->request->data);
			if ($this->Kitchen->validates()) {
				$recieveAccountInfo = 0;
					if(($this->request->data['User']['paypal_id'] != $userNamenEmail['User']['paypal_id']) || $userNamenEmail['User']['is_paypal_verified']==0)
					{
						$detailsAre['emailAddress'] = $this->request->data['User']['paypal_id'];
						$detailsAre['firstName'] = $this->request->data['User']['paypal_name'];
						$detailsAre['lastName'] = $this->request->data['User']['paypal_lname'];
						$detailsAre['matchCriteria'] = "NAME";
						$detailsAre['requestEnvelope.errorLanguage'] = "en_US"; 
						$detailsAre['requestEnvelope.detailLevel'] = "ReturnAll"; 

						$url = 'https://svcs.paypal.com/AdaptiveAccounts/GetVerifiedStatus';
						$getExpData = $this->Paypal->pay_me($detailsAre,$url);
						
						if(isset($getExpData->responseEnvelope->ack) && $getExpData->responseEnvelope->ack=='Success')
						{
							$paypalConfirmation['User']['id'] = $userId;
							$paypalConfirmation['User']['is_paypal_verified'] = 1;
							$paypalConfirmation['User']['paypal_name'] = 1;
							$paypalConfirmation['User']['paypal_name'] = $this->request->data['User']['paypal_name'];
							$paypalConfirmation['User']['paypal_lname'] = $this->request->data['User']['paypal_lname'];
							$this->User->save($paypalConfirmation);
							$userNamenEmail['User']['is_paypal_verified'] = 1;
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
							$userNamenEmail['User']['is_paypal_verified'] = 0;
						}
					}

					if($kitchenData['Kitchen']['status']=='Off' || (isset($userNamenEmail['User']['stripe_user_id']) && !empty($userNamenEmail['User']['stripe_user_id'])) || (isset($userNamenEmail['User']['is_paypal_verified']) && $userNamenEmail['User']['is_paypal_verified']==1))
					{
						if(!empty($this->request->data['Kitchen']['address']))
						{
							$geoAddress = $this->SuiteTest->_getAddressFormGeoode($this->request->data['Kitchen']['address']);
							
							$this->request->data['Kitchen']['city'] = (!empty($geoAddress['locality'])) ? $geoAddress['locality'] : "";
							$this->request->data['Kitchen']['state'] = (!empty($geoAddress['administrative_area_level_1'])) ? $geoAddress['administrative_area_level_1'] : "";
							$this->request->data['Kitchen']['country'] = (!empty($geoAddress['country'])) ? $geoAddress['country'] : "";
							if(empty($this->request->data['Kitchen']['lat']) || empty($this->request->data['Kitchen']['lng']))
							{
								$this->request->data['Kitchen']['lat'] = (!empty($geoAddress['lat'])) ? $geoAddress['lat'] : "";
								$this->request->data['Kitchen']['lng'] = (!empty($geoAddress['lng'])) ? $geoAddress['lng'] : "";
							}
						}
						if(empty($userNamenEmail['Kitchen'])){
							$this->Kitchen->create();
						}
						
					
						if($this->Kitchen->saveAll($this->request->data))
						{
							/*
							Upload Cover photo and save this to kitchen database.
							*/
							$kitchen = $this->Kitchen->find('first',array('conditions'=>array('Kitchen.user_id'=>$userId)));
							if(isset($kitchenData['Kitchen']['cover_photo']['tmp_name']) && !empty($kitchenData['Kitchen']['cover_photo']['tmp_name']))
							{
								$ext = substr(strtolower(strrchr($kitchenData['Kitchen']['cover_photo']['name'], '.')), 1);
								$FileName = mt_rand().'-'.time().'.'.$ext;
								
								move_uploaded_file($kitchenData['Kitchen']['cover_photo']['tmp_name'],KITCHEN_IMAGE_PATH.$FileName);
								
								// store the filename in the array to be saved to the db
								$this->Kitchen->set('id',$kitchen['Kitchen']['id']);
								if($this->Kitchen->saveField('cover_photo',$FileName)){
									if(!empty($userNamenEmail['Kitchen']['cover_photo']))
									unlink(KITCHEN_IMAGE_PATH.$userNamenEmail['Kitchen']['cover_photo']);
								}
							}

							/*
							Upload Kitchen photos and save this to Upload Image database.
							*/
							$uploadData = array();
							if(isset($kitchenData['UploadImage']['name'][0]['name']) && !empty($kitchenData['UploadImage']['name'][0]['name']))
							{
								foreach ($kitchenData['UploadImage']['name'] as $key => $value)
								{
									$ext = substr(strtolower(strrchr($value['name'], '.')), 1);
									$FileName = str_replace(' ', '-', $kitchenData['Kitchen']['name']).mt_rand().'-'.time().'.'.$ext;
									
									move_uploaded_file($value['tmp_name'],KITCHEN_IMAGE_PATH.$FileName);
									
									// store the filename in the array to be saved to the db
									$uploadData['UploadImage'][$key]['name'] = $FileName;
									$uploadData['UploadImage'][$key]['type'] = 'kitchen';
									$uploadData['UploadImage'][$key]['related_id'] = $kitchen['Kitchen']['id'];
								}
							}

							/*
							Save all kitchen images to database
							*/
							if(!empty($uploadData)){
								$this->loadModel('UploadImage');
								$this->UploadImage->saveAll($uploadData['UploadImage']);
							}

							if($this->request->is('ajax'))
							{
								echo 1;
								exit;
							}
							else
							{	
								$this->Session->setFlash(__('The Kitchen has been saved.'),'success');
								if(isset($kitchen) && empty($kitchen['Dish']))
									return $this->redirect(array('controller'=>'dishes','action' => 'list_dishes'));
								else
									return $this->redirect(array('action' => 'index'));
							}
						}
						else
						{
							$this->Session->setFlash(__('The Kitchen could not be saved. Please, try again.'),'error');
						}
					}
					else
					{ 
						$options = array('conditions' => array('Kitchen.user_id' => $userId));
						$this->request->data = $this->Kitchen->find('first', $options);
						$errors[] = array(0=>"Stripe connect or paypal account required to Set your kitchen On.");
						$this->set('errors',$errors);
					}	
			}else{
				$errors = $this->Kitchen->validationErrors;
				$this->set('errors',$errors);
			}
		}
		else
		{
			$options = array('conditions' => array('Kitchen.user_id' => $userId));
			$this->request->data = $this->Kitchen->find('first', $options);
		}
		$this->set(compact(array('userNamenEmail','encrypted')));
	}

	/**
	 * Method	: Kitchen's prefrences page
	 * Author	: Bharat Borana
	 * Created	: 06 Jan 2015
	 */
	public function prefrences() { 
			$this->loadModel('User');
			$userId = $this->Auth->user('id');
			$this->User->recursive = -1;
			$UserDetails = $this->User->findById($userId);
			
			$this->loadModel('Allergy');
			$allergies = $this->Allergy->find('list',array('conditions'=>array('Allergy.is_active'=>1)));
			
			if($this->request->is(array('post','put'))){
				$this->User->set('id',$UserDetails['User']['id']);
				$allergy = '';
				if(isset($this->request->data['User']['allergy']) && !empty($this->request->data['User']['allergy'])){
					foreach ($this->request->data['User']['allergy'] as $key => $value) {
						if($key =='other' && $value == 1 && !empty($this->request->data['User']['other_allergy_text'])){
							$otherAllergy['is_active'] = 0;
							$otherAllergy['name'] = $this->request->data['User']['other_allergy_text']; 
							$this->Allergy->create();
							if($this->Allergy->save($otherAllergy)){
								$allergy .= $this->request->data['User']['other_allergy_text'].'::::::::';
							}
						}else{
							if($value==1)
								$allergy .= $key.'::::::::';
						}
					}
				}
				$diets = '';
				if(isset($this->request->data['User']['diet']) && !empty($this->request->data['User']['diet'])){
					foreach ($this->request->data['User']['diet'] as $key => $value) {
						if($value==1)
							$diets = $key;
					}
				}

				if(!empty($allergy)){
					$this->User->set('allergy',trim($allergy,'::::::::'));
				}

				if(!empty($diets)){
					$this->User->set('diet',$diets);
				}

				if($this->User->save())
				{
					$this->Session->setFlash(__('Your prefrences has been successfully saved.'),'success');
				}
				else
				{
					$this->Session->setFlash(__('The prefrences could not be saved. Please, try again.'),'error');
				}
				$this->User->recursive = -1;
				$UserDetails = $this->User->findById($userId);
			}
			
			$this->set(compact(array('UserDetails','allergies')));
	}

	/**
	 * Method	: Uploaded image delete function
	 * Author	: Bharat Borana
	 * Created	: 07 Jan 2015
	 */
	public function deleteimage(){
		$this->autoRender = false;
		if($this->request->is('ajax')){
			$result = 0;
			$userId = $this->Auth->user('id');
			if(isset($this->request->data['id']) && !empty($this->request->data['id']) && !empty($userId)){
				$this->loadModel('UploadImage');
				if($this->request->data['type'] == 'kitchen'){
					$conditions = array('UploadImage.id'=>$this->request->data['id'],'UploadImage.type'=>$this->request->data['type']);
					
					$uploadedData = $this->UploadImage->find('first',array('conditions'=>$conditions, 'joins'=>array(array('table'=>'kitchens','alias'=>'UserKitchen','type'=>'inner','conditions'=>array('UploadImage.related_id = UserKitchen.id','UserKitchen.user_id ='.$userId)))));
					if(!empty($uploadedData)){
						$this->UploadImage->id = $uploadedData['UploadImage']['id'];
						if($this->UploadImage->delete()){
							if(!empty($uploadedData['UploadImage']['name']))
							unlink(KITCHEN_IMAGE_URL.$uploadedData['UploadImage']['name']);
							$result = 1;
						}
					}
				}
				else if($this->request->data['type'] == 'dish'){
					$conditions = array('UploadImage.id'=>$this->request->data['id'],'UploadImage.type'=>$this->request->data['type']);
					
					$uploadedData = $this->UploadImage->find('first',array(	'conditions'=>$conditions,
																			'joins'	=>	array(
																							array('table'=>'dishes','alias'=>'UserDish','type'=>'inner','conditions'=>array('UploadImage.related_id = UserDish.id')),
																							array('table'=>'kitchens','alias'=>'UserKitchen','type'=>'inner','conditions'=>array('UserDish.kitchen_id = UserKitchen.id','UserKitchen.user_id ='.$userId)))
																							));
					if(!empty($uploadedData)){
						$this->UploadImage->id = $uploadedData['UploadImage']['id'];
						if($this->UploadImage->delete()){
							if(!empty($uploadedData['UploadImage']['name']))
							unlink(DISH_IMAGE_URL.$uploadedData['UploadImage']['name']);
							$result = 1;
						}
					}
				}
			}
		}
		echo $result;
		exit;
	}	

	/**
	 * Method	: Kitchen feedback add system.
	 * Author	: Bharat Borana
	 * Created	: 13 Jan 2015
	 */
	public function addFeedback(){
		$this->layout = 'ajax';
		$userId = $this->Auth->user('id');
		if($this->request->is('ajax')){	
			$this->loadModel('Review');
			$review = $this->Review->find('first', array('conditions'=>array('Review.user_id' => $userId,'Review.kitchen_id'=>$this->request->data['Review']['kitchen_id'])));
			if(!empty($review))
			{
				$review['Review']['feedback'] = $this->request->data['Review']['feedback'];
				$review['Review']['rating'] = $this->request->data['Review']['rating'];
				$review['Review']['timestamp'] = time();
				$this->Review->set($review['Review']);
				$this->Review->save();
				$reviewId = $review['Review']['id'];
			}
			else {
				$this->Review->create();
				$this->request->data['Review']['user_id'] = $userId;
				$this->request->data['Review']['timestamp'] = time();
				$this->Review->save($this->request->data['Review']);
				$reviewId = $this->Review->getLastInsertId();
			}

			$this->Review->bindModel(array(
							'belongsTo'=>array(
										'User'=>array(
													'className' => 'User',
													'fields'=>	array('User.name','User.image')
										))));
			$reviewData = $this->Review->findById($reviewId);
			$this->set('reviewData',$reviewData);
	  	}
	}	

	/**
	 * Method	: admin_index
	 * Author	: Bharat Borana
	 * Created	: 03 Feb 2015
	 * Purpose	: List of all kitchens
	 */ 
	 public function admin_index()
	 {
	 	$this->paginate = array(
							'order' => 'Kitchen.created DESC'
		);
		$rows = $this->paginate('Kitchen');
		$this->set(compact('rows'));
	 }

	 /**
	 * Method	: admin_view
	 * Author	: Bharat Borana
	 * Created	: 03 Feb, 2015
	 * Purpose	: Details of particular kitchens
	 */ 
	 public function admin_view($id)
	 {
	 	$this->request->data = $this->Kitchen->findById($id);	
	 	$this->loadModel('Allergy');
		$this->loadModel('Cuisine');
		$allergy = $this->Allergy->find('list', array('fields'=> array('name','name')));
		$cuisine = $this->Cuisine->find('list', array('fields'=> array('name','name')));
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
		if($this->request->is('put'))
		{
			if(!empty($this->request->data['User']['allergy']))
			{
				$this->request->data['User']['allergy'] = implode('::::::::', $this->request->data['User']['allergy']);
			}

			$this->Kitchen->saveAssociated($this->request->data);
			$this->redirect(array('action' => 'index'));
		}
		$this->request->data = $this->Kitchen->find('first', array('conditions' => array('Kitchen.id' =>$id)));	
		
		if(!empty($this->request->data['User']['allergy']))
		{
			$this->request->data['User']['allergy'] = explode('::::::::', $this->request->data['User']['allergy']);
		}
		$this->loadModel('Allergy');
		$this->loadModel('Cuisine');
		$allergy = $this->Allergy->find('list', array('fields'=> array('name','name')));
		$cuisine = $this->Cuisine->find('list', array('fields'=> array('name','name')));
		$this->set(compact('allergy','cuisine'));
	}
}
