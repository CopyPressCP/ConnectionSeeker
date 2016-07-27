<?php
$this->breadcrumbs=array(
	'Backlinks'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('backlink-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Backlinks</h1>

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
	'id'=>'backlink-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'domain',
		'googlepr',
		'acrank',
		'anchortext',
        array(
            'name' => 'api_called',
            'type' => 'raw',
            'value' => 'date("Y-m-d", ($data->fresh_called) ? $data->fresh_called : $data->historic_called)',
        ),
		'url',
		'targeturl',
		/*
        'fresh_called'
        'historic_called'
		'competitor_id',
		'domain_id',
		'date',
		'flagredirect',
		'flagframe',
		'flagnofollow',
		'flagimages',
		'flagdeleted',
		'flagalttext',
		'flagmention',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
