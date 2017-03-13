<?php

class Comment extends AppModel {
	
	public $name  = 'Comment';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
							'User' => array(
								'className' => 'User',
								'foreignKey' => 'user_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
							),
							'Discussion' => array(
								'className' => 'Discussion',
								'foreignKey' => 'discussion_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
							)
	);

	public $validate = array(
		'comment' => array(
			'rule' => 'notempty'
			)
		);
	
}
