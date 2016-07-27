<?php
$contentios = array("0"=>"Ideation","1"=>"Idea Approval","2"=>"Place Order",
                    "3"=>"Ordered","4"=>"Content Approval","5"=>"Delivered");
$currentlable = "ALL";
if (isset($content_step)) $currentlable = $contentios[$content_step];
$this->breadcrumbs=array(
	'Content Step'=>array("step1"),
	$currentlable,
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

$_themebaseurl = Yii::app()->theme->baseUrl;

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'
);

$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/char.count.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');

$types = Types::model()->bytype(array("channel",'category'))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$channels = $gtps['channel'] ? $gtps['channel'] : array();
natcasesort($channels);
$channelstr = Utils::array2String($channels);

$categories = $gtps['category'] ? $gtps['category'] : array();
$categorystr = Utils::array2String(array("0" => '[Website Category]') + $categories);

$_editors = User::model()->with("rauthassignment")->editor()->findAll();
$cseditors = CHtml::listData($_editors, 'id', 'username');
natcasesort($cseditors);
$cseditorstr = Utils::array2String(array("0" => '[Editor]') + $cseditors);

$tiers = CampaignTask::$tier;
$tierstr = Utils::array2String($tiers);

$isvisible = false;
if ($iostatus == 1 && !isset($roles['Marketer'])) {
    $isvisible = true;
}

$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);

$isadmin = isset($roles['Admin']) ? 1 : 0;
$ispublisher = isset($roles['Publisher']) ? 1 : 0;
$isoutreacher = (isset($roles['InternalOutreach']) || isset($roles['Outreach'])) ? 1 : 0;
$ismarketer = isset($roles['Marketer']) ? 1 : 0;

//##$notetypes = array("1" => 'Ideation', "2"=>"Writer Note", "3"=>"Extra Writer Note");
$notetypes = array("1" => 'Ideation', "2"=>"Client Comment", "3"=>"Extra Writer Note");

//This function you need pay attention to!!!!
function genCategorStr($idstr, $catstr, $allstr){
    if (empty($idstr) || empty($catstr)) {
        return $allstr;//it was passed from str, but here it will become an array; 
    } else {
        $_ids = explode("|", $idstr);
        array_pop($_ids);
        array_shift($_ids);
        $_catestr = explode(", ", $catstr);
        if (count($_ids) == count($_catestr)) {
            $categories = array_combine($_ids, $_catestr);
            return array("0" => '[Website Category]') + $categories;//Here will return the array, not the string!!!!
        } else {
            return $allstr;
        }
    }
}

function editable($isoutreacher,$um_chl_id,$curr_chl_id) {
    if ($isoutreacher) {
        if ($um_chl_id == $curr_chl_id) {
            return true;
        }
        return false;
    }

    return true;
}

$nextvalue = $content_step + 1;

$passedqas = $clientapproves = array("0" => 'No', "1"=>"Yes");

if($ismarketer) {
    $clients = Client::model()->byuser()->findAll(array('order'=>'company ASC'));
} else {
    $clients = Client::model()->actived()->findAll(array('order'=>'company ASC'));
}
?>

