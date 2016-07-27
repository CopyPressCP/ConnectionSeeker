    <div class="wide form leftform" id="taskRatings" >
    <?php foreach ($notes as $row) { ?>
        <div class="row">
            <div class="target-rating" id="ratingstar"></div>
            <div id="ratinghint" class="ratinghint"></div><br />
            <div><?php echo nl2br(CHtml::encode($row->notes)); ?></div><br />
            <div><?php echo $row->created; ?> Created by <?php echo $row->rcreatedby->username ?></div>
        </div>
    <?php } ?>
    </div>

<?php if (empty($notes)) { ?>
    <div class="form" id="ratingformdiv">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'task-rating-form',
        'enableAjaxValidation'=>true,
        'focus'=>array($model,'notes'),
    )); ?>
        <?php echo $form->hiddenField($model,'task_id'); ?>
        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>
        <div class="row">
            <?php echo $form->labelEx($model,'rating'); ?>
            <div class="target-rating" id="ratingstar"></div>
            <div id="ratinghint" class="ratinghint"></div>
            <?php /* ?>
            <select class="ratinghint" id="ratinghint" readOnly="readOnly">
              <option value="">--</option>
              <option value="bad">bad</option>
              <option value="poor">poor</option>
              <option value="regular">regular</option>
              <option value="good">good</option>
              <option value="gorgeous">gorgeous</option>
            </select>
            <?php */ ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'writer_name'); ?>
            <?php echo $form->textField($model,'writer_name'); ?>
            <?php echo $form->error($model,'writer_name'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'notes'); ?>
            <?php echo $form->textArea($model,'notes',array('style'=>'height:150px; width:420px;')); ?>
            <?php echo $form->error($model,'notes'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Submit', array('id' => 'addRating')); ?>
        </div>

    <?php $this->endWidget(); ?>

    </div><!-- form -->

<div class="clear"></div>

<script type="text/javascript">
$('#addRating').unbind('click').click(function(){

    if ($("input[name='TaskRating[rating]']").val() == "") {
        alert("Please choose one rating star at least.");
        return false;
    }

    if ($("input[name='TaskRating[rating]']").val() == 1 && $("#TaskRating_notes").val() == "") {
        alert("Please provide feedback for this rating.");
        return false;
    }

    $.ajax({
        'success': function(data) {
            $("#taskRatings").append(data);
            $("#ratingformdiv").html("");
            __custraty(__currscore, true);
        },
        'type':'POST',
        'dataType': 'html',
        'url':"<?php echo Yii::app()->createUrl('/taskRating/rating', array('task_id' => $model->task_id));?>",
        'cache': false,
        'data': $("#task-rating-form").serialize(),
        'complete':function(XHR,TS){XHR = null;}
    });

    $("#task-rating-form").unbind();
    return false;
});

var __currscore;
var __custraty = function(scr, ro) {
    $('#ratingstar').raty({
      click: function(score, evt) {
        __currscore = score;
      },
      //hints: ['Bad', 'Poor', 'Neutral', 'Good', 'Gorgeous'],
      hints: ['Bad', 'Neutral', 'Good'],
      number: 3,
      readOnly  : ro,
      score     : scr,
      size      : 24,
      path      : 'js/raty/img',
      //single    : true,
      scoreName : 'TaskRating[rating]',
      starOff   : 'star-off-big.png',
      starOn    : 'star-on-big.png',
      width     : 200,
      target    : '#ratinghint',
      targetKeep: true
    });
};

$(document).ready(function(){
    $("#ratingformdiv").css({'float':'right','width':'480px'});
    __custraty(null, false);
});

</script>

<?php } else { ?>

<script type="text/javascript">
$(document).ready(function() {
    $('#ratingstar').raty({
      readOnly  : true,
      score     : <? echo $row->rating ? $row->rating : 1;?>,
      //hints: ['Bad', 'Poor', 'Neutral', 'Good', 'Gorgeous'],
      hints: ['Bad', 'Neutral', 'Good'],
      number: 3,
      size      : 24,
      path      : 'js/raty/img',
      scoreName : 'TaskRating[rating]',
      starOff   : 'star-off-big.png',
      starOn    : 'star-on-big.png',
      width     : 200,
      target    : '#ratinghint',
      targetKeep: true
    });
});
</script>

<?php } ?>

<div class="clear"></div>
