<?php
$this->breadcrumbs=array(
	'Templates'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('template-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Templates</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>


<!-- <?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div> -->

<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'template-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'subject',
        //'content',
        array(
            'name' => 'content',
            'type' => 'html',
            'value' => '$data->content',
        ),
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->createdby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
            'name' => 'modified',
            'filter' => ''
        ),
		/*
		'notes',
        'created',
		'created_by',
		
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>

<style type="text/css">
.grid-view table.items tr.even td { background-image: none;}
</style>