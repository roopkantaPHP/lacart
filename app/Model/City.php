<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 * @property User $User
 * @property Order Address $Order
 */
class City extends AppModel {

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
		'State' => array(
			'className' => 'State',
		)
	);
	
	function getAllCityName($state_id){
		$cities = '';
		$conditions = array('City.is_publish'=>1);
		if(!empty($state_id))
		$conditions = array('City.state_id'=>$state_id,'City.is_active'=>1);
		$rawData = $this->find('all',array(	'fields'=>array('City.name','City.id'),
														'conditions'=>$conditions,
														'recursive'=>0,
														'order'=>'City.name')
										);
		if(!empty($rawData)){
			foreach($rawData as $cData){
				$cities[] = array('id'=>$cData['City']['id'],'name'=>$cData['City']['name']);
			}
		}								               
		return $cities;	   	
	}
}
