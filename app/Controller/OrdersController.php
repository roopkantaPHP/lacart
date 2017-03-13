<?php

App::uses('AppController', 'Controller');

class OrdersController extends AppController {

	public $uses = array();
	public $components = array('SuiteTest','Paypal','Email','Push','Stripe');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(array('paymypaypal','success','fail','mobile_redirect','saveMySession','testmail'));
	}

	/*
	 * Method	: admin_index page
	 * Author 	: Bharat Borana
	 * Created	: 02 Jan, 2015
	 * @List Order data from backend admin side.
	 */
    public function admin_index()
    {
    	$this->theme = "Admin";
    	$this->_admin_manage_orders();
	}

	function _admin_manage_orders()
	{
		$this->loadModel('Order');
		$status = "";
		$email = "";
		$o_from = "";
		$o_to = "";
		$pType = "";
		$sort = "Order.created";
		$order = "desc";
		$query = $this->request->query;		
		$qstring = "?action=import";		
		$login = '';
		if ($this->request->is("post"))
		{
			$request = $this->request->data;
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if (!empty($ids))
			{
				foreach ($ids as $id)
				{ 
					if ($action == "pay")
					{
						$orderPay = $this->Order->find('first', array(	'conditions'=>array('Order.id' => $id,'Order.payment_type' => 0),
														   			'recursive'=>0 ));
						if(!empty($orderPay))
						{
							if($orderPay['Order']['is_verified']==1){
								$this->loadModel('PaymentSetting');
								$admin_cutoff = $this->PaymentSetting->getProcessingfee();
								$pay_merchant = $this->SuiteTest->pay_merchant($orderPay['Order']['order_href'],$admin_cutoff);
								
								if(isset($pay_merchant['status']) && $pay_merchant['status']==1){
									$this->Order->id = $id;
									$this->Order->saveField('merchant_paid', 1);
									$this->Session->setFlash("Operation successful", 'success');
								}
								else
								{
									$this->Session->setFlash($pay_merchant['message'], 'error');
								}
							}
						}
					}
					elseif ($action == "confirm")
					{
						$this->Order->id = $id;
						$this->Order->set('is_verified', 1);
						$this->Order->save();
						$this->Session->setFlash("Operation successful", 'success');
					}
					elseif ($action == "complete")
					{
						$this->Order->id = $id;
						$this->Order->set('is_verified', 2);
						$this->Order->save();
						$this->Session->setFlash("Operation successful", 'success');
					}
					else
					{
						$this->Session->setFlash("Please select an action to continue.", 'error');
					}
				}
			}
		
			$this->redirect($this->referer());
		}
		
		if (count($query)>0 && is_array($query))
		{					
			$status = isset($query['status']) ? strtolower($query['status']) : '';
			
			$pType = isset($query['pType']) ? strtolower($query['pType']) : '';

			$email = isset($query['email']) ? $query['email'] : '';

			$o_from = isset($query['o_from']) ? $query['o_from'] : '';
			$o_to = isset($query['o_to']) ? $query['o_to'] : '';
			
			$sort = isset($query['sort']) ? $query['sort'] : "Order.created";
			$order = isset($query['order']) ? $query['order'] : 'desc';				
		}

		$conditions = array();
		if ($status != "")
		{
			$conditions['Order.is_verified'] = $status;
			$qstring .= "&status=".$status;
		}

		if ($pType != "")
		{
			$conditions['Order.payment_type'] = $pType;
			$qstring .= "&pType=".$pType;
		}

		if ($email != "")
		{
			$conditions['OR']['User.name LIKE'] = '%'.$email.'%';
			$conditions['OR']['User.username LIKE'] = '%'.$email.'%';
			$conditions['OR']['User.email LIKE'] = '%'.$email.'%';
			$qstring .= "&email=".$email;
		}
		
		if ($o_from != "")
		{
			$conditions[] = array('Order.created >='=>$o_from);
			$qstring .= "&o_from=".$o_from;
		}
		if ($o_to != "")
		{
			$conditions[] = array('Order.created <='=>$o_to);
			$qstring .= "&o_to=".$o_to;
		}			
		$conditions = array_merge($conditions);
		
		$this->paginate = array(				
			'limit' => 25,
			'conditions' => $conditions,
			'order' => array($sort => strtoupper($order)),
		);			
		$results = $this->paginate('Order');			
		$totalRecords = count($results);		
		$this->set(Compact('totalRecords','pagingStr','results','status','o_from','o_to','email','order','sort','pType'));
	}
	
	/*
	 * Method	: List of dishes in particular
	 * Author 	: Bharat Borana
	 * Created	: 02 Jan, 2015
	 * @List of Dish data for particular order from backend admin side.
	 */
    public function admin_dishlist($orderId=null){
		$this->theme = "Admin";
		$Orders = array();
		$this->loadModel('OrderDish');
		if($orderId){
			$Orders = $this->OrderDish->find('all',array('conditions'=>array('OrderDish.order_id'=>$orderId)));
		}
		$this->set('Orders', $Orders);
	}
	
	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid email template'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$testiData =  $this->request->data;
			unset($this->request->data['Order']['image']);
			$this->request->data['Order']['id'] = $id;
			$this->Order->set($this->request->data);
			if ($this->Order->validates()) { 
				if ($this->Order->save($this->request->data)) {
					$this->Session->setFlash(__('The Order has been saved.'),'success');
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The Order could not be saved. Please, try again.'),'error');
				}
			} else {
				$errors = $this->Order->validationErrors;
				$this->set('errors',$errors);
				// handle errors
			}
		}  else {
			$options = array('conditions' => array('Order.' . $this->Order->primaryKey => $id));
			$this->request->data = $this->Order->find('first', $options);			
		}
	}
	
	/**
	 * payment setting
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_paymentsetting() {
		$this->loadModel('PaymentSetting');
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['PaymentSetting']['id'] = 1;
			
			$this->PaymentSetting->set($this->request->data);
			if ($this->PaymentSetting->validates()) { 
				if ($this->PaymentSetting->save($this->request->data)) {
					$this->Session->setFlash(__('The Payment settings has been saved.'),'success');
					return $this->redirect(array('action' => 'index','admin'=>true));
				} else {
					$this->Session->setFlash(__('The Payment Setting could not be saved. Please, try again.'),'error');
				}
			} else {
			}
		}  else {
			$this->request->data = $this->PaymentSetting->find('first');
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_changeStatus($id = null) {
		$this->Order->id = $id;
		if (!$this->Order->exists()) {
			throw new NotFoundException(__('Invalid email template'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Order->delete()) {
			$this->Session->setFlash(__('The email template has been deleted.'),'success');
		} else {
			$this->Session->setFlash(__('The email template could not be deleted. Please, try again.'),'error');
		}
		return $this->redirect(array('action' => 'index'));
	}

	/*
     * Purpose: Order history
     * @Created: Bharat Borana
     * @Date: 15 Jan 15
     * @Parameters: user_id (int)
     * @Response: orders data
     */
    
    function history($orderId = null)
    {
       $userId = $this->Auth->user('id');
       $this->loadModel('Order');
       $this->loadModel('Kitchen');
       $ordersArray = $this->Kitchen->getallorderIds($userId);
	   $this->Order->Behaviors->attach('Containable');
       $orders_recieved = array();
       if(!empty($ordersArray)){
		   $orders_recieved = $this->Order->find('all',array('conditions'=>array('Order.id IN('.$ordersArray.')'),
														   'contain'=>array('User'=>array('fields'=>array('User.name','User.id')),
																			'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name')))),
														   'order'=>'Order.created DESC',
														   'recursive'=>2));
	   }
       $orders_placed = $this->Order->find('all',array('conditions'=>array('Order.user_id'=>$userId),
														 'contain'=>array('OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name')))),
														  'order'=>'Order.created DESC',
														  'recursive'=>2));
       $this->Order->Behaviors->detach('Containable');
       
	   $results['order_placed'] = $orders_placed;
       $results['order_recieved'] = $orders_recieved;
       if(!empty($orderId))
       	$results['order_id'] = $orderId;

       $this->set('results',$results);
    }

    function testmail($order_id = null)
    {
   	   $this->layout = false;
       $this->loadModel('Order');
       
       $this->Order->Behaviors->attach('Containable');
       $conditions[] = array('Order.id'=>$order_id);
       $orderDataEmail = $this->Order->find('first',array('conditions'=>$conditions,
														   'contain'=>array('User'=>array('fields'=>array('User.name','User.email','User.id')),
																			'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name','Kitchen.sales_tax')))),
														   'recursive'=>2));
       $this->Order->Behaviors->detach('Containable');
    	
    	$this->Paypal->sendMail($orderDataEmail);
       
   	   $this->set(compact(array('order_details','paymentSettings')));
    }

   /*
     * Purpose: Order history
     * @Created: Bharat Borana
     * @Date: 15 Jan 15
     * @Parameters: user_id (int)
     * @Response: orders data
     */
    
    function order_detail($order_id)
    {
       $userId = $this->Auth->user('id');
       $this->layout = 'ajax';
       $this->loadModel('Order');
       $this->loadModel('Kitchen');
       
       $ordersArray = $this->Kitchen->getallorderIds($userId);
	   
	   $this->Order->Behaviors->attach('Containable');
       $orders_recieved = array();

       
       if(!empty($ordersArray)){
	   	$conditions['OR'][] = array('Order.user_id'=>$userId);
       	$conditions['OR'][] = array('Order.id IN('.$ordersArray.')');
        $conditions[] = array('Order.id'=>$order_id);
       }
       else
       {
       	$conditions[] = array('Order.user_id'=>$userId);
       	$conditions[] = array('Order.id'=>$order_id);
       }

	   $order_details = $this->Order->find('first',array('conditions'=>$conditions,
														   'contain'=>array('User'=>array('fields'=>array('User.name','User.id')),
																			'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name','Kitchen.sales_tax')))),
														   'recursive'=>2));
       $this->Order->Behaviors->detach('Containable');
    
       $this->loadModel('PaymentSetting');
       $paymentSettings = $this->PaymentSetting->find('first');

   	   $this->set(compact(array('order_details','paymentSettings')));
    }

     /*
	 * Purpose: Order completion action
	 * @Created: Bharat Borana
	 * @Date: 02 Fab 15
	 * @Parameters: order_id, user_id
	 * @Response: success if order_exists
	 */
    
    public function order_completion()
	{
		$this->autoRender = false;
		
		$result["message"] = "This action could not be performed.";
		$result["status"] = 0;

		if($this->request->is('ajax'))
		{
			$orderId = $this->request->data['order_id'];
			$userId = $this->Auth->user('id');
		
			$this->loadModel('Order');
			$order = $this->Order->find('first', array('conditions'=>array('Order.id' => $orderId,
																			'Order.user_id' => $userId),
													   'recursive'=>0));
			
			if(!empty($order))
			{
				if($order['Order']['is_verified']==1){
					$this->Order->id = $orderId;
					$this->Order->set('is_verified', 2);
					if($this->Order->save())
					{
						$result["message"] = "Your order has been successfully completed.";
						$result["status"] = 1;
					}
					else 
					{
						$result["message"] = "Error, Please try again.";
					}
				}
				else if($order['Order']['is_verified']==2)
				{
					$result["message"] = "Your order already has been completed.";
				}
				else
				{
					$result["message"] = "Seller has not confirmed this order. Try again later.";
				}
			}
			else 
			{
				$result = "Order does not exists.";
			}
		}
		echo json_encode($result);
	}  
	
	/*
	 * Purpose: Order confirmation action
	 * @Created: Bharat Borana
	 * @Date: 02 Fab 15
	 * @Parameters: order_id, user_id
	 * @Response: success if order_exists
	 */
    
    public function order_confirmation()
	{
		$this->autoRender = false;
	
		$result["message"] = "This action could not be performed.";
		$result["status"] = 0;

		if($this->request->is('ajax'))
		{
			$orderId = $this->request->data['order_id'];
			$kitchenId = $this->request->data['kitchen_id'];
			$status = $this->request->data['status'];

			$userId = $this->Auth->user('id');
		
			$this->loadModel('OrderDish');
			$this->loadModel('Order');
			
			$order = $this->Order->find('first', array('conditions'=>array('Order.id' => $orderId)));
			if(isset($order['Order']['id']) && $order['OrderDish'][0]['kitchen_id'] == $kitchenId)
			{
				$this->Order->id = $orderId;
				if($status==1)
				{
					if((strtotime(date('Y-m-d')) < strtotime($order['Order']['delivery_date'])) || (strtotime(date('Y-m-d')) == strtotime($order['Order']['delivery_date']) && time() < strtotime($order['Order']['delivery_time'])))
					{
						$this->Order->set('is_verified', 1);
						$result["message"] = "Your order has successfully confirmed.";
						$result["status"] = 1;
					}
					else
					{
						$this->Order->set('is_verified', 3);
						$result["message"] = 'You are not allowed to accept this order. You order has been expired.';
						$this->loadModel('ActivityLog');
						$activityLog = $this->ActivityLog->updateAll(array('ActivityLog.status'=>0),array('ActivityLog.activity_id'=>2,'ActivityLog.user_id'=>$userId,'ActivityLog.order_id'=>$order['Order']['id']));
					}
				}
				else
				{
					if($order['Order']['is_verified'] == 0)
					{
						$result["message"] = "Your order has successfully declined.";
						$result["status"] = 1;
						$this->Order->set('is_verified', 3);
						$this->loadModel('ActivityLog');
						$activityLog = $this->ActivityLog->updateAll(array('ActivityLog.status'=>0),array('ActivityLog.activity_id'=>2,'ActivityLog.user_id'=>$userId,'ActivityLog.order_id'=>$order['Order']['id']));
					}
					else
					{
						$result["message"] = "This order has already accepted.";
					}
				}

				if($this->Order->save())
				{
					//
				}
				else 
				{
					$result["message"] = 'Error, Please try again';
				}
			}
			else 
			{
				$result["message"] = "Order does not exists.";
			}
		}
		echo json_encode($result);
	}

	  /*
	 * Purpose: Order cancellation action
	 * @Created: Bharat Borana
	 * @Date: 26 May 15
	 * @Parameters: order_id, user_id
	 * @Response: success if order_exists
	 */
    
    public function order_cancellation()
	{
		$this->autoRender = false;
		
		$result["message"] = "This action could not be performed.";
		$result["status"] = 0;

		if($this->request->is('ajax'))
		{
			$orderId = $this->request->data['order_id'];
			$userId = $this->Auth->user('id');
		
			$this->loadModel('Order');
			$this->Order->bindModel(array('belongsTo'=>array('Kitchen')));
			$order = $this->Order->find('first', array('conditions'=>array('Order.id' => $orderId,
																			'Order.user_id' => $userId),
													   'recursive'=>0					
										));
			$this->Order->unbindModel(array('belongsTo'=>array('Kitchen')));
			
			if(!empty($order))
			{
				if($order['Order']['is_verified']==0)
				{
					$this->Order->id = $orderId;
					$this->Order->set('is_verified', 4);
					if($this->Order->save())
					{
						$this->loadModel('ActivityLog');
						$activityLog = $this->ActivityLog->updateAll(array('ActivityLog.status'=>0),array('ActivityLog.activity_id'=>2,'ActivityLog.user_id'=>$order['Kitchen']['user_id'],'ActivityLog.order_id'=>$order['Order']['id']));
						
						$result["message"] = "Your order has cancelled successfully.";
						$result["status"] = 1;
					}
					else 
					{
						$result["message"] = "Error, Please try again.";
						$result["status"] = 0;
					}
				}
				else if($order['Order']['is_verified']==1)
				{
					$result["message"] = "Your order has already verified by kitchen owner.";
					$result["status"] = 0;
				}
				else
				{
					$result["message"] = "Your order has already completed.";
					$result["status"] = 0;
				}
			}
			else 
			{
				$result["message"] = "Order does not exists.";
				$result["status"] = 0;
			}
		}
		echo json_encode($result);
	}

    /**
	 * Method	: Order Submit page
	 * Author	: Bharat Borana
	 * Created	: 21 Jan 2015
	 */
	public function summery() 
	{
		$this->loadModel('User');
		$userId = $this->Auth->user('id');
		$userDetails = array();
		if(!empty($userId))
			$userDetails = 	$this->User->findById($userId);

		$this->loadModel('PaymentSetting');
		$paymentSettings = $this->PaymentSetting->find('first');

		$this->set('api_key',STRIPE_PUBLISHABLE_KEY);

		if($this->request->is(array('post','put')))
		{
			if(!isset($userDetails['User']['id']) || empty($userDetails['User']['id']))
			{
				return $this->redirect(array('controller'=>'users','action'=>'index','pleaselogin'));
			}
			else
			{
				$orderDish = array();
				$errors = array();
				if(isset($this->request->data['OrderDish']) && !empty($this->request->data['OrderDish'])){
					$this->loadModel('Dish');
					$this->loadModel('Kitchen');
					
					
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

			    	
					if(!isset($this->request->data['User']['mobile']) || empty($this->request->data['User']['mobile']))
						$errors[][0] = 'Please enter your mobile number.';
					
					if(!isset($this->request->data['User']['state']) || empty($this->request->data['User']['state']))
						$errors[][0] = 'Please select your state.';
					
					if(!isset($this->request->data['User']['city']) || empty($this->request->data['User']['city']))
						$errors[][0] = 'Please select your city.';
					
					if(isset($this->request->data['User']['line-1']) && empty($this->request->data['User']['line-1']) && isset($this->request->data['User']['line-2']) && empty($this->request->data['User']['line-2']))
					{
						$errors[][0] = 'Please enter your address.';
					}

					if(!isset($this->request->data['User']['zipcode']) || empty($this->request->data['User']['zipcode']))
						$errors[][0] = 'Please enter your zipcode.';

					/*if(!isset($this->request->data['User']['dining_take_out']) && !isset($this->request->data['User']['dining_dine_in']))
						$errors[][0] = 'Please select your Dining Option.';
					*/
					$dish_res = array();
					$amount = 0;
					$tax = 0;
					$ordertotal = 0;
		   			foreach ($this->request->data['OrderDish'] as $key => $value)
					{ 
						if(empty($kitchenForOrderSummery))
						{
							$this->Kitchen->recursive = 2;
			   				$kitchenForOrderSummery = $this->Kitchen->findById($value['kitchen_id']);
			   			}
						if(isset($value['is_checked']) && !empty($value['is_checked']))
						{
							$DishDetails = $this->Dish->findById($value['dish_id']);
							$orderDish['Order'][] = array('OrderDish'=>$value, 'Dish'=>$DishDetails);
							if(!empty($DishDetails))
							{
								/*if(isset($this->request->data['User']['dining_take_out']) && $this->request->data['User']['dining_take_out']==1)
									$dineType = 'Take out';
								else
									$dineType = 'Dine in';
								*/
								if(empty($value['quantity']))
									$value['quantity'] = 1;

								if(empty($value['portion']))
									$value['portion'] = 'budget';

								$price = 0;
								if($value['portion'] == 'custom' && !empty($DishDetails['Dish']['p_custom_price']))
									$price = $value['quantity'] * $DishDetails['Dish']['p_custom_price'];
								elseif($value['portion'] == 'premium' && !empty($DishDetails['Dish']['p_big_price']))
									$price = $value['quantity'] * $DishDetails['Dish']['p_big_price'];
								elseif($value['portion'] == 'budget' && !empty($DishDetails['Dish']['p_small_price']))
									$price = $value['quantity'] * $DishDetails['Dish']['p_small_price'];
								

								if($price != 0)
								{
									$dish_res[] = array('dish_id' => $DishDetails['Dish']['id'], 
				                                        'kitchen_id' => $DishDetails['Dish']['kitchen_id'], 
				                                        'quantity'=>$value['quantity'], 
				                                        'price'=>$price,
				                                        /*'type'=>$dineType,*/ 
				                                        'dish_name'=>$DishDetails['Dish']['name'],                                         
				                                        'portion'=>$value['portion']
				                                       );
									$amount += $price ;

									$amountForTax = $amount+$paymentSettings['PaymentSetting']['service_fee'];

									$tax = $amountForTax * ( $kitchenForOrderSummery['Kitchen']['sales_tax'] / 100);

									$ordertotal = $amountForTax + $tax;
								}
							}
						}
					}
					if((isset($orderDish['Order']) && empty($orderDish['Order'])) || !isset($orderDish['Order']))
						$errors[][0] = 'Please select at least one Dish.';
					
					if((isset($this->request->data['PaymentMethod']) && empty($this->request->data['PaymentMethod']['paytype'])) || !isset($this->request->data['PaymentMethod']['paytype']))
						$errors[][0] = 'Please select mode of payment.';
					else
					{
						if($this->request->data['PaymentMethod']['paytype']==1)
						{
							if(!isset($this->request->data['PaymentMethod']['id']) || (isset($this->request->data['PaymentMethod']['id']) && empty($this->request->data['PaymentMethod']['id'])))
								$errors[][0] = 'Please select your card.';

							if(isset($this->request->data['PaymentMethod']['cvv_no1']) && $this->request->data['PaymentMethod']['cvv_no1']=='')
								$errors[][0] = 'Please enter your cvv number.';

						}
						else if($this->request->data['PaymentMethod']['paytype']==2)
						{
							//if(empty($this->request->data['User']['paypal_id']) || empty($this->request->data['PaymentMethod']['paypal_pass']))
								//$errors[][0] = 'Please enter your paypal details.';
						}
						else
						{
							if(isset($this->request->data['PaymentMethod']['card_no']) && $this->request->data['PaymentMethod']['card_no']=='')
								$errors[][0] = 'Please enter your card number.';

							/*if(isset($this->request->data['PaymentMethod']['card_name']) && $this->request->data['PaymentMethod']['card_name']=='')
								$errors[][0] = 'Please enter your card name.';
							*/
							if(isset($this->request->data['PaymentMethod']['type']) && $this->request->data['PaymentMethod']['type']=='')
								$errors[][0] = 'Please select your credit card type.';
							
							if(isset($this->request->data['PaymentMethod']['cvv_no']) && $this->request->data['PaymentMethod']['cvv_no']=='')
								$errors[][0] = 'Please enter your cvv number.';

							if(isset($this->request->data['PaymentMethod']['exp_month']) && $this->request->data['PaymentMethod']['exp_month']=='')
								$errors[][0] = 'Please select your credit card expiry month.';

							if(isset($this->request->data['PaymentMethod']['exp_year']) && $this->request->data['PaymentMethod']['exp_year']=='')
								$errors[][0] = 'Please select your credit card expiry year.';

							if(empty($errors) && isset($this->request->data['PaymentMethod']['stripeToken']) && $this->request->data['PaymentMethod']['stripeToken']=='')
								$errors[][0] = 'Invalid token. Please enter your credit card details.';
						}
					}
					
					if(empty($errors) && !empty($dish_res))
					{
						$search_data = $this->Session->read('search_data');
						if(isset($search_data['date']) && !empty($search_data))
							$deliveryDate = date('Y-m-d H:i:s',strtotime($search_data['date']));
						else
							$deliveryDate = date('Y-m-d H:i:s');

						if(isset($search_data['time']) && !empty($search_data['time']))
							$deliveryTime = date('H:i:s',strtotime($search_data['time']));
						else if(date('H:i:s',strtotime($DishDetails['Dish']['serve_start_time'])) != '00:00:00')
							$deliveryTime = date('H:i:s',strtotime($DishDetails['Dish']['serve_start_time']));
						else
							$deliveryTime = date('H:i:s',strtotime($DishDetails['Dish']['serve_end_time']));

						$address = $this->request->data['User']['line-1'].', '.$this->request->data['User']['line-2'].', '.$this->request->data['User']['city'].', '.$this->request->data['User']['state'];
						
						//If address, latitude, longitude were not found in session data
						if(!isset($search_data['address']) || empty($search_data['address']))
							$search_data['address'] = $address;

						if(!isset($search_data['latitude']) || empty($search_data['latitude']) || !isset($search_data['longitude']) || empty($search_data['longitude']))
						{
							$orderLatLng = $this->SuiteTest->_getAddressFormGeoode($address);
							if(isset($orderLatLng['latitude']) && !empty($orderLatLng['latitude']))
								$search_data['latitude'] = $orderLatLng['latitude'];
							
							if(isset($orderLatLng['longitude']) && !empty($orderLatLng['longitude']))
								$search_data['longitude'] = $orderLatLng['longitude'];
						}
						
						$inst = array(
						    'Order' => array('user_id' => $userId, 'amount'=>$ordertotal, 'sale_tax' => $tax, 'tax_percent'=>$kitchenForOrderSummery['Kitchen']['sales_tax'], 'service_fee'=>$paymentSettings['PaymentSetting']['service_fee'], 'order_value' => $amount, /*'dine_type' => $dineType,*/ 'delivery_date' => $deliveryDate, 'delivery_time' => $deliveryTime,'kitchen_id'=>$kitchenForOrderSummery['Kitchen']['id']),
						    'OrderDish' => $dish_res,
						    'OrderAddress' => array('order_address'=>$address , 'phone'=>$this->request->data['User']['mobile'],'delivery_location' => $search_data['address'], 'address_lat' => $search_data['latitude'],'address_lng' => $search_data['longitude']),         
						);
						
						if($this->request->data['PaymentMethod']['paytype']==2)
						{
							$inst['Order']['merchant_paid'] = 1;
							$inst['Order']['payment_type'] = 1;
							$this->Session->write('OrderDetails',$inst);

							if($paymentSettings['PaymentSetting']['fee_type']==0)
							{
								$transferAmmount = $ordertotal - $paymentSettings['PaymentSetting']['processing_fee'];
							}
							else
							{
								$transferAmmount = $ordertotal - (($amount)/100)*$paymentSettings['PaymentSetting']['processing_fee'];
							}
							
							$detailsAre['actionType'] = "PAY"; 	 #The action taken in the Pay request (that is, the PAY action)
							$detailsAre['clientDetails.applicationId'] = PAYPAL_APPID; 	#Standard Sandbox App ID
							$detailsAre['clientDetails.ipAddress'] = "127.0.0.1"; 		#Address from which request is sent
							$detailsAre['currencyCode'] = "USD"; 	#The currency, e.g. US dollars
							//$detailsAre['feesPayer'] = "EACHRECEIVER"; 		# URL redirect if customer cancels payment
							$detailsAre['memo'] = "Lacart Payment"; 
							$detailsAre['receiverList.receiver(0).amount'] = round($ordertotal,2); 				#The payment amount for the first receiver 
							$detailsAre['receiverList.receiver(0).email'] = $paymentSettings['PaymentSetting']['paypal_email']; 		# Admin Email
							//$detailsAre['receiverList.receiver(0).primary'] = "true"; 		# URL of your payment confirmation page
							$detailsAre['receiverList.receiver(1).amount'] = round($transferAmmount,2); 				# amount of transaction
							$detailsAre['receiverList.receiver(1).email'] = $kitchenForOrderSummery['User']['paypal_id']; 		# Merchant Email
							//$detailsAre['receiverList.receiver(1).primary'] = "false"; 		# URL of your payment confirmation page
							$detailsAre['requestEnvelope.errorLanguage'] = "en_US"; 		# currency of transaction
							$detailsAre['returnUrl'] = BASE_URL."orders/success"; 		# URL of your payment confirmation page
							$detailsAre['cancelUrl'] = BASE_URL."orders/fail"; 		# URL redirect if customer cancels payment
							
							$url = 'https://svcs.paypal.com/AdaptivePayments/Pay';
							$tokenData = $this->Paypal->pay_me($detailsAre,$url);

							if(!empty($tokenData) && isset($tokenData->responseEnvelope->ack) && $tokenData->responseEnvelope->ack=='Success')
							{
								$this->Session->write('PaypalPayKey',$tokenData->payKey);	
								
								return $this->redirect('https://www.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey='.$tokenData->payKey);
							}
							else
							{ 
								$errors[][0] = $tokenData->error[0]->message;
								$this->set("errors",$errors);
							}
						}
						else
						{
						
							$cardDetails['User'] = $this->request->data['User'];
							$cardHref = $this->request->data['PaymentMethod']['stripeToken'];
							$chargeArray['card'] = $cardHref;
							$chargeArray['amount'] = round($ordertotal,2)*100;
							$chargeArray['currency'] = 'usd';
							$chargeArray['description'] = "Order for ".$kitchenForOrderSummery['Kitchen']['name']." kitchen";

							if(!empty($cardHref))
							{
				               $this->loadModel('Order');
				               
				               if(empty($error))
							   {
								$paymentDone = $this->Stripe->charge($chargeArray);
								
								if($paymentDone['status']=='success')
								    $inst['Order']['order_href']=$paymentDone['response']['id'];
								
								$inst['Order']['payment_type'] = 0;

								if($paymentDone['status']=='success')
								   {
									   $this->Order->create();
									   if ($this->Order->SaveAssociated($inst))
									   {
										   //Update Activity Log
										   $this->loadModel('ActivityLog');
										   

										   //Update Activity Log For Order Place Activity
										   $this->ActivityLog->updateLog($userId,1,$this->Order->id,time());
										   
										   //Update Activity Log For Order Received Activity
										   $this->ActivityLog->updateLog($kitchenForOrderSummery['Kitchen']['user_id'],2,$this->Order->id,time());
										   
										   //send email with order details
										   $this->Order->Behaviors->attach('Containable');
									       $conditions[] = array('Order.id'=>$this->Order->id);
									       $orderDataEmail = $this->Order->find('first',array('conditions'=>$conditions,
																							   'contain'=>array('User'=>array('fields'=>array('User.name','User.email','User.id')),
																												'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name','Kitchen.sales_tax')))),
																							   'recursive'=>2));
									       $this->Order->Behaviors->detach('Containable');

									       $message['_message']['m'] = "You have received an order from ".$orderDataEmail['User']['name'];
	               					   	   $pushNoti = $this->Push->send($kitchenForOrderSummery['Kitchen']['user_id'],$message,$this->Order->id,2);

									       $this->Paypal->sendMail($orderDataEmail);
									       //////////////*********///////////////

										    if(isset($this->request->data['PaymentMethod']['save_card']) && $this->request->data['PaymentMethod']['save_card']==1 )
											{
											  $this->request->data['PaymentMethod']['user_id'] = $userId;
											  $isCardExists = $this->PaymentMethod->find('first',array('conditions'=>array('PaymentMethod.user_id'=>$userId,'PaymentMethod.card_no'=>$this->request->data['PaymentMethod']['card_no'])));
											  if(empty($isCardExists))
											  {
											  	$paymentMethodData = $this->request->data['PaymentMethod'];
											  	unset($paymentMethodData['id']);
											  	$this->PaymentMethod->create();
												$this->PaymentMethod->save($paymentMethodData);
											  }
											}
											$this->Session->write('order_checkout','');
											$this->Session->setFlash(__('Order has been placed successfully'),'success');
										    $orderId = $this->Order->id; 
										    return $this->redirect(array('controller'=>'orders','action'=>'history',$orderId)); 
									   }
									   else
									   {
										   $errors[][0] = 'uh oh! Something went wrong while saving your order data.';
										   $this->set("errors",$errors);  
									   }                
											   
									}
								   else
								   {
								   		$errors[][0] = $paymentDone['response']['message'];
										$this->set("errors",$errors); 
								   }
							   }
							   else
							   {
								   $this->set("errors",$errors);
							   }
				            }
				            else
				            {
				            	$errors[][0] = 'Invalid request token. Please enter correct credit card details.';
				            	$this->set("errors",$errors);
				            }
						}
						
					}
					else
					{
						$this->set("errors",$errors);
					}
					$this->request->data['Order'] = $orderDish['Order'];
					$this->set(compact(array('paymentDetails','states','userDetails','kitchenForOrderSummery','paymentSettings')));		
				}
			}
		}
		else
		{
			return $this->redirect(array('controller'=>'users','action'=>'index'));
		}
	}

	public function success()
	{
		$paypalPayKey = $this->Session->read('PaypalPayKey');
		
		$orderDetails = $this->Session->read('OrderDetails');	
		
			$this->Session->delete('PaypalPayKey');
		
		if(isset($paypalPayKey) && !empty($paypalPayKey))
		{
			$userId = $orderDetails['Order']['user_id'];

			$detailsAre['payKey'] = $paypalPayKey; 	#Value of the pay key, received in the Pay call above
			$detailsAre['requestEnvelope.errorLanguage'] = "en_US"; 

			$url = 'https://svcs.paypal.com/AdaptivePayments/PaymentDetails';
			$getExpData = $this->Paypal->pay_me($detailsAre,$url);

			if(!empty($getExpData) && isset($getExpData->responseEnvelope->ack) && $getExpData->responseEnvelope->ack=='Success')
			{
				$transactionId = $getExpData->paymentInfoList->paymentInfo[1]->transactionId;
							
				$checkAnyOrder = $this->Order->find('first',array('conditions'=>array('Order.transaction_id'=>$transactionId)));

				if(empty($checkAnyOrder) && isset($transactionId) && !empty($transactionId))
				{
					$orderDetails['Order']['transaction_id']=$transactionId;
					$orderDetails['Order']['payment_type'] = 1;
					$this->loadModel('Order');
					$this->Order->create();
					if ($this->Order->SaveAssociated($orderDetails))
					{
					   //Update Activity Log
					   $this->loadModel('ActivityLog');
					   
					    if(isset($orderDetails['timestamp']))
					   		$data['timestamp'] = $orderDetails['timestamp'];
					   	else
					   		$data['timestamp'] = time();
					   //Update Activity Log For Order Place Activity
					   $this->ActivityLog->updateLog($userId,1,$this->Order->id,$data['timestamp']);
					   
					   //send email with order details
					   $this->Order->Behaviors->attach('Containable');
				       $conditions[] = array('Order.id'=>$this->Order->id);
				       $orderDataEmail = $this->Order->find('first',array('conditions'=>$conditions,
																		   'contain'=>array('User'=>array('fields'=>array('User.name','User.email','User.id')),
																							'OrderDish'=>array('Kitchen'=>array('fields'=>array('Kitchen.name','Kitchen.sales_tax')))),
																		   'recursive'=>2));
				       $this->Order->Behaviors->detach('Containable');

				       $this->Paypal->sendMail($orderDataEmail);
				       //////////////*********///////////////

					   $this->loadModel('Kitchen');
					   $kitchenDetails = $this->Kitchen->findById($orderDetails['OrderDish'][0]['kitchen_id']);

					   //Update Activity Log For Order Received Activity
					   $this->ActivityLog->updateLog($kitchenDetails['Kitchen']['user_id'],2,$this->Order->id,$data['timestamp'] );
					   
					   $message['_message']['m'] = "You have received an order from ".$orderDataEmail['User']['name'];
				   	   $pushNoti = $this->Push->send($kitchenDetails['Kitchen']['user_id'],$message,$this->Order->id,2);

					   $this->Session->write('order_checkout','');
					   $this->Session->setFlash(__('Order has been placed successfully'),'success');
					   $orderId = $this->Order->id; 

					   	if(isset($orderDetails['Request_from_mobile']) && $orderDetails['Request_from_mobile']==1)
					   	{
					   		$this->Session->delete('OrderDetails');
					   		return $this->redirect(array('controller'=>'orders','action'=>'mobile_redirect',$transactionId)); 	
					   	} 
					   	else
					   	{
					   	    $this->Session->delete('OrderDetails');	
							return $this->redirect(array('controller'=>'orders','action'=>'history',$orderId)); 
						}
					}
					else
					{
					   $errors[][0] = 'uh oh! Something went wrong while saving your order data.';
					   $this->set("errors",$errors);  
					}   
				}
				else
				{
					$this->Session->setFlash("Order has been already placed.",'error');
			
				}
			}
		}
		
		if(isset($orderDetails['Request_from_mobile']) && $orderDetails['Request_from_mobile']==1) 
		{
		   	return $this->redirect(array('action'=>'fail'));
		}
		else
		{	
			$this->Session->delete('OrderDetails');
			return $this->redirect(array('action'=>'history'));
		}


	}
	
	public function fail()
	{
		$orderDetails = $this->Session->read('OrderDetails');	
		$this->Session->setFlash(__('Your order has been cancelled. Please try again leter.'),'error');
		
		if(isset($orderDetails['Request_from_mobile']) && $orderDetails['Request_from_mobile']==1) 
		{
		   	$this->autoRender = false;
		   	$this->Session->delete('OrderDetails');
		}
		else	
		{
			$this->Session->delete('OrderDetails');
			return $this->redirect(array('action'=>'history'));
		}
	}

	public function mobile_redirect($orderId)
	{
		$this->autoRender = false;
		echo "Your paypal transaction successfully done, Your transaction id is ".$orderId;
	}

	/*
	 * Method	: saveSessionForOrder
	 * Author	: Bharat Borana
	 * Created	: 18 Feb, 2015
	 * @Store session using an ajax
	 */
	public function saveMySession()
	{
		$this->autoRender = false;
		if($this->request->is('ajax'))
		{
			$this->Session->write('order_checkout',$this->request->data);
		}
	}

	/*
	 * Method	: payMyPaypal
	 * Author	: Bharat Borana
	 * Created	: 29 Jan, 2015
	 * @Pay my order using Paypal
	 */
	public function paymypaypal()
	{
	    $data = $this->request->query;
		$error = '';
		
		$this->autoRender = false;
		if(!isset($data['user_id']) || empty($data['user_id']))
        {
            $error .= 'User id is Required. ';
        }
        if(!isset($data['amount']) || empty($data['amount']))
        {
            $error .= 'Order amount is required. ';
        }            
        if(!isset($data['phone']) || empty($data['phone']))
        {
            $error .= 'Phone is required. ';
        }
        if(!isset($data['address']) || empty($data['address']))
        {
            $error .= 'Address is required. ';
        }
		if(!isset($data['address_lat']) || empty($data['address_lat']))
		{
		    $error .= 'Address Latitude is required. ';
		}
		if(!isset($data['address_lng']) || empty($data['address_lng']))
		{
		    $error .= 'Address Longitude is required. ';
		}
		if(!isset($data['delivery_location']) || empty($data['delivery_location']))
        {
            $error .= 'Delivery location is required. ';
        }
        if(!isset($data['kitchen_id']) || empty($data['kitchen_id']))
        {
            $error .= 'Kitchen id is required. ';
        }
        if(!isset($data['country_code']) || empty($data['country_code']))
        {
            $error .= 'Country code is required. ';
        }
        /*if(!isset($data['dine_type']) || $data['dine_type']=='')
        {
            $error .= 'Please select type of dining.';
        }*/
        if(!isset($data['timestamp']) || $data['timestamp']=='')
        {
            $error .= 'Please Send timestamp value for this order.';
        }
        if(!isset($data['dish']) || count($data['dish'])<=0 )
        {
            $error .= 'Dish array is required. ';
        }
       if(!isset($data['delivery_date']) || empty($data['delivery_date']))
		{
			$error .= 'Please select delevery date.';
		}
		if(!isset($data['delivery_time']) || empty($data['delivery_time']))
		{
			$error .= 'Please select delevery time.';
		}
    	if(!isset($data['paypal_id']) || empty($data['paypal_id']))
		{
			$error .= 'Paypal id required.';
		}
		
		if(!isset($data['sale_tax']) || empty($data['sale_tax']))
			$data['sale_tax'] = 0;

		if(!isset($data['service_fee']) || empty($data['service_fee']))
		{
			$error .= 'Service fee required.';
		}
		//	$data['service_fee'] = 0;

		if(!isset($data['tax_percent']) || empty($data['tax_percent']))
			$data['tax_percent'] = 0;

		if(!isset($data['order_value']) || empty($data['order_value']))
			$data['order_value'] = $data['amount'];

		if(empty($error))
        {  
           $data['dish'] = $data['dish'];
           //$this->loadModel('Dish');
           //$dish_info = $this->Dish->findById($data['dish_id'], array('Dish.name','Kitchen.name')); 
           $this->loadModel('Order');
           //$chk = $this->Order->findById('3');
           //pr($chk);

           $data['dish'] = json_decode($data['dish']);

           $dish_res = array();
           foreach ($data['dish'] as $dis)
           {   
               $dish_res[] = array('dish_id' => $dis->dish_id, 
                                    'kitchen_id' => $dis->kitchen_id, 
                                    'quantity'=>$dis->quantity, 
                                    'price'=>$dis->price,
                                    'type'=>$dis->type, 
                                    'dish_name'=>$dis->dish_name,                                         
                                    'portion'=>$dis->portion
                                   );
           }            
           
           $inst = array(
                'Order' => array('user_id' => $data['user_id'], 'amount'=>$data['amount'], 'sale_tax'=>$data['sale_tax'], 'tax_percent'=>$data['tax_percent'], 'service_fee'=>$data['service_fee'], 'order_value'=>$data['order_value'], /*'dine_type' => $data['dine_type'],*/ 'delivery_date' => $data['delivery_date'], 'delivery_time' => $data['delivery_time'], 'merchant_paid' => 1, 'payment_type' => 1,'kitchen_id'=>$data['kitchen_id']),
                'OrderDish' => $dish_res,
                'OrderAddress' => array('order_address'=>$data['address'] , 'phone'=>$data['phone'],'delivery_location' => $data['delivery_location'], 'address_lat' => $data['address_lat'],'address_lng' => $data['address_lng']),         
				'Request_from_mobile' => 1 ,
                'timestamp' => $data['timestamp'],       
           );
        	
           if(empty($error)){
           		$this->Session->write('OrderDetails',$inst);

				$this->loadModel('PaymentSetting');
				$paymentSettings = $this->PaymentSetting->find('first');
				if($paymentSettings['PaymentSetting']['fee_type']==0)
				{
					$transferAmmount = $data['amount'] - $paymentSettings['PaymentSetting']['processing_fee'];
				}
				else
				{
					$transferAmmount = $data['amount'] - (($data['amount'])/100)*$paymentSettings['PaymentSetting']['processing_fee'];
				}
				
				$detailsAre['actionType'] = "PAY"; 	 #The action taken in the Pay request (that is, the PAY action)
				$detailsAre['clientDetails.applicationId'] = PAYPAL_APPID; 				#Standard Sandbox App ID
				$detailsAre['clientDetails.ipAddress'] = "104.197.30.193"; 		#Address from which request is sent
				$detailsAre['currencyCode'] = "USD"; 	#The currency, e.g. US dollars
				//$detailsAre['feesPayer'] = "EACHRECEIVER"; 		# URL redirect if customer cancels payment
				$detailsAre['memo'] = "Lacart Payment"; 	# type of payment
				$detailsAre['receiverList.receiver(0).amount'] = round($data['amount']-$transferAmmount, 2); #The payment amount for the first receiver 
				$detailsAre['receiverList.receiver(0).email'] = $paymentSettings['PaymentSetting']['paypal_email']; # Admin Email
				//$detailsAre['receiverList.receiver(0).primary'] = true; 		# URL of your payment confirmation page
				$detailsAre['receiverList.receiver(1).amount'] = round($transferAmmount, 2); 				# amount of transaction
				$detailsAre['receiverList.receiver(1).email'] = $data['paypal_id']; 		# Merchant Email
				//$detailsAre['receiverList.receiver(1).primary'] = false; 		# URL of your payment confirmation page
				$detailsAre['requestEnvelope.errorLanguage'] = "en_US"; 		# currency of transaction
				$detailsAre['returnUrl'] = BASE_URL."orders/success"; 		# URL of your payment confirmation page
				$detailsAre['cancelUrl'] = BASE_URL."orders/fail"; 		# URL redirect if customer cancels payment
				
				$url = 'https://svcs.paypal.com/AdaptivePayments/Pay';
				$tokenData = $this->Paypal->pay_me($detailsAre,$url);

				if(!empty($tokenData) && isset($tokenData->responseEnvelope->ack) && $tokenData->responseEnvelope->ack=='Success')
				{
					$this->Session->write('PaypalPayKey',$tokenData->payKey);	
					
					return $this->redirect('https://www.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey='.$tokenData->payKey);
				}
				else
				{ 
					$errors[][0] = $tokenData->error[0]->message;
					echo $tokenData->error[0]->message;
				}
			}
	    }
	    else
	    {
	    	echo $error;
	    	exit;
	    }
    }
} ?>
