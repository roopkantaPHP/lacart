<?php
App::uses('AppModel', 'Model');
/**
 * Testimonial Model
 *
 */
class CmsCity extends AppModel {
	
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
		    'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter city name.',
			),
		),
		'image' => array(
		     'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select image.',
			),
		),
		);
	
	/*
	 * Method	: getfeaturedTestimonials
	 * Author	: Bharat Borana
	 * Created	: 31 Dec, 2014
	 * @Featured Testimonial data for app CMS
	 */
	public function getfeaturedCities(){
		   $featuredTestimonial = array();
		   $tData = $this->find('all',array('conditions'=>array('CmsCity.is_display'=>1),
											'fields'=>array('CmsCity.*'),
								));
		   if(isset($tData) && !empty($tData)){
				foreach($tData as $key=>$testiData){
					if(isset($testiData['CmsCity']['image']))
						$testiData['CmsCity']['image']  = (!empty($testiData['CmsCity']['image'])) ? Router::url('/'.CMS_IMAGE_URL.$testiData['CmsCity']['image'],true) : "";
					$featuredTestimonial[] = $testiData;
				}	
			}
		return $featuredTestimonial;	
	}	
	
}
