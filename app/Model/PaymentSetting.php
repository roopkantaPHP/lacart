<?php
App::uses('AppModel', 'Model');
/**
 * PaymentSetting Model
 *
 */
class PaymentSetting extends AppModel {
	
	public function getProcessingfee(){
		$payDetails = $this->find('first',array('fields'=>array('processing_fee','fee_type')));
		return $payDetails['PaymentSetting'];
	}

}
