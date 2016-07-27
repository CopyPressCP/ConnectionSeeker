<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Users</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php
/*
$this->renderPartial('_search',array(
	'model'=>$model,
));
*/
Yii::import('system.web.helpers.CHtml', true);

?>
<!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
		'id',
		'username',
		'email',
		'last_visit_time',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->createdby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
		//'createdby.username',
		/*
		'modified',
		'modified_by',
		'last_visit_time',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
