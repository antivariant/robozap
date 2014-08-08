<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" 
   xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

 <head>
<jdoc:include type="head" />
 <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template;?>/js/main.js"></script>
<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
 <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700&subset=latin,cyrillic-ext,cyrillic' rel='stylesheet' type='text/css'>
 <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/k2-styles.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
<jdoc:include type="modules" name="header" /> 
</head>  

<body>
<?php include_once("analytics.php") ?>
<jdoc:include type="modules" name="top" />

<!-- HEADER -->

<div class="header">
	<div class="header-top">
		<a class="textLogo" href="<?php echo $this->baseurl ?>">ROBOZAP</a>

		<div class="login">
		<div class="log-head">РЕГИСТРАЦИЯ И ВХОД<span class="close"><span></div>
			<jdoc:include type="modules" name="login" />
		</div>

		<div class="cart-login">

			<span id="btnLogin"><a href="#">
					<?php 
						$user = JFactory::getUser();
						if ($user->guest){
							echo "Войти";
						}
						else
						{
							echo "Выйти";
						}
					?>
			</a></span> <span class="devider"></span>

			<div class="cart-icon"><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/cart-icon.png"/> </div>
			<jdoc:include type="modules" name="cart" />
		</div>
	</div>

	<div class="nav">
	<img src="<?php echo $this->params->get('logo')?>" style="float:left;"/>
	<jdoc:include type="modules" name="position-7" /> 
	<jdoc:include type="modules" name="search" /> 
	</div>

</div>

<!-- MAIN BODY -->
<div class="wrapper">
<div class="bredate">
<jdoc:include type="modules" name="breadcrumb" /> 
<div id="today">
  <?php echo JHtml::_('date', 'now', 'l, d M Y');?>
</div>
</div>



<?php 
$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
if ( !preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ):?>
	<jdoc:include type="modules" name="banner1"/>
<?php endif?>


<jdoc:include type="modules" name="mainbody_top" style="xhtml"/> 
<jdoc:include type="component" style="xhtml"/>
<jdoc:include type="modules" name="mainbody_bottom" style="xhtml"/>
<jdoc:include type="modules" name="bottom" style="xhtml"/>
<jdoc:include type="modules" name="user9" style="xhtml"/>

</div>


<?php
//Adsense внизу страницы кроме главной и магазина
$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();


if ( $active != $menu->getDefault() && $active->id != 167 && $active != null):?>

<div class="footer_ad">
	<script type="text/javascript"><!--
	google_ad_client = "ca-pub-1883754163093653";
	/* Robozap широкий */
	google_ad_slot = "5111435327";
	google_ad_width = 970;
	google_ad_height = 90;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
<div>
        
<?php endif?>




<div class="footer_nav"><jdoc:include type="modules" name="footer_nav" style="xhtml"/></div>








</body>

</html>