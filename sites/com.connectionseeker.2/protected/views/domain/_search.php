<?php
//http://www.alexa.com/topsites/countries
//for the short term, we can put the countries into an array, but for long term we need create a table to store the countries into there.
/*
$countries = array("Albania" => "Albania",
"Algeria" => "Algeria",
"Argentina" => "Argentina",
"Armenia" => "Armenia",
"Australia" => "Australia",
"Austria" => "Austria",
"Azerbaijan" => "Azerbaijan",
"Bahrain" => "Bahrain",
"Bangladesh" => "Bangladesh",
"Belarus" => "Belarus",
"Belgium" => "Belgium",
"Bolivia" => "Bolivia",
"Bosnia and Herzegovina" => "Bosnia and Herzegovina",
"Brazil" => "Brazil",
"Bulgaria" => "Bulgaria",
"Cambodia" => "Cambodia",
"Cameroon" => "Cameroon",
"Canada" => "Canada",
"Chile" => "Chile",
"China" => "China",
"Colombia" => "Colombia",
"Costa Rica" => "Costa Rica",
"Croatia" => "Croatia",
"Cyprus" => "Cyprus",
"Czech Republic" => "Czech Republic",
"Denmark" => "Denmark",
"Dominican Republic" => "Dominican Republic",
"Ecuador" => "Ecuador",
"Egypt" => "Egypt",
"El Salvador" => "El Salvador",
"Estonia" => "Estonia",
"Finland" => "Finland",
"France" => "France",
"Georgia" => "Georgia",
"Germany" => "Germany",
"Ghana" => "Ghana",
"Greece" => "Greece",
"Guatemala" => "Guatemala",
"Honduras" => "Honduras",
"Hong Kong" => "Hong Kong",
"Hungary" => "Hungary",
"Iceland" => "Iceland",
"India" => "India",
"Indonesia" => "Indonesia",
"Iran" => "Iran",
"Iraq" => "Iraq",
"Ireland" => "Ireland",
"Israel" => "Israel",
"Italy" => "Italy",
"Jamaica" => "Jamaica",
"Japan" => "Japan",
"Jordan" => "Jordan",
"Kazakhstan" => "Kazakhstan",
"Kenya" => "Kenya",
"Kuwait" => "Kuwait",
"Latvia" => "Latvia",
"Lebanon" => "Lebanon",
"Libya" => "Libya",
"Lithuania" => "Lithuania",
"Luxembourg" => "Luxembourg",
"Macao" => "Macao",
"Macedonia" => "Macedonia",
"Madagascar" => "Madagascar",
"Malaysia" => "Malaysia",
"Malta" => "Malta",
"Mauritius" => "Mauritius",
"Mexico" => "Mexico",
"Moldova" => "Moldova",
"Mongolia" => "Mongolia",
"Montenegro" => "Montenegro",
"Morocco" => "Morocco",
"Nepal" => "Nepal",
"Netherlands" => "Netherlands",
"New Zealand" => "New Zealand",
"Nigeria" => "Nigeria",
"Norway" => "Norway",
"Oman" => "Oman",
"Pakistan" => "Pakistan",
"Palestinian Territory" => "Palestinian Territory",
"Panama" => "Panama",
"Paraguay" => "Paraguay",
"Peru" => "Peru",
"Philippines" => "Philippines",
"Poland" => "Poland",
"Portugal" => "Portugal",
"Puerto Rico" => "Puerto Rico",
"Qatar" => "Qatar",
"Reunion" => "Reunion",
"Romania" => "Romania",
"Russia" => "Russia",
"Saudi Arabia" => "Saudi Arabia",
"Serbia" => "Serbia",
"Singapore" => "Singapore",
"Slovakia" => "Slovakia",
"Slovenia" => "Slovenia",
"South Africa" => "South Africa",
"South Korea" => "South Korea",
"Spain" => "Spain",
"Sri Lanka" => "Sri Lanka",
"Sweden" => "Sweden",
"Switzerland" => "Switzerland",
"Taiwan" => "Taiwan",
"Thailand" => "Thailand",
"Tunisia" => "Tunisia",
"Turkey" => "Turkey",
"Uganda" => "Uganda",
"Ukraine" => "Ukraine",
"United Kingdom" => "United Kingdom",
"United States" => "United States",
"Uruguay" => "Uruguay",
"Venezuela" => "Venezuela",
"Vietnam" => "Vietnam",
"Yemen" => "Yemen",);
*/

