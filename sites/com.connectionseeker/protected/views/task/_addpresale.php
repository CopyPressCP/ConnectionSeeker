<div class="grid-view">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-form',
	'enableAjaxValidation'=>false,
));
?>
    <div class="form"><?php echo $form->errorSummary($model); ?></div>

<?php

echo $form->hiddenField($model, 'campaign_id');
echo $form->hiddenField($model, 'client_id');
echo $form->hiddenField($model, 'content_category_id');
echo $form->hiddenField($model, 'tasktype');
echo $form->hiddenField($model, 'inventory_ids');

foreach ($ivtmodel as $k => $iv) {
    $id = $iv->id;
    //$model->optionalkw4 = $iv->domain;
    $model->optionalkw3 = $iv->domain;
    $model->optionalkw4 = $iv->category_str;
    echo $form->hiddenField($model, 'inventory_id', array('value' => $id, 'name' => 'inventory_id[]'));
?>
    <div><span class="hintTitle"><?php echo CHtml::link($iv->domain, Yii::app()->createUrl("inventory/view", array("id"=>$iv->id)), array('target'=>'_blank'));?></span>
    (Age:<span class="hintTitle">
         <?php echo ($iv->rdomain->onlinesince>658454400) ? date("Y-m-d", $iv->rdomain->onlinesince) : "-1";?>
         </span>
    Page Rank:<span class="hintTitle"><?php echo $iv->rdomain->googlepr;?></span>
    Category:<span class="hintTitle"><?php echo $iv->category_str;?></span>
    Site Type:<span class="hintTitle"><?php echo $_stypes[$iv->rdomain->stype];?></span>
    Channel:<span class="hintTitle"><?php echo $_channels[$iv->channel_id];?></span>
    )
    </div>

    <div class="clear"></div>
    <table class="items" id="table-<?php echo $k; ?>" >
    <tr>
        <th><?php echo Yii::t("Task","Basic Information"); ?></th>
        <th nowrap><?php echo $form->labelEx($model,'optional_keywords'); ?></th>
        <th nowrap><?php echo Yii::t("Task","Other Information"); ?></th>
        <th></th>
    </tr>
    <tr>
        <td>
          <dl>
            <dt><?php echo $form->labelEx($model,'anchortext'); ?></dt>
            <dd style="width:390px;">
            <?php
            if ($_kws) {
                echo $form->dropDownList($model,'anchortext', $_kws, array('name' => 'anchortext' . $id . '[]', 'onchange'=>"autoMcOpt(this, 'anchortext');", 'prompt'=>'-- Keyword --'));
            } else {
                echo $form->textField($model,'anchortext', array('name' =>'anchortext' . $id . '[]', 'style'=>"width:370px;"));
            }
            ?>
            </dd>
            <dt><?php echo $form->labelEx($model,'targeturl'); ?></dt>
            <dd style="width:390px;">
            <?php
            /*
            if ($_urls) {
                echo $form->dropDownList($model,'targeturl',$_urls, array('name' => 'targeturl' . $id . '[]', 'onchange'=>"autoMcOpt(this, 'targeturl');", 'prompt'=>'-- Target URL --'));
            } else {
                echo $form->textField($model,'targeturl', array('name' =>'targeturl' . $id . '[]', 'style'=>"width:370px;"));
            }
            */
            echo $form->textField($model,'targeturl', array('name' =>'targeturl' . $id . '[]', 'style'=>"width:370px;", 'readonly'=>'readonly'));
            ?>
            </dd>
            <dt><?php echo $form->labelEx($model,'title'); ?></dt>
            <dd><?php echo $form->textField($model,'title', array('name' =>'title' . $id . '[]', 'style'=>"width:370px;")); ?></dd>
          </dl>
        </td>
        <td>
        <div>
         <ol>
           <li>
           <?php echo $form->textField($model,'optionalkw1', array('name' =>'optional_keywords' . $id . '[optionalkw1][]' , 'size' => 25)); ?>
           </li>
           <li><?php echo $form->textField($model,'optionalkw2', array('name' =>'optional_keywords' . $id . '[optionalkw2][]' , 'size' => 25)); ?></li>
           <li><?php echo $form->textField($model,'optionalkw3', array('name' =>'optional_keywords' . $id . '[optionalkw3][]' )); ?></li>
           <li><?php echo $form->textField($model,'optionalkw4', array('name' =>'optional_keywords' . $id . '[optionalkw4][]' , 'size' => 25)); ?></li>
         </ol>
         </div>
        </td>
        <td>
          <dl>
            <dt><?php echo $form->labelEx($model,'mapping_id'); ?></dt>
            <dd><?php echo $form->textField($model,'mapping_id', array('name' =>'mapping_id' . $id . '[]' )); ?></dd>
            <dt><?php echo $form->labelEx($model,'notes'); ?></dt>
            <dd><?php echo $form->textArea($model,'notes', array('name' =>'notes' . $id . '[]', 'style'=>'width:210px;height:150px;')); ?></dd>
          </dl>
        </td>
        <td><?php echo CHtml::button('Add', array('value' => '+', 'onclick' =>"copytr(this)", 'class'=>'clonebtn') ) .  CHtml::button('remove', array('value' => '-', 'onclick' =>"removetr(this)", 'class'=>'clonebtn'));?></td>
    </tr>
    </table>
    <br />

<?php 
    }
