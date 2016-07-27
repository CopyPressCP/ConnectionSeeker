<?php
$this->widget('zii.widgets.CMenu', array(
	'firstItemCssClass'=>'first',
	'lastItemCssClass'=>'last',
	'htmlOptions'=>array('class'=>'actions'),
	'items'=>array(
		array(
			'label'=>Menus::t('email', 'Report By User'),
			'url'=>array('email/report'),
		),
		array(
			'label'=>Menus::t('email', 'Report By Mailer'),
			'url'=>array('email/mreport'),
		),
		array(
			'label'=>Menus::t('email', 'Report By Template'),
			'url'=>array('email/treport'),
		),
		array(
			'label'=>Menus::t('email', "Report By Template For {$_current_user}"),
			'url'=>array('email/treport', 'user_id'=>$_REQUEST['user_id']),
			'visible'=>(!empty($_current_user) && $sortby == 'mailer') ? true : false,
		),
		array(
			'label'=>Menus::t('email', "Report By Mailer For {$_current_user}"),
			'url'=>array('email/mreport', 'user_id'=>$_REQUEST['user_id']),
			'visible'=>(!empty($_current_user) && $sortby == 'template') ? true : false,
		),
	)
));	?>
<div class="clear"></div>