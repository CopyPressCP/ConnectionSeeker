<?php
$_iostatuses = Task::$iostatuses;
//print_r($_iostatuses);

$this->breadcrumbs=array(
	'Campaign Task'=>array(strtolower($_iostatuses[$iostatus])),
	$_iostatuses[$iostatus],
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
$cs->registerCssFile($_themebaseurl . '/css/gridview/styles.css');
$cs->registerScriptFile($_themebaseurl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
$cs->registerCssFile((isset(Yii::app()->theme) ? $_themebaseurl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview') . '/styles.css');
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'
);

$cs->registerScriptFile($_themebaseurl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile($_themebaseurl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile($_themebaseurl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile($_themebaseurl . '/js/multiselect/jquery.multiselect.filter.css');

$cs->registerScriptFile(Yii::app()->baseUrl . '/js/raty/jquery.raty.min.js', CClientScript::POS_HEAD);


$types = Types::model()->bytype(array("linktask","channel",'category'))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
//print_r($gtps);
$linktask = $gtps['linktask'];
$tasktypestr = Utils::array2String($linktask);

$channels = $gtps['channel'] ? $gtps['channel'] : array();
natcasesort($channels);
$channelstr = Utils::array2String(array("0" => ' ')+$channels);

$categories = $gtps['category'] ? $gtps['category'] : array();
$categorystr = Utils::array2String(array("0" => '[Desired Category]') + $categories);

$tiers = CampaignTask::$tier;
$tierstr = Utils::array2String($tiers);

$iostatuses = Task::$iostatuses;
$iostatusesstr =  Utils::array2String($iostatuses);


$isvisible = false;
if ($iostatus == 1 && !isset($roles['Marketer'])) {
    $isvisible = true;
}

$rewindvalue = 2;
$rewindlabel = "Rewind";

$nextvalue = 3;
if ($iostatus == 2) {
    $nextvalue = 21;
} elseif ($iostatus == 21) {
    $nextvalue = 3;
} elseif ($iostatus == 32) {
    $nextvalue = 31;
    $rewindlabel = "Move it to accepted";
} elseif ($iostatus == 31) {
    $nextvalue = 5;

    $rewindvalue = 3;
    $rewindlabel = "Send back to Approved";
}

if ($iostatus == 1) {
    $denyvalue = 4;
} else {
    $denyvalue = 1;
}

/*
$isadmin = 0;//false
if(isset($roles['Admin']) || isset($roles['Marketer'])){
    $isadmin = 1;//true
}

$ispublisher = 0;//false
if(isset($roles['Publisher']) || isset($roles['InternalOutreach']) || isset($roles['Outreach'])){
    $ispublisher = 1;//true
}

$isoutreacher = 0;//false
if(isset($roles['InternalOutreach']) || isset($roles['Outreach'])){
    $isoutreacher = 1;//true
}

$ismarketer = 0;
if(isset($roles['Marketer'])){
    $ismarketer = 1;//true
}
*/

$isadmin = isset($roles['Admin']) ? 1 : 0;
$ispublisher = isset($roles['Publisher']) ? 1 : 0;
$isoutreacher = (isset($roles['InternalOutreach']) || isset($roles['Outreach'])) ? 1 : 0;
$ismarketer = isset($roles['Marketer']) ? 1 : 0;

if($ismarketer) {
    $clients = Client::model()->byuser()->findAll(array('order'=>'company ASC'));
} else {
    $clients = Client::model()->actived()->findAll(array('order'=>'company ASC'));
}

$iolabel = $_iostatuses[$iostatus];

//##7/10/2014
if ($this->action->id == 'rebuilt') {
    $iolabel = "Completed - Rebuilt";
}

$rebuildimg = $_themebaseurl."/css/gridview/star-on.png";
$contentdelivered = $_themebaseurl."/css/gridview/dlword16.png";
$contentios = array("0"=>"Ideation","1"=>"Idea Approval","2"=>"Place Order",
                    "3"=>"Ordered","4"=>"Content Approval","5"=>"Delivered");

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
            return array("0" => '[Desired Category]') + $categories;//Here will return the array, not the string!!!!
        } else {
            return $allstr;
        }
    }
}
?>

<h1><?php echo $iolabel; ?> IOs</h1>
<div id="processing" style="float:left;width:220px;">&nbsp;</div>
<br />
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('/task/_ios_search',array(
	'model'=>$model,
	'iostatus'=>$iostatus,
	'_channels'=>$channels,
	'roles'=>$roles,
	'tiers'=>$tiers,
	'clients'=>$clients,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'task-grid',
	'dataProvider'=>$model->unhidden()->search(),
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
            'visible' => ($iostatus == 1 || $iostatus == 21),
        ),
        array(
            'name' => 'id',
            'header' => 'Task ID',
            'type' => 'html',
            'value'=> '($data->iostatus==5&&$data->rebuild)?"$data->id - " . CHtml::link(CHtml::image("'.$rebuildimg.'"), "#"):"$data->id"',
            'htmlOptions'=>array(
                'nowrap'=>'nowrap',
            )
        ),
        array(
            'header' => 'Client',
            //'name' => 'rcampaign.rclient.company',
            'name' => 'client_id',
            'type' => 'raw',
            'value' => 'CHtml::link(CHtml::encode($data->rcampaign->rclient->company), array("client/view", "id" =>$data->rcampaign->client_id))',
            'filter' => CHtml::listData($clients,'id','company'),
            'visible' => ($iostatus == 5 && !$ismarketer),
        ),
        array(
            'header' => 'Campaign',
            //'name' => 'campaign_id',
            'name' => 'campaign_name',
            'type' => 'raw',
            //'value' => 'CHtml::encode($data->rcampaign->name)',
            'value' => 'CHtml::encode($data->rcampaign->name)." - ".($data->rcampaign->rcampaigntask->internal_done)* 100 ."%"',
            'filter' => isset($roles['Marketer']) ? CHtml::listData(Campaign::model()->byclient()->byduty()->findAll(),'id','name') : null,
        ),
        array(
            //'header' => 'TOA',
            'name' => 'rcampaign.approval_type',
            'type' => 'raw',
            'visible' => ( ($iostatus == 1 || $iostatus == 2) && !$ismarketer),
        ),
        array(
            'name' => 'iostatus',
            'type' => 'raw',
            'value' => '$data->iostatus ? Utils::getValue(' . $iostatusesstr . ', $data->iostatus) : ""',
            'filter' => $iostatuses,
        ),
        array(
            'name' => 'anchortext',
            'type' => 'raw',
            'value' => '(($data->iostatus==31 || $data->iostatus==32) && '."$isadmin".') ?  CHtml::textField("anchortext[]", $data->anchortext) : CHtml::encode($data->anchortext)',
            'visible' => isVisible('anchortext', $dparr),
        ),
        array(
            'name' => 'targeturl',
            'type' => 'raw',
            //'value' => 'domain2URL($data->targeturl,true)',
            'value' => '(($data->iostatus==31 || $data->iostatus==32) && '."$isadmin".') ?  CHtml::textField("targeturl[]", $data->targeturl) : domain2URL($data->targeturl,true)',
            'visible' => isVisible('targeturl', $dparr),
        ),
        array(
            'name' => 'tierlevel',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $tierstr . ', $data->tierlevel)',
            'filter' => $tiers,
            'visible' => isVisible('tierlevel', $dparr) && ($iostatus != 3),
        ),
        array(
            'header' => 'Desired Category',
            'name' => 'content_category_id',
            'type' => 'raw',
            //'value' => 'CHtml::dropDownList("content_category_id[]", $data->content_category_id, '.$categorystr.')',
            'value' => 'CHtml::dropDownList("content_category_id[]", $data->content_category_id, genCategorStr($data->rcampaign->category, $data->rcampaign->category_str, '.$categorystr.') )',
            'visible' => isVisible('desired_domain_id', $dparr) && ($iostatus == 2),
        ),
        //Here you need pay attention to ($data->desired_domain_id ? "D:".$data->desired_domain_id : "C:".$data->channel_id)
        array(
            'name' => 'desired_domain',
            'type' => 'raw',
            'value' => '($data->iostatus != 2) || '."$ismarketer".'? domain2URL($data->desired_domain, true, array("target"=>"_blank")) : CHtml::textField("desired_domain[]", $data->desired_domain)',
            'visible' => isVisible('desired_domain_id', $dparr),
        ),
        array(
            'name' => 'rewritten_title',
            'type' => 'raw',
            'value' => '(($data->iostatus==31 || $data->iostatus==32) && '."$isadmin".') || ($data->iostatus==2 && ('."$isadmin || $ispublisher".')) ?  CHtml::textField("rewritten_title[]", $data->rewritten_title) : CHtml::encode($data->rewritten_title)',
            //##7/7/2014##//'value' => '($data->iostatus != 2) || '."$isoutreacher || $ismarketer".' ? CHtml::encode($data->rewritten_title) : CHtml::textField("rewritten_title[]", $data->rewritten_title)',
            //'value' => 'in_array($data->iostatus, array(1,3,4,21,5)) ? CHtml::encode($data->rewritten_title) : CHtml::textField("rewritten_title[]", $data->rewritten_title)',
            'visible' => isVisible('rewritten_title', $dparr),
        ),
        array(
            'name' => 'siteonly',
            'type' => 'raw',
            'value' => '($data->iostatus == 21) ? (($data->siteonly) ? "Y" : "N") : CHtml::checkBox("siteonly[]", $data->siteonly)',
            'visible' => ($isadmin || isset($roles['InternalOutreach'])) && in_array($iostatus, array(2,21)),
        ),
        array(
            'header' => 'Delivered',
            'name' => 'content_step',
            'type' => 'raw',
            'filter' => $contentios,
            'value'=> '($data->content_step==5)? CHtml::link(CHtml::image("'.$contentdelivered.'"), "#"):""',
            'visible' => ($iostatus == 3),
        ),
        array(
            'name' => 'sentdate',
            'type' => 'raw',
            'value' => 'in_array($data->iostatus, array(4,21,5)) || '."$ismarketer".' ? $data->sentdate : CHtml::textField("sentdate[]", $data->sentdate, array("id"=>"sentdate_".$data->id, "readOnly"=>"readOnly"))',
            'visible' => isVisible('sentdate', $dparr) && ($iostatus == 3),
        ),
        array(
            'name' => 'sourceurl',
            'type' => 'raw',
            //'value' => '($data->iostatus == 4 || $data->iostatus == 21 || $data->iostatus == 5) ? CHtml::encode($data->sourceurl) : CHtml::textField("sourceurl[]", $data->sourceurl)',
            'value' => 'in_array($data->iostatus, array(4,21,31,32,5)) || '."$ismarketer".' ? domain2URL($data->sourceurl,true) : CHtml::textField("sourceurl[]", $data->sourceurl, array("id"=>"sourceurl_".$data->id))',
            'visible' => isVisible('sourceurl', $dparr) && ($iostatus != 1 && $iostatus != 2),
        ),
        array(
            'name' => 'publication_pending',
            'type' => 'raw',
            'value' => 'CHtml::checkBox("publication_pending[]", $data->publication_pending)',
            'visible' => isVisible('publication_pending', $dparr) && ($iostatus == 3),
        ),

        array(
            'name' => 'livedate',
            'type' => 'raw',
            'value' => 'in_array($data->iostatus, array(4,21,5,31,32)) || '."$ismarketer".' ? $data->livedate : CHtml::textField("livedate[]", $data->livedate, array("id"=>"livedate_".$data->id, "readOnly"=>"readOnly"))',
            //'visible' => isVisible('livedate', $dparr) && ($iostatus == 3),
            //#2015-10-22#'visible' => isVisible('livedate', $dparr) && ($iostatus == 3 || $iostatus == 5),
            'visible' => isVisible('livedate', $dparr) && in_array($iostatus, array(3,5,31,32)),
        ),
        array(
            'name' => 'tierlevel_built',
            'type' => 'raw',
            'value' => (isset($roles['Admin']) || isset($roles['InternalOutreach']) || isset($roles['Outreach'])) ? 'CHtml::dropDownList("tierlevel_built[]", $data->tierlevel_built, '.$tierstr.')' : 'Utils::getValue(' . $tierstr . ', $data->tierlevel_built)',
            'filter' => $tiers,
            //'visible' => isVisible('tierlevel_built', $dparr) && ($iostatus == 3 || $iostatus == 5),
            'visible' => isVisible('tierlevel_built', $dparr) && ($iostatus == 5),
        ),
        array(
            'name' => 'assisted_by',
            'type' => 'raw',
            //'value' => 'Utils::getValue(' . $channelstr . ', $data->assisted_by)',
            'value' => 'CHtml::dropDownList("assisted_by[]", $data->assisted_by, '.$channelstr.')',
            'filter' => $channels,
            'visible' => isVisible('assisted_by', $dparr) && ($iostatus == 5) && ($isadmin || $isoutreacher),
        ),
        array(
            'name' => 'channel_id',
            'type' => 'raw',
            'value' => 'Utils::getValue(' . $channelstr . ', $data->channel_id)',
            'filter' => $channels,
            'visible' => isVisible('channel_id', $dparr) && !($ispublisher || $isoutreacher),
            'footer' => ($iostatus == 5 && (isset($roles['Admin']) || isset($roles['InternalOutreach']))) ? "Total Spent:" : null,
        ),
        array(
            'name' => 'spent',
            'type' => 'raw',
            'value' => ($iostatus == 3 || $iostatus == 5) ? '"$".CHtml::textField("spent[]", $data->spent, array("size"=>"6"))' : '$data->spent',
            'visible' => isVisible('spent', $dparr) && ($iostatus == 3 || $iostatus == 5) && (isset($roles['Admin']) || isset($roles['InternalOutreach'])),
            'htmlOptions'=>array('nowrap'=>'nowrap',),
            'footer' => ($iostatus == 5) ? $model->totalspent() : null,
        ),
        /*
        array(
            'name' => 'client_request',
            'type' => 'raw',
            'value' => 'CHtml::link("Client Request", "javascript:void(0);", array("class"=>"clientrequest"))',
            'visible' => isVisible('client_request', $dparr),
        ),
        */
        array(
            'name' => 'duedate',
            'type' => 'raw',
            'value' => '$data->duedate ? date("M/d/Y",strtotime($data->duedate)) : ""',
            'visible' => ($iostatus != 3),
        ),
        array(
            'name' => 'iodate',
            'header' => 'Date '.$iolabel,
            'value' => '$data->iodate ? $data->iodate : "00-00-0000"',
        ),

		array(
			'class'=>'CButtonColumn',
            //##'template'=>'{note} {rating} {accept} {approve} {rewind} {repair} {complete} {denywithreason} {deny}',
            'template'=>'{note} {rating} {iohistoric} {accept} {approve} {rebuild} {rewind} {rewindinapprove} {repair} {complete} {ioblacklist} {denywithreason}',
            'buttons' => array(
                'accept' => array(
                    'label' => 'Accept Or Resolved',
                    'imageUrl' => $_themebaseurl.'/css/gridview/accept.png',
                    //##'visible' => "'$isvisible' ||  (('$isadmin' || '$ismarketer') && " . '($data->iostatus == 4))',
                    'visible' => "'$isvisible' ||  ('$isadmin' && " . '($data->iostatus == 4 || $data->iostatus == 2)) || ($data->iostatus==4' . " && '$ismarketer')",
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>($data->iostatus==2)?21:2))',
                    'options' => array(
                        'name'=>'ioaccept',
                    ),
                ),

                'approve' => array(
                    'label' => ($iostatus == 32) ? "Move it to Pre QA" : 'Approve',
                    'imageUrl' => $_themebaseurl.'/css/gridview/approve.png',
                    'visible' => "( (('$ispublisher' || '$isoutreacher') && " . '$data->iostatus == 2) || ($data->iostatus == 21 && '."('$isadmin' || '$ismarketer')) || (('$isadmin' || '$isoutreacher') && ".'($data->iostatus == 31||$data->iostatus == 32)'.") )",
                    //'visible' => "($isadmin && " . 'in_array($data->iostatus, array(2,21))) || '."($ispublisher && ".'in_array($data->iostatus, array(2,21)))',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$nextvalue.'))',
                    'options' => array(
                        'name'=>'ioaccept',
                    ),
                ),

                'rewind' => array(
                    'label' => $rewindlabel,
                    'imageUrl' => $_themebaseurl.'/css/gridview/rewind.png',
                    //#'visible' => '($data->iostatus == 3 || ( ($data->iostatus == 32 || $data->iostatus==21) && '."$isoutreacher".') || '. "(('$isadmin' || '$isoutreacher') && ".'$data->iostatus == 31) )',
                    //##2015-07-20###'visible' => '( ( ($data->iostatus == 32 || $data->iostatus==21) && '."$isoutreacher".') || '. "(('$isadmin' || '$isoutreacher') && ".'$data->iostatus == 31) )',
                    'visible' => '( ( $data->iostatus==21 && '."$isoutreacher".') || '. "(('$isadmin' || '$isoutreacher') && ".'($data->iostatus == 31 || $data->iostatus == 32) ) )',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$rewindvalue.'))',
                    'options' => array(
                        'name'=>'ioaccept',
                    ),
                ),

                'rewindinapprove' => array(
                    'label' => $rewindlabel,
                    'imageUrl' => $_themebaseurl.'/css/gridview/rewind.png',
                    'visible' => '($data->iostatus == 3)',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$rewindvalue.'))',
                    'options' => array(
                        'name'=>'ioreason',
                    ),
                ),

                'complete' => array(
                    'label' => "Move it to completed directly",
                    'imageUrl' => $_themebaseurl.'/css/gridview/finish.png',
                    'visible' => "('$isadmin' && ".'$data->iostatus == 32'." )",
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>5))',
                    'options' => array(
                        'name'=>'ioaccept',
                    ),
                ),

                'repair' => array(
                    'label' => 'Repair it',
                    'imageUrl' => $_themebaseurl.'/css/gridview/repair.png',
                    'visible' => '( ($data->iostatus == 31 || $data->iostatus == 5) && '."'$isadmin'".')',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>32))',
                    'options' => array(
                        'name'=>'ioreason',
                    ),
                ),

                'ioblacklist' => array(
                    'label' => 'IO Blacklist',
                    'imageUrl' => $_themebaseurl.'/css/gridview/blacklist.png',
                    'url' => 'Yii::app()->createUrl("ioblacklist/create", array("domain"=>$data->desired_domain))',
                    'visible' => "($isadmin && ".'$data->iostatus == 21 && !empty($data->desired_domain)'.")",
                    'options' => array(
                        'name'=>'ioblacklist',
                    ),
                ),

                'denywithreason' => array(
                    'label' => 'Deny With Reason',
                    'imageUrl' => $_themebaseurl.'/css/gridview/deny2reason.png',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$denyvalue.'))',
                    //'visible' => "'$isvisible' || (($ismarketer && ".'$data->iostatus == 21'."))",
                    'visible' => " (( ('$ispublisher' || '$isoutreacher') && " . '$data->iostatus == 2) || ($data->iostatus == 21 && '."'$isadmin')) || (($ismarketer && ".'$data->iostatus == 21'."))",
                    'options' => array(
                        'name'=>'ioreason',
                    ),
                ),

                /*
                'ideationrevision' => array(
                    'label' => 'Ideation Revision',
                    'imageUrl' => $_themebaseurl.'/css/gridview/revision.png',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"content_step","attrvalue"=>4))',
                    'visible' => "('$isoutreacher' || '$isadmin') && (".'$data->iostatus == 2 || $data->iostatus == 3'.")",
                    'options' => array(
                        'name'=>'ideationrevision',
                    ),
                ),

                'deny' => array(
                    'label' => 'Deny the entire row',
                    'imageUrl' => $_themebaseurl.'/css/gridview/deny.png',
                    //'visible' => "'$isvisible' || ($isadmin && " . '($data->iostatus == 2 || $data->iostatus == 21))',
                    //'visible' => "'$isvisible' || (($ispublisher && " . '$data->iostatus == 2) || ($data->iostatus == 21 && '.$isadmin.'))',
                    'visible' => "'$isvisible' || (( ('$ispublisher' || '$isoutreacher') && " . '$data->iostatus == 2) || ($data->iostatus == 21 && '."'$isadmin'))",
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"iostatus","attrvalue"=>'.$denyvalue.'))',
                    'options' => array(
                        'name'=>'iodeny',
                    ),
                ),
                */
                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => $_themebaseurl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data))),
                    'click' => "function(){
                        addNote(this);
                        return false;
                    }",
                ),
                'rating' => array(
                    'label' => 'Add Content Rating',
                    'imageUrl' => $_themebaseurl.'/css/gridview/star-off.png',
                    'url' => 'Yii::app()->createUrl("task/view", array("id"=>$data->id))',
                    'options' => array('id'=>$this->evaluateExpression('$data->id', array('data'=>$data))),
                    //'visible' => 'in_array($data->iostatus, array(21,3,5)) && '."('$isadmin' || '$isoutreacher')",
                    //'visible' => "('$isadmin' || '$isoutreacher')",
                    'visible' => "!'$ismarketer' && $iostatus != 31",
                    'click' => "function(){
                        addRating(this);
                        return false;
                    }",
                ),
                'rebuild' => array(
                    'label' => 'Complete - Rebuild',
                    'imageUrl' => $_themebaseurl.'/css/gridview/rebuild.png',
                    'url' => 'Yii::app()->createUrl("task/setattr", array("id"=>$data->id,"attrname"=>"rebuild","attrvalue"=>1))',
                    'visible' => "!'$ismarketer' && ( ($iostatus==31) || ($iostatus==32) || ($iostatus == 5 && ".'$data->rebuild==0) )',
                    'options' => array(
                        'name'=>'iorebuild',
                    ),
                ),
                'iohistoric' => array(
                    'label' => 'IO History',
                    'imageUrl' => $_themebaseurl.'/css/gridview/viewall.png',
                    'url' => 'Yii::app()->createUrl("ios/historic", array("domain_id"=>$data->desired_domain_id,"desired_domain"=>$data->desired_domain,"currentaction"=>"denied"))',
                    //'visible' => "'$isadmin' && $iostatus == 21",
                    'visible' => "false",
                    'options' => array(
                        'name'=>'iohistoric',
                    ),
                ),
            ),
            'htmlOptions'=>array(
                'nowrap'=>'nowrap',
            )
        ),
	),
)); ?>

