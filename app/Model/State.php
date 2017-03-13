<?php
App::uses('AppModel', 'Model');
/**
 * State Model
 *
 * @property User $User
 * @property Order Address $Order
 */
class State extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'is_publish' => array(
			'boolean' => array(
				'rule' => array('boolean'),
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
	public $belongsTo = array(
		'Country' => array(
			'className' => 'Country',
		)
	);
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'state_id',
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
	
	function getAllStateName($country_id){
		$states = '';
		$conditions = array('State.is_publish'=>1);
		if(!empty($country_id))
		$conditions = array('State.country_id'=>$country_id,'State.is_publish'=>1);
		$rawData = $this->find('list',array(	'fields'=>array('State.name','State.name'),
														'conditions'=>$conditions,
														'recursive'=>0,
														'order'=>'State.name')
										);
		return $rawData;	   	
	}

}
