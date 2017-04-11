<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/bootstrap.css" type="text/css">
	<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/slider.css" type="text/css">
	<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/colorbox.css" type="text/css">
	<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/bootstrap-select.css" type="text/css">
	<link rel="stylesheet" href="/wp-content/themes/myspartansublease/css/datepicker.css" type="text/css">
	<?php wp_head(); ?>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/bootstrap.js"></script>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/bootstrap-slider.js"></script>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/jquery.mask.min.js"></script>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/jquery.imagemapster.js"></script>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/jquery.form.min.js"></script>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="/wp-content/themes/myspartansublease/js/additional-methods.min.js"></script>
	<script type="text/javascript"
	  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKCXeUffiddwRUAfdVT5xf5keehGI4wn4&sensor=false">
	</script>
	<script type='text/javascript' src="/wp-content/themes/myspartansublease/js/mss.googlemaps.js"></script>
	<script type='text/javascript' src="/wp-content/themes/myspartansublease/js/mss.common.js"></script>
	<script type='text/javascript' src="/wp-content/themes/myspartansublease/js/jquery.colorbox-min.js"></script>
	<script type='text/javascript' src="/wp-content/themes/myspartansublease/js/bootstrap-select.min.js"></script>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">
		<div id="header">
			<div class="logoheader">
				<a href="/"><div class="logo"></div></a>
			</div>
		</div><!-- end header -->
		<div id="navigation">
			<div class="navwrapper">
				<div class="navitem first">
					<a href="/">Home</a>
				</div>
				<div class="navitem">
					<a href="/about-us">About Us</a>
				</div>
				<div class="navitem">
					<a href="/contact-us">Contact Us</a>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div id="sitebody">
			<div class="site-content">
