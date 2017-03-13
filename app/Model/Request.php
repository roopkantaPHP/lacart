<?php
App::uses('AppModel', 'Model');
/**
 * Dish Model
 */
class Request extends AppModel {
	
	public $actsAs = array('Containable');
  
  public $validate = array(
      'dish_name' => array(
       'notempty' => array(
        'rule' => array('notempty'),
        'message' => 'Please add dish title.',
      ),
    ),
    'message' => array(
       'notempty' => array(
        'rule' => array('notempty'),
        'message' => 'Please add message for this request.',
      ),
    ),
    'cuisine_id' => array(
       'notempty' => array(
        'rule' => array('notempty'),
        'message' => 'Please select Cuisine for this discussion.',
      ),
    ),
    'user_id' => array(
       'notempty' => array(
        'rule' => array('notempty'),
        'message' => 'Please login for start a new discussion.',
      ),
    )
    );  
	
/**
 * belongsTo associations
 * 
 * @var array
 */
 	public $hasMany = array(
                       'RequestAnswer' => array(
                           'className' => 'RequestAnswer',
                           'foreignKey' => 'request_id',
                           
                       )
                   );
        
public $belongsTo = array(
               'User' => array(
                   'className' => 'User',
                   'foreignKey' => 'user_id',
                ),
                'Cuisine' => array(
                   'className' => 'Cuisine',
                   'foreignKey' => 'cuisine_id',
                )
           );
	
}
