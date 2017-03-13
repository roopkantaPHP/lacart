<?php
DEFINE('SITE_TITLE', 'FNB');
DEFINE('SITE_KEYWORDS', 'FNB');
DEFINE('SUPER_ADMIN', '1');
DEFINE('NORMAL_USER', '2');

DEFINE('FRONT_END', 'FNB');
DEFINE('BACK_END', 'Admin');

define('LOGIN_COOKIE_NAME', 'fnb_login');
define('SITE_FACEBOOK_URL', 'http://www.facebook.com');
define('SITE_TWITTER_URL', 'http://www.twitter.com');
define('SITE_GOOGLE_URL', 'http://www.google.com');

$config = array
(
);

//Can change from database
if($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'fnb.localhost')
{
	/***** facebook api details *******/
	define('FB_APP_ID', '1425835014351043');
	define('FB_APP_SECRET_KEY', '5e3fe6edcdce59ff6fe969ff55d9cc07');

	/***** google captcha details *******/
	define('CAPCHA_PUBLIC_KEY', '6LcCmfMSAAAAAB_efRsPjRfeLOsbH97jt8R-4ogv');
	define('CAPCHA_PRIVATE_KEY', '6LcCmfMSAAAAAPoiqFmkGRWBFrqazDcv7W5QEOvd');

	define('PROFILE_DEFAULT_IMAGE', '/images/defaultavatar.jpg');

	define('BASE_URL', 'http://localhost/lacart/');

} else
{
	/***** facebook api details *******/
	define('FB_APP_ID', '242015032658184');
	define('FB_APP_SECRET_KEY', '2681a7b94dacf7c89e55e02fa31f437b');

	/***** google captcha details *******/
	define('CAPCHA_PUBLIC_KEY', '6LcCmfMSAAAAAB_efRsPjRfeLOsbH97jt8R-4ogv');
	define('CAPCHA_PRIVATE_KEY', '6LcCmfMSAAAAAPoiqFmkGRWBFrqazDcv7W5QEOvd');

	define('PROFILE_DEFAULT_IMAGE', '/images/defaultavatar.jpg');

	define('BASE_URL', 'http://www.lacart.com/');
}

define('ADMIN_EMAIL', 'admin@lacart.com');
define('FROM_EMAIL', 'info@lacart.com');
define('REPLYTO_EMAIL', 'admin@lacart.com');
define('GOOGLE_GEOCODE_API', 'AIzaSyDmIidll36CyUil6DG-FCAqthHKu05gr9s');

define('TWILIO_SID', 'ACaadfec16db7041abe15be5ede0dfd197');
define('TWILIO_TOKEN', '1c1666ef0ea550362480747a45909cf5');
define('TWILIO_FROM_NUMBER', '+12028001769');

//Demo paypal credentials
//define('PAYPAL_USER', 'tech-facilitator_api1.lacart.com');
//define('PAYPAL_PWD', 'QEDV8FWA7W6WMBD9');
//define('PAYPAL_SIGNATURE', 'A0F.-QOn1ULI4bl1Q43RiH0eUWuzAUFtnMvlcw.wCXiQ4VVuyEML-LWF');
//define('PAYPAL_APPID', 'APP-80W284485P519543T');

//Live paypal credentials
define('PAYPAL_USER', 'tech_api1.lacart.com');
define('PAYPAL_PWD', '9YWBK42JBV7F5JTP');
define('PAYPAL_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31ArS9ZTDZJ670xxZTpnMDPPMMrKFQ');
define('PAYPAL_APPID', 'APP-0CN68919BL325024Y');

//Stripe Testing Api keys
//define('STRIPE_SECRET_KEY',"sk_test_mvmxdrlnE6Gmi85rs0mIYtWs");
//define('STRIPE_PUBLISHABLE_KEY',"pk_test_YQuCXOXTSYwChZJkpDkslRMe");
//define('STRIPE_CLIENT_ID',"ca_6Db37pvQYcpgdaKIm9yYYB4DNo6Ye74K");

//Stripe Live Api keys
define('STRIPE_SECRET_KEY',"sk_live_437cAkKej4tqoQKMvdbLgjTV");
define('STRIPE_PUBLISHABLE_KEY',"pk_live_KlNxeYaCzfqJq7iornVBn1F6");
define('STRIPE_CLIENT_ID',"ca_6Db3axxfAzNWHMypGr9AWVPupIXc671z");

//Balanaced Credentials
define('BALANCED_API_KEY',"ak-test-1avcdMIi8dOyzENt9WDpLpPN4tz7z68LR");
define('BALANCED_URL_KEY',"TEST-MP7GwGXp7Guzt8Bq4XERoQ1y");

if (!defined('SITE_IMAGES')) {
	define('SITE_IMAGES', WWW_ROOT . 'images' . DS);
}

if (!defined('SITE_IMAGES_URL')) {
	define('SITE_IMAGES_URL', 'images/');
}
define('PROFILE_IMAGE_PATH', SITE_IMAGES.'profile/');
define('PROFILE_IMAGE_URL', SITE_IMAGES_URL.'profile/');
define('PROFILE_IMAGE_FOLDER', 'profile/');

define('KITCHEN_IMAGE_PATH', SITE_IMAGES.'kitchen/');
define('KITCHEN_IMAGE_URL', SITE_IMAGES_URL.'kitchen/');
define('KITCHEN_IMAGE_FOLDER', 'kitchen/');

define('DISH_IMAGE_PATH', SITE_IMAGES.'dish/');
define('DISH_IMAGE_URL', SITE_IMAGES_URL.'dish/');
define('DISH_IMAGE_FOLDER', 'dish/');

define('CMS_IMAGE_PATH', SITE_IMAGES.'cms/');
define('CMS_IMAGE_URL', SITE_IMAGES_URL.'cms/');
define('CMS_IMAGE_FOLDER', 'cms/');

/*
	set email address for sending emails
*/
define("NEWSLETTER_EMAIL_FROM", 'fnb@xicom.com');

/*
	set site name for sending emails
*/
define("NEWSLETTER_NAME_FROM", 'FNB Website');

/*
 	Set Api security token, this token same as api end
*/
define('API_SECURITY_KEY', 'FXNIBCOM#589$');
define('API_REQUEST_EXPIRED', '120'); //expire time in second

$config['UNIT'] = array('oz' => 'oz', 'pcs' => 'pcs');
?>
