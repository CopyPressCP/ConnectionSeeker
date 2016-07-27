<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

$types = Types::model()->actived()->bytype('category')->findAll();
$cats = CHtml::listData($types, 'refid', 'typename');
$tiers = CampaignTask::$tier;

if ($model->category && !is_array($model->category)) {
    $_category = explode("|", $model->category);
    array_pop($_category);
    array_shift($_category);
    $model->category = $_category;
}

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
} else {
    $clients = Client::model()->actived()->findAll();
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("campaign/upload"),
	'method'=>'post',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'id'=>'campaign-form',
	'enableAjaxValidation'=>false,
)); ?>
    <h2>Upload File (Support CSV, Excel2005, Excel2007, Gnumeric and Openoffice Calc for now)</h2>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>

  <div id="leftbasicinfo" style="width:520px;">

<?php
$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $__uinfo = User::model()->findByPk(Yii::app()->user->id);
    echo $form->hiddenField($model,'client_id',array('value'=>$__uinfo->client_id));
} else {
?>
	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
        <?php echo $form->dropDownList($model, 'client_id', CHtml::listData($clients,'id','company'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>
<?php
}
?>
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<!-- <div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php /*
        $form->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'model'=>$model,
            'name'=>'Campaign_domain',
            'value'=>$model->domain,
            'source'=>'js:[]', // <- use this for pre-set array of values
            'source'=>Yii::app()->createUrl("clientDomain/domains"),// <- path to controller which returns dynamic data
            // additional javascript options for the autocomplete plugin
            'options'=>array(
                'minLength'=>0, // min chars to start search
                'select'=>'js:function(event, ui) { console.log(ui.item.id +":"+ui.item.value);}'
            ),
            'htmlOptions'=>array(
                'id'=>'Campaign_domain',
            ),
        ));
        */
        ?>
		<?php echo $form->error($model,'domain'); ?>
	</div> -->

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
    <div class="clear"></div>
  </div><!-- end of leftbasicinfo -->

  <div id="middlebasicinfo" style="width:460px;">
    <div>
        <div class="row">
            <?php echo $form->labelEx($model,'duedate'); ?>
            <?php 
            //1209600 means 14 days
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'name' => 'Campaign[duedate]',
                'value' => date("M/d/Y", ($model->duedate ? strtotime(str_replace("/","-",$model->duedate)) : time()+1209600)), 
                // additional javascript options for the date picker plugin
                'options'=>array(
                    'showAnim'=>'fold',
                    'dateFormat'=>Yii::app()->getLocale()->getDateFormat('short', $model->duedate),
                    'changeMonth' => 'true',
                    'changeYear'=>'true',
                    'constrainInput' => 'false',
                ),
                // DONT FORGET TO ADD TRUE this will create the datepicker return as string
            ));
            ?>
            <?php echo $form->error($model,'duedate'); ?>
        </div>
        <div class="row">
            <?php echo $form->labelEx($model,'category',array('label'=>'Categories(For now,You\'d better choose one category only)')); ?>
            <?php
            $htmlOptions = array();
            $htmlOptions['multiple'] = true;
            $htmlOptions['style'] = "width:480px;";
            echo $form->dropDownList($model, 'category', $cats, $htmlOptions);
            ?>

            <?php echo $form->error($model,'category'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'notes'); ?>
            <?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
            <?php echo $form->error($model,'notes'); ?>
        </div>

    </div>
  </div><!-- end of rightbasicinfo -->
  <div class="clear"></div>


    <div>
	<div class="row">
        <?php echo CHtml::label('Import from local file', 'Campaign_upfile');?>
        <?php echo CHtml::activeFileField($model, 'upfile', array('style'=>'height:30px;'));?>
	</div>
    </div>

    <div class="clear"></div>

    <div class="row buttons">
		<?php echo CHtml::submitButton('Upload'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Campaign_category").multiselect({noneSelectedText:'Select Category',selectedList:10}).multiselectfilter();
}); //end of dom.ready
//-->
</script>