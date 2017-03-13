<?php
App::uses('AppModel', 'Model');
/**
 * Video Model
 *
 */
class Video extends AppModel {
	
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter you name.',
			),
		),
		);
	
	/*
	 * Method	: getfeaturedVideos
	 * Author	: Bharat Borana
	 * Created	: 31 Dec, 2014
	 * @Featured Video data for app CMS
	 */
	public function getfeaturedVideos(){
		   $featuredVideo = array();
		   $tData = $this->find('all',array('conditions'=>array('Video.is_display'=>1),
											'fields'=>array('Video.*'),
								));
		   if(isset($tData) && !empty($tData)){
				foreach($tData as $key=>$testiData){
					if(isset($testiData['Video']['url']) && !empty($testiData['Video']['url']))
						preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $testiData['Video']['url'], $matches);
						$testiData['Video']['video_id']  = $matches[0];
					$featuredVideo[] = $testiData;
				}	
			}
		return $featuredVideo;	
	}	
	
}
