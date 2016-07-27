<?php

/**
 * This is the model class for table "{{client}}".
 *
 * The followings are the available columns in table '{{client}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $company
 * @property string $name
 * @property string $contact_name
 * @property string $email
 * @property string $telephone
 * @property string $cellphone
 * @property string $note
 * @property integer $assignee
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 * @property string $last_visit_time
 * @property string $last_visit_ip
 */
class Client extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Client the static model class
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
		return '{{client}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('company, name, email, assignee', 'required'),
            array('email', 'email'),
			array('assignee, status, created_by, modified_by, user_id', 'numerical', 'integerOnly'=>true),
			array('company, telephone, cellphone, last_visit_ip', 'length', 'max'=>255),
			array('name, contact_name, email', 'length', 'max'=>128),
			array('note, created, modified, last_visit_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, company, name, contact_name, email, telephone, cellphone, note, assignee, status, created, created_by, modified, modified_by, last_visit_time, last_visit_ip', 'safe', 'on'=>'search'),
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
			'rdomain' => array(self::HAS_MANY, 'ClientDomain', 'client_id'),
		);
	}

    public function scopes()
    {
        return array(
            'actived'=>array(
                'condition'=>'status=1',
            ),
            'byuser'=>array(
                'condition'=>'user_id='.Yii::app()->user->id,
            ),
            /*
            'recently'=>array(
                'order'=>'create_time DESC',
                'limit'=>5,
            ),
            */
            //Usage: $clients = Client::model()->actived()->recently(3)->findAll(); 
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'Belongs To',
			'company' => 'Company',
			'name' => 'User Name',
			'contact_name' => 'Contact Name',
			'email' => 'Email',
			'telephone' => 'Telephone',
			'cellphone' => 'Cellphone',
			'note' => 'Note',
			'assignee' => 'Assignee (PM)',
			'status' => 'Active',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
			'last_visit_time' => 'Last Visit Time',
			'last_visit_ip' => 'Last Visit Ip',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('cellphone',$this->cellphone,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('assignee',$this->assignee);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);
		$criteria->compare('last_visit_time',$this->last_visit_time,true);
		$criteria->compare('last_visit_ip',$this->last_visit_ip,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}