<?php
$items =  array(
	array('label'=>'Create Cart', 'url'=>array('create')),
	array('label'=>'Manage Cart', 'url'=>array('index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>