<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
    'id'=>'ioSearchForm',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'task_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'task_id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'campaign_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'campaign_id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'channel_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'channel_id'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_current'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_current'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_accepted'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_accepted'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_approved'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_approved'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_pending'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_pending'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_completed'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_completed'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_denied'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'date_denied'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_initial'); ?></td>
	<td class="formSearch"><?php echo $form->textField($model,'date_initial'); ?></td>
	<td class="txtfrm"></td>
	<td class="formSearch"></td>
	<td class="txtfrm"></td>
	<td class="formSearch"></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2current'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2current'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2accepted'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2accepted'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2approved'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2approved'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2pending'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2pending'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2completed'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2completed'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'time2denied'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'time2denied'); ?></td>
  </tr>
  <tr>
    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit', 'value' => 'Search')); ?>
       </div>
       </div>
    </td>
    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php echo CHtml::Button('Export', array('id' => 'downloadIOH', 'type' => 'button', 'value' => 'Export')); ?>
       </div>
       </div>
    </td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->