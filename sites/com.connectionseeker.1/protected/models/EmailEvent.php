<?php

/**
 * This is the model class for table "{{email_event}}".
 *
 * The followings are the available columns in table '{{email_event}}':
 * @property string $id
 * @property string $email
 * @property string $domain_id
 * @property integer $template_id
 * @property string $category
 * @property string $event
 * @property string $rawdata
 * @property integer $created
 */
class EmailEvent extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return EmailEvent the static model class
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
		return '{{email_event}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created,email', 'required'),
			array('template_id, created', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('queue_id', 'length', 'max'=>20),
			array('category', 'length', 'max'=>100),
			array('event', 'length', 'max'=>20),
			array('rawdata', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, email, domain_id, queue_id, template_id, category, event, rawdata, created', 'safe', 'on'=>'search'),
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
			'email' => 'Email',
			'domain_id' => 'Domain',
			'template_id' => 'Template',
			'queue_id' => 'Queue',
			'category' => 'Category',
			'event' => 'Event',
			'rawdata' => 'Rawdata',
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
		$criteria->compare('email',$this->email,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('template_id',$this->template_id);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('event',$this->event,true);
		$criteria->compare('rawdata',$this->rawdata,true);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}