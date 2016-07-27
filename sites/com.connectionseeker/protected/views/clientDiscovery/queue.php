<?php
$this->breadcrumbs=array(
	'Client Discoveries'=>array('index'),
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

//frequency * 60;
//latest_senttime  datatime;
//time_start  datatime;
//time_end  datatime;
//Yii::app()->user->getState('pageSize');  datatime;
//you can use the $row variable for the value property as $i
//http://www.yiiframework.com/forum/index.php/topic/7912-show-number-list-and-set-value-in-cgridview/
//'value'=>'$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1',       // 
//'value'=>'$this->grid->dataProvider->pagination->offset + $row+1'

/*
echo Yii::app()->user->getState('currentPage');

$nowtimestamp = time();
$hour = date("H:i", $nowtimestamp);
$hourstamp = strtotime($hour.":00");
$week = date("w", $nowtimestamp);
$now = date("Y-m-d H:i:s", $nowtimestamp);
$lastsendtime = $nowtimestamp;
if ($automodel) {
    if ($automodel->latest_senttime) $lastsendtime = strtotime($automodel->latest_senttime);
}
*/

function getEmailAddr($e1=null, $e2=null, $e3=null) {
    if (!empty($e1)) {
        echo $e1;
    } elseif(!empty($e2)) {
        echo $e2;
    } elseif(!empty($e3)) {
        echo $e3;
    } else {
        echo "";
    }
}
?>
<div id="innermenu">
    <?php $this->renderPartial('/clientDiscovery/_menu'); ?>
</div>

<h2>Sites about to be emailed</h2>

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
	'dataProvider'=>$model->sendable()->search(),
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
		'rdomain.owner',
		array(
			'name' => 'rdomain.primary_email',
			'header' => 'Email',
            //##'value' => '$data->rdomain->primary_email ? $data->rdomain->primary_email : ($data->rdomain->primary_email2 ? $data->rdomain->primary_email2 : ($data->ronpage->contactemail ? $data->ronpage->contactemail : ""))',
            'value' => 'getEmailAddr($data->rdomain->primary_email, $data->rdomain->primary_email2, $data->rdomain->ronpage->contactemail)',
			'filter' => false,
		),
        /*
		'rdomain.primary_email',
		array(
			'name' => 'id',
			'header' => 'Estimated Send',
            'value' => estimatedTime($i),
			'filter' => false,
		),
        */
	),
)); ?>
