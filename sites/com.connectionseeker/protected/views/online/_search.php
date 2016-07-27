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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'user_id'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'user_id'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'date_tracked'); ?></td>
	<td class="formSearch" >
    <?php 
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'model'=>$model,
        'name' => 'Online[date_tracked]',
        // additional javascript options for the date picker plugin
        'options'=>array(
            'showAnim'=>'fold',
            'dateFormat'=>"yy-mm-dd",
            'changeMonth' => 'true',
            'changeYear'=>'true',
            'constrainInput' => 'false',
        ),
        // DONT FORGET TO ADD TRUE this will create the datepicker return as string
    ));
    ?>
    </td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'total_online'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'total_online'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'login_time'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'login_time'); ?>
</td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'session_online'); ?>
</td>
	<td class="formSearch" ><?php echo $form->textField($model,'session_online'); ?>
</td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'last_operation_time'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'last_operation_time'); ?>

	<td class="txtfrm" height="50" ><?php echo $form->label($model,'datefrom'); ?></td>
	<td class="formSearch" >
    <?php 
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'model'=>$model,
        'name' => 'Online[datefrom]',
        // additional javascript options for the date picker plugin
        'options'=>array(
            'showAnim'=>'fold',
            'dateFormat'=>"yy-mm-dd",
            'changeMonth' => 'true',
            'changeYear'=>'true',
            'constrainInput' => 'false',
        ),
        // DONT FORGET TO ADD TRUE this will create the datepicker return as string
    ));
    ?>
    </td>

	<td class="txtfrm" height="50" ><?php echo $form->label($model,'dateto'); ?></td>
	<td class="formSearch" >
    <?php 
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'model'=>$model,
        'name' => 'Online[dateto]',
        'options'=>array(
            'showAnim'=>'fold',
            //'dateFormat'=>Yii::app()->getLocale()->getDateFormat('short', $model->dateto),
            'dateFormat'=>"yy-mm-dd",
            'changeMonth' => 'true',
            'changeYear'=>'true',
            'constrainInput' => 'false',
        ),
        // DONT FORGET TO ADD TRUE this will create the datepicker return as string
    ));
    ?>
    </td>
  </tr>
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?>
</td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->