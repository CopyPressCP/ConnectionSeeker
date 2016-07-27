<?php

/**
 * This is the model class for table "{{domain_audit}}".
 *
 * The followings are the available columns in table '{{domain_audit}}':
 * @property string $id
 * @property string $domain
 * @property string $domain_id
 * @property string $backlinkshistory
 * @property integer $fresh_called
 * @property integer $historic_called
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Audit extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Audit the static model class
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
		return '{{domain_audit}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fresh_called, historic_called, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('backlinkshistory, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, domain_id, backlinkshistory, fresh_called, historic_called, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
			'domain' => 'Domain',
			'domain_id' => 'Domain',
			'backlinkshistory' => 'Backlinkshistory',
			'fresh_called' => 'Fresh Called',
			'historic_called' => 'Historic Called',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('backlinkshistory',$this->backlinkshistory,true);
		$criteria->compare('fresh_called',$this->fresh_called);
		$criteria->compare('historic_called',$this->historic_called);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}