<?php
?>

<div id="innermenu">
    <?php
    $this->widget('zii.widgets.CMenu', array(
        'firstItemCssClass'=>'first',
        'lastItemCssClass'=>'last',
        'htmlOptions'=>array('class'=>'actions'),
        'items'=>array(
            array(
                'label'=>Menus::t('core', 'Add a New Site'),
                'url'=>array('ioblacklist/create'),
            ),
            array(
                'label'=>Menus::t('core', 'Manage Blacklist'),
                'url'=>array('ioblacklist/index'),
            ),
            array(
                'label'=>Menus::t('core', 'Download Upload Template'),
                'url'=>'assets/upload-io-blacklist.xls',
            ),
        )
    ));
    ?>
    <div class="clear"></div>

</div>
<h2>Upload File (Support CSV, Excel2005, Excel2007, Gnumeric and Openoffice Calc for now)</h2>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("ioblacklist/upload"),
	'method'=>'post',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'id'=>'ioblacklist-form',
	'enableAjaxValidation'=>false,
)); ?>


	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
        <?php echo CHtml::label('Import from local file', 'Ioblacklist_upfile');?>
        <?php echo CHtml::activeFileField($model, 'upfile', array('style'=>'height:30px;'));?>
	</div>

    <div class="clear"></div>

    <div class="row buttons">
		<?php echo CHtml::submitButton('Upload'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
