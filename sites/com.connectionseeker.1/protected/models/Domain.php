<?php

/**
 * This is the model class for table "{{domain}}".
 *
 * The followings are the available columns in table '{{domain}}':
 * @property integer $id
 * @property string $domain
 * @property string $tld
 * @property integer $googlepr
 * @property integer $onlinesince
 * @property integer $linkingdomains
 * @property integer $inboundlinks
 * @property integer $indexedurls
 * @property integer $alexarank
 * @property string $ip
 * @property string $subnet
 * @property string $title
 * @property string $owner
 * @property string $email
 * @property string $telephone
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $zip
 * @property string $street
 * @property integer $stype
 * @property integer $otype
 * @property string $touched
 * @property integer $touched_by
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Domain extends CActiveRecord
{
    //Work In Progress(contacted, bounced, opened, clicked)
    public static $status = array('0' => 'Unchecked',
                           '1' => 'Site Untouched',
                           '2' => 'Contacted',
                           '3' => 'Bounced',
                           '4' => 'Opened',
                           '5' => 'Clicked',
                           '6' => 'Site Acquired',
                           '7' => 'Site Denied',
                           '8' => 'Not Interested',
                           '9' => 'Queued',
                           '10' => 'Replied',
						   '11' => 'Contact Form',
						   '12' => 'No Form Found',
						   '13' => 'Internal Outreach',);


	/**
	 * Returns the static model of the specified AR class.
	 * @return Domain the static model class
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
		return '{{domain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('googlepr, onlinesince, linkingdomains, inboundlinks, indexedurls, alexarank, touched_by, created_by, modified_by, stype, otype,', 'numerical', 'integerOnly'=>true),
			array('domain, title, owner, email, telephone', 'length', 'max'=>255),
			array('tld', 'length', 'max'=>10),
			array('ip, subnet', 'length', 'max'=>32),
			array('country, zip', 'length', 'max'=>64),
			array('state, city', 'length', 'max'=>128),
			array('street, stype, otype, touched, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, tld, googlepr, onlinesince, linkingdomains, inboundlinks, indexedurls, alexarank, ip, subnet, title, owner, email, telephone, country, state, city, zip, street, stype, otype, touched_status, touched, touched_by, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
		);
	}

    public function scopes()
    {
        return array(
            'touched'=>array(
                'condition'=>'touched_status > 0',
            ),
            //Usage: $clients = Types::model()->actived()->findAll();
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
            'rbacklink' => array(self::HAS_MANY, 'Backlink', 'domain_id'),
			'touchedby' => array(self::BELONGS_TO, 'User', 'touched_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'domain' => 'Domain',
			'tld' => 'Tld',
			'googlepr' => 'PR',
			'onlinesince' => 'Onlinesince',
			'linkingdomains' => 'Linkingdomains',
			'inboundlinks' => 'Inboundlinks',
			'indexedurls' => 'Indexedurls',
			'alexarank' => 'Alexarank',
			'ip' => 'Ip',
			'subnet' => 'Subnet',
			'title' => 'Title',
			'owner' => 'Owner',
			'email' => 'Email',
			'telephone' => 'Telephone',
			'country' => 'Country',
			'state' => 'State',
			'city' => 'City',
			'zip' => 'Zip',
			'street' => 'Street',
			'stype' => 'Site Type',
			'otype' => 'Outreach Type',
			'touched' => 'Touched',
			'touched_status' => 'Status',
			'touched_by' => 'Touched By',
			'created' => 'Added',
			'created_by' => 'Added By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
 
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
            $this->created = $this->modified = date('Y-m-d H:i:s');
            $this->created_by = $this->modified_by = Yii::app()->user->id;
            //$this->salt = $this->generateSalt();
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
            $this->modified = date('Y-m-d H:i:s');
            $this->modified_by = Yii::app()->user->id;
            //if (empty($this->password)) unset($this->password);
        }

        return parent::beforeValidate();
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
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('tld',$this->tld,true);
		$criteria->compare('googlepr',$this->googlepr);
		$criteria->compare('onlinesince',$this->onlinesince);
		$criteria->compare('linkingdomains',$this->linkingdomains);
		$criteria->compare('inboundlinks',$this->inboundlinks);
		$criteria->compare('indexedurls',$this->indexedurls);
		$criteria->compare('alexarank',$this->alexarank);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('subnet',$this->subnet,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('stype',$this->stype);
		$criteria->compare('otype',$this->otype);
		$criteria->compare('touched_status',$this->touched_status);
		$criteria->compare('touched',$this->touched,true);
		$criteria->compare('touched_by',$this->touched_by);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}