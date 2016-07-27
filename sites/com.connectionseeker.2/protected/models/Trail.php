<?php

/**
 * This is the model class for table "{{operation_trail}}".
 *
 * The followings are the available columns in table '{{operation_trail}}':
 * @property string $id
 * @property string $old_value
 * @property string $new_value
 * @property string $description
 * @property string $action
 * @property string $model
 * @property integer $field
 * @property integer $user_id
 * @property integer $model_id
 * @property string $created
 */
class Trail extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Trail the static model class
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
		return '{{operation_trail}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model_id', 'required'),
			array('user_id, model_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>500),
			array('field, action, model', 'length', 'max'=>50),
			array('old_value, new_value, created, operation', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, old_value, new_value, description, action, model, field, user_id, model_id, created', 'safe', 'on'=>'search'),
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
			'rcreatedby' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'old_value' => 'Old Value',
			'new_value' => 'New Value',
			'description' => 'Description',
			'operation' => 'Operation',
			'action' => 'Action',
			'model' => 'Model',
			'field' => 'Field',
			'user_id' => 'User',
			'model_id' => 'Model',
			'created' => 'Created',
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
		$criteria->compare('old_value',$this->old_value,true);
		$criteria->compare('new_value',$this->new_value,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('operation',$this->operation,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('model_id',$this->model_id);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}