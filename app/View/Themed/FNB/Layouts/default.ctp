<?php
/**
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

$cakeDescription = __d('cake_dev', 'Lacart');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('style');
		echo $this->Html->css('custom_input');
		echo $this->Html->css('responsive');
		echo $this->Html->css('jquery.fancybox');
		echo $this->Html->css('jquery.bxslider');
		echo $this->Html->css('jRating.jquery');
		echo $this->Html->css('custom');
 		
		echo $this->Html->script('jquery-1.11.0.min');
		echo $this->Html->script('jquery.bxslider.min');
		echo $this->Html->script('jquery.validate');
		echo $this->Html->script('jquery.fancybox.pack');
		echo $this->Html->script('jquery.fancybox-buttons');
		echo $this->Html->script('jRating.jquery');
		
		echo $this->Html->script('common');
		echo $this->Html->script('search');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	
	<script type="text/javascript">
		$(document).ready(function(){
		  if($('.bxslider').length)
		  {
		 	$('.bxslider').bxSlider({
			  'pager': false
			}); 	
		  }
				  
        //$('.signupbtn').click(function(){
         // $('.signuppopup').fadeToggle(300);
        //});	

       // $('.loginbtn').click(function(){
        //  $('.loginpopup').fadeToggle(300);
        //});		
		
		});
	</script>
</head>
<body>
	<div id="wrapper">
		<?php echo $this->element('header');?>
		
    	<?php echo $this->fetch('content'); ?>
	  
	  	<?php echo $this->element('footer');?>
	  
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>
