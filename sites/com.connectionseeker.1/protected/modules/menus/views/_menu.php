<?php $this->widget('zii.widgets.CMenu', array(
	'firstItemCssClass'=>'first',
	'lastItemCssClass'=>'last',
	'htmlOptions'=>array('class'=>'actions'),
	'items'=>array(
		array(
			'label'=>Menus::t('core', 'Menus'),
			'url'=>array('tab/index'),
			'itemOptions'=>array('class'=>'item-assignments'),
		),
		array(
			'label'=>Menus::t('core', 'Tabs'),
			'url'=>array('tab/menus'),
			'itemOptions'=>array('class'=>'item-permissions'),
		)
	)
));	?>