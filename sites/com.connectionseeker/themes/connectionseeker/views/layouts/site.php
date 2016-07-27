<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/site.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/960.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/newstyle.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/reset.css" />
	<link href='http://fonts.googleapis.com/css?family=Glegoo' rel='stylesheet' type='text/css'>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container_16 center">
<div class="grid_16">

<div class="grid_1 login" style="text-align:center;"><a href="<?php echo Yii::app()->createUrl('/site/login');?>">Login</a></div>
</div>

<div class="grid_16 logo"><a href="http://dev.connectionseeker.com"><img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/home-logo.png"></a></div>
<div class="clear">&nbsp;</div>
<div class="grid_10 shadow center">
<p>Since the launch of the Internet marketers have been trying to find ways to connect with potential customers.</p>
<p>Connection Seeker is looking to redefine content marketing through scalability, ease of use, and unmatched customer service.</p></div>
<div class="clear"></div>
<div class="clear"></div>


<div class="grid_10 signup center">
Sign up Now!</div>

<div class="clear">&nbsp;</div>


<div class="grid_16 center">
<?php echo $content; ?>
<div class="grid_16" style="height:70px;">&nbsp;</div>

  <div class=" grid_16 footer"><a href="#"> About</a> |     <a href="<?php echo Yii::app()->createUrl('/site/contact');?>">Contact</a> |    <a href="#"> TOS</a> |     <a href="#"> Privacy Policy</a></div>

</div>

</div>

    <div class="footercp">
        Copyright &copy; <?php echo date('Y'); ?> by CopyPress. All Rights Reserved.<br/>
        <?php echo 'Powered By <a href="http://www.copypress.com" rel="external">CopyPress.com</a>'; ?>
    </div><!-- footer -->
</div><!-- page -->

</body>
</html>