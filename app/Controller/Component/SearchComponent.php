<?php
/** 
*  Project : Konverge Mobile 
*  Author : Xicom Technologies 
*  Creation Date : 21-Jan-2014 
*  Description : This is Search Component which creates the condition according to data
*/
	App::uses('Component', 'Controller');
	class SearchComponent extends Component
	{
		public $components = array('Aws');
/*
 * Purpose :- this function will create conditions for input data 
 * 
 * Inputs : $data - The data from which we need to create conditions   
 * 
 * Outputs : $conditions - condition array after iterating the data array
 * 
 * Returns : It will return condition from the data.
*/
    	public function create_conditions_by_name($name_letter = '', $model_name = '', $field = '')
    	{
    		$conditions = array();
    		if(strcasecmp ( $name_letter , 'Show All' ) == 0)
			{
			} else if($name_letter == '#') {
				$conditions = array(
										"{$model_name}.{$field} REGEXP '^[[:digit:]]'" 
									);
			} else if($name_letter != '')
			{
				$conditions = array(
										"{$model_name}.{$field} LIKE '$name_letter%'" 
									);
			}
			return $conditions;
	    }

/*
 * Purpose :- this function will create conditions for input data 
 * 
 * Inputs : $data - The data from which we need to create conditions   
 * 
 * Outputs : $conditions - condition array after iterating the data array
 * 
 * Returns : It will return condition from the data.
*/
    	public function create_conditions_by_keyword($keyword = '', $model_name = '', $fields = array())
    	{
    		foreach($fields as $field)
			{
				$keyword_conditions['OR'][] = "{$model_name}.{$field} LIKE '%$keyword%'";
			}
			return $keyword_conditions;
	    }
/*
 * Purpose :- this function will update the input data for given action 
 * 
 * Inputs : $data - The data which we need to update
 * 			$modelName = name of model for which we need to update data   
 * 
 * Outputs : $status - 1 if data updated , 0 if not updated
 * 
 * Returns : It will return status
*/		
		public function update_data_by_action($data, $modelName = '')
		{
			App::import('Model', $modelName);
			$object = new $modelName();
			$ids = array_keys($data[$modelName]['id_value']);
			switch ($data[$modelName]['action']) {
				case 'deactivate':
					$status = $object->updateAll(
						array("{$modelName}.is_active" => false),
					     array("{$modelName}.id" => $ids)
					);
					break;
				case 'delete':
					if($object->Behaviors->enabled('SoftDelete'))
					{
						App::import('Model', 'DeletedRecord');
						$dele_obj = new DeletedRecord();
						if(!empty($ids))
						{
							foreach($ids as $key => $aa)
							{
								if($object->name == 'Show')
								{
									$object->recursive = -1;
									$subdomain = $object->read('subdomain', $aa);
									if(isset($subdomain['Show']['subdomain']))
									{
										$this->Aws->delete_cname($subdomain['Show']['subdomain']);
									}
								}
								$data_not['DeletedRecord'][$key]['deleted_id'] = $aa;
								$data_not['DeletedRecord'][$key]['name'] = $object->useTable;
							}
							if(!empty($data_not))
							{
								$dele_obj->saveAll($data_not['DeletedRecord']);
							}
						}
						$status = $object->updateAll(
							array("{$modelName}.deleted" => 1),
						    	array("{$modelName}.id" => $ids)
							);
					} else
					{
						$status = $object->deleteAll(
							array("{$modelName}.id" => $ids),
								true, true
							);
					}
					break;
				case 'undelete':
						$save = true;
						foreach($ids as $key => $aa)
						{
							if($object->name == 'Show')
							{
								$object->recursive = -1;
								$subdomain = $object->find(
									'first', array(
										'fields' => array('subdomain'),
										'conditions' =>array('id' => $aa, 'deleted' => 1)
									)
								);
								if(isset($subdomain['Show']['subdomain']))
								{
									if(!$this->Aws->create_cname($subdomain['Show']['subdomain']))
									{
										$save = false;
									}
								}
							}
						}
						if($save)
						{
							$status = $object->updateAll(
							array("{$modelName}.deleted" => 0),
						    	array("{$modelName}.id" => $ids)
							);
						} else {
							$status = false;
						}
					break;
				case 'archive':
					$status = $object->updateAll(
						array("{$modelName}.is_archived" => true),
					     array("{$modelName}.id" => $ids)
					);
					break;
				case 'unarchive':
					$status = $object->updateAll(
						array("{$modelName}.is_archived" => false),
					     array("{$modelName}.id" => $ids)
					);
					break;
				case 'unfeatured':
					$status = $object->updateAll(
						array("{$modelName}.is_featured" => false),
					     array("{$modelName}.id" => $ids)
					);
					break;
				default:
					$status = false;
					break;
			}
			return $status;
		}

/*
 * Purpose :- this function will create conditions for input data 
 * 
 * Inputs : $data - The data from which we need to create conditions   
 * 
 * Outputs : $conditions - condition array after iterating the data array
 * 
 * Returns : It will return condition from the data.
*/
    	public function create_search_conditions($data = array(), $modelName = '')
    	{
    		$like_columns_find = Configure::read('like_columns_find');
			if(!$like_columns_find){
				$like_columns_find = array();
			}
			$conditions = array();
    		if(empty($data))
    		{
    			return $data;
    		}
    		foreach($data[$modelName] as $key => $single_condition) {
    			if(!$single_condition) {
    				$conditions["$modelName.{$key} !="] = '';
					continue;
    			}
    			if(in_array($key, $like_columns_find)) {
    				$conditions["$modelName.{$key} LIKE  "] = "%$single_condition%";
    				continue;
    			}
				$conditions[$modelName . '.' . $key] = $single_condition;
    		}
			return $conditions;
	    }
    }
?>