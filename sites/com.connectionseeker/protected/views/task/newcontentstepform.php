<?php $themeurl = Yii::app()->theme->baseUrl;
$readonly = ($content_step == 0) ? false : true;
if (isset($roles['Admin']) && $content_step != 5) {
    $readonly = false;
}
?>
<div id="targetboxdiv">
    <div id="ifrdiv">
        <br />
        <iframe id="ifr_webpreview" name="ifr_webpreview" style="width:650px;height:720px" frameborder="0" scrolling="auto" src="<?php echo $targeturl; ?>"></iframe>
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
            <?php echo $stepform->textField($model,'step_title',array('style'=>"width:300px;",'maxlength'=>2000, 'readonly'=>$readonly)); ?>
            <?php echo $stepform->error($model,'step_title'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'direction'); ?>
            <?php echo $stepform->textArea($model,'direction',array('style'=>"width:300px;height:100px;", 'readonly'=>$readonly)); ?>
            <?php echo $stepform->error($model,'direction'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'resource_link_1'); ?>
            <?php echo $stepform->textField($model,'resource_link_1',array('style'=>"width:300px;",'maxlength'=>2000, 'readonly'=>$readonly)); ?>
            <?php echo $stepform->error($model,'resource_link_1'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'resource_link_2'); ?>
            <?php echo $stepform->textField($model,'resource_link_2',array('style'=>"width:300px;",'maxlength'=>2000, 'readonly'=>$readonly)); ?>
            <?php echo $stepform->error($model,'resource_link_2'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'resource_link_3'); ?>
            <?php echo $stepform->textField($model,'resource_link_3',array('style'=>"width:300px;",'maxlength'=>2000, 'readonly'=>$readonly)); ?>
            <?php echo $stepform->error($model,'resource_link_3'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'client_comment'); ?>
            <?php echo $stepform->textArea($model,'client_comment',array('style'=>"width:300px;height:100px;", 'readonly'=>$readonly)); ?>
            <?php echo $stepform->error($model,'client_comment'); ?>
        </div>

        <div class="row">
            <?php echo $stepform->labelEx($model,'extra_writer_note'); ?>
            <?php echo $stepform->textArea($model,'extra_writer_note',array('style'=>"width:300px;height:100px;", 'readonly'=>$readonly)); ?>
            <?php echo $stepform->error($model,'extra_writer_note'); ?>
        </div>

        <?php if (!$readonly) { ?>
        <div class="row buttons">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('id' => 'updateStepBtn')); ?>
        </div>
        <?php } ?>

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
    if ($("#ContentStep_step_title").val() == "") {
        alert("Please enter the title.");
        return false;
    }
    if ($("#ContentStep_direction").val() == "" || $("#ContentStep_direction").val().length<25) {
        alert("Please enter the direction (25 CHARACTER MINIMUM).");
        return false;
    }
    if ($("#ContentStep_resource_link_1").val() == "" || $("#ContentStep_resource_link_2").val() == ""
        || $("#ContentStep_resource_link_3").val() == "") {
        alert("All of these 3 resource links are required, Please enter it.");
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
</script>

