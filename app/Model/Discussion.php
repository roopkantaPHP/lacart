<?php
class Discussion extends AppModel {
	
    public $belongsTo = array(
			        'Community' => array(
			            'counterCache' => true,
			        )
    );

    public $validate = array(
    	'title' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please add title for this discussion.',
			),
		),
		'description' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please add description for this discussion.',
			),
		),
		'community_id' => array(
		   'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select Community to start discussion.',
			),
		),
    );
}