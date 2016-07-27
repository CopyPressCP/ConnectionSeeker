<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm"><?php echo $form->label($model,'id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'id'); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'domain_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'domain'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm"><?php echo $form->label($model,'isallclient'); ?></td>
	<td class="formSearch" ><?php echo $form->dropDownList($model,'isallclient',$yesorno, array('prompt'=>'')); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'clients'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'clients',array('size'=>60,'maxlength'=>500)); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'clients_str'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'clients_str',array('size'=>60,'maxlength'=>1000)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm"><?php echo $form->label($model,'notes'); ?></td>
	<td class="formSearch" ><?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'isblacklist'); ?></td>
	<td class="formSearch" ><?php echo $form->dropDownList($model,'isblacklist',$yesorno, array('prompt'=>'')); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'created'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm"><?php echo $form->label($model,'created_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'modified'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'modified_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified_by'); ?></td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->