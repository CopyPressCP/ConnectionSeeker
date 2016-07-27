<?php
$items =  array(
	array('label'=>'Create Trail', 'url'=>array('create')),
	array('label'=>'Manage Trail', 'url'=>array('index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>