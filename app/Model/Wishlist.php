<?php
App::uses('AppModel', 'Model');

class Wishlist extends AppModel {
	
	public $name = 'Wishlist';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'Dish' => array(
			'className' => 'Dish',
			'foreignKey' => 'dish_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
}
