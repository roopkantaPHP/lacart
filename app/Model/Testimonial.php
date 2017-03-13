<?php
App::uses('AppModel', 'Model');
/**
 * Testimonial Model
 *
 */
class Testimonial extends AppModel {
	
	
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
		'message' => array(
		     'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter you message.',
			),
		),
		);
	
	/*
	 * Method	: getfeaturedTestimonials
	 * Author	: Bharat Borana
	 * Created	: 31 Dec, 2014
	 * @Featured Testimonial data for app CMS
	 */
	public function getfeaturedTestimonials(){
		   $featuredTestimonial = array();
		   $tData = $this->find('all',array('conditions'=>array('Testimonial.is_display'=>1),
											'fields'=>array('Testimonial.*'),
								));
		   if(isset($tData) && !empty($tData)){
				foreach($tData as $key=>$testiData){
					if(isset($testiData['Testimonial']['image']))
						$testiData['Testimonial']['image']  = (!empty($testiData['Testimonial']['image'])) ? Router::url('/'.PROFILE_IMAGE_URL.$testiData['Testimonial']['image'],true) : "";
					$featuredTestimonial[] = $testiData;
				}	
			}
		return $featuredTestimonial;	
	}	
	
}
