<?php
//http://www.alexa.com/topsites/countries
//for the short term, we can put the countries into an array, but for long term we need create a table to store the countries into there.

$inoprs = array(
    "IN" => "IN",
    "NOT IN" => "NOT IN",
);

$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
    'id'=>'domainSearchForm',
)); ?>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'id'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'domain'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'tld'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'tld',array('size'=>10,'maxlength'=>10)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'googlepr'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'googlepr'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'alexarank'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'alexarank',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'price'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Price Exist]';
    //echo $form->dropDownList($model, 'price', array("1" => "Yes", "-1"=>"No"), $htmlOptions);
    echo $form->dropDownList($model, 'price', array("1" => "Yes"), $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'email'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'host_country'); ?></td>
	<td class="formSearch" >
    <?php echo $form->textField($model,'host_country',array('size'=>60,'maxlength'=>64)); ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'stype'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Site Type]';
    echo $form->dropDownList($model, 'stype', $stypes, $htmlOptions);
    ?></td>
  </tr>
  <tr>

<?php if ($this->action->id == "bulkattr") {
    //echo CHtml::dropDownList('Domain[category_inoprs]', $_GET['Domain']['category_inoprs'], $inoprs);
?>
	<td class="txtfrm" height="50" ><?php echo CHtml::label('Exclude Category', 'Domain_excludecategory'); ?></td>
	<td class="formSearch" >
    <?php
    $htmlOptions = array();
    $htmlOptions['multiple'] = true;
    $htmlOptions['name'] = 'Domain[excludecategory]';
    $htmlOptions['id'] = 'Domain_excludecategory';
    echo $form->dropDownList($model, 'category', $categories, $htmlOptions);
    ?></td>
<?php } else { ?>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'otype'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Outreach Type]';
    echo $form->dropDownList($model, 'otype', $otypes, $htmlOptions);
    ?></td>
<?php } ?>

	<td class="txtfrm" height="50" ><?php echo $form->label($model,'category'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['multiple'] = true;
    //$htmlOptions['prompt'] = '[Category]';
    $categories = array('-1'=>"Uncategorized") + $categories;
    echo $form->dropDownList($model, 'category', $categories, $htmlOptions);
    ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'touched_status'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['multiple'] = true;
    //$htmlOptions['prompt'] = '[Status]';
    echo $form->dropDownList($model, 'touched_status', $touchedstatus, $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'mozrank'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'mozrank',array('size'=>60,'maxlength'=>64)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'mozauthority'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'mozauthority',array('size'=>60,'maxlength'=>64)); ?>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'tierlevel'); ?></td>
	<td class="formSearch" >
    <?php
    $htmlOptions = array();
    //$htmlOptions['prompt'] = '[Select Tier]';
    $htmlOptions['multiple'] = true;
    echo $form->dropDownList($model, 'tierlevel', $tierleveles, $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50"><?php echo $form->label($model,'semrushkeywords'); ?></td>
	<td class="formSearch"><?php echo $form->textField($model,'semrushkeywords',array('size'=>60,'maxlength'=>128)); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'meta_keywords'); ?></td>
	<td class="formSearch"><?php echo $form->textField($model,'meta_keywords',array('size'=>60,'maxlength'=>128)); ?></td>
	<td class="txtfrm"><?php echo $form->label($model,'meta_description'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'meta_description'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50"></td>
	<td class="formSearch"></td>
	<td class="txtfrm"></td>
	<td class="formSearch"></td>
	<td class="txtfrm">Auto-Fill Form</td>
	<td class="formSearch" ><?php
    $autofillarr = array();
    $autofillarr["ctrl_name"] = "domain";
    $autofillarr["view_name"] = "outreach";
    echo CHtml::dropDownList("searches_autofill", 0, CHtml::listData(Searches::model()->myOwn()->findAllByAttributes($autofillarr),'id','name'), array('prompt'=>'-- Select --')); ?></td>
  </tr>
</table>
<table border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td>
        <div class="form">
            <div class="row buttons"> 
            <?php echo CHtml::submitButton('Search', array('id' => 'searchDomain', 'type' => 'submit' , 'value' => 'Search')); ?> 
            </div>
        </div>
    </td>
    <?php if ($this->action->id != "bulkattr") { ?>
    <td>
        <div class="form">
            <div class="row buttons"> 
            <?php echo CHtml::Button('Download', array('id' => 'downloadDomain', 'type' => 'button', 'value' => 'Download')); ?>
            </div>
        </div>
    </td>
    <?php } ?>
    <td class="formSearch" style="width:100px;">
        &nbsp;
    </td>
    <td class="formSearch">
        <?php echo CHtml::textField('Searches[name]',"",array("id"=>'Searches_name')); ?>&nbsp;&nbsp;&nbsp;
    </td>
    <td>
        <div class="form">
            <div class="row buttons"> 
            <?php echo CHtml::Button('savePrePopulate', array('id' => 'savePrePopulate', 'type' => 'button', 'value' => 'Save Pre-Populate')); ?>
            </div>
        </div>
    </td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#Domain_touched_status").multiselect({noneSelectedText:'Select Status',selectedList:5}).multiselectfilter();
    $("#Domain_category").multiselect({noneSelectedText:'Include All Categories',selectedList:5}).multiselectfilter();
    $("#Domain_excludecategory").multiselect({noneSelectedText:'Exclude Categories',selectedList:5}).multiselectfilter();
    $("#Domain_tierlevel").multiselect({noneSelectedText:'Select Tier',selectedList:5}).multiselectfilter();


    $("#savePrePopulate").click(function(){
        $("#Searches_name").val($.trim($("#Searches_name").val()));
        if ($("#Searches_name").val() == "") {
            $("#Searches_name").focus();
            alert("Please Provide Auto-fill Name.");
            return false;
        }
        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/searches/create');?>",
            'data': $('#domainSearchForm').serialize(),
            'success':function(data){
                //donothing for now;
                if (data.success){
                    //do something here.
                    $("#searches_autofill").append("<option value='"+data.id+"'>"+data.name+"</option>");  
                    alert(data.msg);
                } else {
                    alert(data.msg);
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $("#searches_autofill").change(function() {
        if (this.value == "") return true;
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/searches/view');?>",
            'data': {'id': this.value},
            'success':function(data){
                if (data.searches){
                    $.each(data.searches.Domain,function(name,value) {
                        //alert(name);
                        name = "#Domain_"+name;
                        $(name).val(value);
                    });
                    $.each(["category","touched_status","excludecategory","tierlevel"], function(i,v){
                        //$("#Domain_category").multiselect('refresh');
                        $("#Domain_"+v).multiselect('refresh');
                    });
                } else {
                    //do something here.
                }
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });
});
</script>
