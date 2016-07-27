<?php
$this->breadcrumbs=array(
	'Iohistories'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('iohistory-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);
?>

<h2>Manage IO Operation Histories</h2>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->


<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'iohistory-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'task_id',
        array(
            'name' => 'oldiostatus',
            'type' => 'raw',
            'value' => 'is_numeric($data->oldiostatus) ? Utils::getValue(' . $iostatusesstr . ', $data->oldiostatus) : ""',
            'filter' => $iostatuses,
        ),
        array(
            'name' => 'iostatus',
            'type' => 'raw',
            'value' => 'is_numeric($data->iostatus) ? Utils::getValue(' . $iostatusesstr . ', $data->iostatus) : ""',
            'filter' => $iostatuses,
        ),
		'timeline',
		/*
		'oldiostatus',
		'iostatus',
        'created_by',
		'timeline',
        */
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
		'role',
		'created',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
