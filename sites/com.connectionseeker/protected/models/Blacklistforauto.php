<?php

/**
 * This is the model class for table "{{blacklistforauto}}".
 *
 * The followings are the available columns in table '{{blacklistforauto}}':
 * @property integer $id
 * @property string $domain_id
 * @property string $domain
 * @property integer $created_by
 * @property string $created
 */
class Blacklistforauto extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Blacklistforauto the static model class
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
		return '{{blacklistforauto}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_by', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, domain, created_by, created', 'safe', 'on'=>'search'),
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
			'created_by' => 'Created By',
			'created' => 'Created',
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
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //do nothing for now;
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
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}