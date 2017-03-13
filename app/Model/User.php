<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 * @property Supplier $Supplier
 * @property Buyer $Buyer
 * @property Group $Group
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('CakeEmail', 'Network/Email');
class User extends AppModel {
	
	public $displayField = 'name';
    public $actsAs = array(
    	'Slug', 'Containable', 'ImageSave',
    	'Acl' => array('type' => 'requester')
	);

	public $containableArray = 	array('Activity'=>array(
											'fields'=>array('Activity.title','Activity.status'),
											'conditions'=>array('Activity.status'=>1),
										),
										'OrderPlaced'=>array(
											'fields'=>array('OrderPlaced.id','OrderPlaced.dine_type','OrderPlaced.amount','OrderPlaced.created','OrderPlaced.is_verified','OrderPlaced.delivery_time','OrderPlaced.delivery_date'),
											'OrderAddress',
											'OrderDish'=>array(
												'Kitchen'=>array(
													'fields'=>array('Kitchen.address','Kitchen.city','Kitchen.state','Kitchen.country','Kitchen.lat','Kitchen.lng','Kitchen.name'),
												)
											),
										),
										'OrderReceived'=>array(
											'fields'=>array('OrderReceived.id','OrderReceived.dine_type','OrderReceived.amount','OrderReceived.created','OrderReceived.is_verified','OrderReceived.delivery_time','OrderReceived.delivery_date'),
											'OrderAddress',
											'OrderDish'=>array(
												'Kitchen'=>array(
													'fields'=>array('Kitchen.address','Kitchen.city','Kitchen.state','Kitchen.country','Kitchen.lat','Kitchen.lng','Kitchen.name'),
												)
											),
											'User'=>array(
												'fields'=>array('User.id','User.name','User.image'),
												'Order'=>array(
													'fields'=>array('COUNT(id) as noOfPlacedOrders'),
												)
											),
										),
										'DishAdded'=>array(
											'fields'=>array('DishAdded.id','DishAdded.name'),
											'UploadImage'=>array(
												'fields'=>array('UploadImage.name'),
												'order'=>'UploadImage.id DESC',
												'limit'=>array(0,1),
											)
										),
										'DishActive'=>array(
											'fields'=>array('DishActive.id','DishActive.name'),
											'UploadImage'=>array(
												'fields'=>array('UploadImage.name'),
												'order'=>'UploadImage.id DESC',
												'limit'=>array(0,1),
											)
										),
										'DishOffline'=>array(
											'fields'=>array('DishOffline.id','DishOffline.name'),
											'UploadImage'=>array(
												'fields'=>array('UploadImage.name'),
												'order'=>'UploadImage.id DESC',
												'limit'=>array(0,1),
											)
										),
										'Conversation'=>array(
											'ConversationReply'=>array(
												'fields'=>array('reply','user_id'),
												'User'=>array(
													'fields'=>array('User.id','User.name','User.image'),
												),
												'order'=>'ConversationReply.created DESC',
												'limit'=>array(0,1),
											)
										),
										'RequestAnswer'=>array(
											'fields'=>array('RequestAnswer.*'),
											'Request'=>array(
												'fields'=>array('Request.dish_name'),
											),
											'Dish'=>array(
												'fields'=>array('Dish.name'),
												'Kitchen'=>array(
													'fields'=>array('Kitchen.name'),
												)
											),
										),
										'order'=>'ActivityLog.modified DESC');
								
    public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        } else {
            return array('Group' => array('id' => $groupId));
        }
    }
