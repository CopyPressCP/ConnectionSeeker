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

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'campaign-grid',
	//###'dataProvider'=>$model->unhidden()->search(),
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
        array(
            'name' => 'id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->id), array("task/processing", "campaign_id" =>$data->id))',
            'footer' => 'Total',
        ),
        array(
            'name' => 'client_id',
            //'name' => 'rclient.company',
            'type' => 'raw',
            //'value' => 'CHtml::link(CHtml::encode($data->rclient->company), array("client/view", "id" =>$data->client_id))',
            'value' => 'CHtml::encode($data->rclient->company)',
            'filter' => CHtml::listData($clients,'id','company'),
            'visible' => $visible,
        ),
        array(
            'name' => 'name',
            'type' => 'raw',
            'value' => isset($roles['Marketer']) ? 'CHtml::encode($data->name)' : 'CHtml::link(CHtml::encode($data->name), array("reporting/campaignDetail", "id" =>$data->id))',
        ),
        array(
            'header' => 'Ordered',
            'name' => 'rcampaigntask.total_count',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->total_count',
            'footer' => $model->summary_total,
        ),
        array(
            'header' => 'Pending',
            'name' => 'rcampaigntask.pending_count',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->total_count',
            'footer' => $model->summary_pending,
        ),
        array(
            'header' => 'Approved',
            'name' => 'rcampaigntask.approved_count',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->approved_count',
            'footer' => $model->summary_approved,
        ),
        array(
            'header' => 'Pre QA',
            'name' => 'rcampaigntask.qa_count',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->qa_count',
            'footer' => $model->summary_qa,
        ),
        array(
            'header' => 'In Repair',
            'name' => 'rcampaigntask.inrepair_count',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->qa_count',
            'footer' => $model->summary_inrepair,
        ),
        array(
            'header' => 'Post QA',
            'name' => 'rcampaigntask.published_count',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->published_count',
            'footer' => $model->summary_published,
        ),
        array(
            'header' => 'Remaining',
            'name' => 'rcampaigntask.remaining_count',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->remaining_count',
            'footer' => $model->summary_remaining,
        ),
        array(
            'header' => 'Percentage Done - A',
            //'name' => 'rcampaigntask.internal_done',
            'name' => 'rct_internal_done',
            'type' => 'raw',
            //'value' => '$data->rcampaigntask->total_count ? round((($data->rcampaigntask->approved_count + $data->rcampaigntask->qa_count + $data->rcampaigntask->published_count)/$data->rcampaigntask->total_count), 4)* 100 ."%" : ""',
            'value' => '($data->rcampaigntask->internal_done * 100)."%"',
            'filter' => array("0"=>"Not Finished","1"=>"Finished"),
            'footer' => $model->summary_total ? (round((($model->summary_approved + $model->summary_qa + $model->summary_published + $model->summary_inrepair)/$model->summary_total), 4) * 100) ."%"  : "" ,
        ),
        array(
            //'name' => 'rcampaigntask.percentage_done',
            'name' => 'rct_percentage_done',
            'header' => 'Percentage Done - C',
            'type' => 'raw',
            'value' => '($data->rcampaigntask->percentage_done)* 100 ."%"',
            'filter' => array("0"=>"Not Finished","1"=>"Finished"),
        ),
        array(
            'name' => 'duedate',
            'type' => 'raw',
            'value' => '$data->duedate ? date("M/d/Y",strtotime($data->duedate)) : ""',
            'filter' => false,
        ),
		/*
        'domain_id',
		'domain',
		'category_str',
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
        /*
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
        */
		array(
			'class'=>'CButtonColumn',
            'template'=>'',
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>
