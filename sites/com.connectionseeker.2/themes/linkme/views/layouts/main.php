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
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
  <div id="wrapper">
    <div id="contentmain">
      <div id="logo">
        <a target="_blank" href="<?php echo Yii::app()->baseUrl; ?>">
        <img border="0" alt="Connectionseeker" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/logo.png"></a>
      </div>
      <div id="acct">Welcome <?php echo CHtml::encode(Yii::app()->user->name); ?> | <?php echo CHtml::link(Yii::t('user', 'Logout'),array('/site/logout')); ?></div>
      <div id="mid" >
        <div class="upmid"></div>
        <div class="midbody">
        <?php 
          $items = array(
            array('img'=> array( 'name'=> 'client_icon.png',  'htmlOptions'=> array('id' => 'client_icon', 'name'=> 'client_icon')), 'url'=>array('/client'), 'activeMenus' => array('clientDomain', 'clientDomainKeyword', 'discovery/index')),
            array('img' => array('name'=>'outsearch_icon.png', 'htmlOptions'=>array('id' => 'outsearch_icon', 'name'=> 'outsearch_icon')), 'url'=>array('/domain/index&touched=ture'), 'activeMenus' => array('domain', 'email')),
            array('img' => array('name'=>'campaign_icon.png', 'htmlOptions'=>array('id' => 'campaign_icon', 'name'=> 'campaign_icon')), 'url'=>array('/campaign'), 'activeMenus' => array('inventory','task')),
            array('img' => array('name'=> 'user_icon.png', 'htmlOptions'=>array('id' => 'user_icon', 'name'=> 'user_icon')), 'url'=>array('/user')),
            array('img' => array('name'=>'email_icon.png', 'htmlOptions'=>array('id' => 'email_icon', 'name'=> 'email_icon')) , 'url'=>array('/mailer'), 'activeMenus' => array('template')),
            array('img' => array('name'=>'system_icon.png', 'htmlOptions'=>array('id' => 'system_icon', 'name'=> 'system_icon')) , 'url'=>array('/rights'), 'activeMenus' => array('types','blacklist')));
           $items = $module = Menus::module()->getMenu()->getTabs();
           $this->widget('application.extensions.lkmenu.LKMenu', array( 'items' => $items));
        ?>

        <div id="midcont">
          <div id="contup"></div>
          <div id="contmid" >
          <?php 
           $this->menu = Menus::module()->getMenu()->getSubMenus();
//          if ($this->menu || $this->getViewFile('menu')) {
          if ($this->menu) {
          ?>
          <div class="menubaseleft"></div>
            <div class="menubase">
        	   <div class="menuupleft"></div>
                <div id="menuup">
                    <?php
                        $this->widget('zii.widgets.CMenu',array('items'=>$this->menu));
                       /*if ($this->menu) { 
                            $this->widget('zii.widgets.CMenu',array('items'=>$this->menu));
                       } else if ($this->getViewFile('menu')) {
                            echo $this->renderPartial('menu');
                       }*/
                     ?>
                </div>  
               <div class="menuupright"></div>
            </div>
          <div class="menubaseright"></div>
          <?php 
          }// sub menu end 
           ?>
          <div style="clear:both"></div>
            <?php if(isset($this->breadcrumbs)):?>
                <?php $this->widget('zii.widgets.CBreadcrumbs', array(
                    'links'=>$this->breadcrumbs,
                )); ?><!-- breadcrumbs -->
            <?php endif?>
          <div style="clear:both"></div>
          <?php echo $content; ?>
            </div>
            <div id="contbottom"></div>
          </div>
        </div>
        <div class="bottommid"></div>
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
 //document.write(unescape("%3Cscript charset='utf-8' id='screenr_recorder' src='" + sEmbedHost + "steelcast.viewscreencasts.com/api/recorder' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">Screenr.Recorder({id:"62f042c27a0d45d5861e3953618729fd"}).embed();</script>

</body>
</html>
