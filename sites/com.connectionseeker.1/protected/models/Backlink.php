<?php

/**
 * This is the model class for table "{{competitor_backlink}}".
 *
 * The followings are the available columns in table '{{competitor_backlink}}':
 * @property integer $id
 * @property integer $competitor_id
 * @property integer $domain_id
 * @property integer $fresh_called
 * @property integer $historic_called
 * @property string $url
 * @property string $domain
 * @property integer $googlepr
 * @property integer $acrank
 * @property string $anchortext
 * @property string $date
 * @property integer $flagredirect
 * @property integer $flagframe
 * @property integer $flagnofollow
 * @property integer $flagimages
 * @property integer $flagdeleted
 * @property integer $flagalttext
 * @property integer $flagmention
 * @property string $targeturl
 */
class Backlink extends CActiveRecord
{
    public $api_called = "";

	/**
	 * Returns the static model of the specified AR class.
	 * @return Backlink the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{competitor_backlink}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('competitor_id, domain_id', 'required'),
			array('competitor_id, domain_id, fresh_called, historic_called, googlepr, acrank, flagredirect, flagframe, flagnofollow, flagimages, flagdeleted, flagalttext, flagmention', 'numerical', 'integerOnly'=>true),
			array('url', 'length', 'max'=>2048),
			array('domain, anchortext, targeturl', 'length', 'max'=>255),
			array('date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, competitor_id, domain_id, fresh_called, historic_called, url, domain, googlepr, acrank, anchortext, date, flagredirect, flagframe, flagnofollow, flagimages, flagdeleted, flagalttext, flagmention, targeturl, api_called', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'competitor_id' => 'Competitor',
			'domain_id' => 'Domain',
			'api_called' => 'Date Called',
			'fresh_called' => 'Fresh Called',
			'historic_called' => 'Historic Called',
			'url' => 'Url',
			'domain' => 'Domain',
			'googlepr' => 'PR',
			'acrank' => 'Acrank',
			'anchortext' => 'Anchortext',
			'date' => 'Date',
			'flagredirect' => 'Flagredirect',
			'flagframe' => 'Flagframe',
			'flagnofollow' => 'Flagnofollow',
			'flagimages' => 'Flagimages',
			'flagdeleted' => 'Flagdeleted',
			'flagalttext' => 'Flagalttext',
			'flagmention' => 'Flagmention',
			'targeturl' => 'Target URL',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('competitor_id',$this->competitor_id);
		$criteria->compare('domain_id',$this->domain_id);
		$criteria->compare('fresh_called',$this->fresh_called);
		$criteria->compare('historic_called',$this->historic_called);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('googlepr',$this->googlepr);
		$criteria->compare('acrank',$this->acrank);
		$criteria->compare('anchortext',$this->anchortext,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('flagredirect',$this->flagredirect);
		$criteria->compare('flagframe',$this->flagframe);
		$criteria->compare('flagnofollow',$this->flagnofollow);
		$criteria->compare('flagimages',$this->flagimages);
		$criteria->compare('flagdeleted',$this->flagdeleted);
		$criteria->compare('flagalttext',$this->flagalttext);
		$criteria->compare('flagmention',$this->flagmention);
		$criteria->compare('targeturl',$this->targeturl,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}