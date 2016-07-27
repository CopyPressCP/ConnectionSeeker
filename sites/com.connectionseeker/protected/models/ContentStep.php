<?php

/**
 * This is the model class for table "{{task_content_step}}".
 *
 * The followings are the available columns in table '{{task_content_step}}':
 * @property integer $id
 * @property integer $task_id
 * @property string $step_title
 * @property string $direction
 * @property string $resource_link_1
 * @property string $resource_link_2
 * @property string $resource_link_3
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class ContentStep extends CActiveRecord
{
    public $triggerTitleSave = true; //prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!

	/**
	 * Returns the static model of the specified AR class.
	 * @return ContentStep the static model class
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
		return '{{task_content_step}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id,step_title,direction,resource_link_1, resource_link_2, resource_link_3', 'required'),
			array('task_id, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('step_title, resource_link_1, resource_link_2, resource_link_3', 'length', 'max'=>2000),
			array('direction, created, modified, step_domain, client_comment, extra_writer_note', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, step_title, direction, resource_link_1, resource_link_2, resource_link_3, created, created_by, modified, modified_by, step_domain, client_comment, extra_writer_note', 'safe', 'on'=>'search'),
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

    public function behaviors()
    {
        return array(
            'ETrailBehavior' => array('class' => 'application.components.ETrailBehavior'),
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
			'step_title' => 'Title',
			'step_domain' => 'Step Domain',
			'direction' => 'Direction',
			'resource_link_1' => 'Resource Link 1',
			'resource_link_2' => 'Resource Link 2',
			'resource_link_3' => 'Resource Link 3',
			'client_comment' => 'Client Comment',
			'extra_writer_note' => 'Extra Writer Notes',
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
            // $this->created = new CDbExpression('NOW()');
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = Yii::app()->user->id;
        } else {
            //not a new record, so nothing to do right now.
            $this->modified = date('Y-m-d H:i:s');
            $this->modified_by = Yii::app()->user->id;

        }

        return parent::beforeValidate();
    }

    protected function afterSave(){

        if ($this->triggerTitleSave == true) {
            $tmodel = Task::model()->findByPk($this->task_id);
            if ($tmodel && $this->step_title != $tmodel->rewritten_title) {
                $tmodel->setIsNewRecord(false);
                $tmodel->setScenario('update');
                $tmodel->triggerTitleSave = false;
                $tmodel->rewritten_title = $this->step_title;
                $tmodel->save();
            }
        }

        return parent::afterSave();
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
		$criteria->compare('step_title',$this->step_title,true);
		$criteria->compare('direction',$this->direction,true);
		$criteria->compare('resource_link_1',$this->resource_link_1,true);
		$criteria->compare('resource_link_2',$this->resource_link_2,true);
		$criteria->compare('resource_link_3',$this->resource_link_3,true);
		$criteria->compare('client_comment',$this->client_comment,true);
		$criteria->compare('extra_writer_note',$this->extra_writer_note,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}