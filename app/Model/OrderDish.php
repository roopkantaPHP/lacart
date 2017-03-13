<?php
App::uses('AppModel', 'Model');
/**
 * OrderDish Model
 *
 * @property User $User
 * @property Order Address $Order
 */
class OrderDish extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Order' => array(
			'className' => 'Order',
		),
		'Kitchen' => array(
			'className' => 'Kitchen',
		)
	);
}
