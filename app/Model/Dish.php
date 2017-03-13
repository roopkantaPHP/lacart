<?php
App::uses('AppModel', 'Model');
/**
 * Dish Model
 */
class Dish extends AppModel {
	
	public $actsAs = array('Containable');
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UploadImage' => array(
			'className' => 'UploadImage',
			'foreignKey' => 'related_id',
			'dependent' => false,
			'conditions' => array('UploadImage.type'=>'dish'),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'OrderDish' => array(
			'className' => 'OrderDish',
			'foreignKey' => 'dish_id',
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
 * belongsTo associations
 * 
 * @var array
 */
 	public $belongsTo = array(
			'Kitchen'
	);
 	
 	public $validate = array(
	    'name' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter your Dish name.',
			),
		),
		'diet' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select your diet.',
			),
		),
	);
 	
 	public function beforeSave($options = array())
 	{
 		if (isset($this->data[$this->alias]['serve_start_time']))
 		{
 			$this->data[$this->alias]['serve_start'] = date("H:i:s", strtotime($this->data[$this->alias]['serve_start_time']));
 		}
 		if (isset($this->data[$this->alias]['serve_end_time']))
 		{
 			$this->data[$this->alias]['serve_end'] = date("H:i:s", strtotime($this->data[$this->alias]['serve_end_time']));
 		}
 		return true;
 	}
	
}
