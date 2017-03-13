<?php
App::uses('AppController', 'Controller');
/**
 * Countries Controller
 *
 * @property Requests $Discussions
 * @property PaginatorComponent $Paginator
 */
class RequestdishesController extends AppController {
	
	public $name = 'Requestdishes';
  public $components = array('SuiteTest','Push');
	/*
     * Purpose: New request for dishes
     * @Created: Bharat Borana
     * @Date: 16 Jan 15
     */
    
    function newrequest()
    {
        $userId = $this->Auth->user('id');
        $this->loadModel('Allergy');
		    $this->loadModel('Request');
        $this->loadModel('User');
		
    		$allergies = $this->Allergy->find('list',array('conditions'=>array('Allergy.is_active'=>1)));

    	  $this->loadModel('Cuisine');
        $cuisines = $this->Cuisine->find('list',array('conditions'=>array('Cuisine.is_active'=>1)));
        
        $userAddress = $this->User->findById($userId,array('address'));
        
        if(isset($userAddress['User']['address']) && !empty($userAddress['User']['address']))
        {
          $geoAddress = $this->SuiteTest->_getAddressFormGeoode($userAddress['User']['address']);
          $locationData['Request']['lat'] = (!empty($geoAddress['lat'])) ? $geoAddress['lat'] : "";
          $locationData['Request']['lng'] = (!empty($geoAddress['lng'])) ? $geoAddress['lng'] : "";
        }
        else
        {
          $locationData['Request']['lat'] = "";
          $locationData['Request']['lng'] = "";
        }
    		
        if($this->request->is(array('post','put')))
        {
            $allergy = '';
      			if(isset($this->request->data['Request']['allergy']) && !empty($this->request->data['Request']['allergy'])){
      				foreach ($this->request->data['Request']['allergy'] as $key => $value) {
      					if($value==1)
      						$allergy .= $key.',';
      				}
      			}
      			
      			if(!empty($allergy))
            {
              $this->request->data['Request']['allergies'] = trim($allergy,',');
      			}
            
            $this->request->data['Request']['user_id'] = $userId;
            $this->request->data['Request']['timestamp'] = time();
            
            $this->Request->create();
      			if($this->Request->save($this->request->data['Request']))
            {
              $this->Session->setFlash(__('Request has been added successfully.'),'success');
              return $this->redirect(array('action' => 'myrequest'));
            }
            else
            {
              $this->Session->setFlash(__('Sorry, this request can not be added.'),'error');
            }
      	}
        else
        {
          $this->request->data = $locationData;
        }

      	$this->set(compact(array('allergies','cuisines')));	
    }
    
    /*
     * Purpose: Myrequest API, It will return requests of a user
     * @Created: Sandeep Jain
     * @Date: 04 Dec 14
     * @Parameters: user_id (int)
     * @Response: users request in json fomrat
     */
    
    function myrequest()
    {
       $userId = $this->Auth->user('id');           
       $this->loadModel('Request');               
       $this->loadModel('User');
       $userDetails = $this->User->findById($userId);
       $req = $this->Request->find('all', array('conditions'=>array('Request.user_id '=>$userId), 'order'=>'Request.id desc', 'recursive'=>2, 'contain'=>array('Cuisine','User'=>array('id','name','image'), 'RequestAnswer.Dish'=>array('name','kitchen_id','id','status','created'), 'RequestAnswer.Dish.Kitchen'=>array('id','name'), 'RequestAnswer.Dish.UploadImage'=>array('name')) ));
       
       $waitingForAns = '';
       if(isset($userDetails['Kitchen']['id']) && !empty($userDetails['Kitchen']['id']))
       {
        $latKitchen = $userDetails['Kitchen']['lat'];
        $lngKitchen = $userDetails['Kitchen']['lng'];
        $expression = "( 3959 * acos( cos( radians($latKitchen) ) * cos( radians( Request.lat ) ) * cos( radians( Request.lng ) - radians($lngKitchen) ) + sin( radians($latKitchen) ) * sin( radians( Request.lat ) ) ) )";
        $radius = 40;
        $waitingForAns = $this->Request->find('all',
                                         array('conditions'=>array(
                                                                  'Request.user_id !='=>$userId,
                                                                  'Request.created >='=>date('Y-m-d',strtotime('-7 Days')),
                                                                  $expression . ' < '.$radius
                                                                  ),
                                                                  'order'=>'Request.id desc',
                                                                  'recursive'=>2,
                                                                  'joins'=>array(
                                                                    ),
                                                                  'contain'=>array(
                                                                    'Cuisine',
                                                                    'User'=>array('id','name','image'),
                                                                    'RequestAnswer.Dish'=>array('name','kitchen_id','id','status','created'),
                                                                    'RequestAnswer.Dish.Kitchen'=>array('id','name'),
                                                                    'RequestAnswer.Dish.UploadImage'=>array('name'))
                                                                  )
                                            );
       }
       
       
       if (!empty($req))
       {
           $i=0;
           foreach ($req as $rq)
           {                         
                $ret['Request'][$i]['dish_name'] = $rq['Request']['id'];
                $ret['Request'][$i]['dish_name'] = $rq['Request']['dish_name'];
                $ret['Request'][$i]['message'] = $rq['Request']['message'];
                $ret['Request'][$i]['allergies'] = $rq['Request']['allergies'];
                $ret['Request'][$i]['cuisine_id'] = $rq['Request']['cuisine_id'];
                $ret['Request'][$i]['timestamp'] = $rq['Request']['timestamp'];
                ///$ret['Request'][$i]['serve_start_time'] = $rq['RequestAnswer']['Dish']['serve_start_time'];
                //$ret['Request'][$i]['serve_start'] = $rq['RequestAnswer']['Dish']['serve_start'];
                $ret['Request'][$i]['request_id'] = $rq['Request']['id'];  
                $ret['Request'][$i]['user_name'] = $rq['User']['name'];
                $ret['Request'][$i]['user_id'] = $rq['User']['id'];
                $ret['Request'][$i]['image'] = ($rq['User']['image'] != "") ? Router::url('/'.PROFILE_IMAGE_URL.$rq['User']['image'],true) : '';
                if (count($rq['RequestAnswer'])>0)
                {
                    foreach ($rq['RequestAnswer'] as $ra)
                    {
                        //pr($ra); exit;
                        $ret['Request'][$i]['Answer'][] = array('dish_id'=>$ra['dish_id'], 'answer_id'=>$ra['id'], 'kitchen_id'=>$ra['Dish']['kitchen_id'], 'serve_start_time'=>$ra['Dish']['serve_start_time'], 'serve_start'=>$ra['Dish']['serve_start'], 'kitchen_name'=>$ra['Dish']['Kitchen']['name'], 'dish_name'=>$ra['Dish']['name'], 'serve_end_time'=>$ra['Dish']['serve_end_time'], 'serve_end'=>$ra['Dish']['serve_end'], 'lead_time'=>$ra['Dish']['lead_time'], 'availability'=>$ra['Dish']['status'], 'servertime'=>$ra['Dish']['created'], 'photo' => isset($ra['Dish']['UploadImage']['0']['name']) ? Router::url('/'.$ra['Dish']['UploadImage']['0']['name'],true) : ''); 
                    }
                }
                $i++;
           }
       }
       $this->set(compact(array('req','waitingForAns','userDetails')));     
    }
    
