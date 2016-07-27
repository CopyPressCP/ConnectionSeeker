<?php

/**
 * This is the model class for table "{{domain_summary}}".
 *
 * The followings are the available columns in table '{{domain_summary}}':
 * @property string $id
 * @property string $domain_id
 * @property string $domain
 * @property integer $googlepr
 * @property integer $onlinesince
 * @property string $linkingdomains
 * @property string $inboundlinks
 * @property string $indexedurls
 * @property string $alexarank
 * @property integer $mozrank
 * @property integer $acrank
 * @property string $uniquevisitors
 * @property string $facebookshares
 * @property string $twittershares
 * @property string $linkedinshares
 */
class Summary extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Summary the static model class
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
		return '{{domain_summary}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('googlepr, onlinesince, acrank', 'numerical', 'integerOnly'=>true),
			array('domain_id, linkingdomains, inboundlinks, indexedurls, alexarank, uniquevisitors, facebookshares, twittershares, mozrank, mozauthority, linkedinshares', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, domain, googlepr, onlinesince, linkingdomains, inboundlinks, indexedurls, alexarank, mozrank, mozauthority, semrushor, acrank, uniquevisitors, facebookshares, twittershares, linkedinshares', 'safe', 'on'=>'search'),
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
            'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
		);
	}

    public function behaviors()
    {
        return array(
            'ETrailBehavior' => array('class' => 'application.components.ETrailBehavior'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'domain_id' => 'Domain',
			'domain' => 'Domain',
			'googlepr' => 'PR',
			'onlinesince' => 'Onlinesince',
			'linkingdomains' => 'Linkingdomains',
			'inboundlinks' => 'Inboundlinks',
			'indexedurls' => 'Indexedurls',
			'alexarank' => 'Alexa Rank',
            'mozrank' => 'Domain Moz Rank',
            'mozauthority' => 'Authority',
			'acrank' => 'Acrank',
			'semrushor' => 'SEM',
			'uniquevisitors' => 'Unique visitors',
			'facebookshares' => 'Facebook shares',
			'twittershares' => 'Twitter shares',
			'linkedinshares' => 'Linkedin shares',
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
        $ids = $this->domain_id;
        if (stripos($this->domain_id, ",") === false) {
            //do nothing;
        } else {
            $ids = explode(",", $this->domain_id);
        }

		$criteria->compare('domain_id',$ids);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('googlepr',$this->googlepr);
		$criteria->compare('onlinesince',$this->onlinesince);
		$criteria->compare('linkingdomains',$this->linkingdomains,true);
		$criteria->compare('inboundlinks',$this->inboundlinks,true);
		$criteria->compare('indexedurls',$this->indexedurls,true);
		$criteria->compare('alexarank',$this->alexarank,true);
		$criteria->compare('mozrank',$this->mozrank);
		$criteria->compare('acrank',$this->acrank);
		$criteria->compare('uniquevisitors',$this->uniquevisitors,true);
		$criteria->compare('facebookshares',$this->facebookshares,true);
		$criteria->compare('twittershares',$this->twittershares,true);
		$criteria->compare('linkedinshares',$this->linkedinshares,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}