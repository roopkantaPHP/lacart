<?php
App::uses('AppModel', 'Model');
/**
 * Activity Model
 *
 * @property Activity Log $Activity Log
 */
class Activity extends AppModel {

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
	public $hasMany = array(
		'ActivityLog' => array(
			'className' => 'ActivityLog',
			'foreignKey' => 'activity_id',
		)
	);
}
