<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>FNB::Secure Admin Suit</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('admin_style');
		echo $this->Html->css('custom');	
		echo $this->Html->script('jquery.min');
		echo $this->Html->script('jquery.validate');	
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body class="greybg">
	<!--Wrapper Start from Here-->
	<div id="wrapper">
		<!--Header Start from Here-->
			<?php echo $this->element('header') ?>
			<div id="container">
				<?php
					echo $this->Session->flash();
					echo $this->fetch('content'); 
				?>
				<?php echo $this->element('footer') ?>
			</div>
		<!--Container end Here-->
	</div>
	 
		<div class="row" align="center">
		</div>
	
	<!--Wrapper End from Here-->
</body>
<script>
$(".numeric").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    $(".count200").keyup(function(e){
		var remainChar = 200-parseInt($(this).val().length);
		$('.char-info').html(remainChar+' characters');
		$(this).val($(this).val().substr(0, 200));
	});
</script>
</html>
