<?php
$items =  array(
	array('label'=>'Create Online', 'url'=>array('create')),
	array('label'=>'Manage Online', 'url'=>array('index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>