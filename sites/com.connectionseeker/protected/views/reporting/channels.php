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

$types = Types::model()->bytype(array("channel"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$channels = $gtps['channel'];
natcasesort($channels);
$channelstr = Utils::array2String($channels);

$i = 0;
$chlvisible = true;
if ($_REQUEST["groupby"] == "campaign") {
    $chlvisible = false;
}
?>

<h1>User Reporting<?php //echo $cmpmodel->rcampaign->name; ?></h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->unhidden()->report(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
        array(
            'name' => 'campaign_id',
            'header' => 'Campaign ID',
            'type' => 'raw',
            'value' => '$data->campaign_id',
            'visible' => !$chlvisible,
            'footer' => 'Total',
        ),
        array(
            'name' => 'campaign_name',
            'header' => 'Campaign Name',
            'type' => 'raw',
            //'value' => '$data->campaign_name',
            'value' => 'CHtml::link(CHtml::encode($data->campaign_name), array("task/processing", "campaign_id" =>$data->campaign_id))',
            'visible' => !$chlvisible,
        ),
        array(
            'name' => 'channel_id',
            'header' => 'Channel ID',
            'type' => 'raw',
            'value' => '$data->channel_id',
            'footer' => 'Total',
            'visible' => $chlvisible,
        ),
        array(
            //'name' => 'channel_name',
            'name' => 'channel_id',
            'header' => 'Channel Name',
            'type' => 'raw',
            //'value' => '',
            'value' => 'CHtml::link(CHtml::encode(empty($data->channel_id) ? "No Channel" : Utils::getValue(' . $channelstr . ', $data->channel_id, true)), array("reporting/channels", "Task[channel_id]" =>$data->channel_id,"groupby"=>"campaign"))',
            'filter' => $channels,
            'visible' => $chlvisible,
        ),
        array(
            'name' => 'total_count',
            'header' => 'Ordered',
            'type' => 'raw',
            'value' => '$data->total_count',
            'footer' => $others["total_count"],
            'filter' => false,
        ),
        array(
            'name' => 'init_count',
            'header' => 'Initial',
            'type' => 'raw',
            'value' => '$data->init_count',
            'footer' => $others["init_count"],
            'filter' => false,
        ),
        array(
            'name' => 'current_count',
            'header' => 'Current',
            'type' => 'raw',
            'value' => '$data->current_count',
            'footer' => $others["current_count"],
            'filter' => false,
       ),
        array(
            'name' => 'accepted_count',
            'header' => 'Accepted',
            'type' => 'raw',
            'value' => '$data->accepted_count',
            'footer' => $others["accepted_count"],
            'filter' => false,
        ),
        array(
            'name' => 'pending_count',
            'header' => 'Pending',
            'type' => 'raw',
            'value' => '$data->pending_count',
            'footer' => $others["pending_count"],
            'filter' => false,
        ),
        array(
            'name' => 'approved_count',
            'header' => 'Approved',
            'type' => 'raw',
            'value' => '$data->approved_count',
            'footer' => $others["approved_count"],
            'filter' => false,
        ),
        array(
            'name' => 'denied_count',
            'header' => 'Denied',
            'type' => 'raw',
            'value' => '$data->denied_count',
            'footer' => $others["denied_count"],
            'filter' => false,
        ),
        /*
        array(
            'name' => 'completed_count',
            'header' => 'Completed',
            'type' => 'raw',
            'value' => '$data->completed_count',
            'footer' => $others["completed_count"],
            'filter' => false,
        ),
        */
        array(
            'header' => 'Pre QA',
            'name' => 'qa_count',
            'type' => 'raw',
            'value' => '$data->qa_count',
            'footer' => $others["qa_count"],
            'filter' => false,
        ),
        array(
            'header' => 'In Repair',
            'name' => 'inrepair_count',
            'type' => 'raw',
            'value' => '$data->inrepair_count',
            'footer' => $others["inrepair_count"],
            'filter' => false,
        ),
        array(
            'header' => 'Post QA',
            'name' => 'published_count',
            'type' => 'raw',
            'value' => '$data->published_count',
            'footer' => $others["published_count"],
            'filter' => false,
        ),
        array(
            'header' => 'Remaining',
            'name' => 'remaining_count',
            'type' => 'raw',
            'value' => '$data->remaining_count',
            'footer' => $others["remaining_count"],
            'filter' => false,
        ),
	),
)); ?>
