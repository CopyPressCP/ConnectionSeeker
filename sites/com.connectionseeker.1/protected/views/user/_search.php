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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'username'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>128)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'salt'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'salt',array('size'=>60,'maxlength'=>128)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'email'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified_by'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'last_visit_time'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'last_visit_time'); ?>
</td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->