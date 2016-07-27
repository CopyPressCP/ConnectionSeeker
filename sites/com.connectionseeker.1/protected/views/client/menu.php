<?php
$items =  array(
	array('label'=>'Create Client', 'url'=>array('create')),
	array('label'=>'Manage Client', 'url'=>array('index')),
	array('label'=>'Create Domain', 'url'=>array('clientDomain/create')),
	array('label'=>'Manage Domain', 'url'=>array('clientDomain/index')),
	array('label'=>'Create Keyword', 'url'=>array('clientDomainKeyword/create')),
	array('label'=>'Manage Keyword', 'url'=>array('clientDomainKeyword/index')),
	array('label'=>'Discovery', 'url'=>array('discovery/index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>