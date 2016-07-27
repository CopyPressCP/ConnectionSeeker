<?php

/**
 * This is the model class for table "{{template}}".
 *
 * The followings are the available columns in table '{{template}}':
 * @property integer $id
 * @property string $name
 * @property string $subject
 * @property string $content
 * @property string $notes
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Template extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Template the static model class
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
		return '{{template}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content, name', 'required'),
			array('id, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('name, subject', 'length', 'max'=>255),
            array('name', 'unique'),
			array('notes', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, subject, content, notes, created_by,  modified_by, status', 'safe', 'on'=>'search'),
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
			'createdby' => array(self::BELONGS_TO, 'User', 'created_by'),
			'modifiedby' => array(self::BELONGS_TO, 'User', 'modified_by'),
		);
	}

    public function behaviors()
    {
        return array(
            'ETrailBehavior' => array('class' => 'application.components.ETrailBehavior'),
        );
    }

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
			'name' => 'Name',
			'subject' => 'Subject',
			'content' => 'Content',
			'notes' => 'Notes',
			'created' => 'Created',
			'created_by' => 'Creator',
			'modified' => 'Last Modified',
			'modified_by' => 'Last Modified By',
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

		$criteria=new CDbCriteria(array('with'=>array('createdby','modifiedby')));


        if (!isset($this->status)) {
            $this->status = 1;
        }
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.subject',$this->subject,true);
		$criteria->compare('t.content',$this->content,true);
		$criteria->compare('t.notes',$this->notes,true);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('createdby.id',$this->created_by);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}