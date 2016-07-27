<?php

/**
 * This is the model class for table "{{discovery_backlink}}".
 *
 * The followings are the available columns in table '{{discovery_backlink}}':
 * @property string $id
 * @property integer $competitor_id
 * @property integer $discovery_id
 * @property string $domain_id
 * @property string $fresh_called
 * @property string $historic_called
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
class DiscoveryBacklink extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DiscoveryBacklink the static model class
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
		return '{{discovery_backlink}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('competitor_id', 'required'),
			array('competitor_id, discovery_id, googlepr, acrank, flagredirect, flagframe, flagnofollow, flagimages, flagdeleted, flagalttext, flagmention, status', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>20),
			array('url', 'length', 'max'=>2048),
			array('domain, anchortext, targeturl', 'length', 'max'=>255),
			array('fresh_called, historic_called, date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, competitor_id, discovery_id, domain_id, fresh_called, historic_called, url, domain, googlepr, acrank, anchortext, date, flagredirect, flagframe, flagnofollow, flagimages, flagdeleted, flagalttext, flagmention, targeturl, status', 'safe', 'on'=>'search'),
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
			'rdiscovery' => array(self::BELONGS_TO, 'ClientDiscovery', 'discovery_id'),
			'rdcvdomain' => array(self::BELONGS_TO, 'DiscoveryDomain', 'competitor_id'),
            'rsummary' => array(self::BELONGS_TO, 'Summary', array('domain_id'=>'domain_id')),
		);
	}

    public function scopes()
    {
        return array(
            /*
            'competitors'=>array(
                'condition'=>"rdcvdomain.domain_id != t.domain_id",
            ),
            */
            'available'=>array(
                'condition'=>"t.status = 0",
            ),
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
			'discovery_id' => 'Discovery',
			'domain_id' => 'Domain',
			'fresh_called' => 'Fresh Called',
			'historic_called' => 'Historic Called',
			'url' => 'Url',
			'domain' => 'Back Domain',
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
			'targeturl' => 'Targeturl',
			'status' => 'Status',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('competitor_id',$this->competitor_id);
		$criteria->compare('discovery_id',$this->discovery_id);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('fresh_called',$this->fresh_called,true);
		$criteria->compare('historic_called',$this->historic_called,true);
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
		$criteria->compare('status',$this->status);
		$criteria->compare('targeturl',$this->targeturl,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}