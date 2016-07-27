<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $email
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 * @property string $last_visit_time
 */
class User extends CActiveRecord
{
    //repeat password
    public $password2;

	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        /*
		return array(
			array('created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('username, password, salt, email', 'length', 'max'=>128),
			array('created, modified, last_visit_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, salt, email, created, created_by, modified, modified_by, last_visit_time', 'safe', 'on'=>'search'),
		);
        */

        return array(
            //array('username, password, salt, email', 'required'),
            array('username, salt, email', 'required'),
            array('password', 'required', 'on'=>'insert'),
            array('username, password, salt, email', 'length', 'max'=>128),
            array('password', 'compare', 'compareAttribute'=>'password2', 'on'=>'insert, update'),
            //array('profile', 'safe'),
            array('password2', 'safe'),
            array('email, username', 'unique'),
            array('email', 'email'),
            array('id, username, last_visit_time, email, created_by', 'safe', 'on'=>'search'),
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
			//'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'createdby' => array(self::BELONGS_TO, 'User', 'created_by'),
			//'modifiedby' => array(self::BELONGS_TO, 'User', 'modified_by'),
		);
	}

	/**
	 * @return array named scopes.
     * Usage: $users = User::model()->actived()->findAll(); 
	 */
    public function scopes()
    {
        return array(
            'actived'=>array(
                'condition'=>'status=1',
            ),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
            'password2' => 'Password Repeat',
			'salt' => 'Salt',
			'email' => 'Email',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
			'last_visit_time' => 'Last Visit Time',
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
		$criteria->compare('username',$this->username,true);
		//$criteria->compare('password',$this->password,true);
		//$criteria->compare('salt',$this->salt,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);
		$criteria->compare('last_visit_time',$this->last_visit_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Checks if the given password is correct.
     * @param string the password to be validated
     * @return boolean whether the password is valid
     */
    public function validatePassword($password)
    {
        return $this->hashPassword($password,$this->salt)===$this->password;
    }

    /**
     * Generates the password hash.
     * @param string password
     * @param string salt
     * @return string hash
     */
    public function hashPassword($password,$salt)
    {
        return md5($salt.$password);
    }

    /**
     * Generates a salt that can be used to generate a password hash.
     * @return string the salt
     */
    public function generateSalt()
    {
        return uniqid('',true);
    }

    /**
     * Prepares salt, create_time, create_user_id, update_time and
     * update_user_ id attributes before performing validation.
     */
    protected function beforeValidate() {
 
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
            $this->created = $this->modified = date('Y-m-d H:i:s');
            $this->created_by = $this->modified_by = Yii::app()->user->id;
            $this->salt = $this->generateSalt();
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
            $this->modified = date('Y-m-d H:i:s');
            $this->modified_by = Yii::app()->user->id;
            if (empty($this->password)) unset($this->password);
        }

        return parent::beforeValidate();
    }

    /**
     * Doing something after performing validation.
     * ...
     */
    protected function afterValidate() {
        parent::afterValidate();
        $this->password = $this->hashPassword($this->password, $this->salt);
    }
}