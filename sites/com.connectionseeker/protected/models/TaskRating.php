<?php

/**
 * This is the model class for table "{{task_rating}}".
 *
 * The followings are the available columns in table '{{task_rating}}':
 * @property integer $id
 * @property integer $task_id
 * @property integer $rating
 * @property string $writer_name
 * @property string $notes
 * @property string $created
 * @property integer $created_by
 */
class TaskRating extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TaskRating the static model class
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
		return '{{task_rating}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id', 'required'),
			array('task_id, rating, created_by', 'numerical', 'integerOnly'=>true),
			array('notes, created, writer_name', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, rating, writer_name, notes, created, created_by', 'safe', 'on'=>'search'),
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
            'rtask' => array(self::BELONGS_TO, 'Task', 'task_id'),
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
			'rating' => 'Rating',
			'writer_name' => "Writer's Name",
			'notes' => 'Feedback',
			'created' => 'Created',
			'created_by' => 'Created By',
		);
	}

    /**
     * Prepares salt, create_time, create_user_id, update_time and
     * update_user_ id attributes before performing validation.
     */
    protected function beforeValidate() {
 
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = new CDbExpression('NOW()');
            $this->created =  date('Y-m-d H:i:s');
            $this->created_by = Yii::app()->user->id;
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
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('rating',$this->rating);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('writer_name',$this->writer_name,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}