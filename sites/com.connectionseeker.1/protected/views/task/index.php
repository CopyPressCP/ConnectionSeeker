<?php
$this->breadcrumbs=array(
	'Tasks'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('task-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$types = Types::model()->bytype("linktask")->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$linktask = $gtps['linktask'];
$tasktypestr = Utils::array2String($linktask);

//print_r($linktask);

function showOpt($sp){
    $rtn = "";
    if ($sp) {
        $opts = unserialize($sp);
        if ($opts) {
            //$rtn = "<dl>";
            foreach ($opts as $k => $v) {
                $rtn .= "<div>$v</div>";
            }
            //$rtn .= "</dl>";
        }

    }

    return $rtn;
}
?>

<h1>Manage Tasks</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'id'=>'ids',
            //'class'=>'CCheckBoxColumn',
            //'checked'=>'($data->tasktype == 1 && $data->rcontent->length)',
            //'value'=>'($data->tasktype == 1 && $data->rcontent->length) ? $data->id : ""',
            'class'=>'application.extensions.lkgrid.LinkmeCheckBoxColumn',
            'displayRow'=>'($data->tasktype == 1 && ($data->rcontent->length || !$data->content_article_id))',
            'expressCBHtmlOptions'=>array(
                //'usage'=>$this->evaluateExpression('$data->content_article_id ? "download" : "send"',array('data'=>$data)),
                'usage'=>'$data->content_article_id ? "download" : "send"',
            ),
        ),
        //'id',
		'content_article_id',
        array(
            'name' => 'campaign_id',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)',
        ),
		'domain',
		'anchortext',
		'targeturl',
        array(
            'name' => 'tasktype',
            'type' => 'raw',
            'value' => 'CHtml::encode(Utils::getValue(' . $tasktypestr . ', $data->tasktype))',
            'filter' => $linktask,
        ),
        array(
            'name' => 'optional_keywords',
            'type' => 'raw',
            'value' => 'showOpt($data->optional_keywords)',
        ),
		'taskstatus',
        array(
            'name' => 'assignee',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rassignee->username)',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        
        array(
            'name' => 'duedate',
            'type' => 'raw',
            'value' => '$data->duedate ? date("M/d/Y",$data->duedate) : ""',
        ),
		/*
		'campaign_id',
		'tasktype',
		'duedate',
		'optional_keywords',
		'domain_id',
		'inventory_id',
		'sourceurl',
		'sourcedomain',
		'title',
		'taskstatus',
		'mapping_id',
		'notes',
		'content_campaign_id',
		'content_category_id',
		'send2cpdate',
		'checkouted',
		'created',
		'created_by',
		'modified',
		'modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'{view} {send2cp} {dlhtml} {dltxt} {update} {delete}',
            'buttons' => array(
                'send2cp' => array(
                    'label' => 'Send to Copypress',
                    'visible' => '($data->tasktype == 1 && !$data->content_article_id)',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/send2cp.png',
                    'url' => 'Yii::app()->createUrl("task/send", array("id"=>$data->id))',
                    'click' => "function(){
                        //alert($(this).parent().parent().children(':nth-child(1)').children().val());
                        sendtask2cp('send', $(this).parent().parent().children(':nth-child(1)').children().val());
                        return false;
                    }",
                    'options' => array(
                        'class'=>'send2cp',
                    ),
                ),
                'dlhtml' => array(
                    'label' => 'Download article as html format',
                    'visible' => '($data->tasktype == 1 && $data->rcontent->length)',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/dlhtml.png',
                    'url' => 'Yii::app()->createUrl("task/download", array("id"=>$data->id))',
                    'options' => array(
                        'class'=>'dlhtml',
                    ),
                ),
                'dltxt' => array(
                    'label' => 'Download article as txt format',
                    'visible' => '($data->tasktype == 1 && $data->rcontent->length)',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/dltxt.png',
                    'url' => 'Yii::app()->createUrl("task/download", array("id"=>$data->id))',
                    'options' => array(
                        'class'=>'dltxt',
                    ),
                ),
                'view' => array(
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/viewdetail.png',
                ),
                'update' => array(
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/edit.png',
                ),
                'delete' => array(
                    'visible' => '!($data->rcontent->length)',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/del.png',
                    'click'=>"function() {
	if(!confirm('Are you sure you want to delete this item?')) return false;
	var th=this;
	var afterDelete=function(){};
	$.fn.yiiGridView.update('task-grid', {
		type:'POST',
		url:$(this).attr('href'),
		success:function(data) {
            if (data) {alert(data);}
			$.fn.yiiGridView.update('task-grid');
			afterDelete(th,true,data);
		},
		error:function(XHR) {
			return afterDelete(th,false,XHR);
		}
	});
	return false;
}",
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>

<div class="clear"></div>
<div class="batchActionDiv">
    <img src="<?php echo Yii::app()->theme->baseUrl;?>/css/gridview/arrow_ltr.png">
    <a title="Send task to CopyPress" onclick="sendtask2cp('send2cp', 0);" href="#">
    <img alt="Send task to CopyPress" src="<?php echo Yii::app()->theme->baseUrl;?>/css/gridview/send2cp.png"></a>
    <a title="Download Checked Articles As HTML Format" onclick="dlarticles('checked', 'html');" href="#">
    <img alt="Download Checked Articles As HTML Format" src="<?php echo Yii::app()->theme->baseUrl;?>/css/gridview/dlhtml.png"></a>
    <a title="Download Checked Articles AS Text Format" onclick="dlarticles('checked', 'text');" href="#">
    <img alt="Download Checked Articles AS Text Format" src="<?php echo Yii::app()->theme->baseUrl;?>/css/gridview/dltxt.png"></a>
    <span class="batchActionSpan"> OR </span>
    Download All Accomplished Articles Base On Search Result: 
    <a onclick="dlarticles('all', 'text');" href="#">Text</a> | 
    <a onclick="dlarticles('all', 'html');" href="#">HTML</a>
</div>

<style>
.grid-view table.items tr.even td {
    background-image: none;
}
.batchActionDiv{
    padding-left:18px;
}
span.batchActionSpan{font-weight:bold;color:red;}
</style>

<script type="text/javascript">
//<![CDATA[
/*
jQuery(document).ready(function($) 
{
$.fn.yiiGridView.getChecked(containerID,columnID);
   // Notice the use of the each() method to acquire access to each elements attributes
   $('#content-container a[tooltip]').each(function()
   {
      $(this).qtip({
         content: {text:$(this).attr('tooltip'),title:{text: "Instructions:"}},
         position: {
            corner: {target: 'bottomMiddle',tooltip: 'topMiddle'},
            adjust: {screen: true}
         },
         hide: {fixed: true,delay: 240},
         style: {
            tip: true,
            border: {width: 0,radius: 4},
            autoScroll: true,
            name: 'light',
            width: {min:480, max:560}
         }
      });
   });
});
*/

function sendtask2cp(cmd, qid) {
    //var qaitems = [];
    var taskids = new Array;

    if (parseInt(qid) > 0){
        taskids.push(qid);
    } else {
        var sendable = false;

        $("input[name='ids[]'][type='checkbox'][usage='send']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            taskids.push($(this).val());
            //alert(e.value);
            //alert($(this).val());
            //taskids[i] = $(this).val();
        });

        if (!sendable){
            alert("No task can send to CopyPress.");
            return false;
        }
    }

    $.ajax({
        'type': 'POST',
        'dataType': 'json',
        'url': "<?php echo Yii::app()->createUrl('/task/send');?>",
        'data': {'ids[]':taskids},
        'success':function(data){
            //do nothing for now.
            alert(data.msg);
            $.fn.yiiGridView.update('task-grid', {
                /*
                put some search data here.
                data: {'ids[]':taskids}
                */
                data: $('.search-form form').serialize()
            });
            return false;
        }
    });

}

//]]>
</script>

