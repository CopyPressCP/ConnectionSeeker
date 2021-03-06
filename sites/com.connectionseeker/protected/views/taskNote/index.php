<?php
$this->breadcrumbs=array(
	'Task Notes'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('task-note-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$ftusers = CHtml::listData(User::model()->findAll(),'id','username');
?>

<h1>Manage Task Notes</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none;padding:0px;margin:0px;">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-note-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'task_id',
		//'rtask.anchortext',
		'notes',
		'created',
		//'created_by',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => $ftusers,
        ),
		array(
			'class'=>'CButtonColumn',
            'template'=>'',
		),
	),
)); ?>
