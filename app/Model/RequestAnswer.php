<?php
App::uses('AppModel', 'Model');
/**
 * Dish Model
 */
class RequestAnswer extends AppModel {
	
	//public $actsAs = array('Containable');
    
	
/**
 * belongsTo associations
 * 
 * @var array
 */
 	public $belongsTo = array(
			'Dish','Request'
	); 	
	
}
