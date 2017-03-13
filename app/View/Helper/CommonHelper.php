<?php
/**
 * Common helper for application
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * Name		: CommonHelper class
 * Author 	: Praveen Pandey
 * Created 	: 19 Nov, 2014 
 */

App::uses('Helper', 'View');

class CommonHelper extends AppHelper {

	var $helpers = array('Session','Number','Text','Html');
	
	/**
	 * Method	: getKitchenImage
	 * Author	: Praveen Pandey
	 * Created	: 19 Nov, 2014
	 * Updated  : 08 Jan, 2015
	 * Updated By : Bharat Borana
	 * Purpose	: used to get kitchen image
	 */
	public function getKitchenImage($data)
	{ 
		$imgName = 'img2.png';
         if(isset($data['UploadImage'][0]) && !empty($data['UploadImage'][0])){
         	
         	if(strpos($data['UploadImage'][0]['name'],'images/')>0)
         	{
	            $url = $data['UploadImage'][0]['name'];
	            $imgName = $data['UploadImage'][0]['name'];
         	}
	        else
	        {
	            $url = KITCHEN_IMAGE_URL.$data['UploadImage'][0]['name']; 
	            $imgName = KITCHEN_IMAGE_FOLDER.$data['UploadImage'][0]['name'];
	        }

            if(FILE_EXISTS($url)){
                $imgName = $imgName;
            }
        }
		return $imgName;
	}

	/**
	 * Method	: getProfileImage
	 * Author	: Bharat Borana
	 * Created	: 08 Jna, 2015
	 * Purpose	: used to get profile image
	 */
	public function getProfileImage($imageName=array())
	{ 
		$freshName = 'img1.png';

         if(isset($imageName['User']['image']) && !empty($imageName['User']['image'])){
         	if(strpos($imageName['User']['image'],'images/')>0)
         	{  
         		$url = PROFILE_IMAGE_URL.substr($imageName['User']['image'],strpos($imageName['User']['image'],'/profile/')+9);
	            $imgName = PROFILE_IMAGE_FOLDER.substr($imageName['User']['image'],strpos($imageName['User']['image'],'/profile/')+9);
	        }
	        else
	        {
	            $url = PROFILE_IMAGE_URL.$imageName['User']['image']; 
	            $imgName = PROFILE_IMAGE_FOLDER.$imageName['User']['image'];
	        }

            if(FILE_EXISTS($url)){ 
                $freshName = $imgName;
            }
        }
		return $freshName;
	}

	/**
	 * Method	: getDishImage
	 * Author	: Bharat Borana
	 * Created	: 08 Jna, 2015
	 * Purpose	: used to get profile image
	 */
	public function getDishImage($imageName=array())
	{
		$imgName = 'img2.png';
         if(isset($imageName['UploadImage'][0]) && !empty($imageName['UploadImage'][0])){
            if(strpos($imageName['UploadImage'][0]['name'],'images/')>0)
         	{
	            $url = $imageName['UploadImage'][0]['name'];
	            $imgName = $imageName['UploadImage'][0]['name'];
         	}
	        else
	        {
	            $url = DISH_IMAGE_URL.$imageName['UploadImage'][0]['name']; 
	            $imgName = DISH_IMAGE_FOLDER.$imageName['UploadImage'][0]['name'];
	        }

            if(FILE_EXISTS($url)){
                $imgName = $imgName;
            }
        }
		return $imgName;
	}

	/**
	 * Method	: getAverageRating
	 * Author	: Bharat Borana
	 * Created	: 08 Jna, 2015
	 * Purpose	: used to get Average rating of user's
	 */
	public function getRatingIcon($rating = null)
	{
		return '<span class="stars">'.$rating.'</span>';
	}
	
	/**
	 * Method	: getSearchUrl
	 * Author	: Praveen Pandey
	 * Created	: 22 Nov, 2014
	 */
	public function getSearchUrl($type, $value)
	{
		$controller = 'dishes';
		$action = 'search';
		$current_url = $this->Html->url('',true);
		$url = $this->Html->url(array('controller'=>$controller,'action'=>$action),true);
		$passkey = array();
		$keyValue = explode('/', $type.':'.$value);
		foreach ($keyValue as $pk) {
			$pkey = explode(':', $pk);
			$passkey[] = $pkey[0];
		}
		$not_in_url = true;
		if(!empty($this->params['named']))
		{
			$cat_url = true;
			foreach ($this->params['named'] as $key=>$val)
			{
				if($key == $type) {
					$not_in_url = false;
					if($value) {
						$url .= '/'.$key.':'.$value;
					}
				} else if(in_array($key, $passkey)) {
					
				} else {
					$url .= '/'.$key.':'.$val;				
				}
			}
		}
		if($not_in_url)
		{
			if($value)
			{
				$url .= '/'.$type.':'.$value;
			}
		}
		return $url;
	}

	public function getTimeAgo($time){
		if(isset($time) && !empty($time)){
			$time = time() - $time; // to get the time since that moment

		    $tokens = array (
		        31536000 => 'year',
		        2592000 => 'month',
		        604800 => 'week',
		        86400 => 'day',
		        3600 => 'hour',
		        60 => 'minute',
		        1 => 'second'
		    );

		    foreach ($tokens as $unit => $text) {
		        if ($time < $unit) continue;
		        $numberOfUnits = floor($time / $unit);
		        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		    }
		}
	}

		function distance($lat1, $lon1, $lat2, $lon2, $unit) {
		  $theta = $lon1 - $lon2;
		  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		  $dist = acos($dist);
		  $dist = rad2deg($dist);
		  $miles = $dist * 60 * 1.1515;
		  $unit = strtoupper($unit);
		  if ($unit == "K") {
		    return (round($miles * 1.609344));
		  } else if ($unit == "N") {
		      return (round($miles * 0.8684));
		    } else {
		        return round($miles,1);
		      }
		}
}
