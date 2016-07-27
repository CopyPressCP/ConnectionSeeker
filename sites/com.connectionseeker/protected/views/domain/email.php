<?php
$this->breadcrumbs=array(
	'Domains'=>array('index'),
	'Email',
);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/xheditor/xheditor-1.1.12-en.min.js', CClientScript::POS_HEAD);

$types = Types::model()->actived()->bytype(array("site","outreach"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$stypes = $gtps['site'] ? $gtps['site'] : array();
$otypes = $gtps['outreach'] ? $gtps['outreach'] : array();

$touchedstatus = Domain::$status;
//$statusstr = Utils::array2String($touchedstatus);
?>

<script type="text/javascript">
<?php
$tplmodel = new Template;
$tplm = $tplmodel->findByAttributes(array("created_by"=>Yii::app()->user->id));
if ($tplm) {
    echo "var defaulttpl = ".$tplm->id.";";
} else {
    echo "var defaulttpl = 0;";
}
?>
</script>

<h1>Email this site (<?php echo $model->domain; ?>)</h1>
<br />
<?php //echo $this->renderPartial('_email', array('model'=>$model)); ?>

<?php $this->renderPartial('_mail',array(
    'model'=>$model,
    'touchedstatus'=>$touchedstatus,
    'stypes'=>$stypes,
    'otypes'=>$otypes,
    'tplm'=>$tplm,
)); ?>

<div id="notesdiv" style="display:none;">
    <div class="row" style="color:#e87129;">Domain Notes:</div>
    <div id="noteboxdiv"></div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#mailboxdiv").show();
    $("#ifr_webpreview").hide();
    $("#contactdiv").hide();

    $("#mail_domain_id").val(<?php echo $model->id;?>);
    $("#mailto").val("");

    $.ajax({
        'type': 'GET',
        'dataType': 'json',
        'url': "<?php echo Yii::app()->createUrl('/domain/view');?>",
        'data': 'id='+<?php echo $model->id;?>+"&ajax=true",
        'success':function(data){
            var rtn = "";
            $.each(data, function(lb, v){
                if ($.inArray(lb, ["domain","googlepr","creation","linkingdomains",
                                   "inboundlinks","indexedurls","alexarank","title","owner","owner2","touched_status","email",
                                   "primary_email","primary_email2","telephone","country","state","city","zip","street"]) >= 0){
                    //rtn += lb + ": " + v + "<br />";
                    if (lb == "primary_email") {
                        lb = "Primary Email";
                        $("#primary_email").val(v);
                    } else if (lb == "email") {
                        $("#email").val(v);
                    } else if (lb == "touched_status") {
                        $("#touched_status").val(v);
                    }
                    rtn += lb + ": " + v + "<br />";
                } else if (lb == "short_meta_keywords"){
                    $("#meta_keywords").html(v);
                } else if (lb == "short_meta_description"){
                    $("#meta_description").html(v);
                } else if (lb == "semrushkeywords"){
                    $("#semrushkeywords").html(v);
                } else if (lb == "lasttouchedby"){
                    $("#lasttouchedby").html(v);
                } else if (lb == "used_campaigns"){
                    var _usedcmp = "";
                    var _proclink = "<?php echo Yii::app()->createUrl('task/processing');?>";
                    $.each(v, function(_ckid, _cvname){
                        //alert(_cvname);
                        //_usedcmp += "<a href='"+_proclink+"&campaign_id="+_ckid+"' target='_blank'>"+_cvname+"</a><br />";
                        _usedcmp += _cvname+"<br />";
                    });
                    $("#used_campaigns").html(_usedcmp);
                } else {
                    //do nothing for now;
                    if ($.inArray(lb, ["spa_twitter_username","spa_facebook_username",
                                       "spa_ggplus_username","spa_linkedin_username"]) >= 0){
                        if (v != null) {
                            //alert(lb);
                            //alert(v);
                            var __b = lb.replace(/username/g, "url");
                            $("#"+lb).show();
                            $("#"+lb).attr("href", data[__b]);
                            $("#"+lb).attr("title", v);
                            //$("#"+lb).html(v);
                        } else {
                            $("#"+lb).hide();
                        }
                    }
                }
            });
            //alert(rtn);
            //$("#domaininfo").html(rtn);
            $("#domaininfo").append(rtn);

            //alert(data.id);
            //$("#mailto").val(data.email);
        },
        'complete':function(XHR,TS){XHR = null;}
    });

    /*
    * Set default template
    */
    if (defaulttpl > 0) {
        /*
        if ($("#template_id").val() != defaulttpl){
            $("#template_id").val(defaulttpl);
        }
        */
        $("#template_id").val(defaulttpl);
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('template/replacement');?>",
            'data': 'id='+defaulttpl,
            'success':function(data){
                $("#subject").val(data.subject);$("#message").val(data.content);
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    }



    $("#email,#primary_email,#touched_status").each(function() {
        var currfunction = function(){
            var currenttrid = $("#mail_domain_id").val();
            //alert(currenttrid);
            //alert($(this).attr('name'));
            var thistd = $(this);

            if (currenttrid)
            {
                $.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'url': "<?php echo CHtml::normalizeUrl(array('/domain/setattr'));?>",
                    'data': 'id='+currenttrid+"&attrname="+$(this).attr('name')+"&attrvalue="+this.value,
                    'success':function(data){
                        //donothing for now;
                        if (data.success){
                            $(thistd).css("background-color","yellow");
                            $(thistd).animate({backgroundColor: 'white'}, 8000);
                        } else {
                            $(thistd).css("background-color","red");
                            $(thistd).animate({backgroundColor: 'white'}, 8000);
                            alert(data.msg);
                        }
                    },
                    'complete':function(XHR,TS){XHR = null;}
                });
            }
        }

        if ($(this).attr('name') == 'touched_status') {
            $(this).unbind('click').change(currfunction);
        } else {
            $(this).unbind('blur').blur(currfunction);
        }
    });


    //Put the Notes List/Create Notes into the signal email page.
    $("#notesdiv").appendTo($("#additionaldiv"));
    $("#notesdiv").show();
    if ($("#noteboxdiv").is(":visible")) {
        $.ajax({
            'type': 'GET',
            //'dataType': 'json',
            'dataType': 'html',
            'url': "<?php echo Yii::app()->createUrl('/domain/note');?>",
            'data': 'domain_id='+<?php echo $model->id;?>+"&ajax=true",
            'success':function(data){
                $("#noteboxdiv").html(data);
                $("#noteboxdiv").css("padding", "0px");
                $("#noteformdiv").css("float", "left");
            },
            'complete':function(XHR,TS){XHR = null;}
        });

    } else {}
});
</script>
