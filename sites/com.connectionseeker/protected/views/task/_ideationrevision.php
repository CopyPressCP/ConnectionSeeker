<?php
$revisionaction = "revision";
?>
<div class="form" id="cr-dialog-form" title="revision ideation with reason" style="display:none">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'revisionreason-form',
	'enableAjaxValidation'=>false,
)); ?>
	<input type="hidden" name="revisiontaskid" id="revisiontaskid" value="" />
	<input type="hidden" name="revisionaction" id="revisionaction" value="<?php echo $revisionaction;?>" />

	<p class="validateTips">Fields with <span class="required">*</span> are required.</p>

	<fieldset>
		<label for="revisionreason"><?php echo "Ideation Note"; ?></label>
		<textarea name="revisionreason" id="revisionreason" class="ui-widget-content ui-corner-all" style="height:200px;width:360px;"></textarea>
	</fieldset>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function() {
    var revisionreason = $("#revisionreason"),
        allFields = $( [] ).add(revisionreason),
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

    $( "#revisionreason-form" ).dialog({
        autoOpen: false,
        height: 460,
        width: 450,
        modal: true,
        buttons: {
            "Revision Ideation With Reason": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );

                bValid = bValid && checkLength( revisionreason, "revisionreason", 3, 1000 );

                if ( bValid ) {
                    //AJAX here.
                    $.ajax({
                        'type': 'POST',
                        'dataType': 'json',
                        'url': "<?php echo Yii::app()->createUrl('/contentStep/revision');?>",
                        'data': $("#revisionreason-form").serialize(),
                        'success':function(data){
                            if (data.success){
                                alert(data.msg);
                                //##$.fn.yiiGridView.update('task-grid');
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
            //allFields.val( "" ).removeClass( "ui-state-error" );
            allFields.removeClass( "ui-state-error" );
            revisionreason.val("");
        }
    });

    /*
    $("a[name^='ideationrevision']").each(function() {
        $(this).click(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            $("#revisiontaskid").val(currenttrid);
            //alert($("#revisiontaskid").val());
            $( "#revisionreason-form" ).dialog( "open" );

            return false;
        });
    });
    */
});
</script>
