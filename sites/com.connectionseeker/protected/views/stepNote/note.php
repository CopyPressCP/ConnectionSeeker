    <div class="wide form leftform" id="stepNotes" >
    <?php foreach ($notes as $row) { ?>
        <div class="row">
            <div><?php echo nl2br(CHtml::encode($row->notes)); ?></div>
            <div><?php echo $row->created; ?> Created by <?php echo $row->rcreatedby->username ?></div>
        </div>
    <?php } ?>
    </div>
    <div class="form" id="maildiv">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'task-note-form',
        'enableAjaxValidation'=>true,
        'focus'=>array($model,'notes'),
    )); ?>
        <?php echo $form->hiddenField($model,'task_id'); ?>
        <?php echo $form->hiddenField($model,'type'); ?>
        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'notes'); ?>
            <?php echo $form->textArea($model,'notes',array('style'=>'height:150px; width:420px;')); ?>
            <?php echo $form->error($model,'notes'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Add Note', array('id' => 'addNote')); ?>
        </div>

    <?php $this->endWidget(); ?>

    </div><!-- form -->

<div class="clear"></div>

<script type="text/javascript">
$('#addNote').unbind('click').click(function(){
    if ($("#StepNote_notes").val() == "") {
        return false;
    }
    $.ajax({
        'success': function(data) {
            $("#stepNotes").append(data);
            $("#StepNote_notes").val("");
        },
        'type':'POST',
        'dataType': 'html',
        'url':"<?php echo Yii::app()->createUrl('/stepNote/note', array('task_id'=>$model->task_id, 'type'=>$model->type));?>",
        'cache': false,
        'data': $("#task-note-form").serialize(),
        'complete':function(XHR,TS){XHR = null;}
    });

    $("#task-note-form").unbind();
    return false;
});
</script>