    /*
     * Purpose: Myrequest delete request
     * @Created: Bharat borana
     * @Date: 16 Jan 15
     * @Response: success or failed message
     */
    
    function deleterequest()
    {
      $this->autoRender = false;
      $result = 0;
      if($this->request->is('ajax'))
      {
          $this->loadModel('Request');
          $userId = $this->Auth->user('id');
         $chk = $this->Request->find('first', array('conditions'=>array('Request.user_id'=>$userId, 'Request.id'=>$this->request->data['id'])));               
         if (!empty($chk))
         {
             $this->Request->id = $this->request->data['id'];
             if ($this->Request->delete())
             {
                 $result = 1;
             }
          }
      }
      echo $result;
    }
    
    /*
     * Purpose: To answer of a request
     * @Created: Sandeep Jain
     * @Date: 04 Dec 14
     * @Parameters: user_id (int) and dish_id (int), request_id (int)
     * @Response: success or failed
     */
    
    function answer_request($request_id)
    {
      $this->loadModel('Dish');
      $this->loadModel('User');
      $userId = $this->Auth->user('id');
      $userDetails = $this->User->findById($userId);
      
      $myAllDishes = array();
      if(isset($userDetails['Kitchen']['id']) && !empty($userDetails['Kitchen']['id']))
        $myAllDishes = $this->Dish->find('all',array('conditions'=>array('Dish.kitchen_id'=>$userDetails['Kitchen']['id'])));
      
      if($this->request->is(array('post','put')))
      {
          $errors = array();
          $this->loadModel('Request');
          $chk = $this->Request->findById($request_id);
          if (empty($chk))
          {
              $errors[][0] = 'Invalid Request';
          }
          if(isset($this->request->data['RequestAnswer']['dish_id']) && !empty($this->request->data['RequestAnswer']['dish_id']))
          {
            $chkDish = $this->Dish->findById($this->request->data['RequestAnswer']['dish_id']);
            if (empty($chkDish))
            {
                $errors[][0] =  'Invalid Answer';
            }  
          }
          else
          {
                $errors[][0] =  'Please select Dish of your choice.';
          }
          
          if(empty($errors))
          {
            $this->loadModel('RequestAnswer'); 
            $checkAnswer = $this->RequestAnswer->find('count',array('conditions'=>array('RequestAnswer.user_id'=>$userId,'RequestAnswer.request_id'=>$request_id,'RequestAnswer.dish_id'=>$this->request->data['RequestAnswer']['dish_id'])));
            if(!$checkAnswer)
            {
                $ins_data = array();
                $this->request->data['RequestAnswer']['user_id'] = $userId;               
                $this->request->data['RequestAnswer']['request_id'] = $request_id;
                $this->request->data['RequestAnswer']['timestamp'] = time();        
                $this->RequestAnswer->create();
                if ($this->RequestAnswer->save($this->request->data['RequestAnswer']))
                {
                   $this->Session->setFlash(__('Your answer has been added.'),'success');
                   $this->loadModel('ActivityLog');
                   $data['timestamp'] = time();
                    //Update Activity Log For Answer Request Activity
                    $this->ActivityLog->updateLog($chk['Request']['user_id'],7,$this->RequestAnswer->getLastInsertID(),$data['timestamp']);
                   
                   $message['_message']['m'] = "Your dish request ".$chk['Request']['dish_name']." has been answered by ".$chkDish['Kitchen']['name'];
                   $pushNoti = $this->Push->send($chk['Request']['user_id'],$message,$chkDish['Kitchen']['id'],1);
                      
                   return $this->redirect(array('action'=>'myrequest'));
                }
                else
                {
                    $this->Session->setFlash(__('Invalid answer.'),'error');
                }
            }
            else
            {
                $this->Session->setFlash(__('This answer has been already added.'),'error');
            }
          }
          else
          {
            $this->set('errors',$errors);
          }          
      }

      $this->set(compact(array('myAllDishes','userDetails')));
    }
}
