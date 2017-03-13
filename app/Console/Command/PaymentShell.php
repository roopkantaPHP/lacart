<?php
/**
 * AppShell file
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 */

App::uses('Shell', 'Console');
App::uses('AppShell', 'Console/Command');
App::uses('CakeEmail', 'Network/Email');
// App::uses('Controller', 'Controller');
// App::uses('ComponentCollection', 'Controller');
// App::uses('Component', 'Controller');
// App::uses('EmailSend', 'Controller/Component');
/**
 * Message Shell
 *
 * Add your Message Model related methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class PaymentShell extends AppShell
{
 

	/**
	 * [grabFeed description]
	 * @return [type] [description]
	 * @author Bharat Borana
	 * @since 30-03-15
	 */
    public function main()
    {
        App::uses('StripeComponent', 'Controller/Component');
        $stripeComponent = new StripeComponent();
        
        //$campaings = $mailchimpData->campaigns();
        
        App::uses('Kitchen', 'Model');
        $kitchenModel = new Kitchen();
        
        App::uses('Order', 'Model');
        $orderModel = new Order();

        App::uses('PaymentSetting', 'Model');
        $paymentSettingModel = new PaymentSetting();
        
        $orders = $orderModel->find('all',array('fields'=>array('SUM(Order.amount) as totalBal, Order.kitchen_id, GROUP_CONCAT(Order.id) as orderIds'),
                                                'conditions'=>array('Order.payment_type'=>0,'Order.is_verified'=>2,'Order.merchant_paid'=>0),
                                                'group'=>'Order.kitchen_id HAVING totalBal >= 100',
                                            	));
        
        if(!empty($orders))
        {
            foreach ($orders as $key => $value)
            {
                if(isset($value['Order']['kitchen_id']) && !empty($value['Order']['kitchen_id']))
                {
                    $paymentSettings = $paymentSettingModel->find('first');
                    if($paymentSettings['PaymentSetting']['fee_type']==0)
                    {
                        $transferAmmount = $value[0]['totalBal'] - $paymentSettings['PaymentSetting']['processing_fee'];
                        $fee = $paymentSettings['PaymentSetting']['processing_fee'];
                    }
                    else
                    {
                        $transferAmmount = $value[0]['totalBal'] - (($value[0]['totalBal'])/100)*$paymentSettings['PaymentSetting']['processing_fee'];
                        $fee = (($value[0]['totalBal'])/100)*$paymentSettings['PaymentSetting']['processing_fee'];
                    }
                    $kitchenData = $kitchenModel->findById($value['Order']['kitchen_id'],array('User.stripe_user_id,User.name,User.email,Kitchen.*'));
                    if(isset($kitchenData['User']['stripe_user_id']) && !empty($kitchenData['User']['stripe_user_id']))
                    {
                        $transferArray['amount'] = round($transferAmmount,2)*100;
                        $transferArray['destination'] = $kitchenData['User']['stripe_user_id'];
                        $transferArray['description'] = 'Transfer for order id '.$value[0]['orderIds'];
                        
                        $payPlease = $stripeComponent->createTransfer($transferArray);

                        if($payPlease['status']=='success')
                        {
                            $transferId = $payPlease['response']['id'];
                            $ordersUpdate = explode(',',$value[0]['orderIds']);
                            $html = '';
                            foreach ($ordersUpdate as $key => $ordId)
                            {
                                $ordData['Order']['id'] = $ordId;
                                $ordData['Order']['merchant_paid'] = 1;
                                $ordData['Order']['merchant_transfer_id'] = $transferId;
                                $ordData['Order']['merchant_transfer_date'] = date('Y-m-d H:i:s');
                                $orderModel->save($ordData);

                                //mail for successfull transfer
                                $mailData['Order'] = $kitchenData;
                                $html .= '  <tr style="background: none repeat scroll 0 0 #FFECCB;">
                                                <td>#order id'. $ordId .'</td>
                                                <td>'.$orderModel['Order']['amount'].'</td>
                                            </tr>';
                            }
                            App::uses('Emailtemplate', 'Model');
                            $emailtemplateModel = new Emailtemplate();
                            $email_content = $emailtemplateModel->findByEmailFor('transfer-success');

                            if(!empty($email_content))
                            {
                                $content = $email_content['Emailtemplate']['content'];
                                $subject = $email_content['Emailtemplate']['subject'];
                                
                                $arr['{{transaction_id}}'] = $transferId;
                                $arr['{{name}}'] = $kitchenData['Kitchen']['name'];
                                $arr['{{amount}}'] = $transferArray['amount'];
                                $arr['{{kitchen_name}}'] = $kitchenData['Kitchen']['name'];
                                $arr['{{payment_value}}'] = $value[0]['totalBal'];
                                $arr['{{fee}}'] = $fee;
                                $arr['{{total}}'] = $transferArray['amount'];
                                $arr['{{date}}'] = date('M d, Y');
                                $arr['{{allOrders}}'] = $html;
                                
                                $subject = str_replace(array_keys($arr), array_values($arr), $subject);
                                $content = str_replace(array_keys($arr), array_values($arr), $content);

                                $reply_to_email = $email_content['Emailtemplate']['reply_to'];

                                $Email = new CakeEmail('smtp');
                                $kitchenData['Kitchen']['name']
                                $Email->to($kitchenData['User']['email']);
                                $Email->bcc("lacart.tech@gmail.com");
                                
                                $Email->emailFormat('html');
                                $Email->from($email_content['Emailtemplate']['from_email']);
                                $Email->replyTo($reply_to_email);
                                $Email->subject($subject);

                                $Email->send($content);
                            } 
                        }
                        else
                        {
                            App::uses('Emailtemplate', 'Model');
                            $emailtemplateModel = new Emailtemplate();
                            $email_content = $emailtemplateModel->findByEmailFor('transfer-error');

                            if(!empty($email_content))
                            {
                                $content = $email_content['Emailtemplate']['content'];
                                $subject = $email_content['Emailtemplate']['subject'];
                                
                                $arr['{{name}}'] = 'Admin';
                                $arr['{{amount}}'] = $transferArray['amount'];
                                $arr['{{kitchen_name}}'] = $kitchenData['Kitchen']['name'];
                                $arr['{{payment_value}}'] = $value[0]['totalBal'];
                                $arr['{{fee}}'] = $fee;
                                $arr['{{total}}'] = $transferArray['amount'];
                                $arr['{{date}}'] = date('M d, Y');
                                $arr['{{error}}'] = $payPlease['response']['message'];
                                
                                $subject = str_replace(array_keys($arr), array_values($arr), $subject);
                                $content = str_replace(array_keys($arr), array_values($arr), $content);

                                $reply_to_email = $email_content['Emailtemplate']['reply_to'];

                                $Email = new CakeEmail('smtp');
                                $Email->to("lacart.tech@gmail.com");
                                
                                $Email->emailFormat('html');
                                $Email->from($email_content['Emailtemplate']['from_email']);
                                $Email->replyTo($reply_to_email);
                                $Email->subject($subject);

                                $Email->send($content);
                            }
                        }
                    }
                    else
                    {
                        //email user to connect to stripe for accepting payments
                    }
                }
            }
        }
		exit;
    }
}