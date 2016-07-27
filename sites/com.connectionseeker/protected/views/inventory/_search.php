<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
    'id'=>'inventorySearchForm',
));

echo $form->hiddenField($model,'currentaction');

$_mname = get_class($model);
$_linked = array('1'=>'Currently linked to domain','2'=>'Not linked to domain','3'=>'Domains with no links built');
$operators = array(
    '='  => '=',       // =
    '>'  => '>',    // >
    '>=' => '>=',   // >=
    '<'  => '<',    // <
    '<=' => '<=',   // <=
    '!=' => '!=',      // !=
    //'LIKE'     => 'LIKE',
    //'NOT LIKE' => 'NOT LIKE',
);
?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="formSearch" ><?php echo $form->label($model,'id'); ?> 
                            <?php echo $form->textField($model,'id',array('style'=>'width:40px;')); ?></td>
	<td class="formSearch" ><?php echo $form->label($model,'domain'); ?>
                            <?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?></td>
    <td class="formSearch" >
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $_categories = array('-1'=>"Uncategorized") + $_categories;
        echo $form->dropDownList($model,'category',$_categories,$htmlOptions); ?></td>
	<td class="formSearch" >
        <?php
        if (!isset($roles['Marketer'])) {
            $htmlOptions = array();
            $htmlOptions['multiple'] = true;
            //echo $form->dropDownList($model,'channel_id',$_channels,$htmlOptions);
            echo $form->dropDownList($model,'owner_channel_id',$_channels,$htmlOptions);
        }
        ?></td>
	<td class="formSearch" ><?php echo CHtml::label('Site Type', $_mname.'_stype'); ?>
        <?php echo CHtml::dropDownList($_mname.'[stype]',$_GET[$_mname]['stype'],$_stypes,array('prompt'=>'-- All --')); ?></td>
  </tr>
</table>

<div class="clear"></div>