?>

<div class="form">	
	<div class="row">
		<?php echo $form->labelEx($model,'style_guide'); ?>
		<?php echo $form->textArea($model,'style_guide',array('style'=>"width:780px;height:280px;")); ?>
        <?php echo $form->error($model,'style_guide'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'assignee'); ?>
		<?php echo $form->dropDownList($model,'assignee', Chtml::listData(User::model()->findAll(),'id','username'), array('prompt'=>'-- Select --')); ?>
        <?php echo $form->error($model,'assignee'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'duedate'); ?>
        <?php 
        //1209600 means 14 days
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
             'model'=>$model,
            'name' => 'Task[duedate]',
            'value' => date("M/d/Y", ($model->duedate ? strtotime($model->duedate) : time() + 1209600)), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>Yii::app()->getLocale()->getDateFormat('short', strtotime($model->duedate)),
                'changeMonth' => 'true',
                'changeYear'=>'true',
                'constrainInput' => 'false',
            ),
            // DONT FORGET TO ADD TRUE this will create the datepicker return as string
        ));
        ?>
        <?php echo $form->error($model,'duedate'); ?>
	</div>
    <div class="row buttons">
		<?php echo CHtml::submitButton('Add Link Task', array('id'=>'contentMarketingBtn')); ?>
	</div>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
var thetargeturls = <?php echo ($_urls) ? json_encode($_urls) : "[]";?>;

function autoMcOpt(t, type){
    var parenttr = $(t).parent().parent().parent().parent();
    var eletxt = $(t).find("option:selected").text();
    var currkey = $(t).val();
    //if (typeof thetargeturls[currkey] == "undefined") {
    if (thetargeturls[currkey] == undefined) {
        alert("No url matched for this keyword in this campaign, please fixed it first");
    }

    if (type == "anchortext") {
        //var eletxt = $(t).find("option:selected").val();
        eletxt = eletxt.replace(/^\d+[\s\-\s]+/,"");
        //alert(eletxt);
        if ($(t).find("option:selected").val().length > 0){
            parenttr.children("td:eq(1)").find("input:eq(1)").val(eletxt);
            parenttr.children("td:eq(0)").find("input:eq(0)").val(thetargeturls[currkey]);

            /*
            parenttr.children("td:eq(1)").find("input:eq(0)").val(eletxt);
            var selecturllen = parenttr.children("td:eq(0)").find("select:eq(1) option").length;
            if ((t.selectedIndex + 1) <= selecturllen) {
                //parenttr.children("td:eq(0)").find("select:eq(1)").val(t.selectedIndex);
                parenttr.children("td:eq(0)").find("select:eq(1)").prop('selectedIndex', t.selectedIndex);
            } else {
                parenttr.children("td:eq(0)").find("select:eq(1)").prop('selectedIndex', 1);
            }

            var urleletxt = parenttr.children("td:eq(0)").find("select:eq(1)").find("option:selected").text();
            parenttr.children("td:eq(1)").find("input:eq(1)").val(urleletxt);
            */
        }
    } else if (type == "targeturl") {
        //parenttr.children("td:eq(1)").find("input:eq(1)").val(eletxt);
    } else {
        //do nothing;
    }
}

$(document).ready(function() {
    //init WYSIWYG editor
    $('#Task_style_guide').xheditor({tools:'full',width:780,height:360});

    $("#contentMarketingBtn").click(function(){
        var rtn = true;
        $("select[name*='anchortext'],input[name*='anchortext']").each(function(){
            if ($(this).val() == "" || $(this).val() == undefined){
                alert("Please Choose Anchor Text First");
                $(this).focus();
                $(this).css("background-color","red");
                return rtn = false;
            }
        });
        if (!rtn) return false;

        $("select[name*='targeturl'],input[name*='targeturl']").each(function(){
            if ($(this).val() == "" || $(this).val() == undefined){
                alert("Please Choose Target URL First");
                $(this).focus();
                $(this).css("background-color","red");
                return rtn = false;
            }
        });
        if (!rtn) return false;

        $("input[name*='optionalkw1'],[name*='optionalkw2'],[name*='optionalkw4']").each(function(){
            if ($(this).val() == "" || $(this).val() == undefined){
                alert("Please Provide Optional Keyword.");
                $(this).focus();
                $(this).css("background-color","red");
                return rtn = false;
            }
        });
        if (!rtn) return false;

        if ($('#Task_campaign_id').value == "" || $('#Task_campaign_id').value == 0){
            alert("Please choose one campaign first.");
            return false;
        }

        if ($('#Task_assignee').val() == "" || $('#Task_assignee').val() == 0){
            alert("Please assign this task to one user at least.");
            $('#Task_assignee').css("background-color","red");
            return false;
        }

        return rtn;
    });
});
</script>
