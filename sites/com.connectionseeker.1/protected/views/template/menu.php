<?php
$items =  array(
	array('label'=>'Create Mailer', 'url'=>array('/mailer/create')),
	array('label'=>'Manage Mailer', 'url'=>array('/mailer/index')),
	array('label'=>'Create Template', 'url'=>array('/template/create')),
	array('label'=>'Manage Template', 'url'=>array('/template/index')),
);
$this->widget('zii.widgets.CMenu',array('items'=>$items)); ?>