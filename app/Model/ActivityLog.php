<?php
App::uses('AppModel', 'Model');
/**
 * Activity Log Model
 *
 * @property User $User
 */
class ActivityLog extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
		),
		'OrderPlaced' => array(
			'className' => 'Order',
			'foreignKey' => 'order_id',
		),
		'OrderReceived' => array(
			'className' => 'Order',
			'foreignKey' => 'order_id',
		),
		'DishAdded' => array(
			'className' => 'Dish',
			'foreignKey' => 'dish_id',
		),
		'DishActive' => array(
			'className' => 'Dish',
			'foreignKey' => 'dish_id',
		),
		'DishOffline' => array(
			'className' => 'Dish',
			'foreignKey' => 'dish_id',
		),
		'Conversation' => array(
			'className' => 'Conversation',
			'foreignKey' => 'conversation_id',
		),
		'RequestAnswer' => array(
			'className' => 'RequestAnswer',
			'foreignKey' => 'request_answer_id',
		)
	);
	
	public function updateLog($userId=null,$activityId=null,$relevantId=null,$timestamp=null,$update=0){
		if(!empty($userId)){
			$actData['user_id'] = $userId;
			$actData['activity_id'] = $activityId;
			if($activityId==1) //Order Placed
				$actData['order_id'] = $relevantId;
			else if($activityId==2) //Order Received
				$actData['order_id'] = $relevantId;
			else if($activityId==3) //Dish Added
				$actData['dish_id'] = $relevantId;
			else if($activityId==4) //Dish Activated
				$actData['dish_id'] = $relevantId;
			else if($activityId==5) //Dish Offline
				$actData['dish_id'] = $relevantId;
			else if($activityId==6) //Conversation updated
				$actData['conversation_id'] = $relevantId;
			else if($activityId==7) //Conversation updated
				$actData['request_answer_id'] = $relevantId;	
			
			if(!empty($timestamp)) // Timestamp for an activity
				$actData['timestamp'] = $timestamp;
			
			if(!empty($activityId) && ($activityId>0 && $activityId<=7)){
				if(!$update){
					$this->create();
					$this->save($actData);
				}else{
					$this->id = $update;
					$this->save($actData);
				}
			}	
		}
	}
}?>