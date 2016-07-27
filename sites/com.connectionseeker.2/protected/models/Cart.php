<?php

/**
 * This is the model class for table "{{domain_cart}}".
 *
 * The followings are the available columns in table '{{domain_cart}}':
 * @property string $id
 * @property integer $client_id
 * @property integer $client_domain_id
 * @property string $client_domain
 * @property string $domain_id
 * @property string $domain
 * @property string $created
 * @property integer $created_by
 */
class Cart extends CActiveRecord
{
    public static $dstatus = array('0' => 'Available',
                           '1' => 'Pending',
                           '2' => 'In Use',);
    public $duty_domain_ids;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Cart the static model class
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
		return '{{domain_cart}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, client_domain_id', 'required'),
			array('client_id, client_domain_id, created_by, modified_by, status', 'numerical', 'integerOnly'=>true),
			array('client_domain, domain', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, client_domain_id, client_domain, domain_id, domain, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
			'rcreatedby' => array(self::BELONGS_TO, 'User', 'created_by'),
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
			'client_id' => 'Client',
			'client_domain_id' => 'Client Domain',
			'client_domain' => 'Client Domain',
			'domain_id' => 'Domain',
			'domain' => 'Domain',
			'status' => 'Status',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
		);
	}

    public function scopes()
    {
        //####################################################//
        //Plugin for the employee of the client! usage: ClientDomain::model()->byduty()->findAll();
        $cuid = Yii::app()->user->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        $_domain_ids = array();
        $onduty = array();
        if(isset($roles['Marketer'])){
            $umodel = User::model()->findByPk($cuid);
            if ($umodel->type == 0) {//owner or root!
                /*
                if ($umodel->client_id != $client_id) {
                    $onduty = array('condition'=>"id = -1");//return ;
                }
                $onduty =  array('condition'=>"client_id = $umodel->client_id");
                */
            } else {
                $cmpids = array();
                if ($umodel->duty_campaign_ids) {
                    $cmpids = unserialize($umodel->duty_campaign_ids);
                    $command = Yii::app()->db->createCommand();
                    $_domain_ids = $command->select('domain_id')->from('{{campaign}}')
                                           ->where("(client_domain_id IN (".implode(",", $cmpids)."))")->queryAll();

                    //##$command->reset();//if you wanna reuse the $command, you have to use the reset() method
                    if ($_domain_ids) {
                        $_dids = array();
                        foreach($_domain_ids as $didv) {
                            $_dids[] = $didv['domain_id'];
                        }
                        $onduty = array('condition'=>"client_domain_id IN (".implode(",", $_dids).")");
                    }
                } else {
                    $onduty = array('condition'=>"client_domain_id = -1");//return ;
                }
            }
        }//end of plugin!

        return array(
            /*
            'actived'=>array(
                'condition'=>'status=0',//the same as status=1
            ),
            */
            'byduty'=>$onduty,
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
            //not a new record, so nothing to do right now.
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
		$criteria->compare('client_id',$this->client_id);
        if ($this->duty_domain_ids) {
            if ($this->client_domain_id && in_array($this->client_domain_id, $this->duty_domain_ids)) {
                $criteria->compare('client_domain_id',$this->client_domain_id);
            } else {
                $criteria->compare('client_domain_id',$this->duty_domain_ids);
            }
        } else {
            $criteria->compare('client_domain_id',$this->client_domain_id);
        }

		$criteria->compare('client_domain',$this->client_domain,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
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