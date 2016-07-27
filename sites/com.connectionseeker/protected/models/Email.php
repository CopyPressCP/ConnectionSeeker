<?php

/**
 * This is the model class for table "{{email_queue}}".
 *
 * The followings are the available columns in table '{{email_queue}}':
 * @property integer $id
 * @property integer $domain_id
 * @property integer $template_id
 * @property integer $from
 * @property string $to
 * @property integer $cc
 * @property string $subject
 * @property string $content
 * @property integer $status
 * @property string $send_time
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Email extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @return Email the static model class
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
		return '{{email_queue}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain_id, template_id, from, status', 'required'),
			array('domain_id, template_id, from, status, created_by, modified_by, parent_id, is_reply', 'numerical', 'integerOnly'=>true),
			array('to', 'length', 'max'=>255),
			array('subject', 'length', 'max'=>1000),
			array('cc', 'length', 'max'=>2000),
			array('content, send_time, replied_time, created, modified, email_from, mid, reply_created_by', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, mid, template_id, from, to, cc, subject, content, status, send_time, created, created_by, modified, modified_by,reply_created_by,replied_time', 'safe', 'on'=>'search'),
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
            'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            'rtemplate' => array(self::BELONGS_TO, 'Template', 'template_id'),
			'rcreatedby' => array(self::BELONGS_TO, 'User', 'created_by'),
			'rmailer' => array(self::BELONGS_TO, 'Mailer', 'from'),
            /*
			'revent' => array(self::HAS_MANY, 'EmailEvent', 'queue_id', 'condition'=>'revent.queue_id>0'),
			'reventone' => array(self::HAS_ONE, 'EmailEvent', 'queue_id', 'condition'=>'reventone.queue_id>0'),
            */
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
			'template_id' => 'Template',
			'from' => 'Mailer',
			'to' => 'To',
			'cc' => 'CC',
			//'emevent' => 'Event',
			'email_from' => 'From',
			'parent_id' => 'Group ID',
			'mid' => 'Message Number',
			'is_reply' => 'Reply',
			'subject' => 'Subject',
			'content' => 'Content',
			'status' => 'Status',
			'send_time' => 'Send Time',
			'replied_time' => 'Replied Time',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
			'reply_created_by' => 'Reply To',
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
     * Update the campaign task's published_count & percentage when you set it complete.
     * 
     */
    protected function afterSave(){
        if ($this->isNewRecord) {
            if ($this->domain_id > 0 && $this->template_id > 0 && $this->from > 0 && $this->status == 1) {
                //insert this email into lkm_outreach_email
                $dmodel = Domain::model()->findByPk($this->domain_id);
                $oem = array();
                if ($dmodel) {
                    $oemodel = new OutreachEmail;
                    $oemodel->setIsNewRecord(true);
                    $oemodel->domain    = $dmodel->domain;
                    $oemodel->domain_id = $this->domain_id;
                    $oemodel->queue_id  = $this->id;
                    $oemodel->template_id  = $this->template_id;
                    $oemodel->mailer_id  = $this->from;
                    $oemodel->send_time  = $this->send_time;
                    $oemodel->efrom      = $this->email_from;
                    $oemodel->eto        = $this->to;
                    $oemodel->created_by = $this->created_by;
                    $oemodel->save();

                    $dmodel->last_sent_email = $this->to;
                    $dmodel->save();
                }
            }
        }

        return parent::afterSave();
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

		$criteria->compare('t.id',$this->id);
        if (is_numeric($this->domain_id)) {
		    //do nothing;
        } else {
            $dm = Domain::model()->find('t.domain=:domain',array(':domain'=>$this->domain_id));
            if ($dm) {
                $tmpdomain = $this->domain_id;
                $this->domain_id = $dm->id;
            }
        }

        if (isset($this->domain_id)) {
            if (is_numeric($this->domain_id)) {
                $criteria->compare('t.domain_id',$this->domain_id);
            } else {
                $criteria->with = array('rdomain');
                $criteria->compare('rdomain.domain',$this->domain_id, true);
            }
        }

		$criteria->compare('t.template_id',$this->template_id);
        if (isset($this->parent_id)) {
		    $criteria->compare('t.parent_id',$this->parent_id);
        } else {
		    //$criteria->compare('parent_id',0);
            $criteria->addCondition('(t.parent_id = 0) OR (t.parent_id IS NULL)');
        }

        //For some reallity reason, we need set the send_time since 2013 by default
        if (isset($this->send_time)) {
            $_send_timestamp = strtotime($this->send_time);
            if ($_send_timestamp > 1356998400) {
		        $criteria->compare('t.send_time',$this->send_time,true);
            } else {
                $criteria->addCondition("t.send_time >= '2013-01-01 00:00:00'");
            }
        } else {
            $criteria->addCondition("t.send_time >= '2013-01-01 00:00:00'");
            //$criteria->compare('send_time',">='2013-01-01 00:00:00'",true);
        }
        $criteria->compare('t.send_time',$this->replied_time,true);

		$criteria->compare('t.email_from',$this->email_from, true);
		$criteria->compare('t.is_reply',$this->is_reply);
		$criteria->compare('`from`',$this->from);
		$criteria->compare('`to`',$this->to,true);
		$criteria->compare('t.cc',$this->cc);
		$criteria->compare('t.subject',$this->subject,true);
		$criteria->compare('t.content',$this->content,true);
		$criteria->compare('t.status',$this->status);
		//$criteria->compare('t.send_time',$this->send_time,true);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.reply_created_by',$this->reply_created_by);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);

        if (isset($tmpdomain)) {
            $this->domain_id = $tmpdomain;
        }

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
    public static function getEvents($es)
    {
        $data = '';
        if ($es) {
            foreach($es as $e) {
                $data .= $e->event.", ";
            }
        }
        return $data;
    }
    */
}