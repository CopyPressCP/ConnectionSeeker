<?php
/*
$this->breadcrumbs=array(
	'Campaigns'=>array('index'),
	'Manage',
);
*/
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

?>

<h1>Domain Metrics Latest Crawl Date</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'crawler-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
        'domain_id',
		'domain',
        array(
            'name' => 'sonlinesince',
            'value' => '$data->sonlinesince ? date("Y-m-d H:i:s", $data->sonlinesince): ""',
            'filter' => false,
        ),
        array(
            'name' => 'sgooglepr',
            'value' => '$data->sgooglepr ? date("Y-m-d H:i:s", $data->sgooglepr): ""',
            'filter' => false,
        ),
        array(
            'name' => 'salexarank',
            'value' => '$data->salexarank ? date("Y-m-d H:i:s", $data->salexarank): ""',
            'filter' => false,
        ),
        array(
            'name' => 'smozrank',
            'value' => '$data->smozrank ? date("Y-m-d H:i:s", $data->smozrank): ""',
            'filter' => false,
        ),
        array(
            'name' => 'ssemrushkeywords',
            'value' => '$data->ssemrushkeywords ? date("Y-m-d H:i:s", $data->ssemrushkeywords): ""',
            'filter' => false,
        ),
        array(
            'name' => 'sip',
            'value' => '$data->sip ? date("Y-m-d H:i:s", $data->sip): ""',
            'filter' => false,
        ),
        array(
            'name' => 'sspa',
            'value' => '$data->sspa ? date("Y-m-d H:i:s", $data->sspa): ""',
            'filter' => false,
        ),
        /*
		'sonlinesince',
		'sgooglepr',
		'salexarank',
		'smozrank',
		'sacrank',
		'sfacebookshares',
		'sip',
		'ssemrushor',
		'ssemrushkeywords',
		'sspa',
        */
	),
)); ?>
