<?php
//print_r($types);

$actions = array(
    "1"=>"Add links to inventory directly without QA (Only enable for admin)",
    "2"=>"Add links to inventory with QA",
    "3"=>"Finish link building task and do batch QA",
    "4"=>"Add domains to inventory directly Or Edit inventory domains",
);


?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("inventory/upload"),
	'method'=>'post',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'id'=>'inventory-form',
	'enableAjaxValidation'=>false,
)); ?>
    <h2>Upload File (Support CSV, Excel2005, Excel2007, Gnumeric and Openoffice Calc for now)</h2>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
        <?php echo CHtml::label('What you wanna do?', 'Inventory_action');?>

        <?php echo CHtml::dropDownList('Inventory[action]', $_GET['Inventory']['action'], $actions, array('onchange'=>'doAction();', 'prompt'=>'-- What you gonna do? --','style'=>'width:430px;')); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Client', 'Inventory_client_id');?>

        <?php
        $htmlOptions = array();
        $htmlOptions['prompt'] = '-- Select Client --';
        $htmlOptions['ajax'] = array(
            'type'=>'GET',
            'url'=>Yii::app()->createUrl('client/campaigns'),
            'dataType'=>"json",
            'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken(),
                          'client_id' =>'js:$("#Inventory_client_id").val()'),
            'success' => 'function(html){jQuery("#Inventory_campaign_id").html(html.campaigns);}',
        );
        echo CHtml::dropDownList('Inventory[client_id]', $_GET['Inventory']['client_id'], CHtml::listData(Client::model()->actived()->findAll(),'id','company'),$htmlOptions);
        ?>

	</div>

	<div class="row">
        <?php echo CHtml::label('Campaign', 'Inventory_campaign_id');?>

        <?php echo CHtml::dropDownList('Inventory[campaign_id]', $_GET['Inventory']['campaign_id'], array(), array('prompt'=>'-- Select Campaign --')); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Channel', 'Inventory_channel_id');?>

        <?php echo CHtml::dropDownList('Inventory[channel_id]', $_GET['Inventory']['channel_id'], CHtml::listData(Types::model()->actived()->bytype("channel")->findAll(), 'refid','typename'), array('prompt'=>'-- Select Channel --')); ?>
	</div>

	<div class="row">
        <?php echo CHtml::label('Import from local file', 'Inventory_upfile');?>

        <?php echo CHtml::activeFileField($model, 'upfile', array('style'=>'height:30px;'));?>

	</div>

	<div class="clear"></div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->


<script type="text/javascript">
//<![CDATA[

function doAction(act) {
    var elms = ["#Inventory_channel_id","#Inventory_client_id","#Inventory_campaign_id"];
    var act = $("#Inventory_action").val();
    if (act == "") act = 3;

    act = parseInt(act);//string to int

    switch (act){
       case 1:
       case 2:
         $.each(elms, function(i,v){
           $(v).attr("disabled",false);
         });
         break;
       case 3:
       case 4:
         $.each(elms, function(i,v){
           //alert($(v).val());
           if (act == 4 && v == "#Inventory_channel_id") {
             $(v).attr("disabled",false);
           } else {
             $(v).attr("disabled",true);
           }
         });
         break;
    }
}

doAction();
//]]>
</script>

