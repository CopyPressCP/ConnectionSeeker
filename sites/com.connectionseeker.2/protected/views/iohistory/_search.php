<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'task_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'task_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'iostatus'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'iostatus'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'timeline'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'timeline'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'role'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'role',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?>
</td>
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