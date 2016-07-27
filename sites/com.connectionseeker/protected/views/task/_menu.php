<?php
$cmpid = $_REQUEST['campaign_id'];
$this->widget('zii.widgets.CMenu', array(
	'firstItemCssClass'=>'first',
	'lastItemCssClass'=>'last',
	'htmlOptions'=>array('class'=>'actions'),
	'items'=>array(
		array(
			'label'=>Menus::t('task', 'Admin'),
			'url'=>array('task/processing', 'campaign_id'=>$cmpid, 'dpm'=>5),
		),
		array(
			'label'=>Menus::t('task', 'Pre-Content'),
			'url'=>array('task/processing', 'campaign_id'=>$cmpid, 'dpm'=>1),
		),
		array(
			'label'=>Menus::t('task', 'Content'),
			'url'=>array('task/processing', 'campaign_id'=>$cmpid, 'dpm'=>4),
		),
		array(
			'label'=>Menus::t('task', 'Client'),
			'url'=>array('task/processing', 'campaign_id'=>$cmpid, 'dpm'=>6),
		),
		array(
			'label'=>Menus::t('task', 'QA'),
			'url'=>array('task/processing', 'campaign_id'=>$cmpid, 'dpm'=>2),
		),
		array(
			'label'=>Menus::t('task', 'Outreach'),
			'url'=>array('task/processing', 'campaign_id'=>$cmpid, 'dpm'=>3),
		),
		array(
			'label'=>Menus::t('task', 'ALL'),
			'url'=>array('task/processing', 'campaign_id'=>$cmpid, 'dpm'=>999),
		),
	)
));	?>
<div class="clear"></div>