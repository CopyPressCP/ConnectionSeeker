<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript( 'jquery.ui' );
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.min.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.min.js', CClientScript::POS_END);
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.css');
$cs->registerCssFile(Yii::app()->theme->baseUrl . '/js/multiselect/jquery.multiselect.filter.css');

//$coreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile( $cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css', 'screen' );

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

$titlematch = array(
    '1' => 'Full',
    '2' => 'Partial',
    '3' => 'None',
);

$cdmodel = new ClientDomain;
//$blmodel = new Backlink;

$yiireq = Yii::app()->request;
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

<table border="0" align="left" cellpadding="0" cellspacing="0" width="100%">
  <tr>
	<td class="txtfrm firsttxt" height="50" ><?php echo $form->label($cdmodel,'id', array('label'=>'Client(s)')); ?></td>
	<td class="formSearch" >
        <?php
        $htmlOptions = array();
        //print_r($_GET);
        //$htmlOptions['prompt'] = '-- Select Client --';
        //if (isset($_GET['ClientDomain']['id'])) {
        if (isset($_GET['client_domain_id'])) {
            $htmlOptions['options'] = array((int)$_GET['client_domain_id']=>array('selected'=>true));
        }
        $htmlOptions['ajax'] = array(
            'type'=>'POST', //request type
            'url'=>Yii::app()->createUrl('clientDomain/competitors'),
            'dataType'=>"json",
            //'update'=>'#Discovery_competitor_id', //selector to update
            'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->getCsrfToken(),
                          'domain_id' =>'js:$("#client_domain_id").val()',
                          'haskeyword'  => true),
            //leave out the data key to pass all form values through
            'success' => 'function(html){jQuery("#competitor_id").html(html.competitor);jQuery("#anchortext").html(html.keyword);}',
        );
        $htmlOptions['id'] = "client_domain_id";
        $htmlOptions['name'] = "client_domain_id";
        echo $form->dropDownList($cdmodel, 'id', CHtml::listData($cdmodel->with('rclient')->findAll('rclient.status=1 AND t.status=1'),'id', 'domain', 'rclient.company'), $htmlOptions);
        ?>
    </td>
	<td class="txtfrm" height="50" ><label for="competitor_id"><?php echo Yii::t('Discovery', 'Competitor(s)');?></label>
	<td class="formSearch" >
        <?php
        $cpts = array();
        if (isset($_GET['client_domain_id'])) {
            $cpts = ClientDomainCompetitor::model()->findAll('domain_id=:domain_id',
                      array(':domain_id'=>(int) $_GET['client_domain_id']));
        }
        $htmlOptions = array();
        //$htmlOptions['prompt'] = '-- Competitors --';
        /*
        if (isset($_GET['Discovery']['competitor_id'])) {
            $htmlOptions['options'] = array((int)$_GET['Discovery']['competitor_id']=>array('selected'=>true));

        }
        echo $form->dropDownList($model, 'competitor_id', CHtml::listData($cpts, 'competitor_id', 'rcompetitor.domain'), $htmlOptions);
        */

        echo CHtml::dropDownList('competitor_id',$yiireq->getQuery('competitor_id'), CHtml::listData($cpts, 'competitor_id', 'rcompetitor.domain'), $htmlOptions);
        ?>
    </td>

	<td class="txtfrm" height="50" ><label for="anchortext"><?php echo Yii::t('Discovery', 'Keyword(s)');?></label>
    </td>
	<td class="formSearch" >
    <?php
    $kws = array();
    if (isset($_GET['client_domain_id'])) {
        $kws = ClientDomainKeyword::model()->findAll('domain_id=:domain_id',
                  array(':domain_id'=>(int) $_GET['client_domain_id']));
    }
    $htmlOptions = array();
    $htmlOptions['prompt'] = '-- Keywords --';

    /*
    if (isset($_GET['Backlink']['anchortext'])) {
        $htmlOptions['options'] = array((int)$_GET['Backlink']['anchortext']=>array('selected'=>true));
    }
    echo $form->dropDownList($blmodel, 'anchortext', CHtml::listData($kws, 'keyword', 'keyword'), $htmlOptions);
    */

    echo CHtml::dropDownList('anchortext', $yiireq->getQuery('anchortext'), CHtml::listData($kws, 'keyword', 'keyword'), $htmlOptions);
    ?>
    </td>

	<td class="txtfrm" height="50" ><?php echo Yii::t('Discovery', 'Anchor Match');?></td>
    <td class="formSearch" >
        <?php
        $htmlOptions = array();
        //print_r($_GET);
        $htmlOptions['prompt'] = '[Choose]';
        echo CHtml::dropDownList('titlematch', $yiireq->getQuery('titlematch'), $titlematch, $htmlOptions);
        ?>
    </td>
  </tr>
</table>
<div class="clear"></div>
<table border="0" align="left" cellpadding="0" cellspacing="0" width="90%">
  <tr>
	<td class="txtfrm firsttxt" height="50" ><label for="googlepr"><?php echo Yii::t('Discovery', 'Page Rank');?></label></td>
    <td class="formSearch" >
        <?php
        echo CHtml::dropDownList('googlepropr', $yiireq->getQuery('googlepropr'), $operators);
        ?>
        <input type="text" value="<?php echo $yiireq->getQuery('googlepr');?>" id="googlepr" name="googlepr" style="width:54px;">
    </td>

	<td class="txtfrm" height="50" ><label for="acrank"><?php echo Yii::t('Discovery', 'AC Rank');?></label></td>
    <td class="formSearch" >
        <?php
        echo CHtml::dropDownList('acrankopr', $yiireq->getQuery('acrankopr'), $operators);
        ?>
        <?php //echo $form->textField($model,'max_acrank',array('style'=>"width:54px;"));?>
        <input type="text" value="<?php echo $yiireq->getQuery('acrank');?>" id="acrank" name="acrank" style="width:54px;">
    </td>

	<td class="txtfrm" height="50" ><label for="age"><?php echo Yii::t('Discovery', 'Age');?></label></td>
    <td class="formSearch" >
        <?php
        echo CHtml::dropDownList('ageopr', $yiireq->getQuery('ageopr'), $operators);
        ?>
        <?php //echo $form->textField($model,'max_acrank',array('style'=>"width:54px;"));?>
        <input type="text" value="<?php echo $yiireq->getQuery('age');?>" id="age" name="age" style="width:54px;">
    </td>

	<td class="txtfrm" height="50" ><label for="hubcount"><?php echo Yii::t('Discovery', 'Hubcount');?></label></td>
    <td class="formSearch" >
        <?php
        echo CHtml::dropDownList('hubcountopr', $yiireq->getQuery('hubcountopr'), $operators);
        ?>
        <?php //echo $form->textField($model,'max_acrank',array('style'=>"width:54px;"));?>
        <input type="text" value="<?php echo $yiireq->getQuery('hubcount');?>" id="hubcount" name="hubcount" style="width:54px;">
    </td>
  </tr>

  <tr>
    <td><div class="row buttons">
        <?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit','value' => ' Filter ')); ?>
        </div></td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->