/**
 * Before save method calls everytime beforive saving to user model
 *
 */	
	public function beforeSave($options = array())
	{
	    if (isset($this->data[$this->alias]['password']))
	    {
	        $passwordHasher = new SimplePasswordHasher();
	        $this->data[$this->alias]['password'] = $passwordHasher->hash(
	            $this->data[$this->alias]['password']
	        );
	    }
		/*if(!isset($this->data[$this->alias]['group_id']))
		{
			 $this->data[$this->alias]['group_id'] = NORMAL_USER;
		}*/
	    return true;
	}
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'supplier_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'buyer_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'Username already exists,Please choose another'
			)
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'confirm_password' => array(
		    'compare'    => array(
		        'rule'      => array('validate_passwords'),
		        'message' => 'The passwords you entered do not match.',
		    )
		),
		'name' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter you name.',
			),
		),
		'phone' => array(
		     'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter you phone number.',
			),
		    'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'Phone number already exists, Please choose another'
			)
		),
		'city_id' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select your city.',
			),
		),
		'state_id' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select your state.',
			),
		),
		'zipcode' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter zipcode.',
			),
		),
		/*'bank_acc_no' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please add you bank account number.',
			),
		),
		'bank_routing_no' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please add your bank routing number',
			),
		),
		'bank_acc_holdername' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter your bank account holder name.',
			),
		),
		'bank_acc_type' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select your bank account type.',
			),
		),*/
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'Email already exists, Please choose another'
			)
		),
		'group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
   public $hasOne = array(
		'Kitchen' => array(
			'className' => 'Kitchen',
			'dependent' => false,
		),
		'BalancedpaymentDetail' => array(
			'className' => 'BalancedpaymentDetail',
			'dependent' => false,
		));
	
	public $hasMany = array(
		'Order' => array(
			'className' => 'Order',
			'dependent' => false,
		),
		'MessageSent' => array(
			'className' => 'Conversation',
			'foreignKey' => 'sender_id',
			'dependent' => false,
		),
		'MessageReceived' => array(
			'className' => 'Conversation',
			'foreignKey' => 'receiver_id',
			'dependent' => false,
		),
		'ActivityLog' => array(
			'className' => 'ActivityLog',
			'foreignKey' => 'user_id',
			'dependent' => false,
		));
			
	public $belongsTo = array(
		'Supplier' => array(
			'className' => 'Supplier',
			'foreignKey' => 'supplier_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Buyer' => array(
			'className' => 'Buyer',
			'foreignKey' => 'buyer_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
  	public function validate_passwords() {
		
	    return $this->data[$this->alias]['password'] === $this->data[$this->alias]['confirm_password'];
	}
	
	/*
	 * Method	: createFromSocialProfile
	 * Author	: Bharat Borana
	 * Created	: 15 Dec, 2014
	 * @Search and create user data from social profile response
	 */
	public function createFromSocialProfile($incomingProfile){
	
		// check to ensure that we are not using an email that already exists
		$existingUser = $this->find('first', array(
			'conditions' => array('email' => $incomingProfile['SocialProfile']['email'])));
		
		if($existingUser){
			// this email address is already associated to a member
			return $existingUser;
		}
		
		// brand new user
		$socialUser['User']['email'] = $incomingProfile['SocialProfile']['email'];
		$socialUser['User']['image'] = $incomingProfile['SocialProfile']['image'];
		$socialUser['User']['username'] = str_replace(' ', '_',$incomingProfile['SocialProfile']['display_name']);
		$socialUser['User']['role'] = NORMAL_USER; // by default all social logins will have a role of bishop
		$socialUser['User']['password'] = date('Y-m-d h:i:s'); // although it technically means nothing, we still need a password for social. setting it to something random like the current time..
		$socialUser['User']['created'] = date('Y-m-d h:i:s');
		$socialUser['User']['modified'] = date('Y-m-d h:i:s');
		
		// save and store our ID
		$this->save($socialUser);
		$socialUser['User']['id'] = $this->id;
		
		return $socialUser;
	}
	
	
	/*
	 * Method	: getDetailedUserData
	 * Author	: Bharat Borana
	 * Created	: 31 Dec, 2014
	 * @Retrieve detailed user data including kitchendata and order data 
	 */
	public function getAllActivities($data = null, $limit = 15){
		$userDetails = array(); 
		if(!empty($data['user_id'])){
			$offset = (!empty($data['offset'])) ? $data['offset'] : 0;
			//$this->containableArray['Conversation']['Conversation']['conditions'] = array('Conversation.receiver_id'=>$data['user_id']);
			if(isset($data['last_fetch']) && !empty($data['last_fetch'])){
					$this->containableArray['conditions'] = array('ActivityLog.created >'=>$data['last_fetch'],'ActivityLog.status'=>1);
				}else{

					$this->containableArray['limit'] = $limit;
					$this->containableArray['offset'] = $offset;
					$this->containableArray['conditions'] = array('ActivityLog.status'=>1);
			   }

			$userActivities = $this->find('first',array('conditions'=>array('User.id'=>$data['user_id']),
														'fields'=>array('id','name','description','image','phone','address'),
														'contain'=>array(
															'Kitchen'=>array(
																'fields'=>array('id','name','avg_rating','description','cover_photo','address', 'lat', 'lng'),
																'Dish' => array(
																	'fields'=>array('COUNT(Dish.id) as activeDish'),
																	'conditions'=>array('Dish.status'=>'on'),
																 ),
															),
															'ActivityLog'=>$this->containableArray,
														),
														));
																
			if(!empty($userActivities)){
				if(isset($userActivities['User']['image']))
					$userActivities['User']['image']  = (!empty($userActivities['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$userActivities['User']['image'],true) : "";
						
				$userDetails['User'] = $userActivities['User'];
				
				if(isset($userActivities['Kitchen']['cover_photo']))
							$userActivities['Kitchen']['cover_photo']  = (!empty($userActivities['Kitchen']['cover_photo'])) ? Router::url('/'.KITCHEN_IMAGE_URL.$userActivities['Kitchen']['cover_photo'],true) : "";
				
				$userDetails['Kitchen'] = $userActivities['Kitchen'];
				if(isset($userActivities['Kitchen']['Dish'][0]['Dish'][0]['activeDish'])){
					$userDetails['Kitchen']['activeDish'] = $userActivities['Kitchen']['Dish'][0]['Dish'][0]['activeDish'];
					unset($userDetails['Kitchen']['Dish']);
				}
				$finalDashboardData = array();
				foreach($userActivities['ActivityLog'] as $actKey => $actData){ 
					if(isset($actData['Activity']) && !empty($actData['Activity'])){
						$rowData = array();
						$rowData['activitylog_id'] = $actData['id'];
						$rowData['activity_title'] = $actData['Activity']['title'];
						$rowData['activity_id'] = $actData['activity_id'];
						$rowData['modified'] = $actData['modified'];
						$rowData['timestamp'] = $actData['timestamp'];
						if($actData['activity_id']==1)
						{
							$rowData['data'] = $actData['OrderPlaced'];
						}
						else if($actData['activity_id']==2){
							if(isset($actData['OrderReceived']['User']['image']))
							$actData['OrderReceived']['User']['image']  = (!empty($actData['OrderReceived']['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$actData['OrderReceived']['User']['image'],true) : "";
						
							if(empty($actData['OrderReceived']['User']['Kitchen']))
								$actData['OrderReceived']['User']['Kitchen'] = new ArrayObject();

							if(isset($actData['OrderReceived']['User']['ActivityLog']))
								unset($actData['OrderReceived']['User']['ActivityLog']);
							
							$rowData['data'] = $actData['OrderReceived'];
						}
						else if($actData['activity_id']==3){
							if(isset($actData['DishAdded']['UploadImage'][0]['name']))
							$actData['DishAdded']['UploadImage'][0]['name']  = (!empty($actData['DishAdded']['UploadImage'][0]['name'])) ? Router::url('/'.DISH_IMAGE_URL.$actData['DishAdded']['UploadImage'][0]['name'],true) : "";
						
							$rowData['data'] = $actData['DishAdded'];
						}
						else if($actData['activity_id']==4){
							if(isset($actData['DishAdded']['UploadImage'][0]['name']))
							$actData['DishActive']['UploadImage'][0]['name']  = (!empty($actData['DishActive']['UploadImage'][0]['name'])) ? Router::url('/'.DISH_IMAGE_URL.$actData['DishActive']['UploadImage'][0]['name'],true) : "";
						
							$rowData['data'] = $actData['DishActive'];
						}
						else if($actData['activity_id']==5){
							if(isset($actData['DishAdded']['UploadImage'][0]['name']))
							$actData['DishOffline']['UploadImage'][0]['name']  = (!empty($actData['DishOffline']['UploadImage'][0]['name'])) ? Router::url('/'.DISH_IMAGE_URL.$actData['DishOffline']['UploadImage'][0]['name'],true) : "";
						
							$rowData['data'] = $actData['DishOffline'];
						}
						else if($actData['activity_id']==6){
							if(isset($actData['Conversation']['ConversationReply'][0]['user_id']) && $actData['Conversation']['ConversationReply'][0]['user_id']!=$data['user_id'])
							{
								if($actData['Conversation']['sender_id']!=$data['user_id'])
									$actData['Conversation']['chat_with']  = $actData['Conversation']['sender_id'];
								else
									$actData['Conversation']['chat_with']  = $actData['Conversation']['receiver_id'];

								if(isset($actData['Conversation']['ConversationReply'][0]['User']['image']) && !empty($actData['Conversation']['ConversationReply'][0]['User']['image']))
									$actData['Conversation']['ConversationReply'][0]['User']['image']  = (!empty($actData['Conversation']['ConversationReply'][0]['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$actData['Conversation']['ConversationReply'][0]['User']['image'],true) : "";
								
								$rowData['data'] = $actData['Conversation'];
							}
							else
								$rowData['data'] = '';
						}
						else if($actData['activity_id']==7){
							if(isset($actData['RequestAnswer']['Request']) && empty($actData['RequestAnswer']['Request']))
								$actData['RequestAnswer']['Request']['dish_name'] = 'test';
							$rowData['data'] = $actData['RequestAnswer'];
						}
						
						if(isset($rowData['data']) && !empty($rowData['data']))
							$finalDashboardData[] = $rowData;
					}
				}
				$userDetails['ActivityLog'] = $finalDashboardData;
			}
		}
		return $userDetails;
	}
	
	/*
	 * Method	: getUserCountData
	 * Author	: Bharat Borana
	 * Created	: 31 Dec, 2014
	 * @Retrieve counted user data including kitchendata and order data 
	 */
	public function getUserCountData($userId = null){
		if(!empty($userId)){
			$userDetails = $this->find('first',array('conditions'=>array('User.id'=>$userId),
																'fields'=>array('id','name','description','image','phone','address'),
																'contain'=>array(
																	'Kitchen'=>array('fields'=>array('id','name','description'),
																					'Dish'=>array('fields'=>array('COUNT(id) as noOfDishes')),
																					'OrderDish' => array(
																							'fields' => array('CONCAT(GROUP_CONCAT(DISTINCT(OrderDish.order_id))) as oIds, COUNT(DISTINCT(OrderDish.order_id)) as totalOrder'),
																						)
																					),
																	'Order'=>array(
																		'fields'=>array('COUNT(id) as noOfPlacedOrders'),
																	)
																))); 
			return $userDetails;
		}
	}
	
}?>