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
			array('domain_id', 'length', 'max'=>20),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, domain_id, client_id, use_historic_index, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
            'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
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
                                           ->where("(id IN (".implode(",", $cmpids)."))")->queryAll();

                    //##$command->reset();//if you wanna reuse the $command, you have to use the reset() method
                    if ($_domain_ids) {
                        $_dids = array();
                        foreach($_domain_ids as $didv) {
                            $_dids[] = $didv['domain_id'];
                        }
                        $onduty = array('condition'=>"id IN (".implode(",", $_dids).")");
                    }
                } else {
                    $onduty = array('condition'=>"id = -1");//return ;
                }
            }
        }//end of plugin!

        return array(
            'actived'=>array(
                //'condition'=>'t.status=0',//the same as status=1
                'condition'=>'status=0',//the same as status=1
            ),
            'byduty'=>$onduty,
        );
    }

    public function behaviors(){
        return array(
            'CAdvancedArBehavior' => array('class' => 'application.components.CAdvancedArBehavior'),
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
			'domain' => 'Domain',
			'domain_id' => 'Domain #',
			'client_id' => 'Client',
			'use_historic_index' => 'Use Historic Index',
			'status' => 'Active',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
		);
	}

    /*
    * Save the domain into the table.domain automatically, so that all of the domain will insert into tbl.domain
    */
    protected function beforeSave(){
        //placeholder here!
        $domodel = new Domain;
        $domain = $domodel->find('domain=:domain',array(':domain'=>$this->domain));

        if ($domain) {
            $this->domain_id = $domain->id;
        } else {
            $domodel->setIsNewRecord(true);
            $domodel->id=NULL;
            $domodel->domain=$this->domain;
            // $this->stype was used to upload way.
            if (isset($this->stype) && $this->stype) $domodel->stype = $this->stype;
            if (isset($this->category) && $this->category) {
                $domodel->category = $this->category;
                $domodel->category_str = $this->category_str;
            }
            $tld = array_pop(explode(".", $this->domain));
            $domodel->tld=$tld;
            if ($domodel->save()) {
                $this->domain_id = $domodel->id;
            } else {
                $this->addErrors(array("domain"=>array('Domain: "'.$this->domain.'" may have format issue.')));
                return false;
                //throw new CHttpException(400,'The domain did not stored. Please try it again.');
            }
        }

        return parent::beforeSave();
    }


    /**
     * Prepares salt, create_time, create_user_id, update_time and
     * update_user_ id attributes before performing validation.
     */
    protected function beforeValidate() {

        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = new CDbExpression('NOW()');
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = Yii::app()->user->id;
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