<?php

/**
 * This is the model class for table "{{user_time_tracking}}".
 *
 * The followings are the available columns in table '{{user_time_tracking}}':
 * @property string $id
 * @property integer $user_id
 * @property string $date_tracked
 * @property integer $total_online
 * @property integer $login_time
 * @property integer $session_online
 * @property integer $last_operation_time
 */
class Online extends CActiveRecord
{
    public $datefrom;
    public $dateto;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Online the static model class
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
		return '{{user_time_tracking}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, login_time, last_operation_time', 'required'),
			array('user_id, total_online, login_time, session_online, last_operation_time', 'numerical', 'integerOnly'=>true),
			array('date_tracked', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, date_tracked, total_online, login_time, session_online, last_operation_time, datefrom, dateto', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'date_tracked' => 'Date Tracked',
			'total_online' => 'Total Online',
			'login_time' => 'Login Time',
			'session_online' => 'Session Online',
			'last_operation_time' => 'Last Operation Time',
			'datefrom' => 'From',
			'dateto' => 'To',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
 
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->date_tracked = new CDbExpression('NOW()');
            $this->date_tracked = date('Y-m-d');
        } else {
            //not a new record, so nothing to do right now.
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('total_online',$this->total_online);
		$criteria->compare('login_time',$this->login_time);
		$criteria->compare('session_online',$this->session_online);
		$criteria->compare('last_operation_time',$this->last_operation_time);
        if ($this->datefrom) {
            $today = date("Y-m-d");
            $stampoftoday = strtotime($today);
            $stampoffrom = strtotime($this->datefrom);
            if ($stampoftoday <= $stampoffrom) {
                $criteria->compare('date_tracked',$today,true);
            } else {
                $criteria->addBetweenCondition('date_tracked', $this->datefrom, $today);
            }
        } else {
		    $criteria->compare('date_tracked',$this->date_tracked,true);
        }

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}