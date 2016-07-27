<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
    'id'=>'taskSearchForm',
));

echo $form->hiddenField($model, 'iostatus', array('value' => $iostatus));
?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'targeturl'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'targeturl',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'anchortext'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'anchortext',array('rows'=>6, 'cols'=>50)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'tierlevel'); ?></td>
	<td class="formSearch" ><?php echo $form->dropDownList($model,'tierlevel',$tiers,array('prompt'=>'[ All ]')); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'rewritten_title'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'rewritten_title',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'desired_domain'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'desired_domain',array('size'=>60,'maxlength'=>255)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'channel_id'); ?></td>
	<td class="formSearch" >
        <?php
        if (!isset($roles['Marketer'])) {
            $htmlOptions = array();
            $htmlOptions['multiple'] = true;
            echo $form->dropDownList($model,'channel_id',$_channels,$htmlOptions);
        }
        ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'duedate'); ?></td>
	<td class="formSearch" >
        <?php 
        //1209600 means 14 days
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model'=>$model,
            'name' => 'Task[duedate]',
            //'value' => date("Y-m-d", $model->duedate), 
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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'content_article_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'content_article_id'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'campaign_id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'campaign_id'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'livedate'); ?></td>
	<td class="formSearch" >
    <?php // echo $form->textField($model,'livedate'); ?>
        <?php 
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model'=>$model,
            'name' => 'Task[livedate]',
            //'value' => date("Y-m-d", $model->livedate), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>"yy-mm-dd",
                'changeMonth' => 'true',
                'changeYear'=>'true',
                'constrainInput' => 'false',
            ),
            'htmlOptions'=>array(
                'style'=>'width:80px;'
            ),

            // DONT FORGET TO ADD TRUE this will create the datepicker return as string
        ));
        ?> - 
        <?php 
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model'=>$model,
            'name' => 'Task[livedate_end]',
            //'value' => date("Y-m-d", $model->livedate_end), 
            // additional javascript options for the date picker plugin
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>"yy-mm-dd",
                'changeMonth' => 'true',
                'changeYear'=>'true',
                'constrainInput' => 'false',
            ),
            'htmlOptions'=>array(
                'style'=>'width:80px;'
            ),
        ));
        ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?></td>
  </tr>
</table>
<table border="0" align="left" cellpadding="0" cellspacing="0">
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
       <?php echo CHtml::Button('Export', array('id' => 'downloadIO', 'type' => 'button', 'value' => 'Export')); ?>
       </div>
       </div>
    </td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->