<?php

/**
 * This is the model class for table "{{client_discovery}}".
 *
 * The followings are the available columns in table '{{client_discovery}}':
 * @property string $id
 * @property integer $client_id
 * @property string $domain_id
 * @property string $domain
 * @property integer $competitora_id
 * @property string $competitora
 * @property integer $competitorb_id
 * @property string $competitorb
 * @property integer $progress
 * @property integer $complete_with_automation
 * @property string $automation_setting
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class ClientDiscovery extends CActiveRecord
{
    //$steps for progress value;
    public static $steps = array('0' => 'Initial',
                           '1' => 'Step #1', //getting backlinks
                           '2' => 'Step #2', //compare backdomain of the client's domain with the competitors
                           '3' => 'Step #3', //Send domain to Crawler API & Getting Data From Crawler API
                           '4' => 'Step #4', //Sending Automation Email Out
                           '5' => 'Step #5', //Finish Email Task
                           );
                           //##'6' => 'Step #6',);

	/**
	 * Returns the static model of the specified AR class.
	 * @return ClientDiscovery the static model class
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
		return '{{client_discovery}}';
	}

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('client_id', 'required'),
            array('client_id, competitora_id, competitorb_id, progress, complete_with_automation, status, created_by, modified_by', 'numerical', 'integerOnly'=>true),
            array('domain_id', 'length', 'max'=>20),
            array('domain, competitora, competitorb', 'length', 'max'=>255),
            array('automation_setting, created, modified', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, client_id, domain_id, domain, competitora_id, competitora, competitorb_id, competitorb, progress, complete_with_automation, automation_setting, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_id' => 'Client',
			'domain_id' => 'Client Domain',
			'domain' => 'Client Domain',
			'competitora_id' => 'Competitor',
			'competitora' => 'Competitor #1',
			'competitorb_id' => 'Competitor',
			'competitorb' => 'Competitor #2',

			'status' => 'Status',
			'progress' => 'Progress Status',
			'automation_setting' => 'Automation Setting',
			'complete_with_automation' => 'Complete This Workflow With Automation Rules',

			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
		);
	}

	/**
	 * @return array named scopes.
     * Usage: $etasks = ClientDiscovery::model()->actived()->findAll(); 
	 */
    public function scopes()
    {
        return array(
            'actived'=>array(
                'condition'=>'status=1',
            ),
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
     * before save a new research/discovery.
     * 
     */
    protected function beforeSave(){
        //if ($this->isNewRecord) {
            Yii::import('application.vendors.*');
            if ($this->domain) {
                $this->domain = SeoUtils::getSubDomain($this->domain);
                $this->domain_id = self::domainExist($this->domain);
            }
            if ($this->competitora) {
                $this->competitora = SeoUtils::getSubDomain($this->competitora);
                $this->competitora_id = self::domainExist($this->competitora);
            }
            if ($this->competitorb) {
                $this->competitorb = SeoUtils::getSubDomain($this->competitorb);
                $this->competitorb_id = self::domainExist($this->competitorb);
            }
        //}

        return parent::beforeSave();
    }

    public static function domainExist($domain){
        $domodel = new Domain;
        $dmdl = $domodel->find('domain=:domain',array(':domain'=>$domain));

        if ($dmdl) {
            $domain_id = $dmdl->id;
        } else {
            $domodel->setIsNewRecord(true);
            $domodel->id=NULL;
            $domodel->domain=$domain;
            $tld = array_pop(explode(".", $domain));
            $domodel->tld=$tld;
            if ($domodel->save()) {
                $domain_id = $domodel->id;
            } else {
                //print_r($domodel->getErrors());
                //$this->addErrors(array("domain"=>array('Domain: "'.$domain.'" may have format issue.')));
                $domain_id = false;
                //exit;
                //throw new CHttpException(400,'The domain did not stored. Please try it again.');
            }
        }

        if ($domain_id) {
            $dcoverdomain = new DiscoveryDomain;
            $dcover = $dcoverdomain->find('domain_id=:did',array(':did'=>$domain_id));
            if (!$dcover) {
                $dcoverdomain->setIsNewRecord(true);
                $dcoverdomain->id=NULL;
                $dcoverdomain->domain=$domain;
                $dcoverdomain->domain_id=$domain_id;
                if ($dcoverdomain->save()) {
                    //Do nothing for now, i think we need put one transation here!;
                } else {
                    //print_r($dcoverdomain->getErrors());
                    //exit;
                }
            }
        }

        return $domain_id;
    }

    protected function afterSave(){
        if ($this->domain_id) {
            $this->_createXref($this->domain_id);
        }
        if ($this->competitora_id) {
            $this->_createXref($this->competitora_id);
        }
        if ($this->competitorb_id) {
            $this->_createXref($this->competitorb_id);
        }

        return parent::afterSave();
    }

    private function _createXref($domain_id) {
        if ($domain_id) {
            $dxdomain = new DiscoveryXrefDomain;
            $dcover = $dxdomain->find('domain_id=:did AND discovery_id=:disid',array(':did'=>$domain_id,":disid"=>$this->id));
            if (!$dcover) {
                $dxdomain->setIsNewRecord(true);
                $dxdomain->id=NULL;
                $dxdomain->discovery_id=$this->id;
                $dxdomain->domain_id=$domain_id;
                if ($dxdomain->save()) {
                    //Do nothing for now, i think we need put one transation here!;
                }
            }
        }
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
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('competitora_id',$this->competitora_id);
		$criteria->compare('competitora',$this->competitora,true);
		$criteria->compare('competitorb_id',$this->competitorb_id);
		$criteria->compare('competitorb',$this->competitorb,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

        $criteria->compare('progress',$this->progress);
        $criteria->compare('complete_with_automation',$this->complete_with_automation);
        $criteria->compare('automation_setting',$this->automation_setting,true);
        $criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}