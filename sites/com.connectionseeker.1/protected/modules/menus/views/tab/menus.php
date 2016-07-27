<?php 
$parent_id = Yii::app()->request->getParam('pid');
$hint = $parent_id ? 'Menus' : 'Tabs';
$this->breadcrumbs = array(
	'Menus'=>Menus::getBaseUrl(),
); 
if ($parent_id > 0) {
    $this->breadcrumbs['Tabs'] = Yii::app()->createUrl('tab/menus');
}
$this->breadcrumbs[] = Menus::t('core', $hint);
?>

<div id="tasks">

	<h2><?php echo Menus::t('core', $hint); ?></h2>

	<p><?php echo CHtml::link(Menus::t('core', ($parent_id > 0 ? 'Create a new Menu'  :'Create a new Tab')), array('tab/create', 'type'=>Menu::TYPE_TAB, 'pid'=> $parent_id), array(
		'class'=>'add-task-link',
	)); ?></p>

	<?php $this->widget('zii.widgets.grid.CGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>'{items}',
	    'emptyText'=>Menus::t('core', 'No Top Menu found.'),
	    'htmlOptions'=>array('class'=>'grid-view task-table'),
	    'columns'=>array(
    		array(
    			'name'=>'name',
    			'header'=>Menus::t('core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
    			'value'=>'$data->getGridNameLink()',
    		),
    		array(
    			'name'=>'itemname',
    			'header'=>Menus::t('core', 'Permission'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
    		),
    		array(
    			'name'=>'url',
    			'header'=>Menus::t('core', 'URL'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
    		),
    		array(
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'actions-column'),
    			'value'=>'$data->getAddMenuLink()',
    		),
    		array(
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'actions-column'),
    			'value'=>'$data->getDeleteLink()',
    		),
	    )
	)); ?>

	<p class="info"><?php echo Menus::t('core', 'Values within square brackets tell how many children each item has.'); ?></p>

</div>