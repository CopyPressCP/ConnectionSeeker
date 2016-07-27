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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'task_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'task_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'campaign_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'campaign_id'); ?>
</td>
  </tr>
<?php /* ?>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'channel_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'channel_id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'tierlevel'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'tierlevel'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_step0'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_step0'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_step1'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_step1'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_step2'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_step2'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_step3'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_step3'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_step4'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_step4'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_step5'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_step5'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2step0'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2step0'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2step1'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2step1'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2step2'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2step2'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2step3'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2step3'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2step4'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2step4'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2step5'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2step5'); ?>
</td>
  </tr>
<?php */ ?>

  <tr>
    <td>
    <div class="form">
    <div class="row buttons"> &nbsp;&nbsp;
        <?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
    </div>
    </div>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->