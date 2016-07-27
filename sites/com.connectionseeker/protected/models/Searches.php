<?php

/**
 * This is the model class for table "{{searches}}".
 *
 * The followings are the available columns in table '{{searches}}':
 * @property integer $id
 * @property string $name
 * @property string $controller
 * @property string $view
 * @property string $searches
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 */
class Searches extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Searches the static model class
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
		return '{{searches}}';
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
			array('name, ctrl_name, view_name', 'length', 'max'=>255),
			array('searches, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, ctrl_name, view_name, searches, created, created_by, modified', 'safe', 'on'=>'search'),
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
	 * @return array named scopes.
     * Usage: $users = Automation::model()->actived()->findAll(); 
	 */
    public function scopes()
    {
        return array(
            'myOwn'=>array(
                'condition'=>'created_by='.Yii::app()->user->id,
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
			'ctrl_name' => 'Controller',
			'view_name' => 'View',
			'searches' => 'Searches',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('ctrl_name',$this->ctrl_name,true);
		$criteria->compare('ctrl_name',$this->ctrl_name,true);
		$criteria->compare('searches',$this->searches,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}