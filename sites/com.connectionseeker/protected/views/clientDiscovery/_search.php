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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'client_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'client_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'competitora_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'competitora_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'competitora'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'competitora',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'competitorb_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'competitorb_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'competitorb'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'competitorb',array('size'=>60,'maxlength'=>255)); ?>
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
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->