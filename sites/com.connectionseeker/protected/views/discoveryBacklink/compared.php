<?php
$this->breadcrumbs=array(
	'Discovery Ref Domains'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('discovery-backlink-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Discovery Backdomains(Ref-Domains)</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_backdomain_search',array(
	'model'=>$model,
)); ?>
</div>

<div style="clear:both"></div>
<?php $this->widget('application.extensions.lkgrid.LinkmeGridView', array(
	'id'=>'discovery-backlink-grid',
	'dataProvider'=>$model->sendable()->search(),
	'filter'=>$model,
	'columns'=>array(
        array(
            'id'=>'ids',
            'class'=>'CCheckBoxColumn',
        ),
		'id',
		'rdiscovery.domain',
		'rdcvdomain.domain',
		'domain',
		'hubcount',
		'rsummary.googlepr',
		'rsummary.alexarank',
		'rsummary.mozrank',
		'rsummary.mozauthority',
		'rdomainonpage.contactemail',
        array(
            'name' => 'rblforauto.domain_id',
            'header' => 'Blacklisted',
            'type' => 'raw',
            'value'=> '($data->rblforauto->domain_id>0)?"Yes":"No"',
        ),
		/*
		'googlepr',
		'anchortext',
		'targeturl',
		'acrank',
		'date',
		'url',
		'flagredirect',
		'flagframe',
		'flagnofollow',
		'flagimages',
		'flagdeleted',
		'flagalttext',
		'flagmention',
		'domain_id',
		'competitor_id',
		'discovery_id',
		'fresh_called',
		'historic_called',
		*/
		array(
			'class'=>'CButtonColumn',
            'template'=>'',
		),
	),
)); ?>

<div class="clear"></div>


<div style="width:416px;float:left">
    <div class="form">
        <div class="row buttons">
            <?php echo CHtml::button('Send to blacklist', array('id'=>'sendToBLBtn')); ?> 
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#sendToBLBtn").click(function(){
        var blids = new Array;

        var sendable = false;
        $("input[name='ids[]'][type='checkbox']:checked").each(function(i,e) {
            //e.value as same as $(this).val()
            sendable = true;
            blids.push($(this).val());
        });

        if (!sendable){
            alert("Please choose one item at least.");
            return false;
        }

        $.ajax({
            'type': 'POST',
            'dataType': 'json',
            'url': "<?php echo Yii::app()->createUrl('/discoveryBacklink/blacklist');?>",
            'data': {'ids[]': blids},
            'success':function(data){
                alert(data.msg);
                $.fn.yiiGridView.update('discovery-backlink-grid', {
                    /*
                    put some search data here.
                    data: {'ids[]':blids}
                    */
                    data: $('.search-form form').serialize()
                });
            },
            'complete':function(XHR,TS){XHR = null;}
        });
    });

    $.fn.yiiGridView.defaults.afterAjaxUpdate();
});
</script>
