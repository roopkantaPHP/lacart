<?php
 
class Review extends AppModel
{
	public $name = 'Review';
	
	public $belongsTo = array('User');

	public $validate = array(
		'feedback' => array(
			'rule' => 'notempty'
			)
		);
	
	public function afterSave($created, $options = Array()){
	   $avgValue = $this->find('all',array('fields'=>array('AVG(rating) as kRating'),'conditions'=>array('kitchen_id'=>$this->data['Review']['kitchen_id'])));	
	   if(!empty($avgValue)){
		   App::import('model','Kitchen');
		   $kitchen = new Kitchen();
		   $kitchen->updateAll(array('Kitchen.avg_rating'=>$avgValue[0][0]['kRating']),array('Kitchen.id'=>$this->data['Review']['kitchen_id']));
	   }
	}
	
	public function afterDelete(){
	   $avgValue = $this->find('all',array('fields'=>array('AVG(rating) as kRating'),'conditions'=>array('kitchen_id'=>$this->data['Review']['kitchen_id'])));	
	   if(!empty($avgValue)){
		   App::import('model','Kitchen');
		   $kitchen = new Kitchen();
		   $kitchen->updateAll(array('Kitchen.avg_rating'=>$avgValue[0][0]['kRating']),array('Kitchen.id'=>$this->data['Review']['kitchen_id']));
	   }
	} 	 	
}
