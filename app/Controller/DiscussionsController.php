<?php
App::uses('AppController', 'Controller');
/**
 * Countries Controller
 *
 * @property Discussions $Discussions
 * @property PaginatorComponent $Paginator
 */
class DiscussionsController extends AppController {
	
	public $name = 'Discussions';
	
	/**
	 * Method	: admin_community
	 * Author	: Praveen Pandey
	 * Created	: 15 Oct, 2014
	 */
	 public function admin_community()
	 {
	 	$this->loadModel('Community');
		if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if (!empty($ids))
			{
				foreach ($ids as $id)
				{
					if ($action == "Delete")
					{
						$this->Community->id = $id;
						$this->Community->delete();
					}
					
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		
		$this->set('rows', $this->paginate('Community'));
	 }
	 
	/**
	 * Method	: admin_add_community
	 * Author	: Praveen Pandey
	 * Created	: 15 Oct, 2014
	 */
	 public function admin_add_community($id = null)
	 {
	 	$this->loadModel('Community');
	  	if($this->request->data)
		{
			$this->Community->create();
			if($this->Community->save($this->request->data))
			{
				$this->Session->setFlash('Community saved successfully');
				$this->redirect(array('action'=>'community'));
			}
			else {
				$this->Session->setFlash('Please try again');
			}
		}
		if(!empty($id))
		{
			$this->request->data = $this->Community->findById($id);
		}
	 }
	 
	 /**
	 * Method	: admin_discussion
	 * Author	: Praveen Pandey
	 * Created	: 16 Oct, 2014
	 */
	 public function admin_discussion()
	 {
		if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if (!empty($ids))
			{
				foreach ($ids as $id)
				{
					if ($action == "Delete")
					{
						$this->Discussion->id = $id;
						$this->Discussion->delete();
					}
					
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		$this->Discussion->bindModel(array('belongsTo'=>array('Community')));
		$this->set('rows', $this->paginate('Discussion'));
	 }
	 
	/**
	 * Method	: admin_add_discussion
	 * Author	: Praveen Pandey
	 * Created	: 16 Oct, 2014
	 */
	 public function admin_add_discussion($id = null)
	 {
	  	if($this->request->data)
		{
			if(empty($this->request->data['Discussion']['user_id']))
			{
				$this->request->data['Discussion']['user_id'] = $this->Auth->user('id');
			}
			$this->Discussion->create();
			if($this->Discussion->save($this->request->data))
			{
				$this->Session->setFlash('Discussion saved successfully');
				$this->redirect(array('action'=>'discussion'));
			}
			else {
				$this->Session->setFlash('Please try again');
			}
		}
		if(!empty($id))
		{
			$this->request->data = $this->Discussion->findById($id);
		}
		$this->loadModel('Community');
		$communities = $this->Community->find('list',array('fields'=>array('Community.id','Community.title')));
		$this->set(compact('communities'));
	 }
	 
	 /**
	  * Method	: admin_discussion_comments
	  * Author 	: Praveen Pandey
	  * Created	: 01 Nov, 2014
	  * Purpose	: all comments on discussion lists
	  */
	  public function admin_discussion_comments()
	  {
	  	$this->loadModel('Comment');
		if ($this->request->is("post"))
		{
			$request = $this->request->data;			
			$ids = $request['ids'];
			$action = $request['listingAction'];
			if(!empty($this->request->data['ids']))
			{
				switch($this->request->data['action'])
				{
					case "delete":
						$this->Comment->deleteAll(array('Comment.id' => $this->request->data['ids']));
					break;
					case "active":
						$this->Comment->updateAll(array('is_publish' => 1), array('Comment.id' =>  $this->request->data['ids'] ));
					break;
					case "deactive":
						$this->Comment->updateAll(array('is_publish' => 0), array('Comment.id' =>  $this->request->data['ids'] ));
					break;
				}
			}
			$this->Session->setFlash("Operation successful", 'success');
			$this->redirect($this->referer());
		}
		$this->paginate = array(
					'order'=> 'Comment.created DESC',
					'contain' => array('User'=>array('id','name'),'Discussion'=>array('id','title'))
		);
		$rows = $this->paginate('Comment');
		$this->set('rows', $rows);
	  }
	  
	 /**
	  * Method	: Community index page
	  * Author 	: Bharat Borana
	  * Created	: 05 Jan 2015
	  * Purpose	: All Community lists, Explore list
	  */
	  public function index()
	  {
		$this->loadModel('Community');
	  	$this->Community->bindModel(array('hasMany'=>array('Discussion')));
	  	$this->loadModel('Discussion');
		$this->Discussion->bindModel(array(
								'hasMany'=>array(
												'Comment'=> array(
															'className'=> 'Comment',
															'conditions' => array('Comment.is_publish'=>1),
															'fields'=> array('Comment.id')
												))));
		if(empty($community_id)){
			$commynityName = $this->Auth->user('community');
			if(empty($commynityName))
				return $this->redirect(array('action'=>'explore_com'));
			else{
				$communityDetails = $this->Community->find('first',array('recursive'=>2,'conditions'=>array('Community.title Like("'.$commynityName.'")')));
				}
		}
		else{
			$communityDetails = $this->Community->find('first',array('recursive'=>2,'conditions'=>array('Community.id' => $community_id)));
		}

		$this->Community->unbindModel(array('hasMany'=>array('Discussion')));
		
		$this->set('communityDetails',$communityDetails);

		$this->render('community');
	  }

	  /**
	  * Method	: Community details page
	  * Author 	: Bharat Borana
	  * Created	: 05 Jan 2015
	  * Purpose	: All Discussion according to particular community
	  */
	  public function community($community_id = null)
	  {
	  	$this->loadModel('Community');
	  	$this->Community->bindModel(array('hasMany'=>array('Discussion')));
	  	$this->loadModel('Discussion');
		$this->Discussion->bindModel(array(
								'hasMany'=>array(
												'Comment'=> array(
															'className'=> 'Comment',
															'conditions' => array('Comment.is_publish'=>1),
															'fields'=> array('Comment.id')
												))));
		if(empty($community_id)){
			$commynityName = $this->Auth->user('community');
			if(empty($commynityName))
				return $this->redirect(array('action'=>'index'));
			else{
				$communityDetails = $this->Community->find('first',array('recursive'=>2,'conditions'=>array('Community.name Like('.$commynityName.')')));
				}
		}
		else{
			$communityDetails = $this->Community->find('first',array('recursive'=>2,'conditions'=>array('Community.id' => $community_id)));
		}

		$this->Community->unbindModel(array('hasMany'=>array('Discussion')));
		
		$this->set('communityDetails',$communityDetails);
	  }
	  
	  /**
	  * Method	: Discussion details page
	  * Author 	: Bharat Borana
	  * Created	: 05 Jan 2015
	  * Purpose	: Particular Discussion details
	  */
	  public function discussion($discussion_id = null)
	  {
		if(empty($discussion_id))
			return $this->redirect(array('action'=>'community'));

		$userId = $this->Auth->user('id');
		$this->loadModel('User');
		$userDetails = $this->User->findById($userId);
		$this->loadModel('Discussion');
		$this->loadModel('Comment');
		$this->Discussion->bindModel(array(
								'belongsTo'=>array('Community'),
								'hasMany'=>array(
												'Comment'=> array(
															'className'=> 'Comment',
															'conditions' => array('Comment.is_publish'=>1),
															'fields'=> array('Comment.comment','Comment.date_time','Comment.user_id','Comment.created')
												)),
		                                                            'belongsTo' => array('User')
		                ));
		$this->Comment->bindModel(array(
		'belongsTo'=>array(
					'User'=>array(
								'className' => 'User',
								'fields'=>	array('User.name','User.image')
					))));
		$discussion = $this->Discussion->find('first', array('conditions'=>array('Discussion.id'=>$discussion_id),'recursive'=>2));

		$this->set(compact(array('discussion','userDetails')));
	  }

	  /**
	  * Method	: Explore Community page
	  * Author 	: Bharat Borana
	  * Created	: 07 Jan 2015
	  * Purpose	: All Community lists, Explore list
	  */
	  public function explore_com()
	  {
			$this->loadModel('Community');		
			$communities = $this->Community->find('all',array('conditions'=> array('Community.is_active'=>1),'fields'=>array('id','title','discussion_count')));
			$this->set('communities',$communities);
	  }

	   /**
	  * Method	: Add comment data
	  * Author 	: Bharat Borana
	  * Created	: 08 Jan 2015
	  * Purpose	: Add comment using ajax
	  */
	  public function addComment()
	  {
	  	$this->layout = false;
	  	if($this->request->is('ajax')){	
			$this->loadModel('Comment');
			$this->request->data['Comment']['user_id'] = $this->Auth->user('id');
	  		$this->Comment->set($this->request->data['Comment']);
	  		if($this->Comment->save($this->request->data['Comment'])){
	  			$this->Comment->bindModel(array(
							'belongsTo'=>array(
										'User'=>array(
													'className' => 'User',
													'fields'=>	array('User.name','User.image')
										))));
			$commentData = $this->Comment->findById($this->Comment->getLastInsertId());
			$this->set('commentData',$commentData);
	  		}
	  	}
	  }

	  /**
	 * Method	: new_discussion
	 * Author 	: Bharat Borana
	 * Created	: 08 Jan, 2015
	 * Purpose	: Create new discussion
	 */
	 	public function add()
		{
			$this->loadModel('Community');		
			$communities = $this->Community->find('list',array('conditions'=> array('Community.is_active'=>1),'fields'=>array('id','title')));
			$userId = $this->Auth->user('id');
			if($this->request->is(array('post','put'))){	
				$this->request->data['Discussion']['user_id'] = $userId;
				$this->Discussion->set($this->request->data);
				if($this->Discussion->validates()){ 
					if($this->Discussion->save($this->request->data))
					{
						return $this->redirect(array('controller'=>'discussions','action'=>'discussion',$this->Discussion->getLastInsertId()));
					}
					else {
						$this->Session->setFlash('Discussion could not be saved.','error');
					}
				}
				else
				{
					$errors = $this->User->validationErrors;
					$this->set('errors',$errors);
				}
			}
			$this->set('communities',$communities);
		}
}
