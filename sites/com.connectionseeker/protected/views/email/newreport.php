<?php
$this->breadcrumbs=array(
	'Mailers'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('mailer-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$_ajaxdata = "";
$_current_user = "";
if (isset($_REQUEST['user_id'])) {
    $_ajaxdata = ",user_id:'".$_REQUEST['user_id']."'";
    $_umodel = User::model()->findByPk($_REQUEST['user_id']);
    if ($_umodel) $_current_user = "For ".$_umodel->username."(User#".$_umodel->id.") ";
}

//In VIEW: $this->id And Yii::app()->controller->id can get the controller id, $this->getAction()->getId() can get the action id
//In Controller: $this->getId() can get the controller id, $action->id can get the action id
$currentaction = strtolower($this->getAction()->getId());
if ($currentaction == "mreport") {
    $sortby = "mailer";
    $dataProvider = $model->search();
} else if ($currentaction == "treport") {
    $sortby = "template";
    $dataProvider = $model->search();
} else {
    $sortby = "user";
    //$dataProvider = $model->with(array('rauthassignment'))->outreacher()->emrinmonth()->search();
    $dataProvider = $model->with(array('rauthassignment'))->outreacher()->actived()->search();
}
$ajaxdata = ",sortby:'$sortby'".$_ajaxdata;
?>
<h2>Emails Reporting <?php echo $_current_user."By ".ucfirst($sortby);?></h2>

<div id="innermenu">
    <?php $this->renderPartial('_rptmenu', array(
        '_current_user'=>$_current_user,
        'sortby'=>$sortby,
    )); ?>
</div>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'email-report-grid',
	//'dataProvider'=>$model->with('rauthassignment')->csemployee()->search(),//the same as below
	//'dataProvider'=>$model->with(array('rauthassignment'))->csemployee()->search(),
	'dataProvider'=>$dataProvider,
	'filter'=>$model,
	'columns'=>array(
		//'id',
        array(
            'name' => 'id',
            'filter' => empty($refids) ? CHtml::activeTextField($model, 'id') : false,
        ),
        array(
            'name' => 'username',
            'header' => 'Sent From',
            'type' => 'raw',
            //'value' => ($currentaction == "report") ? 'CHtml::link(CHtml::encode($data->username), array("user/view", "id" =>$data->id))' : 'CHtml::link(CHtml::encode($data->username), array("mailer/view", "id" =>$data->id))',
            'visible' => ($currentaction == "treport") ? false : true,
        ),
        array(
            'name' => 'display_name',
            'visible' => ($currentaction == "mreport") ? true : false,
        ),
        array(
            'name' => 'last_visit_time',
            'value' => '$data->last_visit_time ? date("M/d/Y g:i A", strtotime($data->last_visit_time)) : ""',
            'visible' => ($currentaction == "report") ? true : false,
        ),
        /*
        array(
            'name' => 'rauthassignment.itemname',
            'visible' => ($currentaction == "report") ? true : false,
        ),
        */

        array(
            'name' => 'name',
            'visible' => ($currentaction == "treport") ? true : false,
        ),
        array(
            'name' => 'subject',
            'visible' => ($currentaction == "treport") ? true : false,
        ),
        /*
        'username',
        array(
            'name' => 'username',
            'header' => 'Sent From',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->username), array("user/view", "id" =>$data->id))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
            'name' => 'created_by',
            'header' => 'Sent From',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->createdby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
            'name' => 'email_from',
            'header' => 'Outgoing Email Address',
            'type' => 'raw',
        ),
        */
        array(
            'name' => 'last24hours',
            'header' => 'Last 24 Hours',
            'type' => 'raw',
            //'value' => "</td><td></td><td>",
            'value' => 'CHtml::decode("0</td><td>0</td><td>0</td><td>0")',
            'filter' => 'CF</td><td>S</td><td>O</td><td>R',
        ),
        array(
            'name' => 'last7days',
            'header' => 'Last 7 Days',
            'type' => 'raw',
            //'value' => "</td><td></td><td>",
            'value' => 'CHtml::decode("0</td><td>0</td><td>0</td><td>0")',
            'filter' => 'CF</td><td>S</td><td>O</td><td>R',
        ),
        array(
            'name' => 'last30days',
            'header' => 'Last 30 Days',
            'type' => 'raw',
            'value' => 'CHtml::decode("0</td><td>0</td><td>0</td><td>0")',
            'filter' => 'CF</td><td>S</td><td>O</td><td>R',
        ),
        array(
            'name' => 'lifetime',
            'header' => 'Life Time',
            'type' => 'raw',
            'value' => 'CHtml::decode("0</td><td>0</td><td>0</td><td>0")',
            'filter' => 'CF</td><td>S</td><td>O</td><td>R',
        ),

        /*
        'last24hours',
        'last7days',
        'last30days',
        'lifetime',
        */

		array(
			'class'=>'CButtonColumn',
            //'template'=>'',
            'template'=>'{bymailer} {bytemplate}',

            'buttons' => array(
                'bymailer'=> array(
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/mailer.png',
                    'url' => 'Yii::app()->createUrl("email/mreport", array("user_id"=>$data->id))',
                    'label' => 'By Mailer',
                    'visible' => ($sortby=='user')? 'true' : 'false',
                ),

                'bytemplate'=> array(
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/template.png',
                    'url' => 'Yii::app()->createUrl("email/treport", array("user_id"=>$data->id))',
                    'label' => 'By Template',
                    'visible' => ($sortby=='user')? 'true' : 'false',
                ),
            ),

            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>

<script type="text/javascript">
$(document).ready(function() {
    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo Yii::app()->theme->baseUrl; ?>" + '/images/loading.gif)');
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        $("#email-report-grid table.items tr:first th:gt(2)").attr('colspan', '4');

        var _ids = [];
        $("#email-report-grid table.items tbody tr > td:first-child").each(function(i){
            //alert($(this).text());
            _ids[i] = $(this).text();
            $(this).parent().attr("id", "etr"+_ids[i]);//reset table.tr.id
        });

        var _firstid = _ids[0];
        if (_ids.length>0 && _firstid.indexOf("No") == -1){
        //###if (_ids.length>0){
            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                //'url': "<?php echo Yii::app()->createUrl('/email/mailerreport');?>",
                'url': "<?php echo Yii::app()->createUrl('/email/reportdetails');?>",
                'data': {'ids[]': _ids <?php echo $ajaxdata;?>},
                'success':function(data){
                    //alert(data.msg);
                    if (data.success){

                        if (data.report){
                            var _tzs = ["24hours","7days","30days","lifetime"];
                            $.each(data.report, function (v, o){
                                /*
                                $.each(_tzs, function(kt, vt){
                                    //alert(vt);
                                    var _curr = "last" + vt;
                                    if (o.open._curr){
                                        $("#etr" +v+" > td:eq(4)").text(o.open._curr);
                                    }
                                });
                                */
                                if (typeof o.ctform == "object") {
                                    $.each(o.ctform, function(ko, vo){
                                        if (vo > 0) vo = "<font color='red'>"+vo+"</font>";

                                        if (ko == "last24hours"){
                                            $("#etr" +v+" > td:eq(3)").html(vo);
                                        } else if (ko == "last7days") {
                                            $("#etr" +v+" > td:eq(7)").html(vo);
                                        } else if (ko == "last30days") {
                                            $("#etr" +v+" > td:eq(11)").html(vo);
                                        } else if (ko == "lastlifetime") {
                                            $("#etr" +v+" > td:eq(15)").html(vo);
                                        }
                                    });
                                }

                                //if (o.open.length > 0) {
                                if (typeof o.open == "object") {
                                    $.each(o.open, function(ko, vo){
                                        //alert(ko);
                                        //alert(vo);
                                        if (vo > 0) vo = "<font color='red'>"+vo+"</font>";
                                        if (ko == "last24hours"){
                                            $("#etr" +v+" > td:eq(5)").html(vo);
                                        } else if (ko == "last7days") {
                                            $("#etr" +v+" > td:eq(9)").html(vo);
                                        } else if (ko == "last30days") {
                                            $("#etr" +v+" > td:eq(13)").html(vo);
                                        } else if (ko == "lastlifetime") {
                                            $("#etr" +v+" > td:eq(17)").html(vo);
                                        }
                                    });


                                }

                                if (typeof o.reply == "object") {
                                    $.each(o.reply, function(ko, vo){
                                        if (vo > 0) vo = "<font color='red'>"+vo+"</font>";

                                        if (ko == "last24hours"){
                                            $("#etr" +v+" > td:eq(6)").html(vo);
                                        } else if (ko == "last7days") {
                                            $("#etr" +v+" > td:eq(10)").html(vo);
                                        } else if (ko == "last30days") {
                                            $("#etr" +v+" > td:eq(14)").html(vo);
                                        } else if (ko == "lastlifetime") {
                                            $("#etr" +v+" > td:eq(18)").html(vo);
                                        }
                                    });
                                }

                                if (typeof o.sent == "object") {
                                    $.each(o.sent, function(ko, vo){
                                        if (vo > 0) vo = "<font color='red'>"+vo+"</font>";

                                        if (ko == "last24hours"){
                                            $("#etr" +v+" > td:eq(4)").html(vo);
                                        } else if (ko == "last7days") {
                                            $("#etr" +v+" > td:eq(8)").html(vo);
                                        } else if (ko == "last30days") {
                                            $("#etr" +v+" > td:eq(12)").html(vo);
                                        } else if (ko == "lastlifetime") {
                                            $("#etr" +v+" > td:eq(16)").html(vo);
                                        }
                                    });
                                }

                                /*
                                if (typeof o.sent == "object") {
                                    var _sent = o.sent;
                                    if (typeof _sent.last24hours == "string"){
                                        $("#etr" +v+" > td:eq(3)").text(_sent.last24hours);
                                    }
                                    if (typeof _sent.last7days == "string"){
                                        $("#etr" +v+" > td:eq(6)").text(_sent.last7days);
                                    }
                                    if (typeof _sent.last30days == "string"){
                                        $("#etr" +v+" > td:eq(9)").text(_sent.last30days);
                                    }
                                    if (typeof _sent.lastlifetime == "string"){
                                        $("#etr" +v+" > td:eq(12)").text(_sent.lastlifetime);
                                        //$("#etr" +v+" > td:eq(12)").text(5);
                                    }
                                }

                                if (o.open.lastlifetime){
                                    $("#etr" +v+" > td:eq(3)").text("src", "");
                                }
                                alert(v);
                                alert(o);
                                $("#etr" +v+" > td:eq(3)").attr("src", "");
                                alert(o.open.last24hours);
                                */
                            });
                        }
                    }
                },
                'complete':function(XHR,TS){XHR = null;}
            });
        }
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
    //alert(_ids.length);
});
</script>