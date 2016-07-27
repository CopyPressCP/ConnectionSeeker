<?php

/**
 * This is the model class for table "{{discovery_domain}}".
 *
 * The followings are the available columns in table '{{discovery_domain}}':
 * @property integer $id
 * @property string $domain_id
 * @property string $domain
 * @property integer $use_historic_index
 * @property string $historic_called
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 */
class DiscoveryDomain extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DiscoveryDomain the static model class
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
		return '{{discovery_domain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('use_historic_index, status, created_by', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			array('historic_called, created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, domain, use_historic_index, historic_called, status, created, created_by', 'safe', 'on'=>'search'),
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
			'domain_id' => 'Domain',
			'domain' => 'Domain',
			'use_historic_index' => 'Use Historic Index',
			'historic_called' => 'Historic Called',
			'status' => 'Status',
			'created' => 'Created',
			'created_by' => 'Created By',
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
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('use_historic_index',$this->use_historic_index);
		$criteria->compare('historic_called',$this->historic_called,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}