<table border="0" align="left" cellpadding="0" cellspacing="0" width="90%">
  <tr>
	<td class="formSearch"><?php echo $form->label($model,'status'); ?> 
                           <?php echo $form->dropDownList($model,'status',$_status,array('prompt'=>'[ All ]')); ?></td>

	<td class="formSearch"><?php echo CHtml::label('PR', $_mname.'_googlepropr'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[googlepropr]',$_GET[$_mname]['googlepropr'],$operators); ?>
       <?php echo CHtml::textField($_mname.'[googlepr]',$_GET[$_mname]['googlepr'],array('style'=>'width:30px;')); ?>
    </td>
    <?php /* ?>
	<td class="formSearch"><?php echo CHtml::label('ACRank', $_mname.'_acrankopr'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[acrankopr]',$_GET[$_mname]['acrankopr'],$operators); ?>
       <?php echo CHtml::textField($_mname.'[acrank]',$_GET[$_mname]['acrank'],array('style'=>'width:30px;')); ?>
    </td>
    <?php */ ?>
	<td class="formSearch"><?php echo CHtml::label('Alexa', $_mname.'_alexarankopr'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[alexarankopr]',$_GET[$_mname]['alexarankopr'],$operators); ?>
       <?php echo CHtml::textField($_mname.'[alexarank]',$_GET[$_mname]['alexarank'],array('style'=>'width:30px;')); ?>
    </td>

	<td class="formSearch"><?php echo CHtml::label('Age', $_mname.'_ageopr'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[ageopr]',$_GET[$_mname]['ageopr'],$operators); ?>
       <?php echo CHtml::textField($_mname.'[age]',$_GET[$_mname]['age'],array('style'=>'width:30px;')); ?>
    </td>
    <?php /* ?>
	<td class="formSearch"><?php echo CHtml::label('HubCount', $_mname.'_hubcountopr'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[hubcountopr]',$_GET[$_mname]['hubcountopr'],$operators); ?>
       <?php echo CHtml::textField($_mname.'[hubcount]',$_GET[$_mname]['hubcount'],array('style'=>'width:30px;')); ?>
    </td>
    <?php */ ?>
	<td class="formSearch"><?php echo CHtml::label('Acquired Date', $_mname.'_acquireddate'); ?> 
       <?php echo CHtml::textField($_mname.'[acquireddate]',$_GET[$_mname]['acquireddate'],array('style'=>'width:30px;')); ?>
    </td>
	<td class="formSearch"><?php echo CHtml::label('View Cart', $_mname.'_cart'); ?> 
       <?php echo CHtml::checkBox($_mname.'[cart]',$_GET[$_mname]['cart'],array('class'=>'chkbox')); ?>
    </td>
  </tr>
</table>
<div class="clear"></div>

<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="formSearch" nowrap="nowrap"><?php echo CHtml::label('Linked', $_mname.'_islinked'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[islinked]',$_GET[$_mname]['islinked'],$_linked,array('prompt'=>'-- All --')); ?>
    </td>

	<td class="formSearch" nowrap="nowrap"><?php echo CHtml::label('Moz Rank', $_mname.'__mozrankopr'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[mozrankopr]',$_GET[$_mname]['mozrankopr'],$operators); ?>
       <?php echo CHtml::textField($_mname.'[mozrank]',$_GET[$_mname]['mozrank'],array('style'=>'width:30px;')); ?>
    </td>

	<td class="formSearch" nowrap="nowrap"><?php echo CHtml::label('Authority', $_mname.'__authorityopr'); ?> 
       <?php echo CHtml::dropDownList($_mname.'[authorityopr]',$_GET[$_mname]['authorityopr'],$operators); ?>
       <?php echo CHtml::textField($_mname.'[authority]',$_GET[$_mname]['authority'],array('style'=>'width:30px;')); ?>
    </td>
	<td class="formSearch" ><?php echo CHtml::label('Site Type', $_mname.'_otype'); ?>
        <?php echo CHtml::dropDownList($_mname.'[otype]',$_GET[$_mname]['otype'],$_otypes,array('prompt'=>'-- All --')); ?></td>
	<td class="formSearch" >
        <?php
        /*
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $form->dropDownList($model,'accept_tasktype',$_linktasks,$htmlOptions);
        */
        ?>
    </td>
  </tr>
</table>
<div class="clear"></div>

<table border="0" align="left" cellpadding="0" cellspacing="0" width="90%">
  <tr>
	<td class="formSearch"><?php echo CHtml::label('Campaign Include', $_mname.'_campaign_include'); ?> 
       <?php echo CHtml::textField($_mname.'[campaign_include]',$_GET[$_mname]['campaign_include'],array('style'=>'width:120px;')); ?>
    </td>

	<td class="formSearch"><?php echo CHtml::label('Campaign Exclude', $_mname.'_campaign_exclude'); ?> 
       <?php echo CHtml::textField($_mname.'[campaign_exclude]',$_GET[$_mname]['campaign_exclude'],array('style'=>'width:120px;')); ?>
    </td>

    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php echo CHtml::submitButton('Search', array('id' => 'searchButton', 'type' => 'submit', 'value' => 'Search')); ?>
       </div>
       </div>
    </td>

    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php
       if (!isset($roles['Marketer'])) {
           echo CHtml::Button('Download', array('id' => 'downloadInventory', 'type' => 'button', 'value' => 'Download'));
       }
       ?>
       </div>
       </div>
    </td>
  </tr>
</table>

<?php $this->endWidget(); ?>
<!-- search-form -->
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td>
    <?php echo CHtml::label('Start Acquired Date', 'acquireddate'); ?> 
    </td>
    <td>
       <div class="form">
            <?php 
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'name' => 'Inventory[acquireddate_start]',
                'value' => date("Y-m-d", time() + 2592000), 
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
    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php
       if (!isset($roles['Marketer'])) {
           echo CHtml::Button('Export', array('id' => 'exportInventoryAcquired', 'type' => 'button', 'value' => 'Export Acquired'));
       }
       ?>
       </div>
       </div>
    </td>
    <td>
       <div class="form">
       <div class="row buttons"> &nbsp;&nbsp;
       <?php
       if (!isset($roles['Marketer'])) {
           echo CHtml::Button('Export', array('id' => 'exportInventoryDeniedIO', 'type' => 'button', 'value' => 'Export ALL Denied IOs'));
       }
       ?>
       </div>
       </div>
    </td>
  </tr>
</table>

<script type="text/javascript">
<!--
    $("#exportInventoryAcquired").click(function(){
        var url = "<?php echo Yii::app()->createUrl('/inventory/exportacquired');?>";
        url = url + "&Inventory[acquireddate_start]=" + $('#Inventory_acquireddate_start').val();

        //added start @2016-02-18
        var rparent = $('#inventorySearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#inventorySearchForm input[type=hidden][name=r]').remove();
        url = url + "&" + $('#inventorySearchForm').serialize();
        rparent.html(rself);
        //added end @2016-02-18

        window.location.href = url;
    });

    $("#exportInventoryDeniedIO").click(function(){
        var url = "<?php echo Yii::app()->createUrl('/download/deniedDomainWithIO');?>";
        var rparent = $('#inventorySearchForm input[type=hidden][name=r]').parent();
        var rself = rparent.html();
        $('#inventorySearchForm input[type=hidden][name=r]').remove();
        //alert($('#inventorySearchForm').serialize());
        url = url + "&" + $('#inventorySearchForm').serialize();
        rparent.html(rself);
        window.location.href = url;
    });
//-->
</script>