<h1>Content - <?php echo $currentlable;?></h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>
<br />
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php
$this->renderPartial('/task/_ios_search',array(
	'model'=>$model,
	'_channels'=>$channels,
	'roles'=>$roles,
	'tiers'=>$tiers,
	'clients'=>$clients,
	'iostatuses'=>$iostatuses,
	'content_step'=>$content_step,
));
?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->contentio()->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
        array(
            'name' => 'id',
            'header' => 'Task ID',
        ),
        array(
            //'name' => 'rcampaign.rclient.name',
            'name' => 'client_id',
            'value' => '$data->rcampaign->rclient->name ." (". $data->rcampaign->rclient->company .")"',
            'header' => 'Client',
            'filter' => CHtml::listData($clients,'id','company'),
        ),
        array(
            'name' => 'campaign_id',
            'type' => 'raw',
            'value' => 'CHtml::encode($data->rcampaign->name)." - ".($data->rcampaign->rcampaigntask->internal_done)* 100 ."%"',
            'filter' => isset($roles['Marketer']) ? CHtml::listData(Campaign::model()->byclient()->byduty()->findAll(),'id','name') : null,
        ),

        array(
            'header' => 'Channel',
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
        ),

        array(
            'name' => 'desired_domain',
            'type' => 'raw',
            'value' => 'domain2URL($data->desired_domain, true, array("target"=>"_blank"))',
        ),

        array(
            'name' => 'rewritten_title',
            'type' => 'raw',
            'visible' => in_array($content_step, array(1,2,3,4,5)),
        ),

        /*
        array(
            'header' => 'Website Category',
            'name' => 'content_category_id',
            'type' => 'raw',
            'value' => '$data->rstep->step_domain ? $data->rstep->step_domain : Utils::getValue(' . $categorystr . ', $data->content_category_id)',
            'visible' => ($content_step != 1),
        ),
        array(
            'header' => 'Website Category',
            'name' => 'content_category_id',
            'type' => 'raw',
            //'value' => 'CHtml::dropDownList("content_category_id[]", $data->content_category_id, '.$categorystr.')',
            'value' => 'CHtml::dropDownList("content_category_id[]", $data->content_category_id, genCategorStr($data->rcampaign->category, $data->rcampaign->category_str, '.$categorystr.') )',
            'visible' => ($content_step == 1),
        ),
        array(
            'header' => 'Title',
            'name' => 'rstep.step_title',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rstep->step_title), array("task/view", "id" =>$data->id), array("name"=>"steptitlehref"))',
            'visible' => ($content_step == 4),
        ),
        */

        /*
        //##7/17/2014
        array(
            'header' => ($content_step==1) ? 'Connector' : 'Team Lead',
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
        ),

        array(
            'header' => 'Website Category',
            'name' => 'content_category_id',
            'type' => 'raw',
            //'value' => 'CHtml::dropDownList("content_category_id[]", $data->content_category_id, '.$categorystr.')',
            'value' => 'Utils::getValue(' . $categorystr . ', $data->content_category_id)',
            //'value' => ($content_step == 1) ? 'CHtml::dropDownList("content_category_id[]", $data->content_category_id, genCategorStr($data->rcampaign->category, $data->rcampaign->category_str, '.$categorystr.') )' : 'Utils::getValue(' . $categorystr . ', $data->content_category_id)',
        ),
        array(
            'name' => 'content_step_editor',
            'type' => 'raw',
            //'value' => 'Utils::getValue(' . $cseditorstr . ', $data->content_step_editor)',
            'value' => (isset($roles['Admin']) && $content_step<3)? 'CHtml::dropDownList("content_step_editor[]", $data->content_step_editor, '.$cseditorstr.')' : 'Utils::getValue(' . $cseditorstr . ', $data->content_step_editor)',
            'visible' => ($content_step!=1),
            'filter' => $cseditors,
        ),
        */

        array(
            'header' => 'Other/Domain',
            'name' => 'rstep.step_domain',
            'type' => 'raw',
            //'value' => ($content_step == 1) ? 'CHtml::textField("step_domain[]", $data->rstep->step_domain)' : '$data->rstep->step_domain',
            'value' => 'editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id) && ('.$content_step.' == 1) ? CHtml::textField("step_domain[]", $data->rstep->step_domain) : $data->rstep->step_domain',
            'visible' => $content_step==0,
        ),

        /*
        array(
            'header' => 'Word Count',
            'name' => 'tierlevel',
            'type' => 'raw',
            'value' => 'Utils::getTierWordCount($data->tierlevel)',
            'visible' => ($content_step > 0),
        ),
        */

        array(
            'name' => 'iostatus',
            'type' => 'raw',
            'value' => '$data->iostatus ? Utils::getValue(' . $iostatusesstr . ', $data->iostatus) : ""',
            'filter' => $iostatuses,
        ),

        array(
            'name' => 'passed_iqa',
            'type' => 'raw',
            'value' => 'CHtml::checkBox("passed_iqa[]", $data->passed_iqa)',
            'filter' => $passedqas,
            'visible' => ($content_step == 0),
        ),

        array(
            'name' => 'targeturl',
            'type' => 'raw',
            'value' => 'domain2URL($data->targeturl,true)',
        ),
        //##'anchortext',
        array(
            'name' => 'anchortext',
            'type' => 'raw',
            //'value' => '($data->rcampaign->is_fixed_anchortext) ? CHtml::textField("anchortext[]", $data->anchortext) : $data->anchortext',
            'value' => '($data->rcampaign->is_fixed_anchortext && editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id)) ? CHtml::textField("anchortext[]", $data->anchortext) : $data->anchortext',
        ),
        array(
            'name' => 'sentdate',
            'type' => 'raw',
            'value' => 'CHtml::textField("sentdate[]", $data->sentdate, array("id"=>"sentdate_".$data->id, "readOnly"=>"readOnly"))',
            'visible' => $content_step==5,
        ),
        array(
            'name' => 'campaign_approval_type',
            'type' => 'raw',
            'header' => 'CA',
            'value' => 'stripos($data->rcampaign->approval_type, "CA") === false ? "No" : "Yes"',
            'filter' => $clientapproves,
            'visible' => $content_step==3,
        ),
        array(
            'name' => 'campaign_approval_type',
            'type' => 'raw',
            'header' => 'Client Approval',
            'value' => '($data->rcampaign->approval_type=="TA") ? "Yes" : "No"',
            'filter' => $clientapproves,
            'visible' => $content_step==4,
        ),
        array(
            'header' => 'Date',
            'name' => 'step_date',
            'type' => 'raw',
        ),
        //'step_date',
		array(
			'class'=>'CButtonColumn',
            //##'template'=>'{ideationnote} {writernote} {extranote} {updatestepattr} {accept} {denywithreason}',
            'template'=>'{note} {updatestepattr} {accept} {denywithreason} {rewind} {dlhtml} {dldoc}',
            'buttons' => array(
                /*
                'ideationnote' => array(
                    'label' => 'Add Ideation Notes',
                    'imageUrl' => $_themebaseurl.'/css/gridview/idea.png',
                    'visible' => '($data->content_step != 1)',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data)),'name'=>'ideationnote','class'=>'ideationnote',),
                ),
                'writernote' => array(
                    'label' => 'Add Client Comment',
                    'imageUrl' => $_themebaseurl.'/css/gridview/note.png',
                    'visible' => '($data->content_step != 1)',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data)),'name'=>'writernote','class'=>'writernote',),
                ),
                'extranote' => array(
                    'label' => 'Add Extra Writer Notes',
                    'imageUrl' => $_themebaseurl.'/css/gridview/extranote.png',
                    'visible' => '($data->content_step >= 4)',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data)),'name'=>'extranote','class'=>'extranote',),
                ),
                */

                'accept' => array(
                    'label' => 'Approve',
                    'imageUrl' => $_themebaseurl.'/css/gridview/accept.png',
                    'visible' => '(( ($data->content_step==0 && ('.$isadmin.' || '.$isoutreacher.')) || ($data->content_step==1 || $data->content_step==4) && ('.$isadmin.' || '.$ismarketer.') ) || ( ($data->content_step==2 || $data->content_step==3) && '.$isadmin.')) && editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id)',
                    //##'visible' => '($data->content_step != 5)',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"content_step","attrvalue"=>'.$nextvalue.'))',
                    'options' => array(
                        'name'=>'stepaccept',
                    ),
                ),

                'rewind' => array(
                    'label' => 'Rewind',
                    'imageUrl' => $_themebaseurl.'/css/gridview/rewind.png',
                    'visible' => '($data->content_step == 5 && '.$isadmin.') && editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id)',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"content_step","attrvalue"=>0))',
                    'options' => array(
                        'name'=>'stepaccept',
                    ),
                ),

                'updatestepattr' => array(
                    'label' => 'Add Ideation',
                    'imageUrl' => ($content_step == 0) ? $_themebaseurl.'/css/gridview/update.png' : $_themebaseurl.'/css/gridview/view.png',
                    //##'visible' => '($data->content_step != 2) || ($data->content_step == 2 && '.$isadmin.')',
                    'visible' => ' (($data->content_step==0 && ('.$isadmin.' || '.$isoutreacher.')) || ('.$isoutreacher.') || ( ($data->content_step==1 || $data->content_step==2 || $data->content_step==5) && '.$isadmin.')) && editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id)',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data)),'name'=>'updatestepattr','class'=>'updatestepattr',),
                ),

                'denywithreason' => array(
                    'label' => 'Deny With Reason',
                    'imageUrl' => $_themebaseurl.'/css/gridview/deny2reason.png',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"content_step","attrvalue"=>0))',
                    //'visible' => '($data->content_step == 4) || ($data->content_step == 2 && '.$isadmin.') || ($data->content_step == 1)',
                    'visible' => '(( ($data->content_step==0 && ('.$isadmin.' || '.$isoutreacher.')) || ($data->content_step==1 || $data->content_step==4) && ('.$isadmin.' || '.$ismarketer.') ) || ( ($data->content_step==2 || $data->content_step==3) && '.$isadmin.')) && editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id)',
                    'options' => array(
                        'name'=>'stepreason',
                    ),
                ),

                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => $_themebaseurl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'visible' => 'editable('.$isoutreacher.','.$um_chl_id.',$data->channel_id)',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data)),'name'=>'addiosnote','class'=>'addiosnote',),
                ),

                'dlhtml' => array(
                    'label' => 'Download article as html format',
                    //#'visible' => '($data->tasktype == 1 && $data->rcontent->length && $data->content_step == 5)',
                    'visible' => '($data->rcontent->length && ($data->content_step==4 || $data->content_step==5) )',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/dlhtml16.png',
                    'url' => 'Yii::app()->createUrl("download/content", array("id"=>$data->id,"format"=>"html"))',
                    'options' => array(
                        'name'=>'dlhtml','class'=>'dlhtml',
                    ),
                ),
                'dldoc' => array(
                    'label' => 'Download article as word format',
                    'visible' => '($data->rcontent->length && ($data->content_step==4 || $data->content_step==5) )',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/dlword16.png',
                    'url' => 'Yii::app()->createUrl("download/content", array("id"=>$data->id,"format"=>"doc"))',
                    'options' => array(
                        'name'=>'dldoc','class'=>'dldoc',
                    ),
                ),
                /*
                'dltxt' => array(
                    'label' => 'Download article as txt format',
                    'visible' => '($data->rcontent->length && ($data->content_step==4 || $data->content_step==5) )',
                    'imageUrl'=>Yii::app()->theme->baseUrl.'/css/gridview/dltxt16.png',
                    'url' => 'Yii::app()->createUrl("download/content", array("id"=>$data->id,"format"=>"text"))',
                    'options' => array(
                        'name'=>'dltxt','class'=>'dltxt',
                    ),
                ),
                */
            ),
            'htmlOptions'=>array(
                'nowrap'=>'nowrap',
            )
        ),
	),
)); ?>

