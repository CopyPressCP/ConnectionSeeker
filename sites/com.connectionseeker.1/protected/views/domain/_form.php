<?php
$types = Types::model()->actived()->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$stypes = $gtps['site'];
$otypes = $gtps['outreach'];
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'domain-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tld'); ?>
		<?php echo $form->textField($model,'tld',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'tld'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'googlepr'); ?>
		<?php echo $form->textField($model,'googlepr'); ?>
		<?php echo $form->error($model,'googlepr'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'onlinesince'); ?>
		<?php echo $form->textField($model,'onlinesince'); ?>
		<?php echo $form->error($model,'onlinesince'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'linkingdomains'); ?>
		<?php echo $form->textField($model,'linkingdomains',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'linkingdomains'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'inboundlinks'); ?>
		<?php echo $form->textField($model,'inboundlinks',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'inboundlinks'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'indexedurls'); ?>
		<?php echo $form->textField($model,'indexedurls',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'indexedurls'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'alexarank'); ?>
		<?php echo $form->textField($model,'alexarank',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'alexarank'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ip'); ?>
		<?php echo $form->textField($model,'ip',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'ip'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'subnet'); ?>
		<?php echo $form->textField($model,'subnet',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'subnet'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'owner'); ?>
		<?php echo $form->textField($model,'owner',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'owner'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telephone'); ?>
		<?php echo $form->textField($model,'telephone',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'telephone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'country'); ?>
		<?php echo $form->textField($model,'country',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'country'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'state'); ?>
		<?php echo $form->textField($model,'state',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'state'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'city'); ?>
		<?php echo $form->textField($model,'city',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'zip'); ?>
		<?php echo $form->textField($model,'zip',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'zip'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'street'); ?>
		<?php echo $form->textArea($model,'street',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'street'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'stype'); ?>
		<?php //echo $form->textField($model,'stype'); ?>
        <?php echo $form->dropDownList($model, 'stype', $stypes,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'stype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'otype'); ?>
		<?php //echo $form->textField($model,'otype'); ?>
        <?php echo $form->dropDownList($model, 'otype', $otypes,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'otype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touched'); ?>
		<?php echo $form->textField($model,'touched'); ?>
		<?php echo $form->error($model,'touched'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touched_status'); ?>
		<?php //echo $form->textField($model,'touched_status'); ?>
        <?php echo $form->dropDownList($model, 'touched_status', Domain::$status); ?>
		<?php echo $form->error($model,'touched_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touched_by'); ?>
		<?php echo $form->textField($model,'touched_by'); ?>
		<?php echo $form->error($model,'touched_by'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->