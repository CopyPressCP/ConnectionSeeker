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

$autorule = json_decode($automation_setting);
if (!$autorule->frequency) $autorule->frequency = 5;
if (!$autorule->name) $autorule->name = "Email Task Automation ".date("Y-m-d");
//##if ($autorule->category) $autorule->category = formatAutoValues($autorule->category);
//if ($autorule->mailer) $autorule->mailer = formatAutoValues($autorule->mailer);
//if ($autorule->template) $autorule->template = formatAutoValues($autorule->template);
//##if ($autorule->touched_status) $autorule->touched_status = formatAutoValues($autorule->touched_status);
//###if ($autorule->days) $autorule->days = formatAutoValues($autorule->days);
if ($this->action->id=='cloneit') {
    $autorule->total = "";
    $autorule->total_sent = "";
    $autorule->current_domain_id = "";
    $autorule->current_mailer_id = "";
    $autorule->latest_senttime = "";
}


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

$types = Types::model()->actived()->bytype(array("site"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$stypes = $gtps['site'] ? $gtps['site'] : array();
?>


	<div class="row">
        <?php echo CHtml::label('Automation Name', 'Automation[name]'); ?>
        <?php echo CHtml::textField('Automation[name]', $autorule->name, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Touched Status', 'Automation[touched_status]'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo CHtml::dropDownList("Automation[touched_status][]", $autorule->touched_status, $touchedstatus, $htmlOptions);
        ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Site Type', 'Automation[site_type]'); ?>
        <div class="checkboxlist">
        <?php echo CHtml::checkBoxList('Automation[site_type]', $autorule->site_type, $stypes, array('separator'=>" ")); ?>
        </div>
	</div>
    <div class="clear"></div>

	<div class="row">
        <?php echo CHtml::label('Alexarank', 'Automation[alexarank]'); ?>
        <div>If you wanna alexa greater than 200, Then you should enter <span style="color:red;font-weight:bold;">>200</span>; If you wanna alexa greater than 30 and less than 20000, then you have to use <span style="color:red;font-weight:bold;">BETWEEN 30 AND 20000</span></div>
        <?php echo CHtml::textField('Automation[alexarank]', $autorule->alexarank, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Mozauthority', 'Automation[mozauthority]'); ?>
        <?php echo CHtml::textField('Automation[mozauthority]', $autorule->mozauthority, array('size'=>120)); ?>
	</div>

	<div class="row horizonleft">
        <?php echo CHtml::label('Primary Name Must Be Exist', 'Automation[has_owner]'); ?>
        <?php echo CHtml::checkBox('Automation[has_owner]', $autorule->has_owner, array('class'=>'chkbox')); ?>
	</div>
    <div class="clear"></div>

	<div class="row">
        <?php echo CHtml::label('Semrushkeywords', 'Automation[semrushkeywords]'); ?>
		<?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '[SEM Rush]';
        echo CHtml::dropDownList("Automation[semrushkeywords]", $autorule->semrushkeywords, $semrushes, $htmlOptions); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Host Country', 'Automation[host_country]'); ?>
        <?php echo CHtml::textField('Automation[host_country]', $autorule->host_country, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Sort By', 'Automation[sortby]'); ?>
		<?php echo CHtml::dropDownList("Automation[sortby]", $autorule->sortby, $sortby); ?>
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
        $mailer = CHtml::dropDownList('Automation[mailer][]','',$mailers,array('prompt'=>'-- Select Mailer --'));
        $template = CHtml::dropDownList('Automation[template][]','',$templates,array('multiple'=>true));
        $current_template_id = CHtml::textField('Automation[current_template_id][]', '', array('style'=>"width:90px;",'readOnly'=>'readOnly'));
        $latest_senttime = CHtml::textField('Automation[latest_senttime][]', '',array('style'=>"width:90px;",'disabled'=>'disabled'));
        $tpl4kyinput = <<<AMTPL
<ul class="horizonlist"><li>{$mailer}</li><li>{$template}</li><li>{$current_template_id}</li><li>{$latest_senttime}</li></ul><div class="clear"></div>
AMTPL;
        /*
        escape html code from javascript error "unterminated string literal",
        http://stackoverflow.com/questions/1733397/javascript-code-unterminated-string-literal
        */
        $tpl4kyinput = str_replace(">\n<", "><", $tpl4kyinput);

        $_t_count = 3;//default text filed count
        if (isset($autorule->mailers) && $autorule->mailers) {
            //print_r($autorule->mailers);
            $mailersobj = unserialize($autorule->mailers);
            //### $mailersobj = $autorule->mailers;
            //print_r($mailersobj);
            //exit;
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
                    echo CHtml::dropDownList('Automation[mailer][]','',$mailers,array('prompt'=>'-- Select Mailer --','options'=>array($_mv['mailer']=>array('selected'=>'selected')) ));
                    echo "</li><li>";
                    echo  CHtml::dropDownList('Automation[template][]','',$templates,array('multiple'=>true,'options'=>$options ));
                    echo "</li><li>";
                    echo CHtml::textField('Automation[current_template_id][]', '',array('style'=>"width:90px;",'value'=>$_mv['current_template_id'],'readOnly'=>'readOnly'));
                    echo "</li><li>";
                    echo CHtml::textField('Automation[latest_senttime][]', '',array('style'=>"width:90px;",'value'=>$_mv['latest_senttime'],'disabled'=>'disabled'));
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
        <?php echo CHtml::label('Frequency', 'Automation[frequency]'); ?>
        <?php echo CHtml::textField('Automation[frequency]', $autorule->frequency, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Days', 'Automation[days]'); ?>
        <div class="checkboxlist">
        <?php echo CHtml::checkBoxList('Automation[days]', $autorule->days, $week, array('separator'=>" ")); ?>
        </div>
        <div class='clear'></div>
	</div>

	<div class="row">
        <?php echo CHtml::label('Time Start', 'Automation[time_start]'); ?>
        <?php echo CHtml::textField('Automation[time_start]', $autorule->time_start, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Time End', 'Automation[time_end]'); ?>
        <?php echo CHtml::textField('Automation[time_end]', $autorule->time_end, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Total', 'Automation[total]'); ?>
        <?php echo CHtml::textField('Automation[total]', $autorule->total, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Total Sent', 'Automation[total_sent]'); ?>
        <?php echo CHtml::textField('Automation[total_sent]', $autorule->total_sent, array('size'=>120)); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Current Domain', 'Automation[current_domain_id]'); ?>
        <?php echo CHtml::textField('Automation[current_domain_id]',$autorule->current_domain_id,array('disabled'=>'disabled')); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Current Mailer', 'Automation[current_mailer_id]'); ?>
        <?php echo CHtml::textField('Automation[current_mailer_id]', $autorule->current_mailer_id, array('disabled'=>'disabled')); ?>
	</div>


	<div class="row">
        <?php echo CHtml::label('Latest Senttime', 'Automation[latest_senttime]'); ?>
        <?php echo CHtml::textField('Automation[latest_senttime]', $autorule->latest_senttime, array('disabled'=>'disabled')); ?>
	</div>

    <div class="clear"></div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#Automation_touched_status").multiselect({noneSelectedText:'Select Status',selectedList:8}).multiselectfilter();
    //##$("#Automation_category").multiselect({noneSelectedText:'Select Categories',selectedList:8}).multiselectfilter();

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
