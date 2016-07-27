<?php

/**
 * This is the model class for table "{{io_historic_reporting}}".
 *
 * The followings are the available columns in table '{{io_historic_reporting}}':
 * @property integer $id
 * @property integer $task_id
 * @property string $date_initial
 * @property string $date_current
 * @property string $date_accepted
 * @property string $date_approved
 * @property string $date_pending
 * @property string $date_completed
 * @property string $date_denied
 * @property integer $time2current
 * @property integer $time2accepted
 * @property integer $time2approved
 * @property integer $time2pending
 * @property integer $time2completed
 * @property integer $time2denied
 */
class IoHistoricReporting extends CActiveRecord
{
    public $client_id;

	/**
	 * Returns the static model of the specified AR class.
	 * @return IoHistoricReporting the static model class
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
		return '{{io_historic_reporting}}';
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
			array('task_id, campaign_id, time2current, time2accepted, time2approved, time2pending, time2completed, time2denied', 'numerical', 'integerOnly'=>true),
			array('client_id, tierlevel, channel_id, date_initial, date_current, date_accepted, date_approved, date_pending, date_completed, date_denied, date_preqa, date_inrepair', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, campaign_id, client_id, tierlevel, channel_id, date_initial, date_current, date_accepted, date_approved, date_pending, date_completed, date_denied, date_preqa, date_inrepair, time2current, time2accepted, time2approved, time2pending, time2completed, time2denied, time2preqa, time2inrepair', 'safe', 'on'=>'search'),
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
			'campaign_id' => 'Campaign',
			'client_id' => 'Client',
			'tierlevel' => 'Tier Level',
			'channel_id' => 'Channel',
			'date_initial' => 'Date Initial',
			'date_current' => 'Date Current',
			'date_accepted' => 'Date Accepted',
			'date_approved' => 'Date Approved',
			'date_pending' => 'Date Pending',
			'date_inrepair' => 'Date In Repair',
			'date_preqa' => 'Date Pre QA',
			'date_completed' => 'Date Completed',
			'date_denied' => 'Date Denied',
			'time2current' => 'Time 2 Current',
			'time2accepted' => 'Time 2 Accepted',
			'time2approved' => 'Time 2 Approved',
			'time2pending' => 'Time 2 Pending',
			'time2preqa' => 'Time 2 Pre QA',
			'time2inrepair' => 'Time 2 In Repair',
			'time2completed' => 'Time 2 Completed',
			'time2denied' => 'Time 2 Denied',
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
		$criteria->compare('tierlevel',$this->tierlevel);
		$criteria->compare('channel_id',$this->channel_id);

		//##$criteria->compare('campaign_id',$this->campaign_id);
        if ($this->campaign_id) {
            $criteria->with = array('rcampaign');
            if (is_numeric($this->campaign_id)) {
                //http://www.yiiframework.com/wiki/199/creating-a-parameterized-like-query/
                $criteria->addCondition("t.campaign_id = :cmpid OR rcampaign.name LIKE :cmpname");
                $_cmpid = addcslashes($this->campaign_id, '%_');
                $criteria->params[':cmpid'] = $_cmpid;
                $criteria->params[':cmpname'] = "%$_cmpid%";
            } elseif(is_array($this->campaign_id)) {
                $criteria->compare('t.campaign_id',$this->campaign_id);
            } else {
                $criteria->compare('rcampaign.name',$this->campaign_id, true);
            }
        }
        /*
		$criteria->compare('date_initial',$this->date_initial,true);
		$criteria->compare('date_current',$this->date_current,true);
		$criteria->compare('date_accepted',$this->date_accepted,true);
		$criteria->compare('date_approved',$this->date_approved,true);
		$criteria->compare('date_pending',$this->date_pending,true);
		$criteria->compare('date_completed',$this->date_completed,true);
		$criteria->compare('date_denied',$this->date_denied,true);
		$criteria->compare('time2current',$this->time2current);
		$criteria->compare('time2accepted',$this->time2accepted);
		$criteria->compare('time2approved',$this->time2approved);
		$criteria->compare('time2pending',$this->time2pending);
		$criteria->compare('time2completed',$this->time2completed);
		$criteria->compare('time2denied',$this->time2denied);
        */
        $this->genBtwCond($criteria, 'date_initial');
        $this->genBtwCond($criteria, 'date_current');
        $this->genBtwCond($criteria, 'date_accepted');
        $this->genBtwCond($criteria, 'date_approved');
        $this->genBtwCond($criteria, 'date_pending');
        $this->genBtwCond($criteria, 'date_completed');
        $this->genBtwCond($criteria, 'date_denied');

        $this->genBtwCond($criteria, 'date_inrepair');
        $this->genBtwCond($criteria, 'date_preqa');
        $this->genBtwCond($criteria, 'time2preqa');
        $this->genBtwCond($criteria, 'time2inrepair');

        $this->genBtwCond($criteria, 'time2current');
        $this->genBtwCond($criteria, 'time2accepted');
        $this->genBtwCond($criteria, 'time2approved');
        $this->genBtwCond($criteria, 'time2pending');
        $this->genBtwCond($criteria, 'time2completed');
        $this->genBtwCond($criteria, 'time2denied');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    //generate between condition
    protected function genBtwCond(&$criteria, $column='id', $partialMatch=false, $operator='AND', $escape=true) {
        if ($this->$column) {
            $_btw = preg_split("/<[\s,]*</", $this->$column);
            if (count($_btw) == 2) {
                $_btw[0] = trim($_btw[0]);
                $_btw[1] = trim($_btw[1]);
                if (stripos($column, "time2") !== false) {
                    $_btw[0] = $_btw[0] * 86400;
                    $_btw[1] = $_btw[1] * 86400;
                }
                $criteria->addBetweenCondition($column, $_btw[0], $_btw[1]);
            } else {
                $criteria->compare($column, $this->$column, $partialMatch, $operator, $escape);
            }
        } else {
            $criteria->compare($column, $this->$column, $partialMatch, $operator, $escape);
        }
        //return $criteria;
    }
}