<?php

/**
 * This is the model class for table "{{task_note}}".
 *
 * The followings are the available columns in table '{{task_note}}':
 * @property integer $id
 * @property integer $task_id
 * @property string $notes
 * @property string $created
 * @property integer $created_by
 */
class TaskNote extends CActiveRecord
{
    //The following public parameters for search;
    public $nalexarank;
    public $ntier;
    public $nmozrank;
    public $ndesireddomain;
    public $nda;

	/**
	 * Returns the static model of the specified AR class.
	 * @return TaskNote the static model class
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
		return '{{task_note}}';
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
			array('task_id, created_by', 'numerical', 'integerOnly'=>true),
			array('notes, created, hidefromclient,isprivate', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, notes, created, created_by, hidefromclient,isprivate,nalexarank,ntier,nmozrank,ndesireddomain,nda', 'safe', 'on'=>'search'),
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
			'notes' => 'Notes',
			'hidefromclient' => 'Hide From Client',
			'isprivate' => 'Private',
			'created' => 'Created',
			'created_by' => 'Created By',

			'nalexarank' => 'Alexa Rank',
			'ntier'      => 'Tier Level',
			'nmozrank'   => 'Moz Rank',
			'ndesireddomain' => 'Desired Domain',
			'nda' => 'Domain Authority',
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

        if ($this->nalexarank) {
            $criteria->addCondition("t.notes LIKE '%Alexa Rank = ".$this->nalexarank."%'",'AND');
        }
        if ($this->ntier) {
            $criteria->addCondition("t.notes LIKE '%Tier = ".$this->ntier."%'",'AND');
        }
        if ($this->nmozrank) {
            $criteria->addCondition("t.notes LIKE '%Mozrank = ".$this->nmozrank."%'",'AND');
        }
        if ($this->ndesireddomain) {
            $criteria->addCondition("t.notes LIKE '%Desired Domain: ".$this->ndesireddomain."%'",'AND');
        }
        if ($this->nda) {
            $criteria->addCondition("t.notes LIKE '%DA = ".$this->nda."%'",'AND');
        }

		$criteria->compare('id',$this->id);
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('hidefromclient',$this->hidefromclient);
		//$criteria->compare('isprivate',$this->isprivate);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

        $cuid = Yii::app()->user->id;
        //$criteria->addCondition("isprivate=0 OR (created_by=$cuid AND isprivate=1)",'AND');
        $roles = Yii::app()->authManager->getRoles($cuid);
        if(isset($roles['Marketer']) || isset($roles['Publisher'])){
            $criteria->addCondition("isprivate=0 OR (created_by=$cuid AND isprivate=1)",'AND');
        }

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}