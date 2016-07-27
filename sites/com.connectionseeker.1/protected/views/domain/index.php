<?php
$this->breadcrumbs=array(
	'Domains'=>array('index'),
	'Manage',
);

$mailvisible = false;

//Yii::app()->request->getQuery this return the $_GET. Yii::app()->request->getPost this return the $_POST 
if (Yii::app()->request->getParam("touched") == true) {
    $dataProvider = $model->touched()->search();
    $mailvisible = true;
    $h1title = "Outreach List";
} else {
    $dataProvider = $model->search();
    $h1title = "Manage Domains";
}

$types = Types::model()->actived()->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$stypes = $gtps['site'];
$otypes = $gtps['outreach'];
//$stypestr = CVarDumper::DumpAsString($stypes);
$stypestr = Utils::array2String(array("" => '[Site Type]') + $stypes);
$otypestr = Utils::array2String(array("" => '[Outreach Type]') + $otypes);

$touchedstatus = Domain::$status;
$statusstr = Utils::array2String($touchedstatus);
//echo $statusstr;

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/xheditor/xheditor-1.1.12-en.min.js', CClientScript::POS_HEAD);

$cs->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('domain-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>


<div class="form">
    <div style="float:left;width:630px;"><h1><?php echo $h1title;?></h1></div>
    <div id="processing" style="float:left;width:220px;">&nbsp;</div>
    <?php if ($mailvisible) { ?>
	<div class="row buttons">
		<?php echo CHtml::ajaxSubmitButton("Send Queued Email", Yii::app()->createUrl('email/send'),
        array('type'=>'POST',
        'data'=>'actiontype=sendall',
        'dataType'=>'json',
        'success' => 'function(data){$("#processing").html(data.message)}')); ?>
	</div>
    <?php }?>
    <div class="clear"></div>
</div>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<!-- search-form -->
<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'touchedstatus'=>$touchedstatus,
	'stypes'=>$stypes,
	'otypes'=>$otypes,
)); ?>
</div><!-- search-form -->

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'domain-grid',
	'dataProvider'=>$dataProvider,
	'filter'=>$model,
    'selectableRows' => '2',
	'columns'=>array(
		//'id',
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
		'domain',
		//'stype',
        array(
            'name' => 'stype',
            'type' => 'raw',
            //'value' => 'CHtml::dropDownList("stype", $data->stype, '.$stypestr.', array("onchange"=>"updateType(this);"))',
            'value' => 'CHtml::dropDownList("stype", $data->stype, '.$stypestr.')',
            'filter' => $stypes,
        ),
        array(
            'name' => 'googlepr',
            'htmlOptions'=>array('width'=>'30px'),
        ),
		//'googlepr',
		//'otype',

        array(
            'name' => 'otype',
            'type' => 'raw',
            //'value' => 'CHtml::dropDownList("otype[]", $data->otype, '.$otypestr.', array("onchange"=>"updateType(this);"))',
            'value' => 'CHtml::dropDownList("otype", $data->otype, '.$otypestr.')',
            'filter' => $otypes,
        ),

		/*
		'tld',
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
		'touched',
		'touched_by',
		'created',
		'created_by',
		'modified_by',
		*/
		//'touched_by',
        array(
            'name' => 'touched_by',
            'type' => 'raw',
            //'value' => 'CHtml::link(CHtml::encode($data->touchedby->username), array("user/view", "id" =>$data->touched_by))',
            'value' => 'CHtml::encode($data->touchedby->username)',
            'filter' => CHtml::listData(User::model()->findAll(),'id','username'),
        ),
        array(
            'name' => 'touched_status',
            'type' => 'raw',
            //'value' => 'CHtml::encode(Utils::getValue(' . $statusstr . ', $data->touched_status))',
            'value' => 'CHtml::dropDownList("touched_status", $data->touched_status, '.$statusstr.')',
            'filter' => $touchedstatus,
        ),
		//'touched_status',
		'modified',
		array(
			'class'=>'CButtonColumn',
            'template'=>'{email} {note} {view} {update}',
            'buttons' => array(
                'email' => array(
                    'label' => 'Mail To',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/email.png',
                    'visible' => "$mailvisible",
                    'url' => 'Yii::app()->createUrl("domain/view", array("id"=>$data->id))',
                    'click' => "function(){
                        addMail(this);
                        return false;
                    }",
                ),
                'note' => array(
                    'label' => 'Add Notes',
                    'imageUrl' => Yii::app()->theme->baseUrl.'/css/gridview/note.png',
                    'url' => 'Yii::app()->createUrl("domain/view", array("id"=>$data->id))',
                    'click' => "function(){
                        addNote(this);
                        return false;
                    }",
                ),
            ),
            'htmlOptions'=>array('nowrap'=>'nowrap'),
		),
	),
)); ?>

<div id="hiddenContainer">

    <?php $this->renderPartial('_mail',array(
        'model'=>$model,
        'touchedstatus'=>$touchedstatus,
        'stypes'=>$stypes,
        'otypes'=>$otypes,
    )); ?>

    <div id="noteboxdiv" style="display:none;">
    </div>

</div>

