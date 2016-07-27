<?php
$items =  array(
	array('label'=>'Campaigns', 'url'=>array('campaign/index')),
	array('label'=>'Create Campaign', 'url'=>array('campaign/create')),
	array('label'=>'Inventories', 'url'=>array('inventory/index')),
	array('label'=>'Create Inventory', 'url'=>array('inventory/create')),
	array('label'=>'Links', 'url'=>array('link/index')),
	array('label'=>'Link Tasks', 'url'=>array('task/index')),
	array('label'=>'Upload', 'url'=>array('inventory/upload')),

);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>