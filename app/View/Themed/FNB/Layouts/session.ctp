<?php
/**
 *
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
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE HTML>
<html>
<head>
	<script>
		var FACEBOOK_APP_ID = '<?php echo FB_APP_ID?>'
	</script>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"  charset="utf-8">
	<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1.0;">
	<script src="<?php echo NODE_SITE_URL?>/socket.io/socket.io.js"> </script>
	<title><?php echo isset($siteTitle) ? $siteTitle : SITE_TITLE; ?></title>	
	<meta name="description" content="<?php echo isset($siteDescription) ? $siteDescription : SITE_DESCRIPTION; ?>" />
	<meta name="keywords" content="<?php echo isset($siteKeywords) ? $siteKeywords : SITE_KEYWORDS; ?>" />
    <!-- Bootstrap core CSS -->
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<?php
		echo $this->Html->css(
			array(
				'session/bootstrap', 'session/font-awesome', 'session/style', 'jquery.gritter', 'flipclock'
			)
		);
	?>
	<?php
		echo $this->Html->script(
			array(
				'jquery.min', 'bootstrap.min', 'custom', 'bootstrap-datepicker', 'jquery.gritter', 'flipclock',
				// 'adapter.js'
			)
		);
	?>
</head>
<body>
	<?php echo $this->fetch('content'); ?>
</body>
</html>
	<script type="text/javascript">
			var this_user_id = '<?php echo $this->Session->read('Auth.User.id')?>'
			if(this_user_id)
			{
				var socket = io.connect('<?php echo NODE_SITE_URL?>');
				socket.on('all_users_list', function(user_ids){
			    	user_ids = JSON.parse(user_ids);
			    	if(typeof(user_ids.this_user_id) == 'undefined')
			    	{
			    		socket.emit('add_user', this_user_id);
			    	}
			    });
			    socket.on('send_notification', function(data){
			    	$.gritter.add({
						title: data['message_title'],
						text: data['message']
					});
			    });
			    socket.on('start_flip', function(data){
			    	$('.flip-counter').FlipClock();
			    });
			}
		</script>
    <script>
		$(document).ready(function(){
			$('#myTab a').click(function (e) {
			  e.preventDefault()
			  $('#myTab a[href="#profile"]').tab('show')
			})
		})
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
		});
    </script>