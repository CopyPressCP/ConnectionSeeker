<?php
$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
} else {
    $clients = Client::model()->actived()->findAll();
}

if ($this->action->id=='cloneit') {
    $model->competitora = $_REQUEST["ClientDiscovery"]["competitora"];
    $model->competitorb = $_REQUEST["ClientDiscovery"]["competitorb"];
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'client-discovery-form',
	'enableAjaxValidation'=>false,
	'action'=>($this->action->id=='update') ? array('clientDiscovery/update', 'id'=>$model->id) : array('clientDiscovery/create'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
        <?php echo $form->dropDownList($model, 'client_id', CHtml::listData($clients,'id','company'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'competitora'); ?>
		<?php echo $form->textField($model,'competitora',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'competitora'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'competitorb'); ?>
		<?php echo $form->textField($model,'competitorb',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'competitorb'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'complete_with_automation'); ?>
		<?php echo $form->checkBox($model,'complete_with_automation', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'complete_with_automation'); ?>
	</div>
    <div class="clear"></div>

    <div id="automation_rules" <?php if (!$model->complete_with_automation) echo "style='display:none'";?>>
    <?php $this->renderPartial('/clientDiscovery/_automation', array('automation_setting'=>$model->automation_setting,)); ?>
    </div>
    <div class="clear"></div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
    <div class="clear"></div>

    <?php /* ?>
	<div class="row">
		<?php echo $form->labelEx($model,'domain_id'); ?>
		<?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'competitora_id'); ?>
		<?php echo $form->textField($model,'competitora_id'); ?>
		<?php echo $form->error($model,'competitora_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'competitorb_id'); ?>
		<?php echo $form->textField($model,'competitorb_id'); ?>
		<?php echo $form->error($model,'competitorb_id'); ?>
	</div>
    <?php */ ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#ClientDiscovery_complete_with_automation").click(function(){
        $("#automation_rules").toggle(1000);
    });
});
</script>