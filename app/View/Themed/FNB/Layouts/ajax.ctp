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
<?php   
	echo $this->Html->css('style');
	echo $this->Html->css('custom_input');
	echo $this->Html->css('responsive');
	echo $this->Html->css('jquery.fancybox');
	echo $this->Html->css('jquery.bxslider');
	echo $this->Html->css('jRating.jquery');
	echo $this->Html->css('custom');
	
	echo $this->Html->script('jquery-1.11.0.min'); 
	echo $this->Html->script('jquery.validate');
	echo $this->Html->script('common'); 

	?>
<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>
