<?php
$items =  array(
	array('label'=>'Create OutreachTracking', 'url'=>array('create')),
	array('label'=>'Manage OutreachTracking', 'url'=>array('index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>