<div class="clear"></div>

<?php
$this->renderPartial('/task/_denystepreason',array(
	'model'=>$model,
    'roles'=>$roles,
));
?>

<div id="hiddenContainer">
    <div id="noteboxdiv" style="display:none;"></div>
    <div id="writernoteboxdiv" style="display:none;"></div>
    <div id="extranoteboxdiv" style="display:none;"></div>
    <div id="ideationnoteboxdiv" style="display:none;margin:5px 10px;"></div>
    <div id="updatestepattrboxdiv" style="display:none;"></div>
</div>
<div class="clear"></div>

<?php if ($content_step == 2) { ?>
<div style="width:416px;float:left">
    <div class="form">
        <div class="row buttons">
            <?php echo CHtml::button('Export', array('id'=>'bulkExportBtn')); ?> 
            <?php
    /*
            if ($isadmin) {
                echo CHtml::button('Export And Ordered', array('id'=>'bulkExport2CompleteBtn'));
            }
    */
            ?> 
        </div>
    </div><!-- form -->
</div>
<?php } elseif($content_step == 4 || $content_step == 1 || $content_step == 3) {
    $bulkacceptbtnlable = ($content_step == 3) ? "Move to Next Step" : "Approve";
    ?>
    <div class="form">
        <div class="row buttons">
            <?php
            if (($content_step==3 && $isadmin) || ($content_step==4 && ($isadmin || $ismarketer)) || $content_step==1) {
                echo CHtml::button($bulkacceptbtnlable, array('id'=>'bulkStepAcceptBtn'));
            }

            if ($content_step==1) {
                echo CHtml::button('Export', array('id'=>'bulkExportBtn'));
            }
            ?> 
        </div>
    </div>
<?php } ?>

