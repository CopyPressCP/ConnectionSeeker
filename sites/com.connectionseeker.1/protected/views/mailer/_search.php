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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'user_alias'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'user_alias',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'smtp_host'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'smtp_host',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'smtp_port'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'smtp_port',array('size'=>5,'maxlength'=>5)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'pop3_host'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'pop3_host',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'pop3_port'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'pop3_port',array('size'=>5,'maxlength'=>5)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'username'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'display_name'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'display_name',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'email_from'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'email_from',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'reply_to'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'reply_to',array('size'=>60,'maxlength'=>255)); ?>
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