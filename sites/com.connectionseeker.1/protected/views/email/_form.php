<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/xheditor/xheditor-1.1.12-en.min.js', CClientScript::POS_HEAD);
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'domain_id'); ?>
		<?php echo $form->textField($model,'domain_id',array('readonly'=>'readonly')); ?>
		<?php echo $form->error($model,'domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'template_id'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'template_id', CHtml::listData(Template::model()->findAll(),'id','name'), $htmlOptions);
        ?>

		<?php echo $form->error($model,'template_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'from'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- select --';
        echo $form->dropDownList($model, 'from', CHtml::listData(Mailer::model()->findAll(),'id','user_alias'), $htmlOptions);
        ?>
		<?php echo $form->error($model,'from'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'to'); ?>
		<?php echo $form->textField($model,'to',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'to'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cc'); ?>
		<?php echo $form->textField($model,'cc'); ?>
		<?php echo $form->error($model,'cc'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'subject'); ?>
		<?php echo $form->textField($model,'subject',array('size'=>60,'maxlength'=>1000)); ?>
		<?php echo $form->error($model,'subject'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('class'=>'xheditor', 'style'=>'width:510px;height:320px;')); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
    <div class="clear"></div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'send_time'); ?>
		<?php echo $model->send_time; ?>
	</div>
    <div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->