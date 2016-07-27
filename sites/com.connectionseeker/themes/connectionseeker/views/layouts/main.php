<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl ; ?>/css/print.css" media="print" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl ; ?>/css/form.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl ; ?>/css/form.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl ; ?>/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl ; ?>/css/dropbox.css" />
	<!--[if IE 9]>
		<link rel="stylesheet" type="text/css" href="href="<?php echo Yii::app()->theme->baseUrl ; ?>/css/ie8.css">
	<![endif]-->
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
    if(isset($roles['Admin']) || isset($roles['InternalOutreach'])){ ?>
    <script type='text/javascript'>
    (function (d, t) {
      var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
      bh.type = 'text/javascript';
      bh.src = '//www.bugherd.com/sidebarv2.js?apikey=rwbjn3upukakwbn8ltnita';
      s.parentNode.insertBefore(bh, s);
      })(document, 'script');
    </script>
    <?php }?>

</head>

<body>
  <div id="wrapper">
    <div id="contentmain">
      <div id="acct">
		<ul>
			<li class="nolink">Hello <?php echo CHtml::encode(Yii::app()->user->name); ?></li>
			<li><?php $roles = Yii::app()->authManager->getRoles(Yii::app()->user->id); if(isset($roles['Admin'])){?> <img src="<?php echo Yii::app()->theme->baseUrl ; ?>/images/nav-icons/system.png" /><?php echo CHtml::link(Yii::t('user', 'System'),array('/rights')); }?></li>
			<li><img src="<?php echo Yii::app()->theme->baseUrl ; ?>/images/nav-icons/account.png" /><?php echo CHtml::link(Yii::t('user', 'Settings'),array('/setting/profile', 'id'=>Yii::app()->user->id)); ?></li>
			<li><img src="<?php echo Yii::app()->theme->baseUrl ; ?>/images/nav-icons/logout.png" /><?php echo CHtml::link(Yii::t('user', 'Logout'),array('/site/logout')); ?></li>
		</ul>
	  </div>
      <div id="mid" >
		<div id="header">
			<div id="header-left">
			</div>
				<div id="header-middle">
					<?php 
          $items = array(
            array('img'=> array( 'name'=> 'client_icon.png',  'htmlOptions'=> array('id' => 'client_icon', 'name'=> 'client_icon')), 'url'=>array('/client'), 'activeMenus' => array('clientDomain', 'clientDomainKeyword', 'discovery/index')),
            array('img' => array('name'=>'outrearch_icon.png', 'htmlOptions'=>array('id' => 'outrearch_icon', 'name'=> 'outsearch_icon')), 'url'=>array('/domain/outreach'), 'activeMenus' => array('domain', 'email')),
            array('img' => array('name'=>'campaign_icon.png', 'htmlOptions'=>array('id' => 'campaign_icon', 'name'=> 'campaign_icon')), 'url'=>array('/campaign'), 'activeMenus' => array('inventory','task')),
            array('img' => array('name'=> 'user_icon.png', 'htmlOptions'=>array('id' => 'user_icon', 'name'=> 'user_icon')), 'url'=>array('/user')),
            array('img' => array('name'=>'email_icon.png', 'htmlOptions'=>array('id' => 'email_icon', 'name'=> 'email_icon')) , 'url'=>array('/mailer'), 'activeMenus' => array('template')),
            array('img' => array('name'=>'system_icon.png', 'htmlOptions'=>array('id' => 'system_icon', 'name'=> 'system_icon')) , 'url'=>array('/rights'), 'activeMenus' => array('types','blacklist')));
           $items = $module = Menus::module()->getMenu()->getTabs();
           $this->widget('application.extensions.lkmenu.LKMenu', array( 'items' => $items));
        ?>
				</div>
				<div id="header-right">
				</div>
				<div class="clear">
				</div>
				</div>
				<div id="nav-middle">
					<div id="logo">
						<a href="<?php echo Yii::app()->createUrl("site/dashboard"); ?>">
							<img border="0" alt="Connectionseeker" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/logo.png">
						</a>
					</div>
					<div id="subnav">
						<?php 
							$this->menu = Menus::module()->getMenu()->getSubMenus();
							//if ($this->menu || $this->getViewFile('menu')) {
							if ($this->menu) {
						?>
								<?php
									$this->widget('zii.widgets.CMenu',array('items'=>$this->menu));
									/*if ($this->menu) { 
										$this->widget('zii.widgets.CMenu',array('items'=>$this->menu));
									} else if ($this->getViewFile('menu')) {
										echo $this->renderPartial('menu');
									}*/
								?>
						<?php 
							}// sub menu end 
						?>
					</div>
				</div>
				<div class="clear"></div>
				<div id="headermain">
					<?php if(isset($this->breadcrumbs)):?>
					<?php $this->widget('zii.widgets.CBreadcrumbs', array(
						'links'=>$this->breadcrumbs,
					)); ?><!-- breadcrumbs -->
					<?php endif?>
					<div style="clear:both"></div>
				</div>
        <div class="midbody">
        

        <div id="midcont">
          <div id="contmid">
          <div style="clear:both"></div>
          <?php echo $content; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<div id="contbottom">
	<div class="contbottom-wrapper">
		<div class="contbottom-nav">
			<ul>
				<li></li>
				<li class="last"></li>
			</ul>
		</div>
		<div class="contbottom-copy">
			Copyright &copy; 2012 by <a href="http://www.copypress.com">CopyPress</a>. All Rights Reserved.
		</div>
	</div>
</div>

<div style="clear:both"></div>

<?php  Yii::app()->getClientScript()->registerScriptFile(Yii::app()->theme->baseUrl . '/js/comment.js'); ?>
<script type="text/javascript">
/*
	$(document).ready(function() {
		//$('.item').selectbox();
	});
*/
</script>

<script type="text/javascript">
 var sEmbedHost = (("https:" == document.location.protocol) ? "https://" : "http://");
// document.write(unescape("%3Cscript charset='utf-8' id='screenr_recorder' src='" + sEmbedHost + "steelcast.viewscreencasts.com/api/recorder' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">Screenr.Recorder({id:"62f042c27a0d45d5861e3953618729fd"}).embed();</script>

</body>
</html>
