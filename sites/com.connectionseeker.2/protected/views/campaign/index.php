<?php
$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('campaign-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
    $visible = false;
} else {
    $clients = Client::model()->actived()->findAll();
    $visible = true;
}

$isadmin = false;
if (isset($roles['Admin'])) {
    $isadmin = true;
}
?>

<h1>Manage Campaigns</h1>

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
	'id'=>'campaign-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'name',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->name), array("task/processing", "campaign_id" =>$data->id))',
        ),
		'domain',
        array(
            'name' => 'client_id',
            //'name' => 'rclient.company',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'filter' => CHtml::listData($clients,'id','company'),
            'visible' => $visible,
        ),

		'category_str',
		'rcampaigntask.total_count',
		'rcampaigntask.published_count',
		//'rcampaigntask.percentage_done',
        array(
            'name' => 'rcampaigntask.percentage_done',
            'type' => 'raw',
            'value' => '($data->rcampaigntask->percentage_done)* 100 ."%"',
        ),
        array(
            'name' => 'duedate',
            'type' => 'raw',
            'value' => '$data->duedate ? date("M/d/Y",$data->duedate) : ""',
        ),
		/*
        'domain_id',
		'name',
		'category',
		'notes',
		'status',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'rcampaigntask.total_count',
		'rcampaigntask.published_count',
		'rcampaigntask.percentage_done',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'{download} {view} {update} {delete}',
            'buttons' => array(
                'download' => array(
                    'label' => 'Download Tasks',
                    //'visible' => '',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/download.png',
                    'url' => 'Yii::app()->createUrl("download/task", array("Task[campaign_id]"=>$data->id))',
                    'options' => array(
                        'class'=>'download',
                    ),
                ),
                'view' => array(
                    'visible' => "$isadmin",
                ),
                'update' => array(
                    'visible' => "$isadmin",
                ),
                'delete' => array(
                    'visible' => "$isadmin",
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>
