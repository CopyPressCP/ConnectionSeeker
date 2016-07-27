<?php

/**
 * This is the model class for table "{{discovery_xref_domain}}".
 *
 * The followings are the available columns in table '{{discovery_xref_domain}}':
 * @property integer $id
 * @property string $domain_id
 * @property integer $discovery_id
 * @property string $fresh_called
 * @property string $historic_called
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class DiscoveryXrefDomain extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DiscoveryXrefDomain the static model class
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
		return '{{discovery_xref_domain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('discovery_id', 'required'),
			array('discovery_id, status, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>255),
			array('fresh_called, historic_called, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, discovery_id, fresh_called, historic_called, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
			'discovery_id' => 'Discovery',
			'fresh_called' => 'Fresh Called',
			'historic_called' => 'Historic Called',
			'status' => 'Status',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
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
		$criteria->compare('discovery_id',$this->discovery_id);
		$criteria->compare('fresh_called',$this->fresh_called,true);
		$criteria->compare('historic_called',$this->historic_called,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}