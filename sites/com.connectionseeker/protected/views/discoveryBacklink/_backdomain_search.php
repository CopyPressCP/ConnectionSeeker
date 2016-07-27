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
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'discovery_id'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'discovery_id'); ?>
</td>
  </tr>
  <tr>
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'domain_id'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'domain_id',array('size'=>20,'maxlength'=>20)); ?>
</td>
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
</td>
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'hubcount'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'hubcount'); ?>
</td>
  </tr>
  <tr>
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'max_acrank'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'max_acrank'); ?>
</td>
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'status'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'status'); ?>
</td>
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'fresh_called'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'fresh_called'); ?>
</td>
  </tr>
  <tr>
    <td class="txtfrm" height="50" ><?php echo $form->label($model,'historic_called'); ?>
</td>
    <td class="formSearch" ><?php echo $form->textField($model,'historic_called'); ?>
</td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->