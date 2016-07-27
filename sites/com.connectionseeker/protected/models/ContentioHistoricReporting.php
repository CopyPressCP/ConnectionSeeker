<?php

/**
 * This is the model class for table "{{contentio_historic_reporting}}".
 *
 * The followings are the available columns in table '{{contentio_historic_reporting}}':
 * @property integer $id
 * @property integer $task_id
 * @property integer $campaign_id
 * @property integer $channel_id
 * @property integer $tierlevel
 * @property string $date_step0
 * @property string $date_step1
 * @property string $date_step2
 * @property string $date_step3
 * @property string $date_step4
 * @property string $date_step5
 * @property integer $time2step0
 * @property integer $time2step1
 * @property integer $time2step2
 * @property integer $time2step3
 * @property integer $time2step4
 * @property integer $time2step5
 */
class ContentioHistoricReporting extends CActiveRecord
{
    public $client_id;
    public $campaign_name;
    public $business_days;
    public $pro_duration_days;

    /**
	 * Returns the static model of the specified AR class.
	 * @return ContentioHistoricReporting the static model class
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
		return '{{contentio_historic_reporting}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, campaign_id', 'required'),
			array('task_id, campaign_id, channel_id, tierlevel, time2step0, time2step1, time2step2, time2step3, time2step4, time2step5', 'numerical', 'integerOnly'=>true),
			array('date_step0, date_step1, date_step2, date_step3, date_step4, date_step5', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, campaign_id, channel_id, tierlevel, date_step0, date_step1, date_step2, date_step3, date_step4, date_step5, time2step0, time2step1, time2step2, time2step3, time2step4, time2step5, campaign_name, client_id', 'safe', 'on'=>'search'),
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
			'task_id' => 'Task',
			'client_id' => 'Client',
			'campaign_id' => 'Campaign',
			'channel_id' => 'Channel',
			'tierlevel' => 'Tierlevel',
			'date_step0' => 'Date Ideation',
			'date_step1' => 'Date Idea Approval',
			'date_step2' => 'Date Place Order',
			'date_step3' => 'Date Ordered',
			'date_step4' => 'Date Content Approval',
			'date_step5' => 'Date Delivered',
			'time2step0' => 'Ideation(min.)',
			'time2step1' => 'Idea Approval(min.)',
			'time2step2' => 'Place Order(min.)',
			'time2step3' => 'Ordered(min.)',
			'time2step4' => 'Content Approval(min.)',
			'time2step5' => 'Delivered(min.)',
			'business_days' => 'Estimate Business days',
			'pro_duration_days' => 'Total Production Duration (days)',
		);
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
		//##$criteria->compare('campaign_id',$this->campaign_id);
		$criteria->compare('channel_id',$this->channel_id);
		$criteria->compare('tierlevel',$this->tierlevel);
		$criteria->compare('date_step0',$this->date_step0,true);
		$criteria->compare('date_step1',$this->date_step1,true);
		$criteria->compare('date_step2',$this->date_step2,true);
		$criteria->compare('date_step3',$this->date_step3,true);
		$criteria->compare('date_step4',$this->date_step4,true);
		$criteria->compare('date_step5',$this->date_step5,true);
		$criteria->compare('time2step0',$this->time2step0);
		$criteria->compare('time2step1',$this->time2step1);
		$criteria->compare('time2step2',$this->time2step2);
		$criteria->compare('time2step3',$this->time2step3);
		$criteria->compare('time2step4',$this->time2step4);
		$criteria->compare('time2step5',$this->time2step5);

        if ($this->client_id) {
		    $criteria->compare('rcampaign.client_id',$this->client_id);
        }

        if ($this->campaign_name) {
            if (is_numeric($this->campaign_name)) {
                //http://www.yiiframework.com/wiki/199/creating-a-parameterized-like-query/
                $criteria->addCondition("t.campaign_id = :cmpid OR rcampaign.name LIKE :cmpname");
                $_cmpid = addcslashes($this->campaign_name, '%_');
                $criteria->params[':cmpid'] = $_cmpid;
                $criteria->params[':cmpname'] = "%$_cmpid%";
            } elseif(is_array($this->campaign_name)) {
                $criteria->compare('t.campaign_id',$this->campaign_name);
            } else {
                $criteria->compare('rcampaign.name',$this->campaign_name, true);
            }
        }

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'attributes'=>array(
                    'client_id' => array(
                        'asc' => 'rcampaign.client_id ASC',
                        'desc' => 'rcampaign.client_id DESC',
                    ),
                    'campaign_name' => array(
                        'asc' => 'rcampaign.name ASC',
                        'desc' => 'rcampaign.name DESC',
                    ),
                    '*',
                ),
            ),
		));
	}
}