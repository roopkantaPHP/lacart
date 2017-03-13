<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 * @property User $User
 * @property Order Address $Order
 */
class Country extends AppModel {

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
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Buyer' => array(
			'className' => 'Buyer',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Supplier' => array(
			'className' => 'Supplier',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'State' => array(
			'className' => 'State',
			'foreignKey' => 'country_id',
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

}
