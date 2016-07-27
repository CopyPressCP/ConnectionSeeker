    <div class="wide form leftform" id="domainPrice" >
    <?php foreach ($prices as $row) { ?>
        <div class="row">
            <div style="font:italic bold 18px arial,sans-serif;color:red;"><?php echo nl2br(CHtml::encode($row->price)); ?></div>
            <div><?php echo $row->created; ?> Created by <?php echo $row->rcreatedby->username ?></div>
        </div>
    <?php } ?>
    </div>
    <div class="form" id="maildiv">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'domain-price-form',
        'enableAjaxValidation'=>true,
        'focus'=>array($model,'price'),
    )); ?>
        <?php echo $form->hiddenField($model,'domain_id'); ?>
        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'price'); ?>
            <?php echo $form->textField($model,'price'); ?>
            <?php echo $form->error($model,'price'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Add Price', array('id' => 'addPrice')); ?>
        </div>

    <?php $this->endWidget(); ?>

    </div><!-- form -->

<div class="clear"></div>

<script type="text/javascript">
$('#addPrice').unbind('click').click(function(){
    if ($("#DomainPrice_price").val() == "") {
        return false;
    }
    $.ajax({
        'success': function(data) {
            $("#domainPrice").append(data);
            $("#DomainPrice_price").val("");
        },
        'type':'POST',
        'dataType': 'html',
        'url':"<?php echo Yii::app()->createUrl('/domain/price', array('domain_id' => $model->domain_id));?>",
        'cache': false,
        'data': $("#domain-price-form").serialize(),
        'complete':function(XHR,TS){XHR = null;}
    });

    $("#domain-price-form").unbind();
    return false;
});
</script>
