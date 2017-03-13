<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
 	Router::connect('/admin', array('controller' => 'users', 'action' => 'login', 'admin' => true));
	Router::connect('/', array('controller' => 'users', 'action' => 'index'));
	Router::connect('/social_login/*', array( 'controller' => 'users', 'action' => 'social_login'));
	Router::connect('/social_endpoint/*', array( 'controller' => 'users', 'action' => 'social_endpoint'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

	Router::connect('/legal', array('controller' => 'cmspages', 'action' => 'index',1));
	Router::connect('/policy', array('controller' => 'cmspages', 'action' => 'index',2));
	Router::connect('/contact-us', array('controller' => 'cmspages', 'action' => 'index',4));
	Router::connect('/careers', array('controller' => 'cmspages', 'action' => 'index',3));
	Router::connect('/invite', array('controller' => 'cmspages', 'action' => 'invite'));
	Router::connect('/city', array('controller' => 'cmspages', 'action' => 'index',5));

  /*
    Roopkanta 10-03-2017
    API routes total 69 APIs
  */
	Router::connect('/register', array('controller' => 'api', 'action' => 'register'));
  Router::connect('/login', array('controller' => 'api', 'action' => 'login'));
  Router::connect('/forgot_password', array('controller' => 'api', 'action' => 'forgot_password'));
  Router::connect('/facebook_login', array('controller' => 'api', 'action' => 'facebook_login'));
  Router::connect('/google_login', array('controller' => 'api', 'action' => 'google_login'));
  Router::connect('/personal_info', array('controller' => 'api', 'action' => 'personal_info'));
  Router::connect('/edit_kitchen', array('controller' => 'api', 'action' => 'edit_kitchen'));
  Router::connect('/search_kitchen', array('controller' => 'api', 'action' => 'search_kitchen'));
  Router::connect('/add_dish', array('controller' => 'api', 'action' => 'add_dish'));
  Router::connect('/mydishes_list', array('controller' => 'api', 'action' => 'mydishes_list'));
  Router::connect('/active_dish', array('controller' => 'api', 'action' => 'active_dish'));
  Router::connect('/dish_info', array('controller' => 'api', 'action' => 'dish_info'));
  Router::connect('/kitchen_info', array('controller' => 'api', 'action' => 'kitchen_info'));
  Router::connect('/profile_info', array('controller' => 'api', 'action' => 'profile_info'));
  Router::connect('/load_data', array('controller' => 'api', 'action' => 'load_data'));//chk
  Router::connect('/preferences_info', array('controller' => 'api', 'action' => 'preferences_info'));
  Router::connect('/update_preferences', array('controller' => 'api', 'action' => 'update_preferences'));
  Router::connect('/change_password', array('controller' => 'api', 'action' => 'change_password'));
  Router::connect('/new_discussion', array('controller' => 'api', 'action' => 'new_discussion'));
  Router::connect('/explore_community', array('controller' => 'api', 'action' => 'explore_community'));//chk
  Router::connect('/discussion_list', array('controller' => 'api', 'action' => 'discussion_list'));
  Router::connect('/discussion_detail', array('controller' => 'api', 'action' => 'discussion_detail'));
  Router::connect('/post_discussion_message', array('controller' => 'api', 'action' => 'post_discussion_message'));
  Router::connect('/new_messageold', array('controller' => 'api', 'action' => 'new_messageold'));
  Router::connect('/message_list', array('controller' => 'api', 'action' => 'message_list'));
  Router::connect('/message_detail', array('controller' => 'api', 'action' => 'message_detail'));
  Router::connect('/message_detaila', array('controller' => 'api', 'action' => 'message_detaila'));
  Router::connect('/add_payment_method', array('controller' => 'api', 'action' => 'add_payment_method'));
  Router::connect('/get_payment_detail', array('controller' => 'api', 'action' => 'get_payment_detail'));
  Router::connect('/update_payment_detail', array('controller' => 'api', 'action' => 'update_payment_detail'));
  Router::connect('/delete_payment_method', array('controller' => 'api', 'action' => 'delete_payment_method'));
  Router::connect('/add_to_wishlist', array('controller' => 'api', 'action' => 'add_to_wishlist'));
  Router::connect('/kitchen_dishes', array('controller' => 'api', 'action' => 'kitchen_dishes'));
  Router::connect('/post_review', array('controller' => 'api', 'action' => 'post_review'));
  Router::connect('/send_verification', array('controller' => 'api', 'action' => 'send_verification'));
  Router::connect('/validate_verification', array('controller' => 'api', 'action' => 'validate_verification'));
  Router::connect('/wishlist', array('controller' => 'api', 'action' => 'wishlist'));
  Router::connect('/removewish', array('controller' => 'api', 'action' => 'removewish'));
  Router::connect('/newrequest', array('controller' => 'api', 'action' => 'newrequest'));
  Router::connect('/myrequest', array('controller' => 'api', 'action' => 'myrequest'));
  Router::connect('/requestlist', array('controller' => 'api', 'action' => 'requestlist'));
  Router::connect('/deleterequest', array('controller' => 'api', 'action' => 'deleterequest'));
  Router::connect('/answer_request', array('controller' => 'api', 'action' => 'answer_request'));
  Router::connect('/get_payment_details', array('controller' => 'api', 'action' => 'get_payment_details'));
  Router::connect('/update_payment_details', array('controller' => 'api', 'action' => 'update_payment_details'));
  Router::connect('/delete_card_details', array('controller' => 'api', 'action' => 'delete_card_details'));
  Router::connect('/submit_order', array('controller' => 'api', 'action' => 'submit_order'));
  Router::connect('/order_history', array('controller' => 'api', 'action' => 'order_history'));
  Router::connect('/getCountries', array('controller' => 'api', 'action' => 'getCountries'));
  Router::connect('/getStates', array('controller' => 'api', 'action' => 'getStates'));
  Router::connect('/getCities', array('controller' => 'api', 'action' => 'getCities'));
  Router::connect('/user_dashboard', array('controller' => 'api', 'action' => 'user_dashboard'));

  Router::connect('/user_profile', array('controller' => 'api', 'action' => 'user_profile'));
  Router::connect('/app_cms', array('controller' => 'api', 'action' => 'app_cms'));//chk
  Router::connect('/order_completion', array('controller' => 'api', 'action' => 'order_completion'));
  Router::connect('/order_cancellation', array('controller' => 'api', 'action' => 'order_cancellation'));
  Router::connect('/removeActivity', array('controller' => 'api', 'action' => 'removeActivity'));//chk
  Router::connect('/getAdminId', array('controller' => 'api', 'action' => 'getAdminId'));
  Router::connect('/checkPaypalVerification', array('controller' => 'api', 'action' => 'checkPaypalVerification'));
  Router::connect('/paypalValidation', array('controller' => 'api', 'action' => 'paypalValidation'));
  Router::connect('/cms_detail', array('controller' => 'api', 'action' => 'cms_detail'));
  Router::connect('/getPaypalVerification', array('controller' => 'api', 'action' => 'getPaypalVerification'));
  Router::connect('/logout', array('controller' => 'api', 'action' => 'logout'));
  Router::connect('/cms_cities', array('controller' => 'api', 'action' => 'cms_cities'));
  Router::connect('/checkStripeStatus', array('controller' => 'api', 'action' => 'checkStripeStatus'));
  Router::connect('/order_detail', array('controller' => 'api', 'action' => 'order_detail'));
  Router::connect('/send_referral', array('controller' => 'api', 'action' => 'send_referral'));
  Router::connect('/updateDeviceToken', array('controller' => 'api', 'action' => 'updateDeviceToken'));




/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
