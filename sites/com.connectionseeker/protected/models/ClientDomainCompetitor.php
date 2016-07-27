<?php

/**
 * This is the model class for table "{{client_domain_competitor}}".
 *
 * The followings are the available columns in table '{{client_domain_competitor}}':
 * @property integer $id
 * @property string $domain_id
 * @property integer $client_id
 * @property integer $competitor_id
 * @property integer $last_call_api_time
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class ClientDomainCompetitor extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ClientDomainCompetitor the static model class
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
		return '{{client_domain_competitor}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('competitor_id', 'required'),
			array('competitor_id, fresh_called, historic_called, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>255),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, competitor_id, fresh_called, historic_called, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
			//'rcompetitor' => array(self::MANY_MANY, 'Competitor', 'competitor_id'),
			'rcompetitor' => array(self::BELONGS_TO, 'Competitor', 'competitor_id'),
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
			'domain_id' => 'Domain',
			'competitor_id' => 'Competitor',
			'fresh_called' => 'Last Call Api Time',
			'historic_called' => 'Last Call Api Time',
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
            //not a new record, so just set the last updated time and last updated user id
            //$this->modified = new CDbExpression('NOW()');
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
		$criteria->compare('domain_id',$this->domain_id,true);
		//$criteria->compare('client_id',$this->client_id);
		$criteria->compare('competitor_id',$this->competitor_id);
		$criteria->compare('historic_called',$this->historic_called);
		$criteria->compare('fresh_called',$this->fresh_called);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}