<?php
$items =  array(
	array('label'=>'Create Domain', 'url'=>array('create')),
	array('label'=>'Manage Blogger Program', 'url'=>array('index')),
	array('label'=>'Upload Domains', 'url'=>array('upload')),
);
$this->widget('zii.widgets.CMenu',array(
        'firstItemCssClass'=>'first',
        'lastItemCssClass'=>'last',
        'htmlOptions'=>array('class'=>'actions'),
        'items'=>$items)); ?>