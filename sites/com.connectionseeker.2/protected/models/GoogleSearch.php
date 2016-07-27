<?php

/**
 * This is the model class for table "{{google_search}}".
 *
 * The followings are the available columns in table '{{google_search}}':
 * @property integer $id
 * @property string $domain
 * @property integer $googlepr
 * @property integer $onlinesince
 * @property integer $alexarank
 * @property integer $inboundlinks
 * @property integer $linkingdomains
 * @property string $seostatus
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 * @property integer $last_call_api_time
 */
class GoogleSearch extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return GoogleSearch the static model class
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
		return '{{google_search}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('googlepr, onlinesince, alexarank, inboundlinks, linkingdomains, created_by, modified_by, last_call_api_time', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('seostatus, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, googlepr, onlinesince, alexarank, inboundlinks, linkingdomains, seostatus, created, created_by, modified, modified_by, last_call_api_time', 'safe', 'on'=>'search'),
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
			'domain' => 'Domain',
			'googlepr' => 'Googlepr',
			'onlinesince' => 'Onlinesince',
			'alexarank' => 'Alexarank',
			'inboundlinks' => 'Inboundlinks',
			'linkingdomains' => 'Linkingdomains',
			'seostatus' => 'Seostatus',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
			'last_call_api_time' => 'Last Call Api Time',
		);
	}

    /**
     * Prepares salt, create_time, create_user_id, update_time and
     * update_user_ id attributes before performing validation.
     */
    protected function beforeValidate() {

        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = new CDbExpression('NOW()');
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = Yii::app()->user->id;
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
            $this->modified = date('Y-m-d H:i:s');
            $this->modified_by = Yii::app()->user->id;
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
		$criteria->compare('googlepr',$this->googlepr);
		$criteria->compare('onlinesince',$this->onlinesince);
		$criteria->compare('alexarank',$this->alexarank);
		$criteria->compare('inboundlinks',$this->inboundlinks);
		$criteria->compare('linkingdomains',$this->linkingdomains);
		$criteria->compare('seostatus',$this->seostatus,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);
		$criteria->compare('last_call_api_time',$this->last_call_api_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}