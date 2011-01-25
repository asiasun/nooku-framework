<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
		<jdoc:include type="head" />

		<link href="template/css/template.css" rel="stylesheet" type="text/css" />
		<?php if($this->direction == 'rtl') : ?>
		<link href="template/css/template_rtl.css" rel="stylesheet" type="text/css" />
		<?php endif; ?>

		<script type="text/javascript" src="../media/system/js/mootools.js"></script>
		<script type="text/javascript" src="includes/js/installation.js"></script>
		<script type="text/javascript" src="template/js/validation.js"></script>

		<script type="text/javascript">
			Window.onDomReady(function(){ new Accordion($$('h3.moofx-toggler'), $$('div.moofx-slider'), {onActive: function(toggler, i) { toggler.addClass('moofx-toggler-down'); },onBackground: function(toggler, i) { toggler.removeClass('moofx-toggler-down'); },duration: 300,opacity: false, alwaysHide:true, show: 1}); });
  		</script>
	</head>
	<body onload="resizeFrame()">
		<div id="header">
			<div id="version"><?php echo JVERSION ?></div>
			<span><?php echo JText::_('Installation') ?></span>
		</div>
		<div id="content-box">
			<jdoc:include type="installation" />
		</div>
	</body>
</html>
