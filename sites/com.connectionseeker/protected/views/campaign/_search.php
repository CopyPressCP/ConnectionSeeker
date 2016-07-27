<?php
//##print_r($doneoptions);
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'name'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'client_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'client_id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain_id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'category'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'category'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'category_str'); ?></td>
	<td class="formSearch" ><?php echo $form->textArea($model,'category_str',array('rows'=>6, 'cols'=>50)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'notes'); ?></td>
	<td class="formSearch" ><?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'status'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'rct_internal_done'); ?></td>
	<td class="formSearch"><?php echo $form->dropDownList($model,'rct_internal_done', $doneoptions, array('prompt'=>'---')); ?></td>
    <td colspan="4">&nbsp;</td>
  </tr>
  <!-- <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified_by'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified_by'); ?>
</td>
  </tr> -->
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->