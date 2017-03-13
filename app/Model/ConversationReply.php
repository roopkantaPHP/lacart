<?php

class ConversationReply extends AppModel
{
	var $name = "ConversationReply";
	
	public $belongsTo = array('User',
						'Conversation' => array( 'counterCache' => true)
	);

}
