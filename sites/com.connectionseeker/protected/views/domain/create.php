<?php
$this->breadcrumbs=array(
	'Domains'=>array('index'),
	'Create',
);

/*
$this->menu=array(
	array('label'=>'List Domain', 'url'=>array('index')),
	array('label'=>'Manage Domain', 'url'=>array('admin')),
);
*/

$types = Types::model()->actived()->bytype(array("site","outreach","category"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$stypes = $gtps['site'] ? $gtps['site'] : array();
$otypes = $gtps['outreach'] ? $gtps['outreach'] : array();
$categories = $gtps['category'] ? $gtps['category'] : array();

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );
?>

<h1>Create Domain</h1>
<?php //echo $this->renderPartial('_form', array('model'=>$model)); ?>


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
		<?php echo $form->labelEx($model,'stype'); ?>
        <?php echo $form->dropDownList($model, 'stype', $stypes,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'stype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'otype'); ?>
        <?php echo $form->dropDownList($model, 'otype', $otypes,array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'otype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:320px;";
        echo $form->dropDownList($model,'category',$categories,$htmlOptions); ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touched_status'); ?>
        <?php echo $form->dropDownList($model, 'touched_status', Domain::$status); ?>
		<?php echo $form->error($model,'touched_status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Domain_category").multiselect({noneSelectedText:'Select Category',selectedList:6}).multiselectfilter();
});
//-->
</script>