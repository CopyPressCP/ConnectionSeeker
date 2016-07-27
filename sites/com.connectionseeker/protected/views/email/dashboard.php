<?php
$events = array('processed','deferred','delivered','open','click','bounce','dropped','spamreport','unsubscribe');
$events = array_combine($events, $events);

$fttemplates = CHtml::listData(Template::model()->findAll(),'id','name');
$ftfromes = CHtml::listData(Mailer::model()->findAllByAttributes(array('created_by'=>$cuid)),'id','user_alias');
//##$ftusers = CHtml::listData(User::model()->findAll(),'id','username');
?>

<h1>Dashboard</h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'email-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'domain_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rdomain->domain), array("domain/view", "id" =>$data->domain_id))',
        ),
        array(
            'name' => 'from',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rmailer->user_alias), array("mailer/view", "id" =>$data->from))',
            'filter' => $ftfromes,
        ),
		'to',
        array(
            'name' => 'template_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rtemplate->name), array("template/view", "id" =>$data->template_id))',
            'filter' => $fttemplates,
        ),
		'send_time',
        array(
            'name' => 'opened',
            'type' => 'raw',
            'value' => 'empty($data->opened) ? "No" : "Yes"',
        ),
        array(
            'name' => 'is_reply',
            'type' => 'raw',
            'value' => 'empty($data->is_reply) ? "No" : "Yes"',
            'filter' => false,
        ),
		/*
		'is_reply',
		'opened',
		'created',
        array(
            'name' => 'created_by',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcreatedby->username)',
            'filter' => $ftusers,
        ),

        'reventone.event',
        array(
            'name' => 'revent.event',
            'type' => 'raw',
            'value' => 'Email::getEvents($data->revent)',
        ),
		'domain_id',
		'template_id',
		'from',
		'cc',
		'subject',
		'content',
		'status',
		'send_time',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/
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
        var _ids = [];
        $("#email-grid table.items tbody tr > td:first-child").each(function(i){
            _ids[i] = $(this).text();
            $(this).parent().attr("id", "etr"+_ids[i]);//reset table.tr.id
        });

        var _firstid = _ids[0];
        if (_ids.length>0 && _firstid.indexOf("No") == -1){
            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': "<?php echo Yii::app()->createUrl('/email/isreplied');?>",
                'data': {'ids[]': _ids},
                'success':function(data){
                    //alert(data.msg);
                    if (data.success){
                        if (data.report){
                            $.each(data.report, function (v, o){
                                $("#etr" +v+" > td:eq(7)").html("<font color='red'>Yes</font>");
                            });
                        }
                    }
                },
                'complete':function(XHR,TS){XHR = null;}
            });
        }
    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});
</script>