<div class="clear"></div>

<!-- bulk-io-form -->
<?php
if (($iostatus == 1 && $isvisible) || ($iostatus == 21 && !isset($roles['Marketer']))) {
    $this->renderPartial('/task/_bulkoprios',array(
        'model'=>$model,
        'roles'=>$roles,
        'iostatus'=>$iostatus,
    ));
}
?>
<!-- bulk-io-form -->


<?php
$this->renderPartial('/task/_denyreason',array(
	'model'=>$model,
    'roles'=>$roles,
    'iostatus'=>$iostatus,
));
?>

<?php
$this->renderPartial('/task/_ioblacklist',array(
    'roles'=>$roles,
    'clients'=>$clients,
));
?>

<?php
/*
if ( ($iostatus==2 || $iostatus==3) && ($isadmin || $isoutreacher) ) {
    $this->renderPartial('/task/_ideationrevision',array(
        'model'=>$model,
        'roles'=>$roles,
        'clients'=>$clients,
    ));
}
*/
?>

<div id="hiddenContainer">
    <div id="noteboxdiv" style="display:none;"></div>
    <div id="ratingboxdiv" style="display:none;margin:5px 10px;"></div>
    <div id="alliohboxdiv" style="display:none;"></div>
</div>

<style>
#ratinghint {
    background-color: #F8F8F8;
    border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
    padding: 2px 10px;
    display: inline-block;
    font-size: 1.8em;
    height: 27px;
    vertical-align: middle;
    width: 135px;
    color:red;
}
</style>

