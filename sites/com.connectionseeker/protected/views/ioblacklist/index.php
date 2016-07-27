<?php
$this->breadcrumbs=array(
	'Ioblacklists'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('ioblacklist-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$yesorno = array("1"=>"Yes","0"=>"No");
?>

<div id="innermenu">
    <?php $this->renderPartial('/ioblacklist/_menu'); ?>
</div>

<h2>Manage IO Blacklists</h2>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'yesorno'=>$yesorno,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'ioblacklist-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'domain_id',
		'domain',
        array(
            'name' => 'isallclient',
            'type' => 'raw',
            'filter' => $yesorno,
        ),
		'clients_str',
        array(
            'name' => 'isblacklist',
            'type' => 'raw',
            'filter' => $yesorno,
        ),
		'notes',
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->actived()->findAll(),'id','username'),
        ),
		/*
		'id',
		'clients',
		'isblacklist',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