<?php /* ?>
<div style="width:416px;float:left">
<?php if ($content_step == 2) {?>
    <div class="form">
        <div class="row">
            <label for="bulk_content_step_editor">Bulk assign these tasks to: </label>
            <?php echo CHtml::dropDownList("bulk_content_step_editor", 0, $cseditors, array('prompt'=>'[Choose an Editor]')); ?>
        </div>
        <div class="row buttons">
            <?php echo CHtml::button('Assign', array('id'=>'bulkIoEditorBtn')); ?> 
        </div>
    </div><!-- form -->
<?php } elseif($content_step == 4 || $content_step == 1 || $content_step == 3) {
    $bulkacceptbtnlable = ($content_step == 1) ? "Send to Idea Assign" : "Accept";
    ?>
    <div class="form">
        <div class="row buttons">
            <?php echo CHtml::button($bulkacceptbtnlable, array('id'=>'bulkStepAcceptBtn')); ?> 
        </div>
    </div><!-- form -->
<?php } elseif($content_step == 5) { ?>
    <div class="form">
        <div class="row buttons">
            <?php echo CHtml::button('Export', array('id'=>'bulkExportBtn')); ?> 
            <?php echo CHtml::button('Export And Complete', array('id'=>'bulkExport2CompleteBtn')); ?> 
        </div>
    </div><!-- form -->
<?php } ?>
</div>
<?php */ ?>


<?php
/*
if ($content_step >= 4 || $content_step == 2) :
if ($content_step == 2) {
    $_defaulttype = 2;
} else {
    $_defaulttype = 3;
}
?>
<div style="width:450px;float:right">
    <div class="form">
        <div class="row">
            <label for="bulk_note_type">Note Type: </label>
            <?php echo CHtml::dropDownList("bulk_note_type", $_defaulttype, $notetypes); ?>
        </div>
        <div class="row">
            <label for="bulk_step_notes">Bulk Notes: </label>
            <?php echo CHtml::textArea("bulk_step_notes", "", array('style'=>'height:150px; width:420px;')); ?>
        </div>
        <div class="row buttons">
            <?php echo CHtml::button('Bulk Note', array('id'=>'bulkIoStepNoteBtn')); ?> 
        </div>
    </div><!-- form -->
</div>
<?php endif;
*/
?>

