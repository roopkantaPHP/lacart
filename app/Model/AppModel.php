<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	function _getAddressFormGeoode($address)
	{
	//	$address = "New Delhi";
		$address = urlencode($address);
		$url="https://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false&key=".GOOGLE_GEOCODE_API;
	    $timeout = 60;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	    $data = curl_exec($ch);
	    curl_close($ch);
		$result = json_decode($data);
	    $address = array();
		if(!empty($result->results[0]))
		{
			foreach ($result->results[0]->address_components as $key => $value) {
				if(isset($value->types[0]) && !empty($value->types[0]))
				{
					$k = $value->types[0];
					$address[$k] = $value->long_name;
				}	
			}	
			if(isset($result->results[0]->geometry->location) && !empty($result->results[0]->geometry->location))
			{
				$address['lat'] = $result->results[0]->geometry->location->lat;
				$address['lng'] = $result->results[0]->geometry->location->lng;
			}	
		}
		return $address;
	}
} ?>