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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'competitor_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'competitor_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'fresh_called'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'fresh_called'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'historic_called'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'historic_called'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'url'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>2048)); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'googlepr'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'googlepr'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'acrank'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'acrank'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'anchortext'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'anchortext',array('size'=>60,'maxlength'=>255)); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'date'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'flagredirect'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'flagredirect'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'flagframe'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'flagframe'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'flagnofollow'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'flagnofollow'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'flagimages'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'flagimages'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'flagdeleted'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'flagdeleted'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'flagalttext'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'flagalttext'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'flagmention'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'flagmention'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'targeturl'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'targeturl',array('size'=>60,'maxlength'=>255)); ?>
</td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->