<?php $this->breadcrumbs = array(
	'Menus'=>Menus::getBaseUrl(),
	Menus::t('core', 'Menus'),
); ?>

<div id="assignments">

	<h2><?php echo Menus::t('core', 'Menus'); ?></h2>

	<p>
		<?php echo Menus::t('core', 'Here you can view All menus'); ?>
	</p>
	<p><?php echo CHtml::link(Rights::t('core', 'Generate items for controller actions'), array('tab/generate'), array(
	   	'class'=>'generator-link',
	)); ?></p>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
	    'dataProvider'=>$dataProvider,
	    'template'=>"{items}\n{pager}",
	    'emptyText'=>Menus::t('core', 'No menu found.'),
	    'htmlOptions'=>array('class'=>'grid-view assignment-table'),
	    'columns'=>array(
    		array(
    			'name'=>'name',
    			'header'=>Menus::t('core', 'Name'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
    			'value'=>'$data->getNameLink()',
    		),
    		array(
    			'name'=>'itemname',
    			'header'=>Menus::t('core', 'Permission'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'name-column'),
    			//'value'=>'$data->getAssignmentNameLink()',
    		),
    		array(
    			'name'=>'url',
    			'header'=>Menus::t('core', 'URL'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'role-column'),
    		),
			array(
    			'name'=>'img',
    			'header'=>Menus::t('core', 'Img'),
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'task-column'),
    		),
    		array(
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'actions-column'),
    			'value'=>'$data->getTabLink()',
    		),
    		array(
    			'header'=>'&nbsp;',
    			'type'=>'raw',
    			'htmlOptions'=>array('class'=>'actions-column'),
    			'value'=>'$data->getDeleteLink()',
    		),
	    )
	)); ?>

</div>