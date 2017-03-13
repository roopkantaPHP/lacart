<?php
App::uses('AppModel', 'Model');
/**
 * Kitchen Model
 */
class Kitchen extends AppModel {
	public $actsAs = array('Containable');
/**
 * hasMany associations
 *
 * @var array
 */
	public $validate = array(
	    'name' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter your kitchen name.',
			),
		),
		'address' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter your address.',
			),
		),
	);
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
		),
		'Dish' => array(
			'className' => 'Dish',
			'foreignKey' => 'kitchen_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'OrderDish' => array(
			'className' => 'OrderDish',
			'foreignKey' => 'kitchen_id',
			'dependent' => false,
			'conditions' => '',
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

	public function getKitchenDetails($kitchenId){
		if($kitchenId!=''){
			$kitchenData = $this->find('first',array('fields'=>array('Kitchen.*','User.email','User.id','User.name','User.bank_acc_no','User.bank_routing_no','User.bank_acc_holdername','User.bank_acc_type'),
																  'conditions'=>array('Kitchen.id'=>$kitchenId),
																  'recursive'=>2));
			return $kitchenData;
		}
	}

	public function searchKitchenForApi($data,$controller)
	{

		App::import('model','SiteSetting');
		$siteSetting = new SiteSetting();
		$data['radius'] = $siteSetting->getIdVal('search_radius');

		$order = array();
		$conditions['Kitchen.status'] = 'on';

		if(!isset($data['date']) || empty($data['date']))
		{
			$data['day'] = strtolower(date('l'));
			$data['date'] = date('Y-m-d');
		}
		else
		{
			$data['day'] = strtolower(date('l',strtotime($data['date'])));
			$data['date'] = date('Y-m-d',strtotime($data['date']));
		}

		if(isset($data['user_id']) && !empty($data['user_id']))
		{
			$conditions['Kitchen.user_id !='] = $data['user_id'];
		}

		if(isset($data['dining_dine_in']) && $data['dining_dine_in']==1)
		{
			$conditions['Kitchen.dining_dine_in'] = $data['dining_dine_in'];
		}
		if(isset($data['dining_take_out']) && $data['dining_take_out']==1)
		{
			$conditions['Kitchen.dining_take_out'] = $data['dining_take_out'];
		}
		if(isset($data['location']) && !empty($data['location']))
		{
			$conditions['Kitchen.city LIKE'] = '%'.$data['location'].'%';
		}

		$field = 'Kitchen.*,User.*';
		$group = array('Kitchen.id');
		if(!empty($data['latitude']) && !empty($data['longitude']))
		{
			$center_lat = $data['latitude'];
			$center_lng = $data['longitude'];
			if(!empty($data['radius']))
			{
				$radius = $data['radius'];
			}
			//$field = "DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance";
			if(!empty($data['radius']))
			{
				//$fielddata = "'DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance'";
				//$group = array("Kitchen.id HAVING distance < $radius");
				$expression = "( 3959 * acos( cos( radians($center_lat) ) * cos( radians( Kitchen.lat ) ) * cos( radians( Kitchen.lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( Kitchen.lat ) ) ) )";
				$conditions[] = $expression . ' < '.$radius;
				$field = 'Kitchen.*,User.*,'.$expression.' as distance';
			}

		}
		else
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


		$this->bindModel(array('hasMany'=>array(
											'Dish' => array(
													'fields' => array('Dish.*'),
												)
											)
										)
						);

		$conditions[] = array('Dish.status'=>'on');

		$conditions[] = array('OR'=>array(array('Dish.repeat !='=>0,'Dish.repeat !='=>'','Dish.repeat LIKE'=>'%'.$data['day'].'%','Dish.status'=>'on'),array('OR'=>array(array('Dish.repeat'=>0),array('Dish.repeat'=>'')),'DATE(Dish.modified)'=>$data['date'],'Dish.status'=>'on')));

		$this->hasMany['Dish']['conditions'] = array('OR'=>array(array('Dish.repeat !='=>0,'Dish.repeat !='=>'','Dish.repeat LIKE'=>'%'.$data['day'].'%','Dish.status'=>'on'),array('OR'=>array(array('Dish.repeat'=>0),array('Dish.repeat'=>'')),'DATE(Dish.modified)'=>$data['date'],'Dish.status'=>'on')));

		if($data['date'] == date('Y-m-d') && isset($data['time']))
		{
			$start_date = new DateTime($data['time']);
			$since_start = $start_date->diff(new DateTime(date("H:i:s")));
			$hoursDiff = $since_start->h;
			$this->hasMany['Dish']['conditions'][] = array('Dish.lead_time <=' => $hoursDiff);
			$conditions['Dish.lead_time <='] = $hoursDiff;
		}

		if(isset($data['cuisine']) && !empty($data['cuisine']))
		{
			$this->hasMany['Dish']['conditions'][] = array('Dish.cuisine' => explode(',', $data['cuisine']),'Dish.status'=>'on');
			$conditions['Dish.cuisine'] = explode(',', $data['cuisine']);
		}

		if(isset($data['diet']) && !empty($data['diet']))
		{
			$this->hasMany['Dish']['conditions'][] = array('Dish.diet' => explode(',', $data['diet']),'Dish.status'=>'on');
			$conditions[] = array('Dish.diet' => explode(',', $data['diet']),'Dish.status'=>'on');
			//$conditions['Dish.diet'] = explode(',', $data['diet']);
		}
		if(isset($data['keyword']) && !empty($data['keyword']))
		{
			$conditions[] = array('OR'=>array('Kitchen.name LIKE' => '%'.$data['keyword'].'%', 'Kitchen.description LIKE' => '%'.$data['keyword'].'%', 'Dish.name LIKE' => '%'.$data['keyword'].'%'));
		}
		if(!empty($data['time']))
		{
			$data['time'] = date("H:i:s", strtotime($data['time']));
			$conditions['Dish.serve_start <='] = $data['time'];
			$conditions['TIMEDIFF(Dish.serve_end, TIME(Dish.lead_time * 10000)) >='] = $data['time'];
			$this->hasMany['Dish']['conditions'][] = array('Dish.serve_start <=' => $data['time'],'TIMEDIFF(Dish.serve_end, TIME(Dish.lead_time * 10000)) >=' =>$data['time'],'Dish.status'=>'on');
		}

		$orderBy = "Kitchen.id";
		if(isset($data['is_rated_high']) && $data['is_rated_high']==1)
				$orderBy = "Kitchen.avg_rating DESC";

		if(isset($data['is_popular']) && $data['is_popular']==1)
				$orderBy = "COUNT(OrderDish.id) DESC";

		if(isset($data['is_popular']) && isset($data['is_rated_high']) && $data['is_popular']==1 && $data['is_rated_high']==1)
				$orderBy = "Kitchen.avg_rating DESC, COUNT(OrderDish.id) DESC";


		$controller->paginate = array(
							'fields'=> array($field),
							'conditions' => $conditions,
							'group' => $group,
							'limit' => 15,
							'page' => (!empty($data['page'])) ? $data['page'] : 1,
							'order' => $orderBy,
							'joins' => array(
							             array(
							                'table' => 'dishes',
							                'alias' => 'Dish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = Dish.kitchen_id')
							             ),
							             array(
							                'table' => 'order_dishes',
							                'alias' => 'OrderDish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = OrderDish.kitchen_id')
							             ),
							         ),
		);

		$results = $controller->paginate('Kitchen');
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
					if(!empty($value[0]['distance']))
					{
						$value['Kitchen']['distance'] = $value[0]['distance'];
					}
					$resultArray[$k] = $value['Kitchen'];
					$k++;
				}
			}
		}
		return $resultArray;
	}

	/*
	 * Method	: searchKitchenForApi
	 * Author	: Bharat Borana
	 * Created	: 29 Dec, 2014
	 * @Kitchen data for dashboard
	 */
	public function searchKitchenForBulk($data,$controller)
	{
		$order = array();
		$conditions['Kitchen.status'] = 'on';

		App::import('model','SiteSetting');
		$siteSetting = new SiteSetting();
		$data['radius'] = $siteSetting->getIdVal('search_radius');

		if(!isset($data['date']) || empty($data['date']))
		{
			$data['day'] = strtolower(date('l'));
			$data['date'] = date('Y-m-d');
		}
		else
		{
			$data['day'] = strtolower(date('l',strtotime($data['date'])));
			$data['date'] = date('Y-m-d',strtotime($data['date']));
		}

		if(isset($data['user_id']) && !empty($data['user_id']))
		{
			$conditions['Kitchen.user_id !='] = $data['user_id'];
		}

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
		if(isset($data['dining_dine_in']) && $data['dining_dine_in']==1)
		{
			$conditions['Kitchen.dining_dine_in'] = $data['dining_dine_in'];
		}
		if(isset($data['dining_take_out']) && $data['dining_take_out']==1)
		{
			$conditions['Kitchen.dining_take_out'] = $data['dining_take_out'];
		}
		if(isset($data['location']) && !empty($data['location']))
		{
			$conditions['Kitchen.city LIKE'] = '%'.$data['location'].'%';
		}

		$field = 'Kitchen.*,User.*';
		$group = array('Kitchen.id');
		if(!empty($data['latitude']) && !empty($data['longitude']))
		{
			$center_lat = $data['latitude'];
			$center_lng = $data['longitude'];
			if(!empty($data['radius']))
			{
				$radius = $data['radius'];
			}
			//$field = "DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance";
			if(!empty($data['radius']))
			{
				//$fielddata = "'DISTINCT *, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( lat ) ) ) ) as distance'";
				//$group = array("Kitchen.id HAVING distance < $radius");
				$expression = "( 3959 * acos( cos( radians($center_lat) ) * cos( radians( Kitchen.lat ) ) * cos( radians( Kitchen.lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( Kitchen.lat ) ) ) )";
				$conditions[] = $expression . ' < '.$radius;
				$field = 'Kitchen.*,User.*,'.$expression.' as distance';
			}

		}


		$this->bindModel(array('hasMany'=>array(
											'Dish' => array(
													'fields' => array('Dish.*'),
												)
											)
										)
						);

		$conditions[] = array('Dish.status'=>'on');

		$conditions[] = array('OR'=>array(array('Dish.repeat !='=>0,'Dish.repeat !='=>'','Dish.repeat LIKE'=>'%'.$data['day'].'%','Dish.status'=>'on'),array('OR'=>array(array('Dish.repeat'=>0),array('Dish.repeat'=>'')),'DATE(Dish.modified)'=>$data['date'],'Dish.status'=>'on')));

		$this->hasMany['Dish']['conditions'] = array('OR'=>array(array('Dish.repeat !='=>0,'Dish.repeat !='=>'','Dish.repeat LIKE'=>'%'.$data['day'].'%','Dish.status'=>'on'),array('OR'=>array(array('Dish.repeat'=>0),array('Dish.repeat'=>'')),'DATE(Dish.modified)'=>$data['date'],'Dish.status'=>'on')));

		if(isset($data['cuisine']) && !empty($data['cuisine']))
		{
			$this->hasMany['Dish']['conditions'][] = array('Dish.cuisine' => explode(',', $data['cuisine']),'Dish.status'=>'on');
			$conditions['Dish.cuisine'] = explode(',', $data['cuisine']);
		}
		if(isset($data['diet']) && !empty($data['diet']))
		{
			$this->hasMany['Dish']['conditions'][] = array('Dish.diet' => explode(',', $data['diet']),'Dish.status'=>'on');
			$conditions[] = array('Dish.diet' => explode(',', $data['diet']),'Dish.status'=>'on');
			//$conditions['Dish.diet'] = explode(',', $data['diet']);
		}
		if(isset($data['keyword']) && !empty($data['keyword']))
		{
			$conditions[] = array('OR'=>array('Kitchen.name LIKE' => '%'.$data['keyword'].'%', 'Kitchen.description LIKE' => '%'.$data['keyword'].'%', 'Dish.name LIKE' => '%'.$data['keyword'].'%'));
		}
		if(!empty($data['time']))
		{
			$data['time'] = date("H:i:s", strtotime($data['time']));
			$conditions['Dish.serve_start <='] = $data['time'];
			$conditions['TIMEDIFF(Dish.serve_end, TIME(Dish.lead_time * 10000)) >='] = $data['time'];
			$this->hasMany['Dish']['conditions'][] = array('Dish.serve_start <=' => $data['time'],'TIMEDIFF(Dish.serve_end, TIME(Dish.lead_time * 10000)) >=' =>$data['time'],'Dish.status'=>'on');
		}

		$orderBy = "Kitchen.id";
		if(isset($data['is_rated_high']) && $data['is_rated_high']==1)
				$orderBy = "Kitchen.avg_rating DESC";

		if(isset($data['is_popular']) && $data['is_popular']==1)
				$orderBy = "COUNT(OrderDish.id) DESC";

		if(isset($data['is_popular']) && isset($data['is_rated_high']) && $data['is_popular']==1 && $data['is_rated_high']==1)
				$orderBy = "Kitchen.avg_rating DESC, COUNT(OrderDish.id) DESC";

		$results = $this->find('all',array(
							'fields'=> array($field),
							'conditions' => $conditions,
							'group' => $group,
							'order' => $orderBy,
							'joins' => array(
							             array(
							                'table' => 'dishes',
							                'alias' => 'Dish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = Dish.kitchen_id')
							             ),
							             array(
							                'table' => 'order_dishes',
							                'alias' => 'OrderDish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = OrderDish.kitchen_id')
							             ),
							         ),
		));

		$resultArray= array();
		if(!empty($results))
		{
			$k = 0;
			foreach ($results as $key => $value) {
				if(!empty($value['Dish']))
				{
					$i = 0;
					$KitData = array();
					$KitData['Kitchen']['id'] = $value['Kitchen']['id'];
					$KitData['Kitchen']['name'] = $value['Kitchen']['name'];
					$KitData['Kitchen']['rating'] = $value['Kitchen']['avg_rating'];
					$KitData['Kitchen']['lat'] = $value['Kitchen']['lat'];
					$KitData['Kitchen']['lng'] = $value['Kitchen']['lng'];
					$KitData['Kitchen']['address'] = $value['Kitchen']['address'];
					if(!empty($value[0]['distance']))
					{
						$KitData['Kitchen']['distance'] = $value[0]['distance'];
					}
					$resultArray[$k] = $KitData['Kitchen'];
					$k++;
				}
			}
		}
		return $resultArray;
	}

	/*
	 * Method	: searchKitchen
	 * Author	: Bharat Borana
	 * Created	: 29 Dec, 2014
	 * @get all kitchens specific to search criteria any kitchen
	 */
	function searchKitchen($controller, $data)
	{
		$order = array();
		$conditions['Kitchen.status'] = 'on';

		App::import('model','SiteSetting');
		$siteSetting = new SiteSetting();
		$data['radius'] = $siteSetting->getIdVal('search_radius');

		if(!isset($data['date']) || empty($data['date']))
		{
			$data['day'] = strtolower(date('l'));
			$data['date'] = date('Y-m-d');
		}
		else
		{
			$data['day'] = strtolower(date('l',strtotime($data['date'])));
			$data['date'] = date('Y-m-d',strtotime($data['date']));
		}

		if(isset($data['login_user_id']) && !empty($data['login_user_id']))
		{
			$conditions['Kitchen.user_id !='] = $data['login_user_id'];
		}

		if(!empty($data['latitude']) && !empty($data['longitude']))
		{
			$center_lat = $data['latitude'];
			$center_lng = $data['longitude'];
			if(!empty($data['radius']))
			{
				$radius = $data['radius'];
			}

			if($radius)
			{
				$expression = "( 3959 * acos( cos( radians($center_lat) ) * cos( radians( Kitchen.lat ) ) * cos( radians( Kitchen.lng ) - radians($center_lng) ) + sin( radians($center_lat) ) * sin( radians( Kitchen.lat ) ) ) )";
				$conditions[] = $expression . ' < '.$radius;
				$field = 'Kitchen.*,User.*,'.$expression.' as distance';
			}
		}
		else if(empty($data['latitude']) && empty($data['longitude']) && !empty($data['address']))
		{
			$geoAddress = $this->_getAddressFormGeoode($data['address']);

			App::uses('CakeSession', 'Model/Datasource');
			if(isset($geoAddress['lat']) && !empty($geoAddress['lat']))
            	CakeSession::write('search_data.latitude',$geoAddress['lat']);

            if(isset($geoAddress['lng']) && !empty($geoAddress['lng']))
            	CakeSession::write('search_data.longitude',$geoAddress['lng']);

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

		if(isset($data['dining_dine_in']) && $data['dining_dine_in']==1)
		{
			$conditions['Kitchen.dining_dine_in'] = $data['dining_dine_in'];
		}
		if(isset($data['dining_take_out']) && $data['dining_take_out']==1)
		{
			$conditions['Kitchen.dining_take_out'] = $data['dining_take_out'];
		}
		if(isset($data['location']))
		{
			$conditions['Kitchen.city LIKE'] = '%'.$data['location'].'%';
		}


		$field = 'Kitchen.*,User.*';
		$group = array('Kitchen.id');



		$this->bindModel(array('hasMany'=>array(
											'Dish' => array(
													'fields' => array('Dish.*'),
												)
											)
										)
						);

		$conditions[] = array('Dish.status'=>'on');

		$conditions[] = array('OR'=>array(array('Dish.repeat !='=>0,'Dish.repeat !='=>'','Dish.repeat LIKE'=>'%'.$data['day'].'%','Dish.status'=>'on'),array('OR'=>array(array('Dish.repeat'=>0),array('Dish.repeat'=>'')),'DATE(Dish.modified)'=>$data['date'],'Dish.status'=>'on')));

		$this->hasMany['Dish']['conditions'] = array('OR'=>array(array('Dish.repeat !='=>0,'Dish.repeat !='=>'','Dish.repeat LIKE'=>'%'.$data['day'].'%','Dish.status'=>'on'),array('OR'=>array(array('Dish.repeat'=>0),array('Dish.repeat'=>'')),'DATE(Dish.modified)'=>$data['date'],'Dish.status'=>'on')));

		if($data['date'] == date('Y-m-d') && isset($data['time']))
		{
			$start_date = new DateTime($data['time']);
			$since_start = $start_date->diff(new DateTime(date("H:i:s")));
			$hoursDiff = $since_start->h;
			$this->hasMany['Dish']['conditions'][] = array('Dish.lead_time <=' => $hoursDiff);
			$conditions['Dish.lead_time <='] = $hoursDiff;
		}
		if(isset($data['cuisine']))
		{
			$this->hasMany['Dish']['conditions'][] = array('Dish.cuisine' => explode(',', $data['cuisine']),'Dish.status'=>'on');
			$conditions['Dish.cuisine'] = explode(',', $data['cuisine']);
		}

		if(isset($data['diet']))
		{
			$this->hasMany['Dish']['conditions'][] = array('Dish.diet' => explode(',', $data['diet']),'Dish.status'=>'on');
			$conditions['Dish.diet'] = explode(',', $data['diet']);
		}

		if(isset($data['keyword']))
		{
			$conditions[] = array('OR'=>array('Kitchen.name LIKE' => '%'.$data['keyword'].'%', 'Kitchen.description LIKE' => '%'.$data['keyword'].'%', 'Dish.name LIKE' => '%'.$data['keyword'].'%'));
		}

		if(!empty($data['time']))
		{
			$data['time'] = date("H:i:s", strtotime($data['time']));
			$conditions['Dish.serve_start <='] = $data['time'];
			$conditions['TIMEDIFF(Dish.serve_end, TIME(Dish.lead_time * 10000)) >='] = $data['time'];
			$this->hasMany['Dish']['conditions'][] = array('Dish.serve_start <=' => $data['time'],'TIMEDIFF(Dish.serve_end, TIME(Dish.lead_time * 10000)) >=' =>$data['time'],'Dish.status'=>'on');
		}



		$orderBy = "Kitchen.id";
		if(isset($data['is_rated_high']) && $data['is_rated_high']==1)
				$orderBy = "Kitchen.avg_rating DESC";

		if(isset($data['is_popular']) && $data['is_popular']==1)
				$orderBy = "COUNT(OrderDish.id) DESC";

		if(isset($data['is_popular']) && isset($data['is_rated_high']) && $data['is_popular']==1 && $data['is_rated_high']==1)
				$orderBy = "Kitchen.avg_rating DESC, COUNT(OrderDish.id) DESC";

		$controller->paginate = array(
							'fields'=> array($field),
							'conditions' => $conditions,
							'group' => $group,
							'order' => $orderBy,
							'joins' => array(
							             array(
							                'table' => 'dishes',
							                'alias' => 'Dish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = Dish.kitchen_id')
							             ),
							             array(
							                'table' => 'order_dishes',
							                'alias' => 'OrderDish',
							                'type' => 'LEFT',
							                'conditions' => array('Kitchen.id = OrderDish.kitchen_id')
							             ),
							         ),
		);

		$results = $controller->paginate('Kitchen');

		return $results;
	}

	/*
	 * Method	: kitchenDashboard
	 * Author	: Bharat Borana
	 * Created	: 22 January, 2015
	 * @get all dishes specific to any kitchen
	 */
	function kitchenDashboard($controller, $data)
	{
		$conditions['Kitchen.status'] = 'on';
		$conditions['Kitchen.id'] = $data['kitchen_id'];

		App::import('model','SiteSetting');
		$siteSetting = new SiteSetting();
		$data['radius'] = $siteSetting->getIdVal('search_radius');

		if(!isset($data['date']) || empty($data['date']))
		{
			$data['day'] = strtolower(date('l'));
			$data['date'] = date('Y-m-d');
		}
		else
		{
			$data['day'] = strtolower(date('l',strtotime($data['date'])));
			$data['date'] = date('Y-m-d',strtotime($data['date']));
		}


		$this->bindModel(array('hasMany'=>array(
											'Dish' => array(
													'fields' => array('Dish.*'),
												)
											)
										)
						);


		$this->hasMany['Dish']['conditions'] = array('OR'=>array(array('Dish.repeat !='=>'','Dish.repeat !='=>0,'Dish.repeat LIKE'=>'%'.$data['day'].'%','Dish.status'=>'on'),array('OR'=>array(array('Dish.repeat'=>0),array('Dish.repeat'=>'')),'DATE(Dish.modified)'=>$data['date'],'Dish.status'=>'on')));

		if(isset($data['cuisine']))
			$this->hasMany['Dish']['conditions'][] = array('Dish.cuisine LIKE' => '%'.$data['cuisine'].'%','Dish.status'=>'on');

		if(isset($data['diet']))
			$this->hasMany['Dish']['conditions'][] = array('Dish.diet' => explode(',', $data['diet']),'Dish.status'=>'on');


		if($data['date'] == date('Y-m-d') && isset($data['time']))
		{
			$start_date = new DateTime($data['time']);
			$since_start = $start_date->diff(new DateTime(date("H:i:s")));
			$hoursDiff = $since_start->h;
			$this->hasMany['Dish']['conditions'][] = array('Dish.lead_time <=' => $hoursDiff,'');
			$conditions['Dish.lead_time <='] = $hoursDiff;
		}
		if(!empty($data['time']))
		{
			$data['time'] = date("H:i:s", strtotime($data['time']));
			$conditions['Dish.serve_start <='] = $data['time'];
			$conditions['Dish.serve_end >='] = $data['time'];
			if(isset($data['keyword']))
			{
				$this->hasMany['Dish']['conditions'][] = array('OR'=>array('AND'=>array('Dish.name LIKE' => '%'.$data['keyword'].'%','Dish.status'=>'on'),'AND'=>array('Dish.serve_start <=' => $data['time'],'Dish.serve_end >=' =>$data['time'],'Dish.status'=>'on')));
			}
			else
			{
				$this->hasMany['Dish']['conditions'][] = array('Dish.serve_start <=' => $data['time'],'Dish.serve_end >=' =>$data['time'],'Dish.status'=>'on');
			}
		}

		$kDetails = $this->find('first',
										array(	'conditions' => $conditions,
												'recursive'	=> 2,
												'joins' => array(
									             array(
									                'table' => 'dishes',
									                'alias' => 'Dish',
									                'type' => 'LEFT',
									                'conditions' => array('Kitchen.id = Dish.kitchen_id')
									             ),
									             array(
									                'table' => 'order_dishes',
									                'alias' => 'OrderDish',
									                'type' => 'LEFT',
									                'conditions' => array('Kitchen.id = OrderDish.kitchen_id')
									             ),
									         ),
										));

		return $kDetails;
	}

	/*
	 * Method	: getallorderIds
	 * Author	: Bharat Borana
	 * Created	: 29 Dec, 2014
	 * @get all orders specific to any kitchen
	 */
	function getallorderIds($userId=null)
	{
		$orderIds='';
		if(!empty($userId)){
				$kitchenOrders = $this->find('first', array(
							'conditions' => array('Kitchen.user_id'=>$userId),
							'contain' => array(
									'OrderDish' => array(
										'fields' => array('CONCAT(GROUP_CONCAT(DISTINCT(OrderDish.order_id))) as oIds'),
									)
							)
						));
		}
		if(isset($kitchenOrders['OrderDish'][0]['OrderDish'][0]['oIds']))
			$orderIds = $kitchenOrders['OrderDish'][0]['OrderDish'][0]['oIds'];
		return $orderIds;
	}

	/*
	 * Method	: getkitchendatafordashboard
	 * Author	: Bharat Borana
	 * Created	: 29 Dec, 2014
	 * @Kitchen data for dashboard
	 */
	function getKitchenDataForDashboard($userId=null)
	{
		$orderIds['oIds'] = '';
		$orderIds['activeDish'] = 0;
		if(!empty($userId)){
			    $kitchenOrders = $this->find('first', array(
							'conditions' => array('Kitchen.user_id'=>$userId),
							'contain' => array(
									'Dish' => array(
										'fields'=>array('COUNT(Dish.id) as activeDish'),
										'conditions'=>array('Dish.status'=>'on'),
									 ),
									'OrderDish' => array(
										'fields' => array('CONCAT(GROUP_CONCAT(DISTINCT(OrderDish.order_id))) as oIds, COUNT(DISTINCT(OrderDish.order_id)) as totalOrder'),
									)
							)
						));

				$kitchensOfflineDish = $this->find('first', array(
							'conditions' => array('Kitchen.user_id'=>$userId),
							'contain' => array(
									'Dish' => array(
										'fields'=>array('id','name','modified'),
										'conditions'=>array('Dish.status'=>'off'),
										'order'=>'Dish.modified DESC',
									 ),
							)
						));
		}
		if(isset($kitchensOfflineDish['Dish']))
			$orderIds['offlineDish'] = $kitchensOfflineDish['Dish'];
		if(isset($kitchenOrders['Kitchen']['id']))
			$orderIds['kitchenId'] = $kitchenOrders['Kitchen']['id'];
		if(isset($kitchenOrders['OrderDish'][0]['OrderDish'][0]['oIds']))
			$orderIds['oIds'] = $kitchenOrders['OrderDish'][0]['OrderDish'][0]['oIds'];
		if(isset($kitchenOrders['OrderDish'][0]['OrderDish'][0]['totalOrder']))
			$orderIds['dishesServed'] = $kitchenOrders['OrderDish'][0]['OrderDish'][0]['totalOrder'];
		if(isset($kitchenOrders['Dish'][0]['Dish'][0]['activeDish'])){
			$orderIds['activeDish'] = $kitchenOrders['Dish'][0]['Dish'][0]['activeDish'];
		}
		return $orderIds;
	}

	/*
	 * Method	: getfeaturedKitchens
	 * Author	: Bharat Borana
	 * Created	: 31 Dec, 2014
	 * @Featured Kitchen data for app CMS
	 */
	public function getfeaturedKitchens(){
		   $featuredKitchen = array();
		   $kData = $this->find('all',array('conditions'=>array('Kitchen.is_featured'=>1),
											'fields'=>array('Kitchen.id','Kitchen.name','Kitchen.description','Kitchen.lat','Kitchen.lng','Kitchen.address','Kitchen.cover_photo','Kitchen.avg_rating','Kitchen.dining_dine_in','Kitchen.dining_take_out'),
											'contain'=>array(
												'User'=>array(
													'fields'=>array('User.id','User.name','User.image'),
												),
												'Dish'=>array(
													'fields'=>array('Dish.*'),
													'conditions'=>array('Dish.status'=>'on'),
												),
												'UploadImage'=>array(
													'fields'=>array('UploadImage.name'),
													'order'=>'UploadImage.id DESC',
													'limit'=>array(0,1),
												)
											)
								));
		   if(isset($kData) && !empty($kData)){
				foreach($kData as $key=>$kitchenData){
					if(isset($kitchenData['Kitchen']['cover_photo']))
						$kitchenData['Kitchen']['cover_photo']  = (!empty($kitchenData['Kitchen']['cover_photo'])) ? Router::url('/'.KITCHEN_IMAGE_URL.$kitchenData['Kitchen']['cover_photo'],true) : "";

					if(isset($kitchenData['UploadImage'][0]['name']))
						$kitchenData['UploadImage'][0]['name']  = (!empty($kitchenData['UploadImage'][0]['name'])) ? Router::url('/'.KITCHEN_IMAGE_URL.$kitchenData['UploadImage'][0]['name'],true) : "";

					if(isset($kitchenData['User']['image']))
						$kitchenData['User']['image']  = (!empty($kitchenData['User']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$kitchenData['User']['image'],true) : "";
					$featuredKitchen[] = $kitchenData;
				}
			}
		return $featuredKitchen;
	}

} ?>
