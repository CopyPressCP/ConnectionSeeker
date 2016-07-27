<?php
$sortby = array(
    "0" => "Domain ID ASC",
    "1" => "Domain ID DESC",
);

$semrushes = array(
    "0" => "Pending",
    "1" => "Yes",
    "-1" => "No",
);

$week = array(
    "0" => "Sunday",
    "1" => "Monday",
    "2" => "Tuesday",
    "3" => "Wednesday",
    "4" => "Thursday",
    "5" => "Friday",
    "6" => "Saturday",
);

function formatAutoValues($v) {
    if ($v) {
        $_tmps = explode("|", $v);
        //array_pop($_tmps);
        //array_shift($_tmps);
        return $v = $_tmps;
    } else {
        return array();
    }
}

if (!$model->frequency) $model->frequency = 5;
if (!$model->name) $model->name = "Automation ".date("Y-m-d");
if ($model->category) $model->category = formatAutoValues($model->category);
//if ($model->mailer) $model->mailer = formatAutoValues($model->mailer);
//if ($model->template) $model->template = formatAutoValues($model->template);
if ($model->touched_status) $model->touched_status = formatAutoValues($model->touched_status);
//print_r($model->category);
if ($model->days) $model->days = formatAutoValues($model->days);


$types = Types::model()->actived()->bytype(array("category","site","outreach"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$categories = $gtps['category'] ? $gtps['category'] : array();
$stypes = $gtps['site'] ? $gtps['site'] : array();
$otypes = $gtps['outreach'] ? $gtps['outreach'] : array();
if ($model->stype) $model->stype = formatAutoValues($model->stype);
if ($model->otype) $model->otype = formatAutoValues($model->otype);

$touchedstatus = Domain::$status;
$statusstr = Utils::array2String($touchedstatus);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/timepicker/jquery.timePicker.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/timepicker/timePicker.css');

$mailers = CHtml::listData(Mailer::model()->actived()->findAll(),'id','display_name');
$templates = CHtml::listData(Template::model()->actived()->findAll(),'id','name');
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'automation-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
		<?php //echo $form->textField($model,'category',array('size'=>60,'maxlength'=>2000)); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model, 'category', $categories, $htmlOptions);
        ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touched_status'); ?>
		<?php //echo $form->textField($model,'touched_status',array('size'=>60,'maxlength'=>2000)); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model, 'touched_status', $touchedstatus, $htmlOptions);
        ?>
		<?php echo $form->error($model,'touched_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'stype'); ?>
        <div class="checkboxlist">
        <?php echo $form->checkBoxList($model, 'stype', $stypes, array('separator'=>" ")); ?>
        </div>
	</div>
    <div class="clear"></div>

	<div class="row">
		<?php echo $form->labelEx($model,'otype'); ?>
        <div class="checkboxlist">
        <?php echo $form->checkBoxList($model, 'otype', $otypes, array('separator'=>" ")); ?>
        </div>
	</div>
    <div class="clear"></div>

	<div class="row">
		<?php echo $form->labelEx($model,'alexarank'); ?>
        <div>If you wanna alexa greater than 200, Then you should enter <span style="color:red;font-weight:bold;">>200</span>; If you wanna alexa greater than 30 and less than 20000, then you have to use <span style="color:red;font-weight:bold;">BETWEEN 30 AND 20000</span></div>
		<?php echo $form->textField($model,'alexarank',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'alexarank'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mozauthority'); ?>
		<?php echo $form->textField($model,'mozauthority',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'mozauthority'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'has_owner'); ?>
		<?php echo $form->checkBox($model,'has_owner', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'has_owner'); ?>
	</div>
    <div class="clear"></div>

	<div class="row">
		<?php echo $form->labelEx($model,'semrushkeywords'); ?>
		<?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '[SEM Rush]';
        echo $form->dropDownList($model, 'semrushkeywords', $semrushes, $htmlOptions); ?>
		<?php echo $form->error($model,'semrushkeywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'host_country'); ?>
		<?php echo $form->textField($model,'host_country',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'host_country'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sortby'); ?>
		<?php echo $form->dropDownList($model, 'sortby', $sortby); ?>
		<?php echo $form->error($model,'sortby'); ?>
	</div>


  <div>
    <div class="row" id="automation_mailers">
        <ul class="horizonlist">
        <li style="width:260px;">Mailer</li>
        <li style="width:260px;">Tempaltes</li>
        <li style="width:112px;">Current Template</li>
        <li style="width:112px;">Latest Senttime</li>
        </ul>
        <div class='clear'></div>
        <?php
        $mailer = $form->dropDownList($model,'mailer[]',$mailers,array('prompt'=>'-- Select Mailer --'));
        $template =  $form->dropDownList($model,'template[]',$templates,array('multiple'=>true));
        //##$frequency = $form->textField($model,'frequency[]',array('style'=>"width:90px;"));
        $current_template_id = $form->textField($model,'current_template_id[]',array('style'=>"width:90px;",'readOnly'=>'readOnly'));
        $latest_senttime = $form->textField($model,'latest_senttime[]',array('style'=>"width:90px;",'disabled'=>'disabled'));
        $tpl4kyinput = <<<AMTPL
<ul class="horizonlist"><li>{$mailer}</li><li>{$template}</li><li>{$current_template_id}</li><li>{$latest_senttime}</li></ul><div class="clear"></div>
AMTPL;
        /*
        escape html code from javascript error "unterminated string literal",
        http://stackoverflow.com/questions/1733397/javascript-code-unterminated-string-literal
        */
        $tpl4kyinput = str_replace(">\n<", "><", $tpl4kyinput);

        $_t_count = 3;//default text filed count

        if (isset($model->id) && $model->id > 0 && isset($model->mailers) && $model->mailers) {
            $mailersobj = unserialize($model->mailers);
            //print_r($mailersobj);
            $autocount = count($mailersobj);
            if ($autocount > 0) {
                foreach ($mailersobj as $_mk => $_mv) {
                    $_mv['template'] = formatAutoValues($_mv['template']);
                    $options = array();
                    if ($_mv['template']) {
                        foreach ($_mv['template'] as $_tv) {
                            $options[$_tv] = array('selected'=>'selected');
                        }
                    }
                    //print_r($_mv['template']);
                    echo "<ul class='horizonlist'><li>";
                    echo $form->dropDownList($model,'mailer[]',$mailers,array('prompt'=>'-- Select Mailer --','options'=>array($_mv['mailer']=>array('selected'=>'selected')) ));
                    echo "</li><li>";
                    echo  $form->dropDownList($model,'template[]',$templates,array('multiple'=>true,'options'=>$options ));
                    echo "</li><li>";
                    //#echo $form->textField($model,'frequency[]',array('style'=>"width:90px;",'value'=>$_mv['frequency']));
                    //#echo "</li><li>";
                    echo $form->textField($model,'current_template_id[]',array('style'=>"width:90px;",'value'=>$_mv['current_template_id'],'readOnly'=>'readOnly'));
                    echo "</li><li>";
                    echo $form->textField($model,'latest_senttime[]',array('style'=>"width:90px;",'value'=>$_mv['latest_senttime'],'disabled'=>'disabled'));
                    echo "</li></ul><div class='clear'></div>";
                }
            } else {
                $autocount = 0;
            }
            $_t_count = $_t_count - $autocount;
        }

        if ($_t_count > 0) {
            for ($i = 0; $i < $_t_count; $i++) {
                echo $tpl4kyinput;
            }
        }
        ?>
    </div>
    <div class="row" id="add_more_mailers">
        <?php echo CHtml::link(Yii::t('Automation', '+Add More'), 'javascript:void(0);'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'frequency'); ?>
		<?php echo $form->textField($model,'frequency'); ?>
		<?php echo $form->error($model,'frequency'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'days'); ?>
        <div class="checkboxlist">
		<?php echo $form->checkBoxList($model,'days',$week,array('separator'=>" ")); ?>
        </div>
        <div class='clear'></div>
		<?php echo $form->error($model,'days'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'time_start'); ?>
		<?php echo $form->textField($model,'time_start',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'time_start'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'time_end'); ?>
		<?php echo $form->textField($model,'time_end',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'time_end'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total'); ?>
		<?php echo $form->textField($model,'total',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'total'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total_sent'); ?>
		<?php echo $form->textField($model,'total_sent',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'total_sent'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'current_domain_id'); ?>
		<?php echo $form->textField($model,'current_domain_id',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'current_domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'current_mailer_id'); ?>
		<?php echo $form->textField($model,'current_mailer_id',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'current_mailer_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'latest_senttime'); ?>
		<?php echo $form->textField($model,'latest_senttime',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'latest_senttime'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
    <div class="clear"></div>

<?php /* ?>
	<div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created_by'); ?>
		<?php echo $form->textField($model,'created_by'); ?>
		<?php echo $form->error($model,'created_by'); ?>
	</div>
<?php */ ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#Automation_touched_status").multiselect({noneSelectedText:'Select Status',selectedList:8}).multiselectfilter();
    $("#Automation_category").multiselect({noneSelectedText:'Select Categories',selectedList:8}).multiselectfilter();

    $("select[name^='Automation[template]']").each(function(i) {
        var currenttpl = "automationtpl" + i;
        $(this).attr("id", currenttpl);
        $(this).attr("name", "Automation[template]["+i+"][]");
        $("#"+currenttpl).multiselect({noneSelectedText:'Select Templates',selectedList:8}).multiselectfilter();
        $(this).next().height(38);
    });
    //$("#Automation_mailer").multiselect({noneSelectedText:'Select Mailer',selectedList:8}).multiselectfilter();
    //$("#Automation_template").multiselect({noneSelectedText:'Select Templates',selectedList:8}).multiselectfilter();
    //$("#Automation_excludecategory").multiselect({noneSelectedText:'Exclude Categories',selectedList:8}).multiselectfilter();


    $('#add_more_mailers').click(function(){
        $('#automation_mailers').append('<?php echo $tpl4kyinput; ?>');
        $('#automation_mailers').append('<?php echo $tpl4kyinput; ?>');

        $("select[name^='Automation[template]']").each(function(i) {
            var currenttpl = "automationtpl" + i;
            $(this).attr("id", currenttpl);
            $(this).attr("name", "Automation[template]["+i+"][]");
            $("#"+currenttpl).multiselect({noneSelectedText:'Select Templates',selectedList:8}).multiselectfilter();
            $(this).next().height(38);
        });
    });

    $("#Automation_time_start, #Automation_time_end").timePicker({step: 15});
});
</script>

<style type="text/css">
ul.horizonlist li{float:left;list-style: none outside none;margin:0 15px 0 0;}
div.checkboxlist label{float:left;}
div.checkboxlist input{float:left;}
</style>
