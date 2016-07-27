<?php

/**
 * This is the model class for table "{{announcement}}".
 *
 * The followings are the available columns in table '{{announcement}}':
 * @property integer $id
 * @property string $description
 * @property string $roles
 * @property string $created
 * @property integer $created_by
 */
class Announcement extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Announcement the static model class
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
		return '{{announcement}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_by', 'required'),
			array('created_by', 'numerical', 'integerOnly'=>true),
			array('roles', 'length', 'max'=>2000),
			array('description, created, addeddate', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, roles, created, created_by, addeddate', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'description' => 'Description',
			'roles' => 'Roles (Who will view this announcement)',
			'addeddate' => 'Date Added',
			'created' => 'Created',
			'created_by' => 'Created By',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        if (!empty($this->roles) && is_array($this->roles)) {
                $this->roles = implode(",", $this->roles);
        }

        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
            $this->created = date('Y-m-d H:i:s');
            $this->created_by  = Yii::app()->user->id;
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
            //$this->modified = date('Y-m-d H:i:s');
            //$this->modified_by = Yii::app()->user->id;
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('roles',$this->roles,true);
		$criteria->compare('addeddate',$this->addeddate,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}