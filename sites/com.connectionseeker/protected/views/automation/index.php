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

$types = Types::model()->actived()->bytype(array("category"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$categories = $gtps['category'] ? $gtps['category'] : array();
$categoriestr = Utils::array2String($categories);

function printCategory($categories, $cats) {
    if ($cats) {
        $_tmps = explode("|", $cats);
        $c = "";
        foreach($_tmps as $k=>$v) {
            $_tmps[$k] = $categories[$v];
        }
        echo implode(", ", $_tmps);
    } else {
        echo "";
    }
}

$ftusers = CHtml::listData(User::model()->actived()->findAll(),'id','username');

$touchedstatus = Domain::$status;
$statusstr = Utils::array2String($touchedstatus);
$status = array("0"=>"No","1"=>"Yes");

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );
?>
<div id="innermenu">
    <?php $this->renderPartial('/automation/_menu',array('roles'=>$roles,)); ?>
</div>

<h2>Manage Automations</h2>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

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
		'id',
		'name',
        array(
            'name' => 'category',
            'type' => 'raw',
            'value' => 'printCategory('.$categoriestr.', $data->category)',
            'filter' => CHtml::activeDropDownList($model, 'category', $categories, array('multiple' =>'true','id'=>'Automation_category_filter','style'=>'width:160px')),
        ),
        array(
            'name' => 'touched_status',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $statusstr . ', $data->touched_status, true)',
        ),
		/*
		'category',
		'touched_status',
		'mailer',
		'current_domain_id',
		'total_sent',
		'total',
		'sortby',
		'created',
		'created_by',
		*/
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => $ftusers,
        ),
        array(
            'name' => 'status',
            'type' => 'raw',
            'header' => 'Active',
            'value' => '$data->status ? "Yes" : "No"',
            'filter' => $status,
        ),
		array(
			'class'=>'CButtonColumn',
            'template'=>'{update} {delete}',
		),
	),
)); ?>

<script type="text/javascript">
$(document).ready(function() {
    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        $("#Automation_category_filter").multiselect({noneSelectedText:'Categories',selectedList:3,minWidth:200}).multiselectfilter();
    }
    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});
</script>