<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

$bpstatuses = BloggerProgram::$bpstatuses;

$types = Types::model()->bytype(array("bloggerprogram","activeprogram"))->findAll();
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$_bloggerprogrames = $gtps['bloggerprogram'] ? $gtps['bloggerprogram'] : array();
$_activeprogrames = $gtps['activeprogram'] ? $gtps['activeprogram'] : array();
//$_cms_usernames = $gtps['cms_username'] ? $gtps['cms_username'] : array();
$syndicationes = array("1"=>"Yes","0"=>"No");

if ($model->category) {
    $_tmps = explode("|", $model->category);
    array_pop($_tmps);
    array_shift($_tmps);
    $model->category = $_tmps;
}
if ($model->activeprogram) {
    $_tmps = array();
    $_tmps = explode("|", $model->activeprogram);
    array_pop($_tmps);
    array_shift($_tmps);
    $model->activeprogram = $_tmps;
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'blogger-program-form',
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
		<?php echo $form->labelEx($model,'first_name'); ?>
		<?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'first_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_name'); ?>
		<?php echo $form->textField($model,'last_name',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'last_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mozauthority'); ?>
		<?php echo $form->textField($model,'mozauthority',array('size'=>9,'maxlength'=>9)); ?>
		<?php echo $form->error($model,'mozauthority'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:320px;";
        echo $form->dropDownList($model,'category',$_bloggerprogrames,$htmlOptions); ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'syndication'); ?>
        <?php echo $form->dropDownList($model, 'syndication', array(""=>"Select Syndication") + $syndicationes); ?>
		<?php echo $form->error($model,'syndication'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cms_username'); ?>
		<?php echo $form->textField($model,'cms_username',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'cms_username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cms_user_id'); ?>
		<?php echo $form->textField($model,'cms_user_id',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'cms_user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_email'); ?>
		<?php echo $form->textField($model,'contact_email',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'contact_email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'per_word_rate'); ?>
		<?php echo $form->textField($model,'per_word_rate',array('size'=>9,'maxlength'=>9)); ?>
		<?php echo $form->error($model,'per_word_rate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'activeprogram'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:320px;";
        echo $form->dropDownList($model,'activeprogram',$_activeprogrames,$htmlOptions); ?>
		<?php echo $form->error($model,'activeprogram'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
        <?php echo $form->dropDownList($model, 'status', $bpstatuses); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'isdelete'); ?>
		<?php //echo $form->textField($model,'isdelete'); ?>
        <?php echo $form->checkBox($model,'isdelete', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'isdelete'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#BloggerProgram_category").multiselect({noneSelectedText:'Select Category',selectedList:6}).multiselectfilter();
    $("#BloggerProgram_activeprogram").multiselect({noneSelectedText:'Select Active Program',selectedList:6}).multiselectfilter();
});
//-->
</script>