<?php
$sortby = array(
    "0" => "Domain ID ASC",
    "1" => "Domain ID DESC",
);

function formatAutoValues($v) {
    if ($v) {
        $_tmps = explode("|", $v);
        //array_pop($_tmps);
        //array_shift($_tmps);
        return $v = $_tmps;
    } else {
        return array();
    }
}

if (!$model->frequency) $model->frequency = 5;
if (!$model->name) $model->name = "Automation ".date("Y-m-d");
if ($model->category) $model->category = formatAutoValues($model->category);
if ($model->mailer) $model->mailer = formatAutoValues($model->mailer);
if ($model->template) $model->template = formatAutoValues($model->template);
if ($model->touched_status) $model->touched_status = formatAutoValues($model->touched_status);
//print_r($model->category);

$types = Types::model()->actived()->bytype(array("category"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$categories = $gtps['category'] ? $gtps['category'] : array();

$touchedstatus = Domain::$status;
$statusstr = Utils::array2String($touchedstatus);

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

?>

<h1><?php echo $model->id ? "Update" : "Create"; ?> Automation Setting</h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'automation-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
		<?php //echo $form->textField($model,'category',array('size'=>60,'maxlength'=>2000)); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model, 'category', $categories, $htmlOptions);
        ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touched_status'); ?>
		<?php //echo $form->textField($model,'touched_status',array('size'=>60,'maxlength'=>2000)); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model, 'touched_status', $touchedstatus, $htmlOptions);
        ?>
		<?php echo $form->error($model,'touched_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mailer'); ?>
		<?php //echo $form->textField($model,'mailer',array('size'=>60,'maxlength'=>2000)); ?>
		<?php echo $form->dropDownList($model,'mailer',CHtml::listData(Mailer::model()->actived()->findAll(),'id','display_name'),array('multiple'=>true)); ?>
		<?php echo $form->error($model,'mailer'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'template'); ?>
		<?php //echo $form->textField($model,'template',array('size'=>60,'maxlength'=>2000)); ?>
		<?php echo $form->dropDownList($model,'template',CHtml::listData(Template::model()->actived()->findAll(),'id','name'),array('multiple'=>true)); ?>
		<?php echo $form->error($model,'template'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'frequency'); ?>
		<?php echo $form->textField($model,'frequency'); ?>
		<?php echo $form->error($model,'frequency'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sortby'); ?>
		<?php //echo $form->textField($model,'sortby',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->dropDownList($model, 'sortby', $sortby); ?>
		<?php echo $form->error($model,'sortby'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total'); ?>
		<?php echo $form->textField($model,'total',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'total'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total_sent'); ?>
		<?php echo $form->textField($model,'total_sent',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'total_sent'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'current_domain_id'); ?>
		<?php echo $form->textField($model,'current_domain_id',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'current_domain_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'current_mailer_id'); ?>
		<?php echo $form->textField($model,'current_mailer_id',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'current_mailer_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'current_template_id'); ?>
		<?php echo $form->textField($model,'current_template_id',array('disabled'=>'disabled')); ?>
		<?php echo $form->error($model,'current_template_id'); ?>
	</div>

<?php /* ?>
	<div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created_by'); ?>
		<?php echo $form->textField($model,'created_by'); ?>
		<?php echo $form->error($model,'created_by'); ?>
	</div>
<?php */ ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#Automation_touched_status").multiselect({noneSelectedText:'Select Status',selectedList:8}).multiselectfilter();
    $("#Automation_category").multiselect({noneSelectedText:'Select Categories',selectedList:8}).multiselectfilter();
    $("#Automation_mailer").multiselect({noneSelectedText:'Select Mailer',selectedList:8}).multiselectfilter();
    $("#Automation_template").multiselect({noneSelectedText:'Select Templates',selectedList:8}).multiselectfilter();
    //$("#Automation_excludecategory").multiselect({noneSelectedText:'Exclude Categories',selectedList:8}).multiselectfilter();
});
</script>

