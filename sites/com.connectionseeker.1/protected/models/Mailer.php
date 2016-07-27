<?php

/**
 * This is the model class for table "{{mailer_account}}".
 *
 * The followings are the available columns in table '{{mailer_account}}':
 * @property integer $id
 * @property string $user_alias
 * @property string $smtp_host
 * @property string $smtp_port
 * @property string $pop3_host
 * @property string $pop3_port
 * @property string $password
 * @property string $username
 * @property string $display_name
 * @property string $email_from
 * @property string $reply_to
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Mailer extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Mailer the static model class
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
		return '{{mailer_account}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('user_alias, smtp_host, pop3_host, username, display_name, email_from, reply_to', 'length', 'max'=>255),
			array('smtp_port, pop3_port', 'length', 'max'=>5),
			array('password', 'length', 'max'=>60),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_alias, smtp_host, smtp_port, pop3_host, pop3_port, password, username, display_name, email_from, reply_to, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
			'user_alias' => 'User Alias',
			'smtp_host' => 'Smtp Host',
			'smtp_port' => 'Smtp Port',
			'pop3_host' => 'Pop3 Host',
			'pop3_port' => 'Pop3 Port',
			'password' => 'Password',
			'username' => 'Username',
			'display_name' => 'Display Name',
			'email_from' => 'Email From',
			'reply_to' => 'Reply To',
			'status' => 'Status',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
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
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
            $this->modified = date('Y-m-d H:i:s');
            $this->modified_by = Yii::app()->user->id;
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
		$criteria->compare('user_alias',$this->user_alias,true);
		$criteria->compare('smtp_host',$this->smtp_host,true);
		$criteria->compare('smtp_port',$this->smtp_port,true);
		$criteria->compare('pop3_host',$this->pop3_host,true);
		$criteria->compare('pop3_port',$this->pop3_port,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('display_name',$this->display_name,true);
		$criteria->compare('email_from',$this->email_from,true);
		$criteria->compare('reply_to',$this->reply_to,true);
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