<?php
$_themebaseurl = Yii::app()->theme->baseUrl;
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
Yii::app()->clientScript->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'
);

$cs->registerScriptFile($_themebaseurl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile($_themebaseurl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile($_themebaseurl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile($_themebaseurl . '/js/multiselect/jquery.multiselect.filter.css');

if ($model->clients) {
    $_clients = explode("|", $model->clients);
    array_pop($_clients);
    array_shift($_clients);
    $model->clients = $_clients;
}
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ioblacklist-form',
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
		<?php echo $form->labelEx($model,'isallclient'); ?>
        <?php echo $form->dropDownList($model,'isallclient',array("0"=>"Specific Clients","1"=>"All Clients")); ?>
		<?php echo $form->error($model,'isallclient'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'clients'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model,'clients',CHtml::listData(Client::model()->actived()->findAll(),'id','company'),$htmlOptions);
        ?>
		<?php echo $form->error($model,'clients'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'isblacklist'); ?>
        <?php echo $form->dropDownList($model,'isblacklist',array("0"=>"Warning","1"=>"Blacklist")); ?>
		<?php echo $form->error($model,'isblacklist'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('style'=>"width:380px;height:100px;")); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#Ioblacklist_clients").multiselect({noneSelectedText:'Select Clients',selectedList:5}).multiselectfilter();
});
</script>
