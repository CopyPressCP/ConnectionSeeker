<?php

/**
 * This is the model class for table "{{client_domain}}".
 *
 * The followings are the available columns in table '{{client_domain}}':
 * @property integer $id
 * @property string $domain
 * @property integer $client_id
 * @property integer $use_historic_index
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class ClientDomain extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ClientDomain the static model class
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
		return '{{client_domain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, domain', 'required'),
			array('use_historic_index, client_id, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, client_id, use_historic_index, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
            'rclient' => array(self::BELONGS_TO, 'Client', 'client_id'),
            'rcompetitor' => array(self::MANY_MANY, 'Competitor', '{{client_domain_competitor}}(domain_id, competitor_id)'),
			'createdby' => array(self::BELONGS_TO, 'User', 'created_by'),
            //'tags'=>array(self::MANY_MANY, 'Tag', '{{tag_campaign}}(campaignId, tagId)', 'on'=>"tags.lng='en'"),
		);
	}

    public function scopes()
    {
        return array(
            'actived'=>array(
                //'condition'=>'t.status=0',//the same as status=1
                'condition'=>'status=0',//the same as status=1
            ),
        );
    }

    public function behaviors(){
        return array( 'CAdvancedArBehavior' => array('class' => 'application.components.CAdvancedArBehavior'));
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'domain' => 'Domain',
			'client_id' => 'Client',
			'use_historic_index' => 'Use Historic Index',
			'status' => 'Active',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('use_historic_index',$this->use_historic_index);
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