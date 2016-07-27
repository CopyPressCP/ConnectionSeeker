<?php

/**
 * This is the model class for table "{{automation_sent}}".
 *
 * The followings are the available columns in table '{{automation_sent}}':
 * @property string $id
 * @property string $domain_id
 * @property string $domain
 * @property string $primary_email
 * @property string $owner
 * @property integer $automation_id
 * @property integer $template_id
 * @property integer $mailer_id
 * @property integer $status
 * @property string $sent
 */
class AutomationSent extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return AutomationSent the static model class
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
		return '{{automation_sent}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('automation_id', 'required'),
			array('automation_id, template_id, mailer_id, status, client_discovery_id', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>20),
			array('domain, owner', 'length', 'max'=>255),
			array('primary_email', 'length', 'max'=>1000),
			array('sent, opened_time, replied_time, queue_id, type_of_automation, client_discovery_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, domain, primary_email, owner, automation_id, template_id, mailer_id, status, sent, opened_time, replied_time, type_of_automation, client_discovery_id, queue_id', 'safe', 'on'=>'search'),
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
            'rtemplate' => array(self::BELONGS_TO, 'Template', 'template_id'),
			'rmailer' => array(self::BELONGS_TO, 'Mailer', 'mailer_id'),
            'rinventory' => array(self::BELONGS_TO, 'Inventory', array('domain_id'=>'domain_id')),//###added 4/17/2014
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
			'domain' => 'Domain',
			'primary_email' => 'Primary Email',
			'owner' => 'Owner',
			'automation_id' => 'Automation',
			'type_of_automation' => 'Type',
			'client_discovery_id' => 'Discovery',
			'template_id' => 'Template',
			'mailer_id' => 'Mailer',
			'status' => 'Status',
			'sent' => 'Sent',
			'opened_time' => 'Open Time',
			'replied_time' => 'Reply Time',
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

        $criteria->addCondition("`sent`>'2015-10-01'");
        if ($this->sent) {
            $this->sent = strtolower($this->sent);
            $sents = explode("to", $this->sent);
            if (count($sents)>=2) {
                $datestart = trim($sents[0]);
                $dateend = trim($sents[1]);
                $dst = strtotime($datestart) - 86400;
                $det = strtotime($dateend) + 86400;
                $datestart = date("Y-m-d", $dst);
                $dateend = date("Y-m-d", $det);
                $criteria->addBetweenCondition('`sent`', $datestart, $dateend, 'AND');
            } else {
                //do nothing for now;
                $criteria->compare('sent',$this->sent,true);
            }
        }

		$criteria->compare('id',$this->id,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('primary_email',$this->primary_email,true);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('automation_id',$this->automation_id);
		$criteria->compare('type_of_automation',$this->type_of_automation);
		$criteria->compare('client_discovery_id',$this->client_discovery_id);
		$criteria->compare('template_id',$this->template_id);
		$criteria->compare('mailer_id',$this->mailer_id);
		$criteria->compare('queue_id',$this->queue_id);
		$criteria->compare('status',$this->status);
		//$criteria->compare('sent',$this->sent,true);
		$criteria->compare('opened_time',$this->opened_time,true);
		$criteria->compare('replied_time',$this->replied_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}