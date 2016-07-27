<?php
$this->breadcrumbs=array(
	'Automations'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('automation-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1>Sites about to be emailed</h1>

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
	'id'=>'automation-grid',
	'dataProvider'=>($musthasowner==1) ? $model->hasemail()->hasowner()->search() : $model->hasemail()->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		array(
			'name' => 'id',
			'header' => 'Domain ID',
			'filter' => false,
		),
		array(
			'name' => 'domain',
			'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->domain),"http://www.".$data->domain, array("target"=>"_blank"))',
		),
		'owner',
		'primary_email',
	),
)); ?>
