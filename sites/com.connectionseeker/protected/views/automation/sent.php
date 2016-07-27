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

$fttemplates = CHtml::listData(Template::model()->actived()->findAll(),'id','name');
natcasesort($fttemplates);
$ftfromes = CHtml::listData(Mailer::model()->actived()->findAll(),'id','user_alias');
?>

<h1>Sites just emailed</h1>

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
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		array(
			'name' => 'domain_id',
			'header' => 'Domain ID',
			//'filter' => false,
		),
		array(
			'name' => 'domain',
			'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->domain),"http://www.".$data->domain, array("target"=>"_blank"))',
		),
		'owner',
		'primary_email',
        array(
            'name' => 'template_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rtemplate->name), array("template/view", "id" =>$data->template_id))',
            'filter' => $fttemplates,
        ),
        array(
            'name' => 'mailer_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rmailer->user_alias), array("mailer/view", "id" =>$data->mailer_id))',
            'filter' => $ftfromes,
        ),
        array(
            'header' => 'ET #',
            'name' => 'client_discovery_id',
            'type' => 'raw',
            //'visible' => '($data->type_of_automation == "client_discovery_id")',
            'visible' => $_GET['type'] == "client_discovery_id",
        ),
        /*
        array(
            'header' => '($data->type_of_automation == "client_discovery_id") ? "ET #" : "GA #"',
            'name' => 'client_discovery_id',
            'type' => 'raw',
            'value' => '($data->type_of_automation=="client_discovery_id") ? $data->client_discovery_id : $data->automation_id',
        ),
        array(
            'header' => 'Type',
            'name' => 'type_of_automation',
            'type' => 'raw',
            'value' => '($data->type_of_automation == "client_discovery_id") ? "EmailTask" : "Global Automation"',
        ),
		'client_discovery_id',
		'type_of_automation',
        */
        array(
            'name' => 'rinventory.acquireddate',
            'type' => 'raw',
            'header' => 'Pending',
            'value' => 'CHtml::encode($data->rinventory->acquireddate)',
        ),
        array(
            'name' => 'rinventory.last_published',
            'type' => 'raw',
            'header' => 'Live',
            'value' => 'CHtml::encode($data->rinventory->last_published)',
        ),
        //'rinventory.last_published',
        'sent',
        'opened_time',
        'replied_time',
	),
)); ?>
