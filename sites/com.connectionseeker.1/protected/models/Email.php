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
			array('domain_id, template_id, from, cc, status, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('to', 'length', 'max'=>255),
			array('subject', 'length', 'max'=>1000),
			array('content, send_time, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, template_id, from, to, cc, subject, content, status, send_time, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
			'from' => 'From',
			'to' => 'To',
			'cc' => 'Cc',
			'subject' => 'Subject',
			'content' => 'Content',
			'status' => 'Status',
			'send_time' => 'Send Time',
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
		$criteria->compare('domain_id',$this->domain_id);
		$criteria->compare('template_id',$this->template_id);
		$criteria->compare('`from`',$this->from);
		$criteria->compare('`to`',$this->to,true);
		$criteria->compare('cc',$this->cc);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('send_time',$this->send_time,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}