<?php
$isprivates = array(
    "0" => "View All",
    "1" => "Private",
);
$cuid = Yii::app()->user->id;
$roles = Yii::app()->authManager->getRoles($cuid);
?>
    <div class="wide form leftform" id="domainNotes" >
    <?php foreach ($notes as $row) {
        //if (empty($row->isprivate) || ($row->isprivate==1 && $row->created_by==$cuid) ) {
        if (empty($row->isprivate) || ($row->isprivate==1 && $row->created_by==$cuid) 
            || !(isset($roles['Marketer']) || isset($roles['Publisher']))) {?>
        <div class="row<?php echo $row->isprivate ? " privateinfo" : ''; ?>">
            <div><?php echo nl2br(CHtml::encode($row->notes)); ?></div>
            <div><?php echo $row->created; ?> Created by <?php echo $row->rcreatedby->username ?></div>
        </div>
    <?php } } ?>
    </div>
    <div class="form" id="noteformdiv" style="float:right;">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'domain-note-form',
        'enableAjaxValidation'=>true,
        'focus'=>array($model,'notes'),
    )); ?>
        <?php echo $form->hiddenField($model,'blogger_program_id'); ?>
        <?php echo $form->hiddenField($model,'domain_id'); ?>
        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'notes'); ?>
            <?php echo $form->textArea($model,'notes',array('style'=>'height:150px; width:420px;')); ?>
            <?php echo $form->error($model,'notes'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'isprivate'); ?>
            <?php 
            $model->isprivate = 1;
            echo $form->dropDownList($model, 'isprivate', $isprivates); ?>
            <?php echo $form->error($model,'isprivate'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Add Note', array('id' => 'addNote')); ?>
        </div>

    <?php $this->endWidget(); ?>

    </div><!-- form -->

<div class="clear"></div>

<script type="text/javascript">
$('#addNote').unbind('click').click(function(){
    if ($("#BloggerProgramNote_notes").val() == "") {
        return false;
    }
    $.ajax({
        'success': function(data) {
            $("#domainNotes").append(data);
            $("#BloggerProgramNote_notes").val("");
        },
        'type':'POST',
        'dataType': 'html',
        'url':"<?php echo Yii::app()->createUrl('/BloggerProgram/note', array('blogger_program_id' => $model->blogger_program_id));?>",
        'cache': false,
        'data': $("#domain-note-form").serialize(),
        'complete':function(XHR,TS){XHR = null;}
    });

    $("#domain-note-form").unbind();
    return false;
});
</script>
