<?php
App::uses('AppModel', 'Model');
/**
 * Order Model
 *
 * @property User $User
 * @property Order Address $Order
 */
class Order extends AppModel {
	public $actsAs = array('Containable');
/**
 * Validation rules
 *
 * @var array
 */


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'OrderAddress' => array(
			'className' => 'OrderAddress',
			'foreignKey' => 'order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'OrderDish' => array(
			'className' => 'OrderDish',
			'foreignKey' => 'order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User'
		),
	);
	
	function getAllCountryName(){
		$countries = '';
		$rawData = $this->find('all',array(	'fields'=>array('Country.country'),
														'conditions'=>array('Country.is_publish'=>1),
														'recursive'=>0)
										);
		if(!empty($rawData)){
			foreach($rawData as $cData){
				$countries[] = array('id'=>$cData['Country']['id'],'name'=>$cData['Country']['country']);
			}
		}								               
		return $countries;	   	
	}
	
	function getUsersOrders($userId=null){
		$usersOrders='';
		if(!empty($userId)){
				$usersOrders = $this->find('all', array(
							'conditions' => array('Order.user_id'=>$userId),
							'fields' => array('id', 'created','amount','order_href','transaction_id','payment_type','dine_type'),
							'contain' => array(
								'OrderDish'=>array(
									'fields'=>array('OrderDish.*'),
									'Dish'=>array('fields'=>array('Dish.name'),),
									'Kitchen'=>array('fields'=>array('Kitchen.name'),),
									)
							)
						));
		}
		return $usersOrders;
	}
	
	function getKitchenOrders($userId=null){
		$kitchenOrders='';
		if(!empty($userId)){
				$kitchenOrders = $this->find('all', array(
							'fields' => array('Kitchen.name','Kitchen.id'),
							'conditions' => array('Order.id'=>$userId),
							'contain' => array(
								'Dish'	=>array(
									'fields' => array('name'),
									'OrderDish' => array(
										'fields' => array('id'),
										'Order' => array(
											'fields' => array('id', 'created','amount','order_href','transaction_id','payment_type','dine_type'),
											'OrderDish'=>array(
												'fields'=>array('OrderDish.*'),
											)
										),
									)
								)
							)
						));
		}
		return $kitchenOrders;
	}

}
