<?php
$items =  array(
	array('label'=>'Create Iohistory', 'url'=>array('create')),
	array('label'=>'Manage Iohistory', 'url'=>array('index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>