<div class="clear"></div>

<?php
$this->renderPartial('/task/_denyreason',array(
	'model'=>$model,
    'roles'=>$roles,
    'iostatus'=>$iostatus,
));
?>

<script type="text/javascript">
function parseIdFromUrl(s){
    s = s.match(/&id=\d+/g);
    s = String(s);

    return s = s.replace(/&id=/,"");
}

function downloadStep(taskids){
    var url = "<?php echo Yii::app()->createUrl('/contentStep/download');?>";
    url = url + "&Task[id]=" + taskids;
    window.location.href = url;
}

function hideAllBox(){
    $("#noteboxdiv,#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv,#updatestepattrboxdiv").hide();
    $("#noteboxdiv,#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv,#updatestepattrboxdiv").appendTo($("#hiddenContainer"));
    return true;
}

var current_step = "<?php echo $content_step;?>";
$(document).ready(function() {
    $("#Task_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:5}).multiselectfilter();
    $("#Task_iostatus").multiselect({noneSelectedText:'Select IO Status',selectedList:5}).multiselectfilter();

    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo Yii::app()->theme->baseUrl; ?>" + '/images/loading.gif)');
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
    });

    $("#downloadIO").click(function(){
        var url = "<?php echo Yii::app()->createUrl('/contentStep/download');?>";
        var rparent = $('#taskSearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#taskSearchForm input[type=hidden][name=r]').remove();
        url = url + "&" + $('#taskSearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        var lastclickid = 0;

        $("a[name^='addiosnote'],a[name^='writernote'],a[name^='ideationnote'],a[name^='extranote'],a[name^='updatestepattr'],a[name^='steptitlehref']").each(function() {
            $(this).unbind('click').click(function(){

                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);

                if ($(this).attr("name") == "updatestepattr" || $(this).attr("name") == "steptitlehref") {
                    $("#noteboxdiv,#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv").hide();
                    $("#noteboxdiv,#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv").appendTo($("#hiddenContainer"));
                    //$("#ideationnoteboxdiv").appendTo($("#hiddenContainer"));
                    var currdiv = "#updatestepattrboxdiv";
                    var _url = "<?php echo Yii::app()->createUrl('/contentStep/updatestep');?>";
                    var _data = 'task_id='+currenttrid+"&ajax=true";
                } else if($(this).attr("name") == "addiosnote") {
                    $("#updatestepattrboxdiv,#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv").hide();
                    $("#updatestepattrboxdiv,#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv").appendTo($("#hiddenContainer"));
                    var currdiv = "#noteboxdiv";
                    var _url = "<?php echo Yii::app()->createUrl('/taskNote/note');?>";
                    var _data = 'task_id='+currenttrid+"&ajax=true";
                } else {
                    $("#updatestepattrboxdiv").hide();
                    $("#updatestepattrboxdiv").appendTo($("#hiddenContainer"));
                    if ($(this).attr("name") == "writernote") {
                        var typevalue = 2;
                        var currdiv = "#writernoteboxdiv";
                        var prevdiv = "#ideationnoteboxdiv,#extranoteboxdiv";
                    } else if ($(this).attr("name") == "extranote") {
                        var typevalue = 3;
                        var currdiv = "#extranoteboxdiv";
                        var prevdiv = "#ideationnoteboxdiv,#writernoteboxdiv";
                    } else {
                        var typevalue = 1;
                        var currdiv = "#ideationnoteboxdiv";
                        var prevdiv = "#writernoteboxdiv,#extranoteboxdiv";
                    }
                    $(prevdiv).hide();
                    var _url = "<?php echo Yii::app()->createUrl('/stepNote/note');?>";
                    var _data = 'task_id='+currenttrid+"&ajax=true"+"&type="+typevalue;
                }
                if (lastclickid == currenttrid || lastclickid == 0) {
                    $(currdiv).toggle();
                } else {
                    $(currdiv).show();
                }

                if ($(currdiv).is(":visible")) {

                    $.ajax({
                        'type': 'GET',
                        //'dataType': 'json',
                        'dataType': 'html',
                        'url': _url,
                        'data': _data,
                        'success':function(data){
                            $(currdiv).html(data);
                        },
                        'complete':function(XHR,TS){XHR = null;}
                    });

                    if ($("#"+currenttrid+"_dtr").length>0) {
                        /*
                        here you couldn't use the find("td"), coz it will search all of the posterity td elements,
                        The .find() and .children() methods are similar,
                        but .children() only travels a single level down the DOM tree.
                        */
                        $(currdiv).appendTo($("#"+currenttrid+"_dtr").children("td"));
                    } else {
                        var tdlength = $("table.items tr:first > th").length;
                        var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
                        //$("#noteboxdiv").appendTo(vartr.find("td"));
                        $(currdiv).appendTo(vartr.children("td"));
                        $(this).parent().parent().after(vartr);
                        $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
                    }

                } else {}

                lastclickid = currenttrid;

                return false;
            });
        });

        $("a[name^='stepreason']").each(function() {
            $(this).click(function(){
                //hideAllBox();
                var newhref = $(this).attr('href');
                currenttrid = parseIdFromUrl(newhref);
                /*
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                */
                $("#denytaskid").val(currenttrid);
                //alert($("#denytaskid").val());
                $( "#denyreason-form" ).dialog( "open" );

                return false;
            });
        });

        $("a[name^='dlhtml'],a[name^='dltxt'],a[name^='dldoc']").each(function() {
            $(this).unbind('click').click(function(){
                //download content from copypress
                //alert("download it");
                window.location.href = $(this).attr('href');
                return false;
            });
        });

        $("a[name^='stepaccept']").each(function() {
            $(this).unbind('click').click(function(){
                var thistr = $(this).parent().parent();
                if (current_step == 1){
                    //alert(thistr.find("select[name^='content_category_id']").val());
                    //alert(thistr.find("input[name^='step_domain']").val().length);
                    if (thistr.find("select[name^='content_category_id']").val() == 0
                        && thistr.find("input[name^='step_domain']").val().length <= 3){
                        alert("You should provide the Website Category and Domain either or both of them.");
                        return false;
                    }
                }

                if (current_step == 3){
                    var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                    var gvoffset = $(this).parent().parent().prevAll().length;
                    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                    var _step_title = "";
                    var t = this;

                    if ($('#updateStepBtn').length>0) {
                        $('#updateStepBtn').click();
                    }

                    $.ajax({
                        'type': 'GET',
                        'dataType': 'json',
                        'url': "<?php echo CHtml::normalizeUrl(array('/contentStep/getattr'));?>",
                        'data': 'task_id='+currenttrid+"&attrname=step_title",
                        'success':function(data){
                            //donothing for now;
                            //_step_title = data.step_title;//right now we set the step_title = rewritten_title, so we can use rewritten_title directly. 
                            _step_title = data.step_title;
                            _step_title = $.trim(_step_title);

                            if (_step_title.length == 0){
                                alert("Please enter step title before you approve this task.");
                                thistr.find("a[name^='updatestepattr']").trigger( "click" );
                                return false;
                            }

                            return stepAction(t);
                        },
                        'complete':function(XHR,TS){XHR = null;}
                    });

                    return false;
                }

                /*
                if(!confirm('Are you sure you want to '+$(this).attr("title")+'?')) return false;
                //###!!!move the notebox back to the hidden container. incase it was removed when the user deny/accept.!!
                $("#writernoteboxdiv,#ideationnoteboxdiv").hide();
                $("#writernoteboxdiv,#ideationnoteboxdiv").appendTo($("#hiddenContainer"));

                var newhref = $(this).attr('href');
                var thishref = $(this);
                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': newhref,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            //alert(data.msg);
                            //thishref.remove();
                            $.fn.yiiGridView.update('task-grid');
                        } else {
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
                //$(this).attr('href',"javascript:void(0);");
                */

                stepAction(this);
                return false;
            });
        });

        function stepAction(t){
            if(!confirm('Are you sure you want to '+$(t).attr("title")+'?')) return false;
            //###!!!move the notebox back to the hidden container. incase it was removed when the user deny/accept.!!
            //$("#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv,#updatestepattrboxdiv").hide();
            //$("#writernoteboxdiv,#ideationnoteboxdiv,#extranoteboxdiv,#updatestepattrboxdiv").appendTo($("#hiddenContainer"));
            hideAllBox();

            var newhref = $(t).attr('href');
            var thishref = $(t);
            $.ajax({
                'type': 'GET',
                'dataType': 'json',
                'url': newhref,
                'success':function(data){
                    //donothing for now;
                    if (data.success){
                        //alert(data.msg);
                        //thishref.remove();
                        $.fn.yiiGridView.update('task-grid');
                    } else {
                        alert(data.msg);
                    }
                },
                'complete':function(XHR,TS){XHR = null;}
            });

            return false;
        }

        $("input[name^='passed_iqa']").each(function() {
            $(this).unbind('click').click(function(){
                //DO NOT REMOVE THIS LINE;
                hideAllBox();

                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                var currentvalue = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+currentvalue,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });
        });

        $("select[name^='content_step_editor'],select[name^='content_category_id']").each(function() {
            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });
        });

        $("input[name^='sentdate']").each(function() {
            $(this).datepicker({ dateFormat: "yy-mm-dd" });
            $(this).keydown(function(e){
                if (e.keyCode == 46 || e.keyCode == 8) {
                    //Delete and backspace clear text 
                    $(this).val(null); //Clear text
                    $(this).datepicker("hide"); //Hide the datepicker calendar if displayed
                    //$(this).blur(); //aka "unfocus"
                    $(this).change(); //aka "unfocus"
                }

                //Prevent user from manually entering in a date - have to use the datepicker box
                e.preventDefault();
            });

            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });
        });

        $("input[name^='step_domain']").each(function() {
            $(this).unbind('blur').blur(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                this.value = stripURL2Domain(this.value);
                if (this.value && !isValidDomain(this.value)){
                    $(thistd).css("background-color","red");
                    alert("Please enter a valid domain here.");
                    return ;
                }
                var currentvalue = encodeURIComponent(this.value);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/contentStep/setattr'));?>",
                    'data': 'task_id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+currentvalue,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });
        });

        $("input[name^='anchortext']").each(function() {
            $(this).unbind('blur').blur(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);
                var currentvalue = encodeURIComponent(this.value);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+currentvalue,
                    'success':function(data){
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                        } else {
                            $(thistd).css("background-color","red");
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });
        });

        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        $("div.pager > ul > li a, table.items thead tr.filters input, table.items thead tr.filters select").each(function(){
            $(this).click(function() {
                hideAllBox();
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            hideAllBox();
            return true;
        });

        //the following code for the note icon changing.
        //------------------------------------------------//
        var _ids = [];
        $('#task-grid > div.keys > span').each(function(i){
            _ids[i] = $(this).html();
        });
        $("#task-grid > table.items > tbody > tr").each(function(i){
            $(this).attr("id", "etr"+_ids[i]);//reset table.tr.id
        });

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/taskNote/icon');?>",
            'data': {'ids[]': _ids},
            'success':function(data){
                //alert(data.msg);
                if (data.success){
                    if (data.ids){
                        $.each(data.ids, function (v){
                            //alert(v);
                            $("#etr" +v+" > td:last > a.addiosnote > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/notes.png");
                        });
                    }

                    if (data.freshnote) {
                        $.each(data.freshnote, function (v){
                            $("#etr" +v+" > td:last > a.addiosnote > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/freshnote.png");
                        });
                        //alert($.inArray(v, data.freshnote));
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        /*
        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/stepNote/icon');?>",
            'data': {'ids[]': _ids, 'type':'1'},
            'success':function(data){
                //alert(data.msg);
                if (data.success){
                    if (data.ids){
                        $.each(data.ids, function (v){
                            //alert(v);
                            $("#etr" +v+" > td:last > a.ideationnote > img").attr("src", "<?php echo Yii::app()->theme->baseUrl?>/css/gridview/ideas.png");
                        });
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/stepNote/icon');?>",
            'data': {'ids[]': _ids, 'type':'2'},
            'success':function(data){
                //alert(data.msg);
                if (data.success){
                    if (data.ids){
                        $.each(data.ids, function (v){
                            //alert(v);
                            $("#etr" +v+" > td:last > a.writernote > img").attr("src", "<?php echo Yii::app()->theme->baseUrl?>/css/gridview/notes.png");
                        });
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
        */

    <?php if($content_step >= 4){ ?>
        var _notetypes = [1,2,3];
    <?php } else { ?>
        var _notetypes = [1,2];
    <?php } ?>

        $.each(_notetypes, function(key,val){
            if (val == 2) {
                var _hrefname = "writernote";
                var _imgon = "notes";
            } else if(val == 3) {
                var _hrefname = "extranote";
                var _imgon = "extranotes";
            } else {
                var _hrefname = "ideationnote";
                var _imgon = "ideas";
            }

            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': "<?php echo Yii::app()->createUrl('/stepNote/icon');?>",
                'data': {'ids[]': _ids, 'type':val},
                'success':function(data){
                    //alert(data.msg);
                    if (data.success){
                        if (data.ids){
                            $.each(data.ids, function (v){
                                //alert(v);
                                $("#etr" +v+" > td:last > a."+_hrefname+" > img").attr("src", "<?php echo Yii::app()->theme->baseUrl?>/css/gridview/"+_imgon+".png");
                            });
                        }
                    }
                },
                'complete':function(XHR,TS){XHR = null;}
            });
        });
        //------------------------------------------------//
    }

    $("#bulkIoEditorBtn,#bulkStepAcceptBtn,#bulkExportBtn,#bulkExport2CompleteBtn").click(function(){
        var taskids = new Array;

        var sendable = false;
        $("input[name='ids[]'][type='checkbox']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            taskids.push($(this).val());
        });

        if (!sendable){
            alert("Please choose one item at least.");
            return false;
        }

        if ($(this).attr('id') == 'bulkIoEditorBtn'){
            var attrname = 'content_step_editor';
            var attrvalue = $("#bulk_content_step_editor").val();
        } else {
            var attrname = 'content_step';
            var attrvalue = parseInt(current_step)+1;
        }

        if ($(this).attr('id') == 'bulkExportBtn') {
            downloadStep(taskids);
        } else {
            if ($(this).attr('id') == 'bulkExport2CompleteBtn') downloadStep(taskids);

            // !!2/8/2014
            if (current_step == 1 && $(this).attr('id') == 'bulkStepAcceptBtn') {
                var _breakopr = false;
                $.each(taskids, function(_idx, _vlu){
                    var _currenttr = "#etr"+_vlu;
                    if ($(_currenttr).find("select[name^='content_category_id']").val() == 0
                        && $(_currenttr).find("input[name^='step_domain']").val().length <= 3){
                        $(_currenttr).find("select[name^='content_category_id']").focus();
                        $(_currenttr).find("select[name^='content_category_id']").css("background-color","red");
                        //##alert("You should provide the Website Category and Domain either or both of them.");
                        _breakopr = true;
                        return false;//break each;
                    }
                });

                if (_breakopr){
                    alert("You should provide the Website Category and Domain either or both of them.");
                    return false;
                }
            }

            $.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': "<?php echo Yii::app()->createUrl('/task/batchattr');?>",
                'data': {'ids[]': taskids, 'attrname':attrname, 'attrvalue':attrvalue},
                'success':function(data){
                    $.fn.yiiGridView.update('task-grid', {
                        /*
                        put some search data here.
                        data: {'ids[]':taskids}
                        */
                        data: $('.search-form form').serialize()
                    });
                },
                //This error function is fixing the canceled status on chorme, ref: http://stackoverflow.com/questions/12009423/what-does-status-canceled-for-a-resource-mean-in-chrome-developer-tools
                error: function (xhr) {
                    //alert(xhr.responseText);
                    setTimeout('$.fn.yiiGridView.update("task-grid", {data: $(".search-form form").serialize()});', 1000);
                    //$.fn.yiiGridView.update('task-grid', {data: $('.search-form form').serialize()});
                },
                'complete':function(XHR,TS){XHR = null;}
            });
        }

    });

    $("#bulkIoStepNoteBtn").click(function(){
        var taskids = new Array;

        var sendable = false;
        $("input[name='ids[]'][type='checkbox']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            taskids.push($(this).val());
        });

        if (!sendable){
            alert("Please choose one item at least.");
            return false;
        }
        //var _t = this;
        //_t.disabled = true;
        if ($("#bulk_step_notes").val().length < 3){
            alert("Your note should more than 3 characters.");
            return false;
        }

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/stepNote/bulknote');?>",
            'data': {'ids[]': taskids, 'note':$("#bulk_step_notes").val(), 'type':$("#bulk_note_type").val()},
            'success':function(data){
                //alert(data.msg);
                //_t.disabled = false;
                $("#bulk_step_notes").val("");
                $.fn.yiiGridView.update('task-grid', {
                    /*
                    put some search data here.
                    data: {'ids[]':taskids}
                    */
                    data: $('.search-form form').serialize()
                });
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});


function stripURL2Domain(url){
    url = url.replace(/^(www\.)/i, "");
    url = url.replace(/^(ht|f)tp(s?)\:\/\/(www\.){0,1}/i, "");
    url = url.replace(/(\/[\s\S]*)/i, "");

    return url;
}

function isValidDomain(domain){
    var rx = /^([a-z0-9]([\-a-z0-9]*[a-z0-9])?\.)+((a[cdefgilmnoqrstuwxz]|aero|arpa)|(b[abdefghijmnorstvwyz]|biz)|(c[acdfghiklmnorsuvxyz]|cat|com|coop)|d[ejkmoz]|(e[ceghrstu]|edu)|f[ijkmor]|(g[abdefghilmnpqrstuwy]|gov)|h[kmnrtu]|(i[delmnoqrst]|info|int)|(j[emop]|jobs)|k[eghimnprwyz]|l[abcikrstuvy]|(m[acdghklmnopqrstuvwxyz]|mil|mobi|museum)|(n[acefgilopruz]|name|net)|(om|org)|(p[aefghklmnrstwy]|pro)|qa|r[eouw]|s[abcdeghijklmnortvyz]|(t[cdfghjklmnoprtvwz]|travel)|u[agkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw])$/i;

    return rx.test(domain) ? true : false;
}
</script>

<style>
span.warning{color:#600;font-size: 0.9em; font-weight: bold;}	
span.exceeded{color:#e00;font-size: 0.9em; font-weight: bold;}	
</style>