<script type="text/javascript">
$(document).ready(function() {

    $('#processing').bind("ajaxSend", function() {
        $(this).html("&nbsp;");
        $(this).css('background-image', 'url(' + "<?php echo Yii::app()->theme->baseUrl; ?>" + '/images/loading.gif)');
        //$(this).show();
    }).bind("ajaxComplete", function() {
        $(this).css('background-image', '');
        $(this).css('color', 'red');
        //$(this).hide();
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate = function(){
        //jquery onchange will firing event twice in ie7, So we need hack it as following.
        $("select[name='stype'],select[name='otype'],select[name='touched_status']").each(function() {
            //way 1
            //$(this).change(updateType);
            //$(this).unbind('click').change(updateType);

            //way 2
            $(this).unbind('click').change(function(){
                var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
                var gvoffset = $(this).parent().parent().prevAll().length;
                var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);

                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/domain/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
                        alert(data.msg);
                    }
                });
            });


            /*
            //bind one times handler in the object.
            $(this).one(
                'change',
                function() {}
            )
            */

        });


        //issue: when the user click the pager(i mean change pager number), then the mailbox & notebox may not showing again.
        //cause we append the mailbox & notebox into the grid already when we click the addmail or addnote button,
        //so when we jump to another outreach page number, the mailbox & notebox may be removed due to the grid overwrited.
        $("div.pager > ul > li a").each(function(){
            $(this).click(function() {
                $("#mailboxdiv").hide();
                $("#noteboxdiv").hide();
                $("#mailboxdiv").appendTo($("#hiddenContainer"));
                $("#noteboxdiv").appendTo($("#hiddenContainer"));

            });
            return true;
        });

    }

    $.fn.yiiGridView.defaults.afterAjaxUpdate();

});


//jquery onchange will firing event twice in ie7, So we couldn't use this way.
function updateType(){
    //alert(this.value);
    var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(this).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);


    $.ajax({
        'type': 'GET',
        'dataType': 'json',
        //'url': "<?php echo CHtml::normalizeUrl(array('/domain/setattr'));?>",
        'url': "<?php echo Yii::app()->createUrl('/domain/setattr');?>",
        'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
        'success':function(data){
            //donothing for now;
            alert(data.msg);
        }
    });

    return false;
}

var lastclickid = 0;

function addMail(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var currentdomain = $(t).parent().parent().children("td:eq(1)").text();
    //$("#currentdomain").text(currentdomain);
    //alert(currentdomain);
    var lc = top.location;

    $("#noteboxdiv").hide();
    if (lastclickid == currenttrid || lastclickid == 0) {
        $("#mailboxdiv").toggle();
    } else {
        $("#mailboxdiv").show();
    }

    //$("#mailboxdiv:visible");
    if ($("#mailboxdiv").is(":visible")) {
        $("#ifr_webpreview").attr({'src': 'http://www.'+currentdomain});
        $("#mail_domain_id").val(currenttrid);

        $("#lnabout").attr({'href': 'http://www.'+currentdomain});
        $("#lnwhois").attr({'href': 'http://who.is/whois/'+currentdomain});
        //$("#mailto").val();

        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/domain/view');?>",
            'data': 'id='+currenttrid+"&ajax=true",
            'success':function(data){
                var rtn = "";
                $.each(data, function(lb, v){
                    if ($.inArray(lb, ["domain","googlepr","creation","linkingdomains",
                                       "inboundlinks","indexedurls","alexarank","title","owner",
                                       "email","telephone","country","state","city","zip","street"]) >= 0){
                        rtn += lb + ": " + v + "<br />";
                    }
                });
                //alert(rtn);
                $("#domaininfo").html(rtn);

                //alert(data.id);
                $("#mailto").val(data.email);
            }
        });

        if ($("#"+currenttrid+"_dtr").length>0) {
            $("#mailboxdiv").appendTo($("#"+currenttrid+"_dtr").find("td"));
        } else {
            var vartr = $('<tr><td colspan="9"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            $("#mailboxdiv").appendTo(vartr.find("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

        //Uninstall the XHeditor first, Then reload(re-install) the WYSIWYG editor
        //these steps will help us keep the focus on the editor always, and can make the WYSIWYG editor always avaliable
        $('#message').xheditor(false);
        $('#message').xheditor({tools:'full',width:420,height:350}).focus();
    } else {}


    //top.location = lc;

    lastclickid = currenttrid;
}

function addNote(t) {
    var gvid = $(t).parent().parent().closest('.grid-view').attr('id');
    var gvoffset = $(t).parent().parent().prevAll().length;
    var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
    var currentdomain = $(t).parent().parent().children("td:eq(1)").text();

    $("#mailboxdiv").hide();
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
            'url': "<?php echo Yii::app()->createUrl('/domain/note');?>",
            'data': 'domain_id='+currenttrid+"&ajax=true",
            'success':function(data){
                $("#noteboxdiv").html(data);
            }
        });


        if ($("#"+currenttrid+"_dtr").length>0) {
            $("#noteboxdiv").appendTo($("#"+currenttrid+"_dtr").find("td"));
        } else {
            var vartr = $('<tr><td colspan="9"></td></tr>').attr({ 'id': currenttrid+"_dtr", 'class':"bltr"});
            $("#noteboxdiv").appendTo(vartr.find("td"));
            $(t).parent().parent().after(vartr);
            $('#'+gvid+' > div.keys > span:eq('+gvoffset+')').after("<span>"+currenttrid+"_dtr</span>");
        }

    } else {}

    lastclickid = currenttrid;
}

//$(window).bind('hashchange', function() {
//   alert("hello");
//});

//window.frames[0].open = function (e){return null;}
//window.frames[0].location.href = "http://www.angelfire.lycos.com/";
</script>