<?php
$items =  array(
	array('label'=>'Permission', 'url'=>array('/rights/assignment/view')),
	array('label'=>'Create Type', 'url'=>array('/types/create')),
	array('label'=>'Manage Type', 'url'=>array('/types/index')),
	array('label'=>'Preferences', 'url'=>array('/types/preference')),
	array('label'=>'Add Blacklist', 'url'=>array('/blacklist/create')),
	array('label'=>'Manage Blacklist', 'url'=>array('/blacklist/index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>