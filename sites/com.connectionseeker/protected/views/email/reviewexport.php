<h2><?php echo Yii::t("Email", "Export Review Of History Data Email"); ?></h2>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'POST',
    'id'=>'exportReviewForm',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo Yii::t("Email", "Date Start"); ?></td>
	<td class="formSearch">
       <div class="form">
            <?php 
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                //'model'=>$model,
                'name' => 'Export[date_start]',
                'id' => 'Export_date_start',
                'value' => date("Y-m-d", time() - 86400), 
                // additional javascript options for the date picker plugin
                'options'=>array(
                    'showAnim'=>'fold',
                    'dateFormat'=>'yy-mm-dd',
                    'changeMonth' => 'true',
                    'changeYear'=>'true',
                    'constrainInput' => 'false',
                ),
                'htmlOptions'=>array(
                    'style'=>'width:100px;',
                ),
                // DONT FORGET TO ADD TRUE this will create the datepicker return as string
            ));
            ?>
       </div>
    </td>
	<td class="txtfrm" height="50" ><?php echo Yii::t("Email", "Date End"); ?></td>
	<td class="formSearch" >
       <div class="form">
            <?php 
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                //'model'=>$model,
                'name' => 'Export[date_end]',
                'id' => 'Export_date_end',
                'value' => date("Y-m-d", time()), 
                // additional javascript options for the date picker plugin
                'options'=>array(
                    'showAnim'=>'fold',
                    'dateFormat'=>'yy-mm-dd',
                    'changeMonth' => 'true',
                    'changeYear'=>'true',
                    'constrainInput' => 'false',
                ),
                'htmlOptions'=>array(
                    'style'=>'width:100px;',
                ),
                // DONT FORGET TO ADD TRUE this will create the datepicker return as string
            ));
            ?>
       </div>
    </td>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td>
        <div class="form">
            <div class="row buttons"> 
            <?php echo CHtml::Button('Export', array('id' => 'exportReviewData', 'type' => 'submit', 'value' => 'Export')); ?>
            </div>
        </div>
    </td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- export form -->