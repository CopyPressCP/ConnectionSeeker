<?php

/**
 * This is the model class for table "{{domain_status_tracking}}".
 *
 * The followings are the available columns in table '{{domain_status_tracking}}':
 * @property string $id
 * @property string $domain_id
 * @property string $domain
 * @property integer $before_value
 * @property integer $after_value
 * @property string $created
 * @property integer $created_by
 */
class OutreachTracking extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return OutreachTracking the static model class
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
		return '{{domain_status_tracking}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('before_value, after_value, created_by', 'required'),
			array('before_value, after_value, created_by', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, domain, before_value, after_value, created, created_by', 'safe', 'on'=>'search'),
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
            'rcreatedby' => array(self::BELONGS_TO, 'User', 'created_by'),
            'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'domain_id' => 'Domain ID',
			'domain' => 'Domain',
			'before_value' => 'Before Value',
			'after_value' => 'After Value',
			'created' => 'Created',
			'created_by' => 'Created By',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = new CDbExpression('NOW()');
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = Yii::app()->user->id;
            //$this->salt = $this->generateSalt();
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
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('before_value',$this->before_value);
		$criteria->compare('after_value',$this->after_value);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}