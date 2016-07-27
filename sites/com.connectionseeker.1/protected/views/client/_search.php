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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'company'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'company',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'name'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'contact_name'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'contact_name',array('size'=>60,'maxlength'=>128)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'email'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'telephone'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'telephone',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'cellphone'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'cellphone',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'note'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'note',array('rows'=>2, 'cols'=>38)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified_by'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'assignee'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'assignee'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?>
</td>
	<td class="formSearch" ><?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
</td>
<td class="txtfrm" height="50" >&nbsp;
</td>
	<td class="formSearch" ></td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->