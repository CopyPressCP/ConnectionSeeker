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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'old_value'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'old_value',array('rows'=>6, 'cols'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'new_value'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textArea($model,'new_value',array('rows'=>6, 'cols'=>50)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'description'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>500)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'action'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'action',array('size'=>50,'maxlength'=>50)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'model'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'model',array('size'=>50,'maxlength'=>50)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'field'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'field'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'user_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'user_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'model_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'model_id'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->