$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
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
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'onlinesince'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'onlinesince'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'linkingdomains'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'linkingdomains',array('size'=>20,'maxlength'=>20)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'inboundlinks'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'inboundlinks',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'indexedurls'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'indexedurls',array('size'=>20,'maxlength'=>20)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'alexarank'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'alexarank',array('size'=>20,'maxlength'=>20)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'ip'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'ip',array('size'=>32,'maxlength'=>32)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'subnet'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'subnet',array('size'=>32,'maxlength'=>32)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'title'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'owner'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'owner',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'email'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'telephone'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'telephone',array('size'=>60,'maxlength'=>255)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'host_country'); ?></td>
	<td class="formSearch" >
    <?php echo $form->textField($model,'host_country',array('size'=>60,'maxlength'=>64)); ?>
    <?php
    /*
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Country]';
    echo $form->dropDownList($model, 'host_country', $countries, $htmlOptions);
    */
    ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'state'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'state',array('size'=>60,'maxlength'=>128)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'city'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'city',array('size'=>60,'maxlength'=>128)); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'zip'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'zip',array('size'=>60,'maxlength'=>64)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'street'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'street',array('size'=>60,'maxlength'=>64)); ?>
    <?php //echo $form->textArea($model,'street',array('rows'=>6, 'cols'=>50)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'stype'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Site Type]';
    echo $form->dropDownList($model, 'stype', $stypes, $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'otype'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Outreach Type]';
    echo $form->dropDownList($model, 'otype', $otypes, $htmlOptions);
    ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'category'); ?></td>
	<td class="formSearch" ><?php //echo $form->textField($model,'category'); ?>
<?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Category]';
    echo $form->dropDownList($model, 'category', $categories, $htmlOptions);
    ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'touched_status'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[Status]';
    echo $form->dropDownList($model, 'touched_status', $touchedstatus, $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'mozrank'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'mozrank',array('size'=>60,'maxlength'=>64)); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'mozauthority'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'mozauthority',array('size'=>60,'maxlength'=>64)); ?>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'semrushor'); ?></td>
	<td class="formSearch" ><?php
    $htmlOptions = array();
    $htmlOptions['prompt'] = '[SEMRush]';
    echo $form->dropDownList($model, 'semrushor', $semrushes, $htmlOptions);
    ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'awis_category'); ?></td>
	<td class="formSearch" ><?php
    if ($awises) {
        $htmlOptions = array();
        $htmlOptions['prompt'] = '[AWIS Category]';
        echo $form->dropDownList($model, 'awis_category', $awises, $htmlOptions);
    } else {
        echo $form->textField($model,'awis_category',array('size'=>60,'maxlength'=>64));
    }
    ?>
    </td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'technorati_category'); ?></td>
	<td class="formSearch" ><?php
    if ($technoraties) {
        $htmlOptions = array();
        $htmlOptions['prompt'] = '[Technorati Category]';
        echo $form->dropDownList($model, 'technorati_category', $technoraties, $htmlOptions);
    } else {
        echo $form->textField($model,'technorati_category',array('size'=>60,'maxlength'=>64));
    }
    ?>
    </td>
	<td class="txtfrm" height="50" ></td>
	<td class="formSearch"></td>
  </tr>
  <!-- <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'touched_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'touched_by'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'created_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'created_by'); ?></td>
  </tr>
  <tr>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified'); ?></td>
	<td class="txtfrm" height="50" ><?php echo $form->label($model,'modified_by'); ?></td>
	<td class="formSearch" ><?php echo $form->textField($model,'modified_by'); ?></td>
  </tr> -->
  <tr>
    <td><?php echo CHtml::submitButton('Search', array('id' => 'button', 'type' => 'submit' , 'value' => 'Search')); ?></td>
  </tr>
</table>
<?php $this->endWidget(); ?>
<!-- search-form -->