<?php

/**
 * This is the model class for table "{{domain_compete_history}}".
 *
 * The followings are the available columns in table '{{domain_compete_history}}':
 * @property string $id
 * @property integer $inventory_id
 * @property string $domain_id
 * @property string $domain
 * @property integer $month
 * @property string $value
 * @property string $rawdata
 * @property string $created
 */
class CompeteHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CompeteHistory the static model class
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
		return '{{domain_compete_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('inventory_id, month', 'required'),
			array('inventory_id, month', 'numerical', 'integerOnly'=>true),
			array('domain_id, value', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			array('rawdata', 'length', 'max'=>2000),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, inventory_id, domain_id, domain, month, value, rawdata, created', 'safe', 'on'=>'search'),
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
			'inventory_id' => 'Inventory',
			'domain_id' => 'Domain',
			'domain' => 'Domain',
			'month' => 'Month',
			'value' => 'Value',
			'rawdata' => 'Rawdata',
			'created' => 'Created',
		);
	}

    /**
     * Prepares "created" attributes before performing validation.
     */
	protected function beforeValidate()
	{
        if ($this->isNewRecord) {
            $this->created = date('Y-m-d H:i:s');
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('inventory_id',$this->inventory_id);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('month',$this->month);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('rawdata',$this->rawdata,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}