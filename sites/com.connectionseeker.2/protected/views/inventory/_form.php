<?php
$types = Types::model()->actived()->findAll("type='site' OR type='category' OR type='channel' OR type='linktask'");
$gtps = CHtml::listData($types, 'refid', 'typename', 'type');
$_stypes = $gtps['site'] ? $gtps['site'] : array();
$_ltypes = $gtps['linktask'] ? $gtps['linktask'] : array();
$_categories = $gtps['category'] ? $gtps['category'] : array();
$_channels = $gtps['channel'] ? $gtps['channel'] : array();
if ($model->category) {
    $_tmps = explode("|", $model->category);
    array_pop($_tmps);
    array_shift($_tmps);
    $model->category = $_tmps;
}

if ($model->accept_tasktype) {
    $_tmps = explode("|", $model->accept_tasktype);
    array_pop($_tmps);
    array_shift($_tmps);
    $model->accept_tasktype = $_tmps;
}

if ($model->channel_id) {
    $_tmps = explode("|", $model->channel_id);
    array_pop($_tmps);
    array_shift($_tmps);
    $model->channel_id = $_tmps;
}

$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );


$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'inventory-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php
        //echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255));
        $form->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'model'=>$model,
            'name'=>'Inventory[domain]',
            //'id'=>'Inventory_domain',
            'value'=>$model->domain,
            'source'=>Yii::app()->createUrl("inventory/domains"),// <- path to controller which returns dynamic data
            // additional javascript options for the autocomplete plugin
            'options'=>array(
                'minLength'=>2, // min chars to start search
                'select'=>'js:function(event, ui) { console.log(ui.item.id +":"+ui.item.value);}'
            ),
            'htmlOptions'=>array(
                'id'=>'Inventory_domain',
            ),
        ));
        ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

<?php
if(!isset($roles['Publisher'])){//means Admin
?>
	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
        <?php echo $form->dropDownList($model,'user_id',CHtml::listData(User::model()->actived()->findAll(),'id','username')); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>
<?php
}
?>

	<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:320px;";
        echo $form->dropDownList($model,'category',$_categories,$htmlOptions); ?>
		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'accept_tasktype'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:320px;";
        echo $form->dropDownList($model,'accept_tasktype',$_ltypes,$htmlOptions); ?>
		<?php echo $form->error($model,'accept_tasktype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'channel_id'); ?>
        <?php
        //echo $form->dropDownList($model,'channel_id',$_channels,array('prompt'=>'-- Select --')); 
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:320px;";
        echo $form->dropDownList($model,'channel_id',$_channels,$htmlOptions); ?>
		<?php echo $form->error($model,'channel_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ext_backend_acct'); ?>
		<?php echo $form->textArea($model,'ext_backend_acct',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'ext_backend_acct'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'link_on_homepage'); ?>
		<?php echo $form->checkBox($model,'link_on_homepage', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'link_on_homepage'); ?>
	</div>
	<div class="clear"></div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status',array('label'=>'Active')); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
	<div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Inventory_category").multiselect({noneSelectedText:'Select Category',selectedList:6}).multiselectfilter();
    $("#Inventory_accept_tasktype").multiselect({noneSelectedText:'Select Accept Type',selectedList:6}).multiselectfilter();
    $("#Inventory_channel_id").multiselect({noneSelectedText:'Select Channel',selectedList:6}).multiselectfilter();
});
//-->
</script>
