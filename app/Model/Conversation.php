<?php

class Conversation extends AppModel
{
	var $name = "Conversation";
	public $actsAs = array('Containable');
	
	public $hasMany = array('ConversationReply');

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			'Sender' => array(
					'className' => 'User',
					'foreignKey' => 'sender_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
			'Reciever' => array(
					'className' => 'User',
					'foreignKey' => 'receiver_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);
}
