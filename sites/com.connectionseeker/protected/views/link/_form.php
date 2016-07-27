<?php
$types = Types::model()->actived()->bytype(array('category','linktask'))->findAll();
if ($types) $gtps = CHtml::listData($types, 'refid', 'typename', 'type');

$cats = $gtps['category'];
$linktasks = $gtps['linktask'];

if ($model->campaign_id && $model->campaign_id > 0) {
    $campain = Campaign::Model()->findByPk($model->campaign_id);
    $campaigns = Campaign::Model()->findAllByAttributes(array('client_id'=>$campain->client_id));
    $_GET['Link']['client_id'] = $campain->client_id;
}
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'link-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'inventory_id'); ?>
		<?php echo $form->textField($model,'inventory_id'); ?>
		<?php echo $form->error($model,'inventory_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sourceurl'); ?>
		<?php echo $form->textField($model,'sourceurl',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'sourceurl'); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Client', 'Link_client_id');?>

        <?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- Select Client --';
        $htmlOptions['ajax'] = array(
            'type'=>'GET',
            'url'=>Yii::app()->createUrl('client/campaigns'),
            'dataType'=>"json",
            'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken(),
                          'client_id' =>'js:$("#Link_client_id").val()'),
            'success' => 'function(html){jQuery("#Link_campaign_id").html(html.campaigns);}',
        );
        echo CHtml::dropDownList('Link[client_id]', $_GET['Link']['client_id'], CHtml::listData(Client::model()->actived()->findAll(),'id','company'),$htmlOptions);
        ?>

	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'campaign_id'); ?>
		<?php //echo $form->textField($model,'campaign_id'); ?>
        <?php echo $form->dropDownList($model, 'campaign_id', CHtml::listData($campaigns,'id','name'), array('prompt'=>'-- Select Campaign --')); ?>
		<?php echo $form->error($model,'campaign_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'targeturl'); ?>
		<?php echo $form->textField($model,'targeturl',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'targeturl'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'targetdomain'); ?>
		<?php echo $form->textField($model,'targetdomain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'targetdomain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'anchortext'); ?>
		<?php echo $form->textArea($model,'anchortext',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'anchortext'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category_id'); ?>
		<?php //echo $form->textField($model,'category_id'); ?>
        <?php echo $form->dropDownList($model, 'category_id', $cats); ?>
		<?php echo $form->error($model,'category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tasktype_id'); ?>
		<?php //echo $form->textField($model,'tasktype_id'); ?>
        <?php echo $form->dropDownList($model, 'tasktype_id', $linktasks); ?>
		<?php echo $form->error($model,'tasktype_id'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
    <div class="clear"></div>

	<div class="row">
		<?php echo $form->labelEx($model,'checked'); ?>
		<?php //echo $form->textField($model,'checked'); ?>
        <?php 
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
             'model'=>$model,
            'name' => 'Link[checked]',
            'value' => date("M/d/Y", ($model->checked ? $model->checked : time())), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>Yii::app()->getLocale()->getDateFormat('short', $model->checked),
                'changeMonth' => 'true',
                'changeYear'=>'true',
                'constrainInput' => 'false',
            ),
            // DONT FORGET TO ADD TRUE this will create the datepicker return as string
        ));
        ?>
		<?php echo $form->error($model,'checked'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->