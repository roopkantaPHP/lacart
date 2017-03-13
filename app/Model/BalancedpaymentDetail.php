<?php
App::uses('AppModel', 'Model');
/**
 * BalancedpaymentDetail Model
 *
 * @property Country $Country
 * @property User $User
 */
class BalancedpaymentDetail extends AppModel {

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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
