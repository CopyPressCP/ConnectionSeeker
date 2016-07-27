<?php
$this->breadcrumbs=array(
	'Domains'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'Outreach', 'url'=>array('domain/index&touched=true')),
	array('label'=>'Create Domain', 'url'=>array('create')),
	array('label'=>'Update Domain', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Domain', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Domain', 'url'=>array('index')),
	array('label'=>'Manage Email', 'url'=>array('email/index')),
	array('label'=>'Audit', 'url'=>array('domain/audit')),
);

//echo $model->stype;
$stype = Types::model()->bytype('site')->find('refid=:refid', array(':refid'=>$model->stype));
$otype = Types::model()->bytype('outreach')->find('refid=:refid', array(':refid'=>$model->otype));
$model->stype = $stype->typename;
$model->otype = $otype->typename;
?>

<h1>View Domain #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'domain',
		'tld',
		'googlepr',
		'onlinesince',
		'linkingdomains',
		'inboundlinks',
		'indexedurls',
		'alexarank',
		'ip',
		'subnet',
		'title',
		'owner',
		'email',
		'telephone',
		'country',
		'state',
		'city',
		'zip',
		'street',
		'stype',
		'otype',
		'touched',
		'touched_status',
		'touched_by',
		'created',
		'created_by',
		'modified',
		'modified_by',
	),
)); ?>
