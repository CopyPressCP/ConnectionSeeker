<?php

/**
 * This is the model class for table "{{outreach_email}}".
 *
 * The followings are the available columns in table '{{outreach_email}}':
 * @property string $id
 * @property string $queue_id
 * @property string $domain
 * @property string $domain_id
 * @property integer $template_id
 * @property integer $mailer_id
 * @property string $send_time
 * @property string $open_time
 * @property string $first_reply_time
 * @property string $latest_reply_time
 * @property integer $isexternal
 * @property integer $isautomation
 * @property string $efrom
 * @property string $eto
 * @property string $extreplied
 * @property string $extsent
 * @property integer $nofextreplied
 * @property integer $nofextsent
 * @property integer $created_by
 */
class OutreachEmail extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return OutreachEmail the static model class
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
		return '{{outreach_email}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('template_id, mailer_id, created_by', 'required'),
			array('template_id, mailer_id, isexternal, isautomation, nofextreplied, nofextsent, created_by', 'numerical', 'integerOnly'=>true),
			array('queue_id, domain_id', 'length', 'max'=>20),
			array('domain, efrom, eto', 'length', 'max'=>255),
			array('send_time, open_time, first_reply_time, latest_reply_time, extreplied, extsent', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, queue_id, domain, domain_id, template_id, mailer_id, send_time, open_time, first_reply_time, latest_reply_time, isexternal, efrom, eto, extreplied, extsent, nofextreplied, nofextsent, created_by', 'safe', 'on'=>'search'),
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
			'queue_id' => 'Queue',
			'domain' => 'Domain',
			'domain_id' => 'Domain',
			'template_id' => 'Template',
			'mailer_id' => 'Mailer',
			'send_time' => 'Send Time',
			'open_time' => 'Open Time',
			'first_reply_time' => 'First Reply Time',
			'latest_reply_time' => 'Latest Reply Time',
			'isexternal' => 'Isexternal',
			'isautomation' => 'Isautomation',
			'efrom' => 'Efrom',
			'eto' => 'Eto',
			'extreplied' => 'Extreplied',
			'extsent' => 'Extsent',
			'nofextreplied' => 'Nofextreplied',
			'nofextsent' => 'Nofextsent',
			'created_by' => 'Created By',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        /*
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            $this->created_by = Yii::app()->user->id;
        } else {
            //do nothing for now
        }
        */

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
		$criteria->compare('queue_id',$this->queue_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('template_id',$this->template_id);
		$criteria->compare('mailer_id',$this->mailer_id);
		$criteria->compare('send_time',$this->send_time,true);
		$criteria->compare('open_time',$this->open_time,true);
		$criteria->compare('first_reply_time',$this->first_reply_time,true);
		$criteria->compare('latest_reply_time',$this->latest_reply_time,true);
		$criteria->compare('isexternal',$this->isexternal);
		$criteria->compare('isautomation',$this->isautomation);
		$criteria->compare('efrom',$this->efrom,true);
		$criteria->compare('eto',$this->eto,true);
		$criteria->compare('extreplied',$this->extreplied,true);
		$criteria->compare('extsent',$this->extsent,true);
		$criteria->compare('nofextreplied',$this->nofextreplied);
		$criteria->compare('nofextsent',$this->nofextsent);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}