<script type="text/javascript">
//This parameter control the toggle()
var lastclickid = 0;
function hideAllBox(){
    $("#noteboxdiv,#ratingboxdiv,#alliohboxdiv").hide();
    $("#noteboxdiv,#ratingboxdiv,#alliohboxdiv").appendTo($("#hiddenContainer"));
    /*
    $("#noteboxdiv").hide();
    $("#noteboxdiv").appendTo($("#hiddenContainer"));
    $("#ratingboxdiv").hide();
    $("#ratingboxdiv").appendTo($("#hiddenContainer"));
    $("#alliohboxdiv").hide();
    $("#alliohboxdiv").appendTo($("#hiddenContainer"));
    */

    return true;
}

function parseIdFromUrl(s){
    s = s.match(/&id=\d+/g);
    s = String(s);

    return s = s.replace(/&id=/,"");
}

$(document).ready(function() {
    $("#Task_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:5}).multiselectfilter();
    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo $_themebaseurl; ?>" + '/images/loading.gif)');
        //$(this).show();
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
        //$(this).hide();
    });

    $("#downloadIO").click(function(){
        //var url = "<?php echo Yii::app()->createUrl('/download/ios');?>";
        var url = "<?php echo Yii::app()->createUrl('/ios/download');?>";
        var rparent = $('#taskSearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#taskSearchForm input[type=hidden][name=r]').remove();
        //alert($('#inventorySearchForm').serialize());
        url = url + "&" + $('#taskSearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        //$("a[name^='ioaccept'],a[name^='ioreason'],a[name^='iodeny']").each(function() {
        $("a[name^='ioaccept'],a[name^='iodeny'],a[name^='iorebuild']").each(function() {
            $(this).unbind('click').click(function(){
                if(!confirm('Are you sure you want to '+$(this).attr("title")+'?')) return false;
                //###!!!move the notebox back to the hidden container. incase it was removed when the user deny/accept.!!

                hideAllBox();//!!Hide All Box

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

                return false;
            });
        });


        $("a[name^='ioreason']").each(function() {
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

        $("a[name^='ioblacklist']").each(function() {
            $(this).click(function(){
                var newhref = $(this).attr('href');
                newhref = newhref.match(/&domain=[\s|\S]+/g);
                newhref = String(newhref);
                newhref = newhref.replace(/&domain=/,"");
                $("#Ioblacklist_domain").val(newhref);
                $( "#ioblacklist-form" ).dialog( "open" );
                return false;
            });
        });

        $("a[name^='ideationrevision']").each(function() {
            $(this).click(function(){
                var newhref = $(this).attr('href');
                currenttrid = parseIdFromUrl(newhref);
                $("#revisiontaskid").val(currenttrid);
                $( "#revisionreason-form" ).dialog( "open" );

                return false;
            });
        });

        $("input[name^='publication_pending'],input[name^='siteonly']").each(function() {
            $(this).unbind('click').click(function(){
                //DO NOT REMOVE THIS LINE;
                $("#alliohboxdiv").hide();
                $("#alliohboxdiv").appendTo($("#hiddenContainer"));

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

        $("select[name^='tierlevel_built'],select[name^='content_category_id'],select[name^='assisted_by']").each(function() {
            $(this).unbind('click').change(function(){
                $("#alliohboxdiv").hide();
                $("#alliohboxdiv").appendTo($("#hiddenContainer"));

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

        var __desired_domains = [];
        $("input[name^='desired_domain']").each(function(){
            __desired_domains.push(this.value);
        });

        $("input[name^='desired_domain'],input[name^='sourceurl'],input[name^='rewritten_title'],input[name^='spent'],input[name^='targeturl'],input[name^='anchortext']").each(function() {

            $(this).unbind('blur').blur(function(){
                $("#alliohboxdiv").hide();
                $("#alliohboxdiv").appendTo($("#hiddenContainer"));

                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                //##########turn it off, remove follwing: start###############//
                if ($(thistd).attr('name') == "desired_domain[]"){
                    this.value = stripURL2Domain(this.value);
                    var pressdel = false;

                    if (this.value == "" && __desired_domains[gvoffset] != ""){
                        //alert(pressdel);
                        if(!confirm("Are you sure you want to remove this")) {
                            this.value = __desired_domains[gvoffset];
                            //alert(gvoffset);
                            $(thistd).css("background-color","#66ff00");
                            return false;
                        }
                    }

                    /*
                    $(thistd).keydown(function(e){
                        if (e.keyCode == 46 || e.keyCode == 8){
                            pressdel = true;
                        } else {
                            pressdel = false;
                        }
                    });
                    if (pressdel && this.value == ""){
                        alert(pressdel);
                        if(!confirm("Are you sure you want to remove this")) return false;
                    }
                    */

                    if (this.value && !isValidDomain(this.value)){
                        //alert(this.value);
                        $(thistd).css("background-color","red");
                        alert("Please enter a valid domain here.");
                        this.value = __desired_domains[gvoffset];
                        return ;
                    }
                }

                //##########turn it off, remove above: end###############//
                /*
                var _attrvalue = this.value;
                if ($(thistd).attr('name') == "rewritten_title[]") {
                    _attrvalue = encodeURI(this.value);
                }
                */
                var _attrvalue = encodeURIComponent(this.value);
                //I will reference this article for the url encode:http://unixpapa.com/js/querystring.html

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/task/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+_attrvalue,
                    'success':function(data){
                        //donothing for now;
                        if ($(thistd).attr('name') == "desired_domain[]"){
                            __desired_domains[gvoffset] = data.desired_domain;
                            //alert(data.desired_domain);
                            //alert(__desired_domains);
                        }
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                            if ($(thistd).attr('name') == "desired_domain[]"){
                                //if (typeof(data.makeitblue) != "undefined" && data.makeitblue){
                                if (data.makeitblue){
                                    $(thistd).css("background-color","blue");
                                }
                                if (data.allowduplicate == 1) {
                                    alert(data.msg);
                                }
                            }

                            if ($(thistd).attr('name') == "sourceurl[]" && data.desired_domain){
                                if (data.makeitred){
                                    $(thistd).css("background-color","red");
                                }
                                var ddelem = $(thistd).parent().parent().find('input[name^="desired_domain"]');
                                var _data_dd = data.desired_domain;
                                if (ddelem.size()){
                                    if (!ddelem.val()) {
                                        ddelem.val(_data_dd);
                                        ddelem.blur();
                                        /*
                                        ddelem.focus();
                                        $(thistd).focus();
                                        //then same as ddelem.blur();
                                        */
                                    } else {
                                        if (ddelem.val() != _data_dd)
                                            alert("The old desired domain not equal the domain of post url, is that ok?");
                                    }
                                } else {
                                    // if we choose the order of the columns, we need change here also
                                    $(thistd).parent().prev().prev().html(_data_dd);
                                    $.ajax({
                                        'type': 'GET',
                                        'dataType': 'json',
                                        'url': "<?php echo Yii::app()->createUrl('/task/setattr');?>",
                                        'data': 'id='+currenttrid+"&attrname=desired_domain[]&attrvalue="+_data_dd,
                                        'success':function(data){
                                        },
                                        'complete':function(XHR,TS){XHR = null;}
                                    });
                                }

                                var livedateelem = $(thistd).parent().parent().find('input[name^="livedate"]');
                                if ($(thistd).val().length > 0 && livedateelem.size() && livedateelem.val().length == 0){
                                    livedateelem.css("border", "1px solid red");
                                }
                            }
                        } else {
                            //####################################
                            /*
                            if ($(thistd).attr('name') == "sourceurl[]"){
                            }
                            */
                            //####################################

                            $(thistd).css("background-color","red");
                            
                            if ($(thistd).attr('name') == "desired_domain[]"){
                                if (data.allowduplicate == 0) {
                                    $(thistd).css("background-color","red");
                                    alert(data.msg);
                                    $(thistd).val("");
                                    return false;
                                }

                                if (data.isblacklist == 1){
                                    alert(data.msg);
                                } else {
                                    /*
                                    if(confirm(data.msg)) {
                                        $(thistd).css("background-color","yellow");
                                        $.ajax({
                                            'type': 'GET',
                                            'dataType': 'json',
                                            'url': "<?php echo Yii::app()->createUrl('/task/setattr');?>",
                                            'data': 'id='+currenttrid+"&forcechange=1&attrname=desired_domain[]&attrvalue="+_attrvalue,
                                            'success':function(data){
                                                //$(thistd).css("background-color","yellow");
                                            },
                                            'complete':function(XHR,TS){XHR = null;}
                                        });
                                    } else {
                                        $(thistd).val("");
                                        //$(thistd).css("background-color","blue");
                                        return false;
                                    }
                                    */

                                    $(thistd).css("background-color","red");
                                    alert(data.msg);
                                    return false;
                                }
                            } else {
                                alert(data.msg);
                            }
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            });

        });

        $("input[name^='livedate'],input[name^='sentdate']").each(function() {
            $(this).datepicker({ dateFormat: "yy-mm-dd" });
            if ($(this).attr('name') == "livedate[]" && $(this).val().length <= 1) {
                var posturlid = $(this).attr('id').replace('livedate','sourceurl');
                posturlid = "#" + posturlid;
                if ($(posturlid).val().length > 3){
                    //#alert($(posturlid).val().length)
                    $(this).css("border", "1px solid red");
                }
            }

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
                $("#alliohboxdiv").hide();
                $("#alliohboxdiv").appendTo($("#hiddenContainer"));

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

        $("a[name^='iohistoric']").each(function(){
            $(this).unbind('click').click(function(){
                var _isshow = false;
                if ($("#alliohboxdiv").is(":visible")) {
                    _isshow = true;
                }

                //DO NOT REMOVE THIS LINE!!
                hideAllBox();

                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
                var thistd = $(this);

                /*
                $("#ratingboxdiv").hide();
                $("#noteboxdiv").hide();
                */
                if (lastclickid == currenttrid || lastclickid == 0) {
                    //$("#alliohboxdiv").toggle();
                    if (!_isshow) $("#alliohboxdiv").show();
                } else {
                    $("#alliohboxdiv").show();
                }

                if ($("#alliohboxdiv").is(":visible")) {
                    $.ajax({
                        'type': 'GET',
                        'dataType': 'html',
                        'url': thistd.attr('href'),
                        'data': 'id='+currenttrid+"&ajax=true",
                        'success':function(data){
                            $("#alliohboxdiv").html(data);
                        },
                        'complete':function(XHR,TS){XHR = null;}
                    });

                    if ($("#"+currenttrid+"_dtr").length>0) {
                        /*
                        here you couldn't use the find("td"), coz it will search all of the posterity td elements,
                        The .find() and .children() methods are similar,
                        but .children() only travels a single level down the DOM tree.
                        */
                        $("#alliohboxdiv").appendTo($("#"+currenttrid+"_dtr").children("td"));
                    } else {
                        var tdlength = $("table.items tr:first > th").length;
                        var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
                        //###!!!You should put this line before you appendTo #alliohboxdiv to the "td" in this case.
                        $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
                        //###!!!DO NOT CHANGE THE SORTING OF THIS PECIES CODE!
                        $("#alliohboxdiv").appendTo(vartr.children("td"));
                        thistd.parent().parent().after(vartr);
                    }
                } else {}

                lastclickid = currenttrid;
                return false;
            });
        });

        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        $("div.pager > ul > li a, table.items thead tr.filters input, table.items thead tr.filters select").each(function(){
            $(this).click(function() {
                hideAllBox();//!!Hide All Box
            });
            return true;
        });

        $("table.items #pageSize").change(function(){
            hideAllBox();//!!Hide All Box

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
                            $("#etr" +v+" > td:last > a.note > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/notes.png");
                        });
                    }

                    if (data.freshnote) {
                        $.each(data.freshnote, function (v){
                            $("#etr" +v+" > td:last > a.note > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/freshnote.png");
                        });
                        //alert($.inArray(v, data.freshnote));
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/taskRating/icon');?>",
            'data': {'ids[]': _ids},
            'success':function(data){
                //alert(data.msg);
                if (data.success){
                    if (data.ids){
                        $.each(data.ids, function (v){
                            //alert(v);
                            $("#etr" +v+" > td:last > a.rating > img").attr("src", "<?php echo $_themebaseurl?>/css/gridview/star-on.png");
                        });
                    }
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
        //------------------------------------------------//
    }


    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});

function addNote(t) {
    $("#ratingboxdiv").hide();
    $("#alliohboxdiv").hide();
    $("#alliohboxdiv").appendTo($("#hiddenContainer"));

    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    //var currentdomain = $(t).parent().parent().children("td:eq(1)").text();

    if (lastclickid == currenttrid || lastclickid == 0) {
        $("#noteboxdiv").toggle();
    } else {
        $("#noteboxdiv").show();
    }

    if ($("#noteboxdiv").is(":visible")) {

        $.ajax({
            'type': 'GET',
            //'dataType': 'json',
            'dataType': 'html',
            'url': "<?php echo Yii::app()->createUrl('/taskNote/note');?>",
            'data': 'task_id='+currenttrid+"&ajax=true",
            'success':function(data){
                $("#noteboxdiv").html(data);
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        if ($("#"+currenttrid+"_dtr").length>0) {
            /*
            here you couldn't use the find("td"), coz it will search all of the posterity td elements,
            The .find() and .children() methods are similar,
            but .children() only travels a single level down the DOM tree.
            */
            $("#noteboxdiv").appendTo($("#"+currenttrid+"_dtr").children("td"));
        } else {
            var tdlength = $("table.items tr:first > th").length;
            var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#noteboxdiv").appendTo(vartr.find("td"));
            $("#noteboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}

function addRating(t) {
    //http://wbotelhos.com/raty/
    $("#noteboxdiv").hide();
    $("#alliohboxdiv").hide();
    $("#alliohboxdiv").appendTo($("#hiddenContainer"));

    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    //var currentdomain = $(t).parent().parent().children("td:eq(1)").text();

    if (lastclickid == currenttrid || lastclickid == 0) {
        $("#ratingboxdiv").toggle();
    } else {
        $("#ratingboxdiv").show();
    }

    if ($("#ratingboxdiv").is(":visible")) {

        $.ajax({
            'type': 'GET',
            //'dataType': 'json',
            'dataType': 'html',
            'url': "<?php echo Yii::app()->createUrl('/taskRating/rating');?>",
            'data': 'task_id='+currenttrid+"&ajax=true",
            'success':function(data){
                $("#ratingboxdiv").html(data);
            },
            'complete':function(XHR,TS){XHR = null;}
        });

        if ($("#"+currenttrid+"_dtr").length>0) {
            /*
            here you couldn't use the find("td"), coz it will search all of the posterity td elements,
            The .find() and .children() methods are similar,
            but .children() only travels a single level down the DOM tree.
            */
            $("#ratingboxdiv").appendTo($("#"+currenttrid+"_dtr").children("td"));
        } else {
            var tdlength = $("table.items tr:first > th").length;
            var vartr = $('<tr><td colspan="'+tdlength+'"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            //$("#ratingboxdiv").appendTo(vartr.find("td"));
            $("#ratingboxdiv").appendTo(vartr.children("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}

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