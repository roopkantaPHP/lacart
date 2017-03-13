<?php
App::uses('AppModel', 'Model');
/**
 * PaymentSetting Model
 *
 */
class SiteSetting extends AppModel {
	
	public function getIdVal($slugVal = null)
	{
		$siteDetails = $this->findBySlug($slugVal);
		if(isset($siteDetails['SiteSetting']['label']))
			return $siteDetails['SiteSetting']['label'];
	}

}
