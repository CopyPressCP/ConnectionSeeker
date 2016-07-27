<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

$types = Types::model()->actived()->bytype('category')->findAll();
$cats = CHtml::listData($types, 'refid', 'typename');
$tiers = CampaignTask::$tier;

if ($model->category) {
    $_category = explode("|", $model->category);
    array_pop($_category);
    array_shift($_category);
    $model->category = $_category;
}

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
} else {
    $clients = Client::model()->actived()->findAll();
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'campaign-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

  <div id="leftbasicinfo" style="width:520px;">

<?php
$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $__uinfo = User::model()->findByPk(Yii::app()->user->id);
    echo $form->hiddenField($model,'client_id',array('value'=>$__uinfo->client_id));
} else {
?>
	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
        <?php echo $form->dropDownList($model, 'client_id', CHtml::listData($clients,'id','company'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>
<?php
}
?>
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<!-- <div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php /*
        $form->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'model'=>$model,
            'name'=>'Campaign_domain',
            'value'=>$model->domain,
            'source'=>'js:[]', // <- use this for pre-set array of values
            'source'=>Yii::app()->createUrl("clientDomain/domains"),// <- path to controller which returns dynamic data
            // additional javascript options for the autocomplete plugin
            'options'=>array(
                'minLength'=>0, // min chars to start search
                'select'=>'js:function(event, ui) { console.log(ui.item.id +":"+ui.item.value);}'
            ),
            'htmlOptions'=>array(
                'id'=>'Campaign_domain',
            ),
        ));
        */
        ?>
		<?php echo $form->error($model,'domain'); ?>
	</div> -->

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
    <div class="clear"></div>
  </div><!-- end of leftbasicinfo -->

  <div id="middlebasicinfo" style="width:460px;">
    <div>
        <div class="row">
            <?php echo $form->labelEx($model,'duedate'); ?>
            <?php 
            //1209600 means 14 days
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'name' => 'Campaign[duedate]',
                'value' => date("M/d/Y", ($model->duedate ? $model->duedate : time() + 1209600)), 
                // additional javascript options for the date picker plugin
                'options'=>array(
                    'showAnim'=>'fold',
                    //'dateFormat'=>Yii::app()->getLocale()->getDateFormat('short', $model->duedate),
                    'dateFormat'=>'M/dd/yy',
                    'changeMonth' => 'true',
                    'changeYear'=>'true',
                    'constrainInput' => 'false',
                ),
                // DONT FORGET TO ADD TRUE this will create the datepicker return as string
            ));
            ?>
            <?php echo $form->error($model,'duedate'); ?>
        </div>
        <div class="row">
            <?php echo $form->labelEx($model,'category',array('label'=>'Categories(For now,You\'d better choose one category only)')); ?>
            <?php
            $htmlOptions = array();
            $htmlOptions['multiple'] = true;
            $htmlOptions['style'] = "width:480px;";
            echo $form->dropDownList($model, 'category', $cats, $htmlOptions);
            ?>

            <?php echo $form->error($model,'category'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'notes'); ?>
            <?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
            <?php echo $form->error($model,'notes'); ?>
        </div>

    </div>
  </div><!-- end of rightbasicinfo -->
  <div class="clear"></div>

  <div>
    <div class="row" id="campaign_keywords">
        <ul class="horizonlist">
        <li style="width:80px;"><?php echo $form->labelEx($ctmodel,'kwcount');?></li>
        <li style="width:280px;"><?php echo $form->labelEx($ctmodel,'keyword');?></li>
        <li style="width:280px;"><?php echo $form->labelEx($ctmodel,'targeturl');?></li>
        <li><?php echo $form->labelEx($ctmodel,'tierlevel');?></li></ul>
        <div class='clear'></div>
        <?php
        $kwcount = $form->textField($ctmodel,'kwcount[]',array('style'=>'width:60px;'));
        $kwkeyword = $form->textField($ctmodel,'keyword[]',array('maxlength'=>255));
        $kwtargeturl = $form->textField($ctmodel,'targeturl[]',array('maxlength'=>255));
        $kwtierlevel = $form->dropDownList($ctmodel,'tierlevel[]', $tiers, array("style"=>"width:120px;", 'options'=>array('1'=>array('selected'=>'selected'))));
        /*
        escape html code from javascript error "unterminated string literal",
        http://stackoverflow.com/questions/1733397/javascript-code-unterminated-string-literal
        */
        $kwtierlevel = str_replace(">\n<", "><", $kwtierlevel);
        $tpl4kyinput = <<<KWTPL
