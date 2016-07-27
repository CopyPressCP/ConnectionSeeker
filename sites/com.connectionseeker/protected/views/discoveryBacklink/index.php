<?php
$this->breadcrumbs=array(
	'Discovery Backlinks'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('discovery-backlink-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Discovery Backlinks</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'discovery-backlink-grid',
	//##'dataProvider'=>$model->with(array("rdiscovery","rdcvdomain"))->competitors()->search(),
	'dataProvider'=>$model->available()->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'rdiscovery.domain',
		'rdcvdomain.domain',
		'url',
		'domain',
		'anchortext',
		'targeturl',
		'acrank',
		'date',
		'googlepr',
		'flagredirect',
		'flagframe',
		'flagnofollow',
		'flagimages',
		'flagdeleted',
		'flagalttext',
		'flagmention',
		/*
		'domain_id',
		'competitor_id',
		'discovery_id',
		'fresh_called',
		'historic_called',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'',
		),
	),
)); ?>
