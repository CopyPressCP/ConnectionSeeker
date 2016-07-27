<?php

/**
 * This is the model class for table "{{io_history}}".
 *
 * The followings are the available columns in table '{{io_history}}':
 * @property string $id
 * @property integer $task_id
 * @property integer $iostatus
 * @property integer $timeline
 * @property string $role
 * @property string $created
 * @property integer $created_by
 */
class Iohistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Iohistory the static model class
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
		return '{{io_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, iostatus, created_by', 'required'),
			array('task_id, iostatus, timeline, created_by', 'numerical', 'integerOnly'=>true),
			array('role', 'length', 'max'=>255),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, iostatus, timeline, role, created, created_by', 'safe', 'on'=>'search'),
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
			'task_id' => 'Task',
			'iostatus' => 'Iostatus',
			'timeline' => 'Timeline',
			'role' => 'Role',
			'created' => 'Created',
			'created_by' => 'Created By',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        $uid = Yii::app()->user->id;
        $now = time();

        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
            $this->timeline = 0;
            if ($this->created) {
                $this->timeline = time() - strtotime($this->created);
            }

            $this->created = date('Y-m-d H:i:s', $now);
            $this->created_by = $uid;
            $roles = Yii::app()->authManager->getRoles($uid);
            $this->role = key($roles);
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
            //$this->modified = date('Y-m-d H:i:s');
            //$this->modified_by = Yii::app()->user->id;
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('iostatus',$this->iostatus);
		$criteria->compare('timeline',$this->timeline);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}