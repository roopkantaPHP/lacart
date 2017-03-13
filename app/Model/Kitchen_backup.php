<?php
App::uses('AppModel', 'Model');
/**
 * Kitchen Model
 */
class Kitchen extends AppModel {
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UploadImage' => array(
			'className' => 'UploadImage',
			'foreignKey' => 'related_id',
			'dependent' => false,
			'conditions' => array('UploadImage.type'=>'kitchen'),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public $belongsTo = array('User');
	
	public function searchKitchenForApi($data,$controller)
	{
		$order = array();
		$conditions['Kitchen.status'] = 'on';
		if(empty($data['radius']))
		{
			$geoAddress = $this->_getAddressFormGeoode($data['address']);
			if(!empty($geoAddress['locality']))
			{
				$conditions['OR']['Kitchen.city LIKE'] = '%'.$geoAddress['locality'].'%';	
			}
			else if($geoAddress['administrative_area_level_1'])
			{
				$conditions['OR']['Kitchen.state LIKE'] = '%'.$geoAddress['administrative_area_level_1'].'%';	
			}
			$conditions['OR']['Kitchen.address LIKE'] = '%'.$data['address'].'%';
		}
		if(isset($data['dining_dine_in']))
		{
			$conditions['Kitchen.dining_dine_in'] = $data['dining_dine_in'];
		}
		if(isset($data['dining_take_out']))
		{
			$conditions['Kitchen.dining_take_out'] = $data['dining_take_out'];
		}
		if(isset($data['diet']))
		{
			$conditions['Kitchen.diet'] = explode(',', $data['diet']);
		}
		if(isset($data['location']))
		{
			$conditions['Kitchen.city LIKE'] = '%'.$data['location'].'%';
		}
		
		
		$field = array(); 
		$group = array('Kitchen.id');
		if(!empty($data['latitude']) && !empty($data['longitude']))
		{
			$center_lat = $data['latitude'];
			$center_lng = $data['longitude'];
			$radius = 20;
			if(!empty($data['radius']))
			{
				$radius = $data['radius'];
			}
			//$field = "DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance";			
			if(!empty($data['radius']))
			{
				$fielddata = "'DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance'";
				$group = array("Kitchen.id HAVING $fielddata < $radius");

			}
				
		}

		
		$this->bindModel(array('hasMany'=>array(
											'Dish' => array(
													'fields' => array('Dish.id','Dish.name','Dish.p_small_price'),
												)	
											)
										)
						);
		if(isset($data['cuisine']))
		{
			$this->hasMany['Dish']['conditions'] = array('Dish.cuisine LIKE' => '%'.$data['cuisine'].'%','Dish.status'=>'on');
			$conditions['Dish.cuisine LIKE'] = '%'.$data['cuisine'].'%';
		}
		if(!empty($data['time']))
		{
			$data['time'] = date("H:i:s", strtotime($data['time']));
			$conditions['Dish.serve_start <='] = $data['time'];
			$conditions['Dish.serve_end >='] = $data['time'];
			$this->hasMany['Dish']['conditions'][] = array('Dish.serve_start <=' => $data['time'],'Dish.serve_end >=' =>$data['time'],'Dish.status'=>'on');
		}
		$conditions['Dish.status'] = 'on';
		
		$controller->paginate = array(
							//'fields'=> array($field),
							'conditions' => $conditions,
							'group' => $group,
							'limit' => 2,
							'page' => (!empty($data['page'])) ? $data['page'] : 1,
							'joins' => array(
							             array(
							                'table' => 'dishes',
							                'alias' => 'Dish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = Dish.kitchen_id')
							             ),
							         ),
		);
		$results = $controller->paginate('Kitchen');
		/*
		$results = $this->find('all', array(
											'fields'=> array($field),
											'conditions' => $conditions,
											'group' => $group
																
									)
							);
		*/
		//$log = $this->getDataSource()->getLog(false, false);
		//print_r ($log);
		//pr($results);		
		$resultArray= array();
		if(!empty($results))
		{
			$k = 0;
			foreach ($results as $key => $value) {
				if(!empty($value['Dish']))
				{
					$i = 0;
					foreach ($value['UploadImage'] as $image) {
						$value['Kitchen']['images'][$i]['id'] = $image['id'];
						$value['Kitchen']['images'][$i]['url'] = Router::url('/'.KITCHEN_IMAGE_URL.$image['name'],true);
						$i++;
					}
					if(!empty($value['Kitchen']['cover_photo']))
					{
						$value['Kitchen']['cover_photo'] = Router::url('/'.KITCHEN_IMAGE_URL.$value['Kitchen']['cover_photo'],true);
					}
					$value['Kitchen']['dishes_count'] = count($value['Dish']);
					$value['Kitchen']['dishes'] = $value['Dish'];
					$value['Kitchen']['User']['image']  = (!empty($value['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$value['User']['image'],true) : "";
					$resultArray[$k] = $value['Kitchen'];
					$k++;
				}				
			}
		}	
		return $resultArray;
	}

	function searchKitchen($controller, $data)
	{
		$order = array();
		$field = array(); 
		$group = array('Kitchen.id');
		$conditions['Kitchen.status'] = 'on';
		if(isset($data['key'])) {
		//$conditions['Kitchen.name LIKE'] = $data['key'].'%';
			$conditions['Dish.name LIKE'] = '%'.$data['key'].'%';
		}
		if(empty($data['lat']) && empty($data['lng']))
		{
			if(isset($data['location'])) {
			$geoAddress = $this->_getAddressFormGeoode($data['location']);
			if(!empty($geoAddress['locality']))
			{
				$conditions['OR']['Kitchen.city LIKE'] = '%'.$geoAddress['locality'].'%';	
			}
			else if($geoAddress['administrative_area_level_1'])
			{
				$conditions['OR']['Kitchen.state LIKE'] = '%'.$geoAddress['administrative_area_level_1'].'%';	
			}
			$conditions['OR']['Kitchen.address LIKE'] = '%'.$data['location'].'%';
			}
		}
		
		if(!empty($data['lat']) && !empty($data['lng']))
		{
			$center_lat = $data['lat'];
			$center_lng = $data['lng'];
			$radius = 20;
			if(!empty($data['radius']))
			{
				$radius = $data['radius'];
			}
			//$field = "DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance";
			if(empty($data['radius']))
			{
				$fielddata = "'DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance'";
				$group = array("Kitchen.id HAVING $fielddata < $radius");
		
			}
		
		}
		
		$this->bindModel(array('hasMany'=>array(
											'Dish' => array(
													'fields' => array('Dish.id','Dish.name','Dish.p_small_price'),
												)	
											)
										)
						);
		if(isset($data['cuisine']))
		{
			$this->hasMany['Dish']['conditions'] = array('Dish.cuisine LIKE' => '%'.$data['cuisine'].'%','Dish.status'=>'on');
			$conditions['Dish.cuisine LIKE'] = '%'.$data['cuisine'].'%';
		}
		
		$conditions['Dish.status'] = 'on';
		
		$controller->paginate = array(
							'fields'=> array($field),
							'conditions' => $conditions,
							'group' => $group,
							'limit' => 10,
							'page' => (!empty($data['page'])) ? $data['page'] : 1,
							'joins' => array(
							             array(
							                'table' => 'dishes',
							                'alias' => 'Dish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = Dish.kitchen_id')
							             ),
							         ),
		);
		$results = $controller->paginate('Kitchen');
		
		return $results;
	}
}