<ul class="horizonlist"><li>{$kwcount}</li><li>{$kwkeyword}</li><li>{$kwtargeturl}</li><li>{$kwtierlevel}</li></ul><div class="clear"></div>
KWTPL;
        $tpl4urlinput = $form->textField($ctmodel,'targeturl[]',array('style'=>"width:360px;"));


        //$prefixname = get_class($ctmodel);
        //$form->textField($_kv,'kwcount',array('style'=>'width:60px;','name'=>$prefixname."[kwcount][ck_"."$_kv->id]"));
        //$form->textField($_kv,'keyword',array('maxlength'=>255,'name'=>$prefixname."[keyword][ck_"."$_kv->id]"));
        echo $form->error($ctmodel,'keyword');
        $_t_count = 3;//default text filed count

        if (isset($model->id) && $model->id > 0 && isset($ctmodel->id)) {
            $kwinfo = $ctmodel->keyword;
            if (!isset($kwinfo[0]['targeturl'])) $urlinfo = $ctmodel->targeturl;
            $keywordcount = count($kwinfo);

            if ($keywordcount > 0) {
                foreach($kwinfo as $_kk => $_kv) {
                    echo "<ul class='horizonlist'><li>";
                    echo $form->textField($ctmodel,'kwcount[]',array('style'=>'width:60px;','value'=>$_kv['kwcount'],'readOnly'=>"readOnly"));
                    echo "</li><li>";
                    echo $form->textField($ctmodel,'keyword[]',array('maxlength'=>255,'value'=>$_kv['keyword']));
                    echo "</li><li>";
                    if (!isset($_kv['targeturl']) && $urlinfo) $_kv['targeturl'] = $urlinfo[$_kk]['targeturl'];
                    echo $form->textField($ctmodel,'targeturl[]',array('maxlength'=>255,'value'=>$_kv['targeturl']));
                    echo "</li><li>";
                    echo $form->dropDownList($ctmodel,'tierlevel[]', $tiers, array('style'=>'width:120px;','options'=>array($_kv['tierlevel']=>array('selected'=>'selected')) ));
                    echo "</li></ul>".Yii::t('Campaign', 'In Use:').$_kv['used']."<div class='clear'></div>";
                }
            } else {
                $keywordcount = 0;
            }
            echo $form->hiddenField($ctmodel,'kwexistcount',array('value'=>$keywordcount));

            $_t_count = $_t_count - $keywordcount;
        }

        if ($_t_count > 0) {
            for ($i = 0; $i < $_t_count; $i++) {
                echo $tpl4kyinput;
            }
        }
        ?>
    </div>
    <div class="row" id="add_more_keyword">
        <?php echo CHtml::link(Yii::t('Campaign', '+Add More'), 'javascript:void(0);'); ?>
    </div>
    <div class="clear">&nbsp;</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

  </div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Campaign_category").multiselect({noneSelectedText:'Select Category',selectedList:10}).multiselectfilter();

    $('#add_more_keyword').click(function(){
        $('#campaign_keywords').append('<?php echo $tpl4kyinput; ?>');
        $('#campaign_keywords').append('<?php echo $tpl4kyinput; ?>');
    });

    /*
    $('#add_more_url').click(function(){
        $('#campaign_target_urls').append('<?php echo $tpl4urlinput; ?>');
        $('#campaign_target_urls').append('<?php echo $tpl4urlinput; ?>');
    });
    */

    $('#Campaign_client_id').change(function(){
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/clientDomain/domains');?>",
            'data': "label=domain&id=domain&value=domain&client_id="+this.value,
            'success':function(data){
                //donothing for now;
                $('#Campaign_domain').autocomplete("destroy");
                $('#Campaign_domain').autocomplete({'minLength':0,
                    'select':function(event, ui) { console.log(ui.item.id +":"+ui.item.value);},
                    'source':data});
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $('#Campaign_domain').click(function(){
        if (this.value == ""){
            //$('#Campaign_domain').autocomplete("enable");
            //$('#Campaign_domain').autocomplete("widget");
            $('#Campaign_domain').autocomplete("search");
        }
    });

}); //end of dom.ready
//-->
</script>

<style type="text/css">
/*
.ui-multiselect-filter input{height:12px;padding:0px;width:80px;margin:0px;}
*/
ul.horizonlist li{float:left;list-style: none outside none;margin:0 15px 0 0;}
</style>
