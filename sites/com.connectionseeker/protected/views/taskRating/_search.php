<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'task_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'task_id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'rating'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'rating'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'notes'); ?></td>
	<td class="formSearch" ><?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'writer_name'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'writer_name'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?>
</td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->