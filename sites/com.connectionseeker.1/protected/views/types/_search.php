<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'type'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'type',array('size'=>8,'maxlength'=>8)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'refid'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'refid'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'typename'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'typename',array('size'=>60,'maxlength'=>256)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'status'); ?>
</td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->