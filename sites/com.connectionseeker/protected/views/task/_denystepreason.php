<?php
$umodel = User::model()->findByPk(Yii::app()->user->id);
$cremail = $umodel->email;
$denyaction = "deny";
?>
<div class="form" id="cr-dialog-form" title="Deny with reason" style="display:none">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'denyreason-form',
	'enableAjaxValidation'=>false,
)); ?>
	<input type="hidden" name="denytaskid" id="denytaskid" value="" />
	<input type="hidden" name="denyaction" id="denyaction" value="<?php echo $denyaction;?>" />

	<p class="validateTips">Fields with <span class="required">*</span> are required.</p>

	<fieldset>
		<label for="email">Email From</label>
		<input type="text" name="denyemail" id="denyemail" value="<?php echo $cremail;?>" size="36" class="text ui-widget-content ui-corner-all" />
        <div class="clear"></div>
		<label for="name"><?php echo isset($roles['Marketer']) ? "Concerns/Feedback" : "Description"; ?></label>
		<textarea name="denyreason" id="denyreason" class="ui-widget-content ui-corner-all" style="height:200px;width:360px;"></textarea>
	</fieldset>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function() {
    var denyemail = $("#denyemail"),
        denyreason = $("#denyreason"),
        allFields = $( [] ).add(denyemail).add(denyreason),
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

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }

    $( "#denyreason-form" ).dialog({
        autoOpen: false,
        height: 460,
        width: 450,
        modal: true,
        buttons: {
            "Deny With Reason": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );

                bValid = bValid && checkLength( denyemail, "email", 6, 80 );
                bValid = bValid && checkRegexp( denyemail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );

                if ( bValid ) {
                    //AJAX here.
                    $.ajax({
                        'type': 'POST',
                        'dataType': 'json',
                        'url': "<?php echo Yii::app()->createUrl('/contentStep/reason');?>",
                        'data': $("#denyreason-form").serialize(),
                        'success':function(data){
                            if (data.success){
                                //donothing for now;
                                alert(data.msg);
                                $.fn.yiiGridView.update('task-grid');
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
            denyreason.val("");
        }
    });

    $("a[name^='ioreason']").each(function() {
        $(this).click(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            $("#denytaskid").val(currenttrid);
            //alert($("#denytaskid").val());
            $( "#denyreason-form" ).dialog( "open" );

            return false;
        });
    });
});
</script>
