<?php
$umodel = User::model()->findByPk(Yii::app()->user->id);
$cremail = $umodel->email;
?>
<div class="form" id="cr-dialog-form" title="Send Client Request">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'client-request-form',
	'enableAjaxValidation'=>false,
)); ?>
	<input type="hidden" name="crtaskid" id="crtaskid" value="" />

	<p class="validateTips">Fields with <span class="required">*</span> are required.</p>

	<fieldset>
		<label for="email">Email</label>
		<input type="text" name="cremail" id="cremail" value="<?php echo $cremail;?>" class="text ui-widget-content ui-corner-all" />
		<label for="name">Concerns/Feedback</label>
		<textarea name="crclientrequest" id="crclientrequest" class="ui-widget-content ui-corner-all" style="height:240px;width:360px;"></textarea>
	</fieldset>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function() {
    var cremail = $("#cremail"),
        crclientrequest = $("#crclientrequest"),
        allFields = $( [] ).add(cremail).add(crclientrequest),
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

    $( "#cr-dialog-form" ).dialog({
        autoOpen: false,
        height: 560,
        width: 450,
        modal: true,
        buttons: {
            "Send Request": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );

                bValid = bValid && checkLength( cremail, "email", 6, 80 );
                bValid = bValid && checkRegexp( cremail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );

                if ( bValid ) {
                    //AJAX here.
                    $.ajax({
                        'type': 'POST',
                        'dataType': 'json',
                        'url': "<?php echo Yii::app()->createUrl('/task/request');?>",
                        'data': $("#client-request-form").serialize(),
                        'success':function(data){
                            if (data.success){
                                //donothing for now;
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
            //allFields.val( "" ).removeClass( "ui-state-error" );
            allFields.removeClass( "ui-state-error" );
            crclientrequest.val("");
        }
    });

    $("a.clientrequest").each(function() {
        $(this).click(function(){
            var gvid = $(this).parent().parent().closest('.grid-view').attr('id');
            var gvoffset = $(this).parent().parent().prevAll().length;
            var currenttrid = $.fn.yiiGridView.getKey(gvid, gvoffset);
            $("#crtaskid").val(currenttrid);
            //alert($("#crtaskid").val());
            $( "#cr-dialog-form" ).dialog( "open" );
        });
    });
});
</script>
