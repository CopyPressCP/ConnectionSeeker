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
?>

<h2>Emails Reporting</h2>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'email-report-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'created_by',
            'header' => 'Sent From',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcreatedby->username), array("user/view", "id" =>$data->created_by))',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
            'name' => 'email_from',
            'header' => 'Outgoing Email Address',
            'type' => 'raw',
        ),
        array(
            'name' => 'last24hours',
            'header' => 'Last 24 Hours',
            'type' => 'raw',
            //'value' => "</td><td></td><td>",
            'value' => 'CHtml::decode("0</td><td>0</td><td>0")',
            'filter' => 'S</td><td>O</td><td>R',
        ),
        array(
            'name' => 'last7days',
            'header' => 'Last 7 Days',
            'type' => 'raw',
            //'value' => "</td><td></td><td>",
            'value' => 'CHtml::decode("0</td><td>0</td><td>0")',
            'filter' => 'S</td><td>O</td><td>R',
        ),
        array(
            'name' => 'last30days',
            'header' => 'Last 30 Days',
            'type' => 'raw',
            'value' => 'CHtml::decode("0</td><td>0</td><td>0")',
            'filter' => 'S</td><td>O</td><td>R',
        ),
        array(
            'name' => 'lifetime',
            'header' => 'Life Time',
            'type' => 'raw',
            'value' => 'CHtml::decode("0</td><td>0</td><td>0")',
            'filter' => 'S</td><td>O</td><td>R',
        ),

        /*
        'last24hours',
        'last7days',
        'last30days',
        'lifetime',
        */

		array(
			'class'=>'CButtonColumn',
            'template'=>'',
		),
	),
)); ?>

<script type="text/javascript">
$(document).ready(function() {
    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        $("#email-report-grid table.items tr:first th:gt(2)").attr('colspan', '3');

        var _ids = [];
        $("#email-report-grid table.items tbody tr > td:first-child").each(function(i){
            //alert($(this).text());
            _ids[i] = $(this).text();
            $(this).parent().attr("id", "etr"+_ids[i]);//reset table.tr.id
        });

        if (_ids.length>0){
            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': "<?php echo Yii::app()->createUrl('/email/mailerreport');?>",
                'data': {'ids[]': _ids},
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
                                //if (o.open.length > 0) {
                                if (typeof o.open == "object") {
                                    $.each(o.open, function(ko, vo){
                                        //alert(ko);
                                        //alert(vo);
                                        if (vo > 0) vo = "<font color='red'>"+vo+"</font>";
                                        if (ko == "last24hours"){
                                            //$("#etr" +v+" > td:eq(4)").text(vo);
                                            $("#etr" +v+" > td:eq(4)").html(vo);
                                        } else if (ko == "last7days") {
                                            $("#etr" +v+" > td:eq(7)").html(vo);
                                        } else if (ko == "last30days") {
                                            $("#etr" +v+" > td:eq(10)").html(vo);
                                        } else if (ko == "lastlifetime") {
                                            $("#etr" +v+" > td:eq(13)").html(vo);
                                        }
                                    });


                                }

                                if (typeof o.reply == "object") {
                                    $.each(o.reply, function(ko, vo){
                                        if (vo > 0) vo = "<font color='red'>"+vo+"</font>";

                                        if (ko == "last24hours"){
                                            $("#etr" +v+" > td:eq(5)").html(vo);
                                        } else if (ko == "last7days") {
                                            $("#etr" +v+" > td:eq(8)").html(vo);
                                        } else if (ko == "last30days") {
                                            $("#etr" +v+" > td:eq(11)").html(vo);
                                        } else if (ko == "lastlifetime") {
                                            $("#etr" +v+" > td:eq(14)").html(vo);
                                        }
                                    });
                                }

                                if (typeof o.sent == "object") {
                                    $.each(o.sent, function(ko, vo){
                                        if (vo > 0) vo = "<font color='red'>"+vo+"</font>";

                                        if (ko == "last24hours"){
                                            $("#etr" +v+" > td:eq(3)").html(vo);
                                        } else if (ko == "last7days") {
                                            $("#etr" +v+" > td:eq(6)").html(vo);
                                        } else if (ko == "last30days") {
                                            $("#etr" +v+" > td:eq(9)").html(vo);
                                        } else if (ko == "lastlifetime") {
                                            $("#etr" +v+" > td:eq(12)").html(vo);
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