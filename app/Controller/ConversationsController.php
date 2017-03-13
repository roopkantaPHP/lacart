<?php
App::uses('AppController', 'Controller');
/**
 * Countries Controller
 *
 * @property Discussions $Discussions
 * @property PaginatorComponent $Paginator
 */
class ConversationsController extends AppController {
	
	public $name = 'Conversation';
	
	public function new_message($receiver_id)
	{
		$this->layout = 'ajax';
		if($receiver_id)
		{
			$this->loadModel('User');
			$userDetails = $this->User->findById($receiver_id);
			$this->set('userDetails',$userDetails);
		}
		if($this->request->is(array('post','put'))){ 
			$userId = $this->Auth->user('id');
			$this->loadModel('Conversation');
			$this->loadModel('ConversationReply');
			
			//Update Activity Log
			$this->loadModel('ActivityLog');
			
			$converstion = $this->Conversation->find('first',array(
				'conditions'=>array('OR'=>array(array('Conversation.sender_id'=>$userId,'Conversation.receiver_id'=>$receiver_id),array('Conversation.sender_id'=>$receiver_id,'Conversation.receiver_id'=>$userId))),
			));
			//$log = $this->Conversation->getDataSource()->getLog(false, false);
			
				
			$replyData['date_time'] = time();
			$replyData['reply'] = $this->request->data['ConversationReply']['reply'];
			$replyData['user_id'] = $userId;
			if($converstion)
			{
				$replyData['conversation_id'] = $converstion['Conversation']['id'];
				$this->Conversation->id = $converstion['Conversation']['id'];
				$this->Conversation->save();
				
				//Check this combination in activity log if found then just update that record with current timestamp
				$checkActivity = $this->ActivityLog->find('first',array('conditions'=>array( 'ActivityLog.user_id'=>$userId,
																			'ActivityLog.activity_id'=>6,
																			'ActivityLog.conversation_id'=>$converstion['Conversation']['id'])));
				if(empty($checkActivity)){
					//Update Activity Log For New Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$converstion['Conversation']['id'],time());
				}else{
					//Update Activity Log For Existing Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$converstion['Conversation']['id'],time(),$checkActivity['ActivityLog']['id']);
				}
			}
			else {
				$conData['date_time'] = time();
				$conData['sender_id'] = $userId;
				$conData['receiver_id'] = $receiver_id;
				$this->Conversation->create();
				if($this->Conversation->save($conData))
				{
					$replyData['conversation_id'] = $this->Conversation->getLastInsertID();
					
					//Update Activity Log For Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$this->Conversation->getLastInsertID(),time());
					//Update Activity Log For Receiver Conversation Activity
					$this->ActivityLog->updateLog($receiver_id,6,$this->Conversation->getLastInsertID(),time());
				}
			}

			if(!empty($replyData['conversation_id']))
			{
				$this->ConversationReply->create();
				if($this->ConversationReply->save($replyData))
				{
					$this->Session->setFlash("Message sent successfully.", 'success');
					$this->set('closeFancy',1);
				}
				else
				{
					$this->Session->setFlash("Message sending failed.", 'error');
				}
			}
			else
			{
				$this->Session->setFlash("Message sending failed.", 'error');
			}
		}
	}
	
	/**
	 * Method	: message_list
	 * Author 	: Bharat Borana
	 * Created	: 15 Jan, 2015
	 */
	 public function list_message()
	 {
	 	$userId = $this->Auth->user('id');

 		$this->loadModel('Conversation');
		$this->Conversation->hasMany['ConversationReply']['order'] = array('ConversationReply.created'=> 'DESC');
		$this->Conversation->hasMany['ConversationReply']['limit'] = 1;
		
		$messages = $this->Conversation->find('all',array(
					'contain' => array('Sender'=> array('id','name','image'),
									   'Reciever'=> array('id','name','image'),
										'ConversationReply'),
					'conditions'=>array('OR'=>array('Conversation.sender_id'=>$userId,'Conversation.receiver_id'=>$userId)),
					'order' => array('Conversation.modified'=>'DESC'),
		
		));
		$this->set(compact(array('messages','userId')));
	 }

/**
 * Method	: message_detail
 * Author 	: Bharat Borana
 * Created	: 15 Jan, 2015
 */
	 public function detail($conversation_id)
	 {
 		$this->loadModel('ConversationReply');
 		$this->loadModel('User');
 		$userId = $this->Auth->user('id');
 		$userDetails = $this->User->findById($userId);

 		$messages = $this->ConversationReply->find('all', array(
 				'conditions' => array(
 						'Conversation.id'=>$conversation_id,
 						'OR' => array('Conversation.sender_id'=>$userId, 'Conversation.receiver_id'=>$userId)
 				),
 				'fields' => array('ConversationReply.*','User.name','User.id','User.image')
 		)
 		);

 		$this->set(compact(array('userDetails','messages')));
 	
	 }

	  /**
	  * Method	: Add Message data
	  * Author 	: Bharat Borana
	  * Created	: 15 Jan 2015
	  * Purpose	: Add Message using ajax
	  */
	  public function add_message()
	  {
	  	$this->layout = false;
	  	if($this->request->is('ajax')){	
			$this->loadModel('User');
			$this->loadModel('Conversation');
			$this->loadModel('ConversationReply');
			
			$userId = $this->Auth->user('id');
			$userDetails = $this->User->findById($userId);
			
			//Update Activity Log
			$this->loadModel('ActivityLog');
			
			$converstion = $this->Conversation->find('first',array(
				'conditions'=>array('Conversation.id'=>$this->request->data['ConversationReply']['conversation_id'],'OR'=>array('Conversation.sender_id'=>$userId,'Conversation.receiver_id'=>$userId)),
			));
				
			$replyData['date_time'] = time();
			$replyData['reply'] = $this->request->data['ConversationReply']['reply'];
			$replyData['user_id'] = $userId;
			if($converstion)
			{
				$replyData['conversation_id'] = $converstion['Conversation']['id'];
				$this->Conversation->id = $converstion['Conversation']['id'];
				$this->Conversation->save();
				
				//Check this combination in activity log if found then just update that record with current timestamp
				$checkActivity = $this->ActivityLog->find('first',array('conditions'=>array( 'ActivityLog.user_id'=>$userId,
																			'ActivityLog.activity_id'=>6,
																			'ActivityLog.conversation_id'=>$converstion['Conversation']['id'])));
				if(empty($checkActivity)){
					//Update Activity Log For New Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$converstion['Conversation']['id'],time());
				}else{
					//Update Activity Log For Existing Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$converstion['Conversation']['id'],time(),$checkActivity['ActivityLog']['id']);
				}
			}

			$freshMessages = '';
			
			if(!empty($replyData['conversation_id']))
			{
				$this->ConversationReply->create();
				if($this->ConversationReply->save($replyData))
				{
					$this->set($closeFancy,1);
					$freshMessages = $this->ConversationReply->find('all', array(
			 				'conditions' => array(
			 						'Conversation.id'=>$this->request->data['ConversationReply']['conversation_id'],
			 						'ConversationReply.created >' =>$this->request->data['ConversationReply']['last_reply_id'],
			 						'OR' => array('Conversation.sender_id'=>$userId, 'Conversation.receiver_id'=>$userId)
			 				),
			 				'fields' => array('ConversationReply.*','User.name','User.id','User.image')
			 		)
			 		);
				}
			}
			$this->set(compact(array('userDetails','freshMessages')));
	  	}
	  } 

	  /**
	  * Method	: Get Message data
	  * Author 	: Bharat Borana
	  * Created	: 15 Jan 2015
	  * Purpose	: Get Message using ajax
	  */
	  public function get_message()
	  {
	  	$freshMessages = '';
	  	$userDetails = '';
	  	$this->layout = false;
	  	if($this->request->is('ajax')){	
			$this->loadModel('User');
			$this->loadModel('Conversation');
			$this->loadModel('ConversationReply');
			$userId = $this->Auth->user('id');
			$userDetails = $this->User->findById($userId);
			if(isset($this->request->data['conversation_id']) && isset($this->request->data['last_reply_id']))
			{
					$freshMessages = $this->ConversationReply->find('all', array(
		 				'conditions' => array(
		 						'Conversation.id'=> $this->request->data['conversation_id'],
		 						'ConversationReply.created >' => $this->request->data['last_reply_id'],
		 						'OR' => array('Conversation.sender_id'=>$userId, 'Conversation.receiver_id'=>$userId)
		 				),
		 				'fields' => array('ConversationReply.*','User.name','User.id','User.image')
		 		)
		 		);
			}
		}
		$this->set(compact(array('userDetails','freshMessages')));
		$this->render('add_message');
	  } 

