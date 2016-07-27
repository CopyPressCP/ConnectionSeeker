<?php $themeurl = Yii::app()->theme->baseUrl;?>
<div id="targetboxdiv">
    <div id="ifrdiv">
        <div id="scs_alltypenotes"></div>
        <iframe id="ifr_webpreview" name="ifr_webpreview" style="width:620px;height:500px" frameborder="0" scrolling="auto" src="<?php echo $targeturl; ?>"></iframe>
        <div class="clear"></div>
    </div>

    <div id="stepdescdiv">
        <div class="form">

        <?php $stepform=$this->beginWidget('CActiveForm', array(
            'id'=>'contentstep-form',
            'action'=>Yii::app()->createUrl('contentStep/updatestep'),
            'enableAjaxValidation'=>true,
        )); 
        echo $stepform->hiddenField($model, 'task_id');
        ?>
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <?php echo $stepform->errorSummary($model); ?>

        <div class="row">
            <?php echo $stepform->labelEx($model,'step_title'); ?>
            <?php echo $stepform->textField($model,'step_title',array('style'=>"width:300px;",'maxlength'=>2000)); ?>
            <?php echo $stepform->error($model,'step_title'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'direction'); ?>
            <?php echo $stepform->textArea($model,'direction',array('style'=>"width:300px;height:100px;")); ?>
            <?php echo $stepform->error($model,'direction'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'resource_link_1'); ?>
            <?php echo $stepform->textField($model,'resource_link_1',array('style'=>"width:300px;",'maxlength'=>2000)); ?>
            <?php echo $stepform->error($model,'resource_link_1'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'resource_link_2'); ?>
            <?php echo $stepform->textField($model,'resource_link_2',array('style'=>"width:300px;",'maxlength'=>2000)); ?>
            <?php echo $stepform->error($model,'resource_link_2'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'resource_link_3'); ?>
            <?php echo $stepform->textField($model,'resource_link_3',array('style'=>"width:300px;",'maxlength'=>2000)); ?>
            <?php echo $stepform->error($model,'resource_link_3'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('id' => 'updateStepBtn')); ?>
        </div>

        <?php $this->endWidget(); ?>

        </div>
    </div>

    <div class="clear"></div>
</div>

<style>
#stepdescdiv{margin:5px 20px;float:right;}
</style>
<script type="text/javascript">
$('#updateStepBtn').unbind('click').click(function(){
    if ($("#ContentStep_task_id").val() == "") {
        alert("Please choose one task before you submit this form.");
        return false;
    }
    $.ajax({
        'success': function(data) {
            alert("Save Ideation Success");
        },
        'type':'POST',
        'dataType': 'html',
        'url':"<?php echo Yii::app()->createUrl('/contentStep/updatestep', array('task_id'=>$model->task_id));?>",
        'cache': false,
        'data': $("#contentstep-form").serialize(),
        'complete':function(XHR,TS){XHR = null;}
    });

    $("#contentstep-form").unbind();
    return false;
});

$(document).ready(function() {
    $.ajax({
        'success': function(data) {
            $("#scs_alltypenotes").append(data);
        },
        'type':'GET',
        'dataType': 'html',
        'url':"<?php echo Yii::app()->createUrl('/stepNote/alltypenotes', array('task_id'=>$model->task_id, 'type'=>'all'));?>",
        'cache': false,
        'complete':function(XHR,TS){XHR = null;}
    });

    $("#ContentStep_step_title").charCount({
        allowed: 70,		
        warning: 20,
        counterText: 'Characters: '	
    });
});
</script>

