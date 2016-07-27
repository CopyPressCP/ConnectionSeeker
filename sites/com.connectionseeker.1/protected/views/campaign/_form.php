<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
//may be we can use another dropdown plugin http://harvesthq.github.com/chosen/
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

$types = Types::model()->actived()->bytype('category')->findAll();
$cats = CHtml::listData($types, 'refid', 'typename');

if ($model->category) {
    $_category = explode("|", $model->category);
    array_pop($_category);
    array_shift($_category);
    $model->category = $_category;
}

$roles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Marketer'])) {
    $clients = Client::model()->byuser()->findAll();
} else {
    $clients = Client::model()->actived()->findAll();
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'campaign-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

  <div id="leftbasicinfo" style="width:520px;">
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'client_id'); ?>
        <?php echo $form->dropDownList($model, 'client_id', CHtml::listData($clients,'id','company'),array('prompt'=>'-- Select --')); ?>
		<?php echo $form->error($model,'client_id'); ?>
	</div>

	<!-- <div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php /*
        $form->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'model'=>$model,
            'name'=>'Campaign_domain',
            'value'=>$model->domain,
            'source'=>'js:[]', // <- use this for pre-set array of values
            'source'=>Yii::app()->createUrl("clientDomain/domains"),// <- path to controller which returns dynamic data
            // additional javascript options for the autocomplete plugin
            'options'=>array(
                'minLength'=>0, // min chars to start search
                'select'=>'js:function(event, ui) { console.log(ui.item.id +":"+ui.item.value);}'
            ),
            'htmlOptions'=>array(
                'id'=>'Campaign_domain',
            ),
        ));
        */
        ?>
		<?php echo $form->error($model,'domain'); ?>
	</div> -->

	<div class="row">
		<?php echo $form->labelEx($model,'domain'); ?>
		<?php echo $form->textField($model,'domain',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domain'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'category',array('label'=>'Categories(For now,You\'d better choose one category only)')); ?>
        <?php
        $htmlOptions = array();
        $htmlOptions['multiple'] = true;
        $htmlOptions['style'] = "width:480px;";
        echo $form->dropDownList($model, 'category', $cats, $htmlOptions);
        ?>

		<?php echo $form->error($model,'category'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row horizonleft">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkBox($model,'status', array('class'=>'chkbox')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

    <div class="clear"></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
  </div><!-- end of leftbasicinfo -->

  <div id="middlebasicinfo" style="width:460px;">
    <div>
        <div class="row" id="campaign_keywords">
            <ul class="horizonlist">
            <li style="width:80px;"><?php echo $form->labelEx($ctmodel,'kwcount');?></li>
            <li><?php echo $form->labelEx($ctmodel,'keyword');?></li></ul>
            <div class='clear'></div>
            <?php
            $kwcount = $form->textField($ctmodel,'kwcount[]',array('style'=>'width:60px;'));
            $kwkeyword = $form->textField($ctmodel,'keyword[]',array('maxlength'=>255));
            $tpl4kyinput = <<<KWTPL
<ul class="horizonlist"><li>{$kwcount}</li><li>{$kwkeyword}</li></ul><div class="clear"></div>
KWTPL;
            $tpl4urlinput = $form->textField($ctmodel,'targeturl[]',array('style'=>"width:360px;"));


            //$prefixname = get_class($ctmodel);
            //$form->textField($_kv,'kwcount',array('style'=>'width:60px;','name'=>$prefixname."[kwcount][ck_"."$_kv->id]"));
            //$form->textField($_kv,'keyword',array('maxlength'=>255,'name'=>$prefixname."[keyword][ck_"."$_kv->id]"));
            echo $form->error($ctmodel,'keyword');
            $_t_count = 3;//default text filed count

            if (isset($model->id) && $model->id > 0 && isset($ctmodel->id)) {
                $kwinfo = $ctmodel->keyword;
                $keywordcount = count($kwinfo);

                if ($keywordcount > 0) {
                    foreach($kwinfo as $_kv) {
                        echo "<ul class='horizonlist'><li>";
                        echo $form->textField($ctmodel,'kwcount[]',array('style'=>'width:60px;','value'=>$_kv['kwcount']));
                        echo "</li><li>";
                        echo $form->textField($ctmodel,'keyword[]',array('maxlength'=>255,'value'=>$_kv['keyword']));
                        echo "</li></ul>".Yii::t('Campagin', 'In Use:').$_kv['used']."<div class='clear'></div>";
                    }
                }
                $_t_count = $_t_count - $keywordcount;
            }

            if ($_t_count > 0) {
                for ($i = 0; $i < $_t_count; $i++) {
                    echo $tpl4kyinput;
                }
            }
            ?>
        </div>
        <div class="row" id="add_more_keyword">
            <?php echo CHtml::link(Yii::t('Campaign', '+Add More'), 'javascript:void(0);'); ?>
        </div>
        <div class="clear">&nbsp;</div>

         <div class="row" id="campaign_target_urls">
            <?php
            echo $form->labelEx($ctmodel,'targeturl');
            echo $form->error($ctmodel,'targeturl');
            $_t_count = 3;//default text filed count

            if (isset($model->id) && $model->id > 0 && isset($ctmodel->id)) {
                $target_url = $ctmodel->targeturl;
                $keywordcount = count($target_url);
                if ($keywordcount > 0) {
                    foreach($target_url as $_kv) {
                        echo $form->textField($ctmodel,'targeturl[]',array('style'=>"width:360px;",'value'=>$_kv['targeturl']));
                    }
                }
                $_t_count = $_t_count - $keywordcount;
            }

            if ($_t_count > 0) {
                for ($i = 0; $i < $_t_count; $i++) {
                    echo $tpl4urlinput;
                }
            }
            ?>
        </div>
        <div class="row" id="add_more_url">
            <?php echo CHtml::link(Yii::t('Client', '+Add More'), 'javascript:void(0);'); ?>
        </div>
    </div>
  </div><!-- end of rightbasicinfo -->
<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
<!--
$(document).ready(function(){
    $("#Campaign_category").multiselect({noneSelectedText:'Select Category',selectedList:10}).multiselectfilter();

    $('#add_more_keyword').click(function(){
        $('#campaign_keywords').append('<?php echo $tpl4kyinput; ?>');
        $('#campaign_keywords').append('<?php echo $tpl4kyinput; ?>');
    });

    $('#add_more_url').click(function(){
        $('#campaign_target_urls').append('<?php echo $tpl4urlinput; ?>');
        $('#campaign_target_urls').append('<?php echo $tpl4urlinput; ?>');
    });

    $('#Campaign_client_id').change(function(){
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/clientDomain/domains');?>",
            'data': "label=domain&id=domain&value=domain&client_id="+this.value,
            'success':function(data){
                //donothing for now;
                $('#Campaign_domain').autocomplete("destroy");
                $('#Campaign_domain').autocomplete({'minLength':0,
                    'select':function(event, ui) { console.log(ui.item.id +":"+ui.item.value);},
                    'source':data});
            }
        });
    });

    $('#Campaign_domain').click(function(){
        if (this.value == ""){
            //$('#Campaign_domain').autocomplete("enable");
            //$('#Campaign_domain').autocomplete("widget");
            $('#Campaign_domain').autocomplete("search");
        }
    });

}); //end of dom.ready
//-->
</script>

<style type="text/css">
/*
.ui-multiselect-filter input{height:12px;padding:0px;width:80px;margin:0px;}
*/
ul.horizonlist li{float:left;list-style: none outside none;margin:0 15px 0 0;}
</style>
