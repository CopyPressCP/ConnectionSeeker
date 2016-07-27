<?php
$items =  array(
	array('label'=>'Outreach', 'url'=>array('domain/index&touched=true')),
	array('label'=>'Create Domain', 'url'=>array('domain/create')),
	array('label'=>'Manage Domain', 'url'=>array('domain/index')),
	array('label'=>'Manage Email', 'url'=>array('email/index')),
	array('label'=>'Audit', 'url'=>array('domain/audit')),
	array('label'=>'Backlink', 'url'=>array('backlink/index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>