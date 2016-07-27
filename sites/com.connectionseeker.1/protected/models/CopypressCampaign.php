<?php

/**
 * This is the model class for table "{{copypress_campaign}}".
 *
 * The followings are the available columns in table '{{copypress_campaign}}':
 * @property integer $id
 * @property integer $client_id
 * @property integer $campaign_id
 * @property integer $content_campaign_id
 * @property string $content_campaign_name
 * @property integer $content_category_id
 * @property integer $month
 * @property string $notes
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class CopypressCampaign extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CopypressCampaign the static model class
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
		return '{{copypress_campaign}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, campaign_id, content_campaign_id, month, created_by', 'required'),
			array('client_id, campaign_id, content_campaign_id, content_category_id, month, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('content_campaign_name', 'length', 'max'=>255),
			array('notes, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, campaign_id, content_campaign_id, content_campaign_name, content_category_id, month, notes, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
            'rcampaign' => array(self::BELONGS_TO, 'Campaign', 'campaign_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_id' => 'Client',
			'campaign_id' => 'Campaign',
			'content_campaign_id' => 'Content Campaign',
			'content_campaign_name' => 'Content Campaign Name',
			'content_category_id' => 'Content Category',
			'month' => 'Month',
			'notes' => 'Notes',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('campaign_id',$this->campaign_id);
		$criteria->compare('content_campaign_id',$this->content_campaign_id);
		$criteria->compare('content_campaign_name',$this->content_campaign_name,true);
		$criteria->compare('content_category_id',$this->content_category_id);
		$criteria->compare('month',$this->month);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}