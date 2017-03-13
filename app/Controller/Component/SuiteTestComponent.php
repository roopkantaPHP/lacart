<?php
App::import('Vendor', 'httpful', array('file' => 'httpful' . DS . 'bootstrap.php'));
App::import('Vendor', 'restful', array('file' => 'restful' . DS . 'bootstrap.php'));
App::import('Vendor', 'balanced', array('file' => 'balanced' . DS . 'bootstrap.php'));
App::import('Vendor', 'phpunit-master');

\Httpful\Bootstrap::init();
\RESTful\Bootstrap::init();
\Balanced\Bootstrap::init();

use Balanced\Settings;
use Balanced\APIKey;
use Balanced\Marketplace;
use Balanced\Credit;
use Balanced\Debit;
use Balanced\Refund;
use Balanced\Merchant;
use Balanced\BankAccount;
use Balanced\Card;
use Balanced\Customer;
use Balanced\Dispute;
use Balanced\BankAccountVerification;
Balanced\Settings::$api_key = BALANCED_API_KEY;

/** 
*  Project : F n B 
*  Author : Xicom Technologies 
*  Creation Date : 18-Dec-2014 
*  Description : Contains Balanced Payments Api methods
*/

class SuiteTestComponent extends Component{
    static $key,
        $marketplace,
        $email_counter = 0;
    protected function init()
    {
		// url root
        $url_root = '';
        if ($url_root != '') {
            Settings::$url_root = $url_root;
        } else
            Settings::$url_root = 'https://api.balancedpayments.com';

        // api key
        $api_key = Balanced\Settings::$api_key;
        
       
        if ($api_key != '') {
            Settings::$api_key = $api_key;
        } else {
            self::$key = new APIKey();
            self::$key->save();
            Settings::$api_key = self::$key->secret;
        }
        // marketplace
        try {
            self::$marketplace = Marketplace::mine();
        } catch (\RESTful\Exceptions\NoResultFound $e) {
            self::$marketplace = new Marketplace();
            self::$marketplace->save();
        }
    }
    

