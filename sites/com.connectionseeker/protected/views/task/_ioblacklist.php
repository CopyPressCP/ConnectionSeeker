<?php
$iomodel = new Ioblacklist;
$iomodel->domain = "abc.com";
?>
<div class="form" id="blad-dialog-form" title="Blacklist A Domain" style="display:none">

<?php $ioform=$this->beginWidget('CActiveForm', array(
	'id'=>'ioblacklist-form',
	'enableAjaxValidation'=>false,
)); ?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $ioform->errorSummary($iomodel); ?>

    <fieldset>

	<div class="row">
		<?php echo $ioform->labelEx($iomodel,'domain'); ?>
		<?php echo $ioform->textField($iomodel,'domain',array('class'=>"text ui-widget-content ui-corner-all")); ?>
		<?php echo $ioform->error($iomodel,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $ioform->labelEx($iomodel,'isallclient'); ?>
        <?php echo $ioform->dropDownList($iomodel,'isallclient',array("0"=>"Specific Clients","1"=>"All Clients"),array('class'=>"text ui-widget-content ui-corner-all")); ?>
		<?php echo $ioform->error($iomodel,'isallclient'); ?>
	</div>

	<div class="row">
		<?php echo $ioform->labelEx($iomodel,'clients'); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        echo $ioform->dropDownList($iomodel,'clients',CHtml::listData($clients,'id','company'),$htmlOptions);
        ?>
		<?php echo $ioform->error($iomodel,'clients'); ?>
	</div>

	<div class="row">
		<?php echo $ioform->labelEx($iomodel,'isblacklist'); ?>
        <?php echo $ioform->dropDownList($iomodel,'isblacklist',array("0"=>"Warning","1"=>"Blacklist")); ?>
		<?php echo $ioform->error($iomodel,'isblacklist'); ?>
	</div>

	<div class="row">
		<?php echo $ioform->labelEx($iomodel,'notes'); ?>
		<?php echo $ioform->textArea($iomodel,'notes',array('style'=>"width:380px;height:100px;",'class'=>"text ui-widget-content ui-corner-all")); ?>
		<?php echo $ioform->error($iomodel,'notes'); ?>
	</div>

	</fieldset>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(document).ready(function() {
    $("#Ioblacklist_clients").multiselect({noneSelectedText:'Select Clients',selectedList:5}).multiselectfilter();
});
</script>


</div><!-- form -->

<script type="text/javascript">
$(function() {
    var bldomain = $("#Ioblacklist_domain"),
        blreason = $("#Ioblacklist_notes"),
        allFields = $( [] ).add(bldomain).add(blreason),
        tips = $( ".validateTips" );

    function updateTips( t ) {
        tips.text( t )
            .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }

    $( "#ioblacklist-form" ).dialog({
        autoOpen: false,
        height: 460,
        width: 450,
        modal: true,
        buttons: {
            "Blacklist A Domain": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );
                bValid = bValid && checkLength( bldomain, "domain", 3, 100 );
                bValid = bValid && checkLength( blreason, "reason", 2, 1000 );
                if ( bValid ) {
                    //AJAX here.
                    $.ajax({
                        'type': 'POST',
                        'dataType': 'json',
                        'url': "<?php echo Yii::app()->createUrl('/ioblacklist/ajaxcreate');?>",
                        'data': $("#ioblacklist-form").serialize(),
                        'success':function(data){
                            if (data.success){
                                alert(data.msg);
                            } else {
                                alert(data.msg);
                            }
                        },
                        'complete':function(XHR,TS){XHR = null;}
                    });

                    $( this ).dialog( "close" );
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            allFields.removeClass( "ui-state-error" );
            blreason.val("");
        }
    });

    $("a[name^='ioblacklist']").each(function() {
        $(this).click(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            $("#ioblacklist").val(currenttrid);
            $( "#ioblacklist-form" ).dialog( "open" );

            return false;
        });
    });
});
</script>
