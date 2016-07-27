<?php
$this->breadcrumbs=array(
	'Trails'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('trail-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Trails</h1>

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
	'id'=>'trail-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
			'name' => 'old_value',
			'type' => 'raw',
            'value' => 'Utils::array2String(unserialize($data->old_value), ",<br />");',
		),
		array(
			'name' => 'new_value',
			'type' => 'raw',
            'value' => 'Utils::array2String(unserialize($data->new_value), ",<br />");',
		),
		array(
			'name' => 'description',
			'type' => 'raw',
		),
		'operation',
		'model',
		'action',
		/*
		'description',
		'old_value',
		'new_value',
		'field',
		'user_id',
		'model_id',
		*/
		'created',
        array(
            'name' => 'user_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->user_id))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