	public function admin_new_message($receiver_id)
	{
		$this->layout = 'ajax';
		if($receiver_id)
		{
			$this->loadModel('User');
			$userDetails = $this->User->findById($receiver_id);
			$this->set('userDetails',$userDetails);
		}
		if($this->request->is(array('post','put'))){ 
			$userId = $this->Auth->user('id');
			$this->loadModel('Conversation');
			$this->loadModel('ConversationReply');
			
			//Update Activity Log
			$this->loadModel('ActivityLog');
			
			$converstion = $this->Conversation->find('first',array(
				'conditions'=>array('OR'=>array(array('Conversation.sender_id'=>$userId,'Conversation.receiver_id'=>$receiver_id),array('Conversation.sender_id'=>$receiver_id,'Conversation.receiver_id'=>$userId))),
			));
			//$log = $this->Conversation->getDataSource()->getLog(false, false);
			
				
			$replyData['date_time'] = time();
			$replyData['reply'] = $this->request->data['ConversationReply']['reply'];
			$replyData['user_id'] = $userId;
			if($converstion)
			{
				$replyData['conversation_id'] = $converstion['Conversation']['id'];
				$this->Conversation->id = $converstion['Conversation']['id'];
				$this->Conversation->save();
				
				//Check this combination in activity log if found then just update that record with current timestamp
				$checkActivity = $this->ActivityLog->find('first',array('conditions'=>array( 'ActivityLog.user_id'=>$userId,
																			'ActivityLog.activity_id'=>6,
																			'ActivityLog.conversation_id'=>$converstion['Conversation']['id'])));
				if(empty($checkActivity)){
					//Update Activity Log For New Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$converstion['Conversation']['id'],time());
				}else{
					//Update Activity Log For Existing Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$converstion['Conversation']['id'],time(),$checkActivity['ActivityLog']['id']);
				}
			}
			else {
				$conData['date_time'] = time();
				$conData['sender_id'] = $userId;
				$conData['receiver_id'] = $receiver_id;
				$this->Conversation->create();
				if($this->Conversation->save($conData))
				{
					$replyData['conversation_id'] = $this->Conversation->getLastInsertID();
					
					//Update Activity Log For Sender Conversation Activity
					$this->ActivityLog->updateLog($userId,6,$this->Conversation->getLastInsertID(),time());
					//Update Activity Log For Receiver Conversation Activity
					$this->ActivityLog->updateLog($receiver_id,6,$this->Conversation->getLastInsertID(),time());
				}
			}

			if(!empty($replyData['conversation_id']))
			{
				$this->ConversationReply->create();
				if($this->ConversationReply->save($replyData))
				{
					$this->Session->setFlash("Message sent successfully.", 'success');
					$this->set('closeFancy',1);
				}
				else
				{
					$this->Session->setFlash("Message sending failed.", 'error');
				}
			}
			else
			{
				$this->Session->setFlash("Message sending failed.", 'error');
			}
		}
	}
	
		/**
	 * Method	: admin_index
	 * Author	: Bharat Borana
	 * Created	: 03 Feb 2015
	 * Purpose	: List of all kitchens
	 */ 
	 public function admin_index()
	 {
	 	$userId = $this->Auth->user('id');
	 	$this->Conversation->hasMany['ConversationReply']['order'] = array('ConversationReply.created'=> 'DESC');
		$this->Conversation->hasMany['ConversationReply']['limit'] = 1;
	 	$this->paginate = array(				
			'limit' => 25,
			'contain' => array('Sender'=> array('id','name','image'),
									   'Reciever'=> array('id','name','image'),
										'ConversationReply'),
			'conditions' => array('OR'=>array('Conversation.sender_id'=>$userId,'Conversation.receiver_id'=>$userId)),
			'order' => 'Conversation.modified DESC'
		);
		$rows = $this->paginate('Conversation');
		$this->set(compact('rows','userId'));
	 }
}
