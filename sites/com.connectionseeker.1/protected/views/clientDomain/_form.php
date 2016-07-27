<?php
//alterative
//http://www.36ria.com/demo/search/
//http://www.36ria.com/3388


$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//$cs->registerScriptFile(Yii::app()->baseUrl . '/js/timepicker/jquery.timePicker.min.js', CClientScript::POS_HEAD);

$cs->registerCssFile(Yii::app()->theme->baseUrl . '/css/gridview/styles.css');
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/css/gridview/jquery.yiigridview.js', CClientScript::POS_END);
//$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.ba-dotimeout.js', CClientScript::POS_END);
//$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.json-2.3.min.js', CClientScript::POS_END);

//outreach table list reference. 
//http://www.webdesignbooth.com/15-great-jquery-plugins-for-better-table-manipulation/
$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
} else {
    $clients = Client::model()->actived()->findAll();
}
?>

<div class="form leftform">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'client-domain-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model, $kymodel); ?>

  <div id="leftbasicinfo" style="width:320px;">
	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
		<?php //echo $form->textField($model,'client_id'); ?>
        <?php echo $form->dropDownList($model, 'client_id', CHtml::listData($clients,'id','company'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'use_historic_index'); ?>
		<?php echo $form->checkBox($model,'use_historic_index', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'use_historic_index'); ?>
	</div>
    <div class="clear"></div>
	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

    <div class="clear"></div>
	<div class="row" id="domain_keywords">
        <?php
        echo $form->labelEx($kymodel,'keyword');
        echo $form->error($kymodel,'keyword');
        $_t_count = 3;//default text filed count

        if (isset($model->id) && $model->id > 0) {
            $_kymodel = $kymodel->findAllByAttributes(array('domain_id'=>$model->id));
            $keywordcount = count($_kymodel);
            if ($keywordcount > 0) {
                foreach($_kymodel as $_kv) {
                    echo $form->textField($_kv,'keyword',array('size'=>60,'maxlength'=>255,'name'=>get_class($kymodel)."[keyword][ck_"."$_kv->id]"));
                }
            }
            $_t_count = $_t_count - $keywordcount;
        }

        if ($_t_count > 0) {
            for ($i = 0; $i < $_t_count; $i++) {
                echo $form->textField($kymodel,'keyword[]',array('size'=>60,'maxlength'=>255));
            }
        }
        ?>
	</div>
	<div class="row" id="add_more_keyword">
        <?php echo CHtml::link(Yii::t('Client', '+Add More'), 'javascript:void(0);'); ?>
	</div>
  </div><!-- end of leftbasicinfo -->

  <div id="middlebasicinfo" style="width:310px;">
     <div class="row" id="domain_competitors">
        <?php
        //echo $model->rcompetitor
        //print_r($model);
        //print_r($model->rcompetitor);

        echo $form->labelEx($cptmodel,'domain', array('label'=>'Competitor Domains'));
        echo $form->error($cptmodel,'domain');
        $_t_count = 6;//default text filed count
        if (isset($model->id) && $model->id > 0) {
            //$_kymodel = $cptmodel->findAllByAttributes(array('domain_id'=>$model->id));
            $_cptarr = $model->rcompetitor;
            /*
            $keywordcount = count($_cptmodel);
            if ($keywordcount > 0) {
                foreach($_cptmodel as $_kv) {
                    echo $form->textField($_kv,'domain',array('size'=>60,'maxlength'=>255,'name'=>get_class($_cptmodel)."[domain][ck_"."$_kv->id]"));
                }
            }
            */
            if (!empty($model->rcompetitor)) {
                $cptcount = count($model->rcompetitor);
                foreach ($model->rcompetitor as $ov) {
                    $tfarr = array();
                    $tfarr = array('size'=>60, 'maxlength'=>255, 'name'=>get_class($ov)."[domain][dk_"."$ov->id]");
                    if (!empty($ov->last_call_api_time)) $tfarr['readonly'] = 'readonly';
                    echo $form->textField($ov,'domain',$tfarr);
                }
            }

            $_t_count = $_t_count - $cptcount;
        }

        if ($_t_count > 0) {
            for ($i = 0; $i < $_t_count; $i++) {
                echo $form->textField($cptmodel,'domain[]',array('size'=>60,'maxlength'=>255));
            }
        }

        ?>
	</div>
	<div class="row" id="add_more_competitor">
        <?php echo CHtml::link(Yii::t('Client', '+Add More'), 'javascript:void(0);'); ?>
	</div>

  </div><!-- end of middlebasicinfo -->

    <div class="clear"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<div class="form rightform">
  <div id="rightbasicinfo" style="width:450px;">
	<div class="row">
    <?php echo Yii::t('Client', 'Search for competitors')?>
	</div>
	<div class="row">
    <?php echo Yii::t('Client', 'Enter Keyword')?>:
    <input type="text" id="GoogleSearch_keyword" name="GoogleSearch[keyword]" maxlength="255" size="60" style="width:210px;">

    <?php echo CHtml::link(Yii::t('Client', 'Search'), 'javascript:void(0);' ,
                           array('id' => 'cptsearch', 'class' => 'linkbutton')); ?>

	</div>

	<div class="row">
      <?php echo Yii::t('Client', 'Search Result')?>
      <div id="searchloading"></div>
	</div>
	<div class="row grid-view" id="rightscroll">
        <table id="livecpt" class="items">
        <thead><tr>
        <th class="checkbox-column"><input type="checkbox" class="select-on-check-all" value="1" name="ids_all" id="ids_all"></th>
        <th>Domain</th>
        <th>PR</th>
        <th>Alexa</th>
        <th>Age</th></tr></thead>
        </table>
	</div>


	<div class="row">
        <?php echo CHtml::link(Yii::t('Client', '+Add To Competitors'), 'javascript:void(0);', 
                               array('id' => 'add_to_competitor', 'class' => 'linkbutton')); ?>
	</div>


  </div><!-- end of middlebasicinfo -->
  <div style="clear:both"></div>


</div>

<?php
$tpl4kyinput = $form->textField($kymodel,"keyword[]",array("size"=>60,"maxlength"=>255));
$tpl4cptinput = $form->textField($cptmodel,"domain[]",array("size"=>60,"maxlength"=>255));
?>

<script type="text/javascript">
//var checkboxes = [];
/*

jQuery.fn.slowEach = function( interval, callback ) {
    var array = this;
    //alert(array.length);
    if( ! array.length ) return;
    var i = 0;
    next();
    function next() {
        if( callback.call( array[i], i, array[i] ) !== false )
            if( ++i < array.length )
                setTimeout( next, interval );
    }
};

// test code
function logit( i ) {
    //alert(i);
    console.log( this.id || this.className );
    if( i == 4 ) return false;
}

console.log( 'FAST' );
$('.row').each( logit );

console.log( 'SLOW' );
$('.row').slowEach( 5000, logit ); 


*/
function fmt(v) {
    if (typeof(v) == "undefined" || v == null){
        return -1;
    }

    return v;
}

$(document).ready(function() {

    $('#searchloading').bind("ajaxSend", function() {
            $(this).show();
    }).bind("ajaxComplete", function() {
            $(this).hide();
    });

    $('#add_more_keyword').click(function(){
        $('#domain_keywords').append('<?php echo $tpl4kyinput; ?>');
        $('#domain_keywords').append('<?php echo $tpl4kyinput; ?>');
    });
    $('#add_more_competitor').click(function(){
        $('#domain_competitors').append('<?php echo $tpl4cptinput; ?>');
        $('#domain_competitors').append('<?php echo $tpl4cptinput; ?>');
    });

    $('#rightscroll').yiiGridView({'ajaxUpdate':['rightscroll'],'ajaxVar':'ajax','pagerClass':'pager','loadingClass':'grid-view-loading','filterClass':'filters','tableClass':'items','selectableRows':'2','pageVar':'User_page'});

    $('#cptsearch').click(function(){
        $.ajax({
            'type':'GET',
            'dataType':'json',
            'url':"<?php echo CHtml::normalizeUrl(array('/googleSearch/index'));?>",
            'data':'GoogleSearch[keyword]='+$("#GoogleSearch_keyword").val(),
            'success':function(data){
                var tabletpl = $('<table></table>').attr({ id:"livecpt", class:"items"});
                var rowtpl = $('<thead><tr><th class="checkbox-column"><input type="checkbox" id="ids_all" name="ids_all" value="1" class="select-on-check-all"></th><th>Domain</th><th>Inbound Links</th><th>Linking Domains</th><th>PR</th><th>Alexa</th><th>Age</th></tr></thead>').appendTo(tabletpl);
                var trclass = "even";
                var ti = 0;
                var dmarr = [];
                $.each(data.cpt_domain,function(index,cptinfo){
                    console.log(cptinfo.domain);

                    trclass = (trclass == "odd") ? "even" : "odd";
                    rowtpl = $('<tr id="cpttr_'+ti+'"></tr>').attr({ class:trclass }).appendTo(tabletpl);
                    $('<td></td>').attr({class:"checkbox-column"}).html($('<input id="ids_'+ti+'" class="select-on-check" type="checkbox" name="ids[]" value="'+fmt(cptinfo.domain)+'">')).appendTo(rowtpl);
                    $('<td></td>').text(fmt(cptinfo.domain)).appendTo(rowtpl);
                    $('<td></td>').text(fmt(cptinfo.inboundlinks)).appendTo(rowtpl);
                    $('<td></td>').text(fmt(cptinfo.linkingdomains)).appendTo(rowtpl);
                    $('<td></td>').text(fmt(cptinfo.googlepr)).appendTo(rowtpl);
                    $('<td></td>').text(fmt(cptinfo.alexarank)).appendTo(rowtpl);
                    if (cptinfo.onlinesince > 0) {
                        var createdon = $.datepicker.formatDate('yy-mm-dd', new Date(cptinfo.onlinesince * 1000));
                    } else {
                        var createdon = 0;
                    }
                    $('<td></td>').text(createdon).appendTo(rowtpl);
                    dmarr.push(cptinfo.domain);

                    ti ++;
                });
                //tabletpl.appendTo($("#rightscroll"));
                $("#rightscroll").html(tabletpl);
                /*
                $.getScript("<?php echo Yii::app()->theme->baseUrl;?>/css/gridview/jquery.yiigridview.js", function(){
                    $('#rightscroll').yiiGridView({'ajaxUpdate':['rightscroll'],'ajaxVar':'ajax','pagerClass':'pager','loadingClass':'grid-view-loading','filterClass':'filters','tableClass':'items','selectableRows':'2','pageVar':'User_page'});
                });
                */

                //eval("var hell="+data.cpt_domain);

                //each是并行的，所以如果要在each调用ajax的话，一定要当心资源被相互篡改
                ti = 0;
                $.each(data.cpt_domain,function(index,cptinfo){
                    //cptinfo = data.cpt_domain[dmarr[ti]];
                    if (!(cptinfo.googlepr>0 && cptinfo.alexarank>0 && cptinfo.onlinesince>0)) {
                      //eachajax = false;
                      //var tridx;
                      $.ajax({
                        'type':'GET',
                         tridx : ti,
                         trdomain : cptinfo.domain,
                        'dataType':'json',
                        'url':"<?php echo CHtml::normalizeUrl(array('/googleSearch/status'));?>",
                        'data':'GoogleSearch[keyword]='+$("#GoogleSearch_keyword").val()+'&GoogleSearch[domain]='+cptinfo.domain,
                        'success':function(data){
                            //$.each(data, function(_i, _v){
                                //alert(_v.inboundlinks);
                            //});
                            var _v = data[this.trdomain];
                            $("#cpttr_"+this.tridx).addClass("ajaxliveupdate");
                            //$("#cpttr_"+this.tridx).children("td:gt(1)").addClass("ajaxliveupdate");

                            $("#cpttr_"+this.tridx).children("td:eq(2)").html(_v.inboundlinks);
                            $("#cpttr_"+this.tridx).children("td:eq(3)").html(_v.linkingdomains);
                            $("#cpttr_"+this.tridx).children("td:eq(4)").html(_v.googlepr);
                            $("#cpttr_"+this.tridx).children("td:eq(5)").html(_v.alexarank);
                            if (_v.onlinesince > 0) {
                                _v.onlinesince = $.datepicker.formatDate('yy-mm-dd', new Date(_v.onlinesince * 1000));
                            }
                            $("#cpttr_"+this.tridx).children("td:eq(6)").html(_v.onlinesince);

                            //alert(data.dm.inboundlinks);
                            //eachajax = true;
                            //console.log(this.tridx);
                         },
                      });
                      //console.log(ti);

                   }

                    ti ++;
                });

                //alert($.inArray('google.com',data.cpt_domain));
                //$("#rightscroll").html($.ajaxSettings.dataFilter(data.cpt_domain));
            },
            'cache':false
        });
    });//end of #cptsearch

    $('#add_to_competitor').click(function(){
        /*
        $("input[name='Competitor[domain][]']").each(function(i, e){
            // i means index for this object. e.value
            //alert($(this).attr('value'));
            //alert($(this).val());
            if ($(this).val() == "") {
                $(this).val("ddddddd");
            }
        });
        */

        var idx = 0;
        var chkbox = "";
        var $chks = $("input[name='ids[]'][type='checkbox']:checked");
        var chkslength = ($chks.length > 0) ? $chks.length : 0;
        var $domaincpt = $("input[name='Competitor[domain][]']");

        $domaincpt.each(function(i, e){
            // i means index for this object, e.value the same with $(this).val()
            if (e.value == "") chkslength--;
            $chks.each(function(){
                //no need use if (this.checked) {, cause the resutl which you select is checked filter already.
                if (e.value == $(this).attr('value')){
                    chkslength--;
                }
            });
        });

        if (chkslength >= 0) {
            for (var i=0; i < chkslength + 1; i++) {
                $('#domain_competitors').append('<?php echo $tpl4cptinput; ?>');
            }
        }

        //rebuild the competitor domain again
        $domaincpt = $("input[name='Competitor[domain][]']");
        //alert($domaincpt.length)
        $chks.each(function(){
            if (this.checked) {
                chkbox = $(this).attr('value');
                //check this domain is exist or not!
                $domaincpt.each(function(i, e){
                    // i means index for this object, e.value the same with $(this).val()
                    //if (e.value == "") chkslength--;
                    if (e.value == chkbox) {
                        chkbox = "";
                        return false;//break the each of the input[name='Competitor[domain][]']
                    }
                });
                //$chks.size() is the same as $chks.length

                if (chkbox != "") {
                    $domaincpt.each(function(i, e){
                        // i means index for this object, e.value the same with $(this).val()
                        if ($(this).val() == "") {
                            $(this).val(chkbox);
                            $(this).attr({class:'googlecpt'});
                            return false;//break the each
                        }
                    });
                }

            }
            //alert($(this).attr('value'));
        });

    });
});

$('#rightscroll').scroll(function() {
    //nothing to do for now.
    //$('#log').append('<div>Handler for .scroll() called.</div>');
});

</script>

<?php //$this->endWidget(); ?>

