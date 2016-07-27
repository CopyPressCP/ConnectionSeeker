<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("domainAdditional/upload"),
	'method'=>'post',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'id'=>'domain-form',
	'enableAjaxValidation'=>false,
)); ?>
    <h2>Upload File (Support CSV, Excel2005, Excel2007, Gnumeric and Openoffice Calc for now)</h2>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
        <?php echo CHtml::label('Upload domain list file', 'Domain_upfile');?>
        <?php echo CHtml::activeFileField($model, 'upfile', array('style'=>'height:30px;'));?>
	</div>

	<div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    //do nothing for now;
});
</script>
