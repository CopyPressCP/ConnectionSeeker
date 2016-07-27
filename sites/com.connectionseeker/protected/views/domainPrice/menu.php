<?php
$items =  array(
	array('label'=>'Create DomainPrice', 'url'=>array('create')),
	array('label'=>'Manage DomainPrice', 'url'=>array('index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>