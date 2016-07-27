<?php
$this->breadcrumbs=array(
	'Types'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('types-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$types = array(
    'site'     => 'Site Type',
    'outreach' => 'Outreach Type',
    'linktask' => 'Link Task Type',
    'category' => 'Content Categories',
    'channel' => 'Channel',
);

$ftstatus = array('0'=>'Inactvie', '1'=>'Actvie');

?>

<h1>Manage Types</h1>

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
?>
<!-- search-form -->
<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'types-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		/*
        'type',
		'status',
        */
        array(
            'name' => 'type',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->type)',
            'filter' => $types,
        ),
		'refid',
		'typename',
        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => '($data->status)? "Yes":"No"',
            'filter' => $ftstatus,
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