    /*
	 * Purpose: To get lat long of particular address using google api
	 * @Created: Bharat Borana
	 * @Date: 13 Jan 2015
	 * @Parameters: address params
	 * @Response: Google api response
	 */
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
				$k = $value->types[0];
				$address[$k] = $value->long_name;
			}
			if(isset($result->results[0]->geometry->location) && !empty($result->results[0]->geometry->location))
			{
				$address['lat'] = $result->results[0]->geometry->location->lat;
				$address['lng'] = $result->results[0]->geometry->location->lng;
			}	
		}
		return $address;
	}

    /*
	 * Purpose: Payment process sequence at balanced payments
	 * @Created: Bharat Borana
	 * @Date: 18 Dec 14
	 * @Parameters: payment details for supplier and buyer
	 * @Response: Balanced payment api response
	 */
	function pleasePay($paymentDetails){ 
		try{
			$this->init();
			
			$buyer = $this->createBuyer($paymentDetails['buyerData']['User']['email'],$paymentDetails['buyerData']['User']['name'],$paymentDetails['card']);
				
			$marketplace = Balanced\Marketplace::mine()->owner_customer;
			
			$marketplace_bank_account = $marketplace->bank_accounts->query()->first();
			
			$merchantHref = $paymentDetails['supplierData']['User']['BalancedpaymentDetail']['merchant_id'];
			
			if(empty($merchantHref)){
				$merchant = $this->createBusinessMerchant($paymentDetails['supplierData'],$bankAcountForMerchant);
			}else{
				$merchant = Balanced\Customer::get("/v1/".$merchantHref);
			}
			
			$order = $merchant->createOrder();
			
			$paymentDetails['amount'] = round($paymentDetails['amount']);

			$debit = $order->debitFrom($buyer->cards->first(), $paymentDetails['amount']);
			
			$credit = $order->creditTo($marketplace_bank_account, $paymentDetails['amount']);
			
			if(($credit->order->id == $order->id) && ($debit->order->id == $order->id)){
				$returnArray['order_href'] = $order->href;
				$returnArray = array(
									'status' => 1,
									'order_href' => $order->href,
									'order_id' => $order->id
									);
			}
		}
		catch (Exception $e) {
			$msgCode	=	'Payment process failed.';
			if(isset($e->response->body->errors[0]->description))
				$msgCode = $e->response->body->errors[0]->description;
			$returnArray = array(
								'status' => 2,
								'message' => $msgCode,
								'status code' => $e->status_code,
								);
        }
		return $returnArray;
	} 
	
	/*
	 * Purpose: Payment process sequence at balanced payments
	 * @Created: Bharat Borana
	 * @Date: 18 Dec 14
	 * @Parameters: payment details for supplier and buyer
	 * @Response: Balanced payment api response
	 */
    function createBuyer($email_address = null, $name = null, $card_href = null)
    {
		$card = Balanced\Card::get("/v1/marketplaces/".Balanced\Settings::$api_key.$card_href);
		
		if(isset($card->customer->href))
		{
			$cardCustomerHref = $card->customer->href;
		
			$buyer = Balanced\Customer::get("/v1/".$cardCustomerHref);
		}
		else{
			$buyer = self::$marketplace->customers->create(array(
				'name' => $name,
				'email' => $email_address,
			));
			
			$card->associateToCustomer($buyer);
		}
	
		return $buyer;
	}
    
    /*
	 * Purpose: Payment process sequence at balanced payments
	 * @Created: Bharat Borana
	 * @Date: 18 Dec 14
	 * @Parameters: payment details for supplier and buyer
	 * @Response: Balanced payment api response
	 */
    function createBankAccount($accountDetails = null, $customer = null)
    {
		if($accountDetails['bank_acc_type'] == 0 )
		$accType = 'checking';
		else
		$accType = 'savings';
		
		$bank_account = self::$marketplace->createBankAccount(
            'Homer Jay',
            '112233a',
            '121042882',
            'checking'
        );
        
        if ($customer != null) {
            $bank_account->associateToCustomer($customer);
        }
      
        return $bank_account;
    }
    
    /*
	 * Purpose: Creating merchant bank account at balanced payments
	 * @Created: Bharat Borana
	 * @Date: 19 Dec 14
	 * @Parameters: Merchant bank details
	 * @Response: Bank account href if successfull otherwise error message
	 */
    function createSupplierBankAccount($accountDetails = null)
    {
		try{
			$this->init();
			
			if($accountDetails['bank_acc_type'] == 0)
			$accType = 'checking';
			else
			$accType = 'savings';
			
			$bank_account = self::$marketplace->bank_accounts->create(array(
			    "account_number" => $accountDetails['account_holder_name'],
			    "account_type" => $accType,
			    "name" => $accountDetails['account_holder_name'],
			    "routing_number" => $accountDetails['routing_number'],
			));

			/*$bank_account = self::$marketplace->createBankAccount(
				$accountDetails['account_holder_name'],
				$accountDetails['bank_account_number'],
				$accountDetails['routing_number'],
				$accType
			);*/
			$returnArray = array(
							'status' => 1,
							'bank_account' => $bank_account,
							'message' => 'Bank Account created successfully.',
							);
		}
		catch(Exception $e){ 
				$msgCode	=	'Bank account information incorrect.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'account_number')>0)
				$msgCode	=	'Invalid bank account number. Please try again.';
			else if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'name')>0)
				$msgCode	=	'Invalid account holder name. Please try again.';
			else if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'routing_number')>0)
				$msgCode	=	'Invalid routing number. Please try again.';
			else if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'account_type')>0)
				$msgCode	=	'Invalid account type. Please try again.';
			
				$returnArray = array(
								'status' => 2,
								'message' => $msgCode,
								'status code' => $e->status_code,
								);
		}
		
        return $returnArray;
    }
    
    /*
	 * Purpose: Payment process sequence at balanced payments
	 * @Created: Bharat Borana
	 * @Date: 18 Dec 14
	 * @Parameters: payment details for supplier and buyer
	 * @Response: Balanced payment api response
	 */
    function createBusinessMerchant($supData = null, $bank_account = null)
    {
        $merchant = array(
            'type' => 'business', //type=person for personal merchent
            'name' => $supData['Kitchen']['name'],
            'street_address' => $supData['Kitchen']['address'],
            'country_code' => $supData['Kitchen']['country'],
            'person' => array(
                'name' => $supData['User']['name'],
            ),
        );
        return self::$marketplace->createMerchant(
            $supData['User']['email'],
            $merchant,
            $bank_account->href
        );
    }
    
     /*
	 * Purpose: Creating merchant at balanced payments
	 * @Created: Bharat Borana
	 * @Date: 23 Dec 14
	 * @Parameters: Merchant personal and bank details
	 * @Response: Merchant href if successfull otherwise error message
	 */
    function createMerchantDirect($supData = null, $bank_account = null)
    {
		try{
			$this->init();
		
			$merchant = array(
				'type' => 'business', //type=person for personal merchent
				'name' => $supData['Kitchen']['name'],
				'street_address' => $supData['Kitchen']['address'],
				'country_code' => $supData['Kitchen']['country'],
				'person' => array(
					'name' => $supData['User']['name'],
				),
			);
			try{
				$merchantObject = self::$marketplace->createMerchant(
					$supData['User']['email'],
					$merchant,
					$bank_account->href
				);
				$returnArray = array(
						'status' => 1,
						'merchant' => $merchantObject,
						'message' => 'Merchant added successfully.',
						);
			}
			catch(Exception $e){ 
				$returnArray = array(
						'status' => 2,
						'message' => $e->response->body->errors[0]->description,
						'status code' => $e->status_code,
						);
			}
		}
		catch(Exception $e){ 
				$msgCode	=	'Bank account information incorrect.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'name')>0)
				$msgCode	=	'Invalid merchant name. Please try again.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'bank_account')>0)
				$msgCode	=	'Invalid account holder name. Please try again.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'email')>0)
				$msgCode	=	'Invalid email address. Please try again.';
			
				$returnArray = array(
								'status' => 2,
								'message' => $msgCode,
								'status code' => $e->status_code,
								);
		}
		
        return $returnArray;
    }
    
    /*
	 * Purpose: Marketplace to Merchant Payments
	 * @Created: Bharat Borana
	 * @Date: 02 Feb 2015
	 * @Parameters: Merchant personal and bank details
	 * @Response: Payment href if successfull otherwise error message
	 */
    function pay_merchant($order_href=null,$paymentSettings=null){
		$returnArray = array();
		if(!empty($order_href)){
			try{
				$this->init(); 
				$order = Balanced\Order::get($order_href);
				if(!empty($order)){
					$merchantBankAccount = $order->merchant->bank_accounts->query()->first();
					$order->refresh();
					if($paymentSettings['fee_type']==0){
						$transferAmmount = $order->amount-$paymentSettings['processing_fee'];
					}else{
						$transferAmmount = $order->amount-(($order->amount)/100)*$paymentSettings['processing_fee'];
					}
					$credit = $order->creditTo($merchantBankAccount, round($transferAmmount));
					$returnArray = array(
										'status' => 1,
										'message' => "Successfully paid to merchant.",
										'status code' => $e->status_code,
										);
					//$returnHref = $credit->href;
				}
			}
			catch(Exception $e){ 
						$returnArray = array(
										'status' => 2,
										'message' => $e->response->body->errors[0]->description,
										'status code' => $e->status_code,
										);
				}
		}
		else
		{
			$returnArray = array(
								'status' => 2,
								'message' => "Order reference href does not found.",
								);
		}
		return $returnArray;
	}

	function createCard($cardDetails= null)
    {
    	try{
			$this->init();
		
			try{
			
				$card = self::$marketplace->createCard(
		            $cardDetails['User']['line-1']." ".$cardDetails['User']['line-2'],
		            $cardDetails['User']['city'],
		            null,
		            $cardDetails['User']['zipcode'],
		            $cardDetails['PaymentMethod']['card_name'],
		            $cardDetails['PaymentMethod']['card_no'],
		            null,
		            $cardDetails['PaymentMethod']['exp_month'],
		            $cardDetails['PaymentMethod']['exp_year']
	            );

		        $returnArray = array(
						'status' => 1,
						'card' => $card->href,
						'message' => 'Buyer added successfully.',
						);
			}
			catch(Exception $e){ 
				$returnArray = array(
						'status' => 2,
						'message' => $e->response->body->errors[0]->description,
						'status code' => $e->status_code,
						);
			}
		}
		catch(Exception $e){ 
				$msgCode	=	'Card information incorrect.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'name')>0)
				$msgCode	=	'Please enter correct card name.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'number')>0)
				$msgCode	=	'Invalid card number, please try again.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'expiration_month')>0)
				$msgCode	=	'Invalid expiration month. Please try again.';
			if(isset($e->response->body->errors[0]->description) && strpos($e->response->body->errors[0]->description,'expiration_year')>0)
				$msgCode	=	'Invalid expiration month. Please try again.';

				$returnArray = array(
								'status' => 2,
								'message' => $msgCode,
								'status code' => $e->status_code,
								);
		}
		
        return $returnArray;
    }

    function deleteBankAccount($accountHref=null){
		$this->init();
		$marketplace = Balanced\Marketplace::mine()->owner_customer;
		
		//$order = $marketplace->createOrder();
		
		//pr($order); exit;
		//$bank_account = Balanced\Card::get("/v1/marketplaces/".Balanced\Settings::$api_key.$accountHref);
		//$bank_account->unstore();
	}
    
 } 
