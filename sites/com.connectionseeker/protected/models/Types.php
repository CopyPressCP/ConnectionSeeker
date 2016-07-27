<?php

/**
 * This is the model class for table "{{types}}".
 *
 * The followings are the available columns in table '{{types}}':
 * @property integer $id
 * @property string $type
 * @property integer $refid
 * @property string $typename
 * @property integer $status
 */
class Types extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Types the static model class
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
		return '{{types}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refid, type, typename', 'required'),
			array('refid, status, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>20),
			array('typename', 'length', 'max'=>256),
            array('outils', 'safe'),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, refid, typename, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
		);
	}

    public function scopes()
    {
        return array(
            'actived'=>array(
                'condition'=>'status=1',
            ),
            //Usage: $clients = Types::model()->actived()->findAll();
        );
    }

    public function bytype($type = "site")
    {
        if (is_array($type)) {
            $type = implode("','", array_values($type));
            $this->getDbCriteria()->mergeWith(array(
                'condition'=>"type IN ('{$type}')",
            ));
            /*
            $this->getDbCriteria()->mergeWith(array(
                'condition'=>"type='{$type}'",
            ), false);
            */
        } else {
            $this->getDbCriteria()->mergeWith(array(
                'condition'=>"type='{$type}'",
                //'order'=>'created DESC',
                //'limit'=>$limit,
            ));
        }

        return $this;
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
			'type' => 'Type',
			'refid' => 'Ref ID',
			'typename' => 'Type Name',
			'status' => 'Active',
			'outils' => 'Utils/Formula/Memo',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
		);
	}

    /**
     * Prepares salt, create_time, create_user_id, update_time and
     * update_user_ id attributes before performing validation.
     */
    protected function beforeValidate() {
        $this->typename = trim($this->typename);
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = new CDbExpression('NOW()');
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = Yii::app()->user->id;
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

        if (!isset($this->status)) {
            $this->status = 1;
        }
		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('refid',$this->refid);
		$criteria->compare('typename',$this->typename,true);
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