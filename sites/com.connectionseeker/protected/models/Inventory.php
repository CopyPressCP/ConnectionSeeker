<?php

/**
 * This is the model class for table "{{inventory}}".
 *
 * The followings are the available columns in table '{{inventory}}':
 * @property integer $id
 * @property string $domain
 * @property string $domain_id
 * @property string $category
 * @property string $category_str
 * @property integer $channel_id
 * @property string $ext_backend_acct
 * @property integer $link_on_homepage
 * @property string $notes
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Inventory extends CActiveRecord
{
    public $upfile;

    public $mozrank;
    public $googlepr;
    public $mozauthority;
    public $alexarank;

    //Add include and negative
    public $campaign_include;
    public $campaign_exclude;

	/**
	 * tbl.domain.stype means the site type, this one will used in the upload way.
	 */
    public $stype;
    public $otype;
    public $currentaction = "published";

    public static $probabilities = array('1' => 'Low',
                           '2' => 'Medium',
                           '3' => 'High',);

    public $triggerDomainSave = true; //prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!

    private $_oldTaskAttributes;
    public function afterFind()
    {
        $this->_oldTaskAttributes = $this->attributes;
        return parent::afterFind();
    }


	/**
	 * Returns the static model of the specified AR class.
	 * @return Inventory the static model class
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
		return '{{inventory}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain','required'),
            array('domain', 'unique'),
            array('upfile', 'file', 'types'=>'csv, xls, xlsx, ods, slk, xml', 'on'=>'upload'),
			array('link_on_homepage, status, created_by, modified_by, user_id, acquired_channel_id, ispublished, islogin,isip', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('category, accept_tasktype, channel_id, campaign_id', 'length', 'max'=>500),
			array('category_str, accept_tasktype_str, channel_str,client_id', 'length', 'max'=>1000),
			array('campaign_str,client_str', 'length', 'max'=>3000),
			array('ext_backend_acct, notes, created, modified, stype, otype, acquireddate, denied_by, denied_by_str, isdenied, owner_channel_id, owner_channel_str, probability, compete_scaned, compete_value,last_published', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, domain_id, user_id, category, category_str, accept_tasktype, accept_tasktype_str, channel_id, channel_str, ext_backend_acct, link_on_homepage, notes, status, isdenied, denied_by, created, created_by, modified, modified_by, currentaction, owner_channel_id, owner_channel_str, probability, compete_scaned, compete_value, campaign_str, campaign_id,last_published,islogin,isip,client_id,client_str', 'safe', 'on'=>'search'),
            //,  googlepr,googlepropr,stype,acrankopr,acrank,ageopr,age,hubcountopr,hubcount,islinked
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
            'risummary' => array(self::HAS_ONE, 'Summary', '', 'on' => 't.domain_id=risummary.domain_id'),
            'rlink' => array(self::HAS_MANY, 'Link', 'inventory_id'),
            'ronenote' => array(self::HAS_ONE, 'Note', '', 'on' => 't.domain_id=ronenote.domain_id', 'order'=>'ronenote.id DESC'),
            'roneprice' => array(self::HAS_ONE, 'DomainPrice', '', 'on' => 't.domain_id=roneprice.domain_id', 'order'=>'roneprice.id DESC'),
			//for get getDomainNote
			'rdnote' => array(self::HAS_MANY, 'Note', '', 'on' => ''),

            //Cause the tbl.task.desired_domain_id is not mapping the tbl.inventory.id, so we leave the PK field empty and use the 'on' condition override 
            'rtask' => array(self::HAS_MANY, 'Task', '', 'on' => 't.domain_id=rtask.desired_domain_id'),
            //'riotrail' => array(self::HAS_MANY, 'Trail', '', 'on' => '(riotrail.old_value LIKE CONCAT("%", t.domain, "%") AND riotrail.model="Taska")'),
            'riotrail' => array(self::HAS_MANY, 'Trail', '', 'on' => ''),
		);
	}

    public function behaviors()
    {
        return array(
            'ETrailBehavior' => array('class' => 'application.components.ETrailBehavior'),
        );
    }

    public function scopes()
    {
        $_nvs[0] = 's:8:"iostatus";i:1;';
        $_nvs[1] = 's:8:"iostatus";s:1:"1";';
        $_nvs[2] = 's:8:"iostatus";i:4;';
        $_nvs[3] = 's:8:"iostatus";s:1:"4";';
        $deniedios = "( (riotrail.new_value LIKE '%".implode("%') OR (riotrail.new_value LIKE '%", $_nvs)."%') ) ";
        $deniedios .= "AND (riotrail.old_value LIKE '%desired_domain_id%')";
		$cuid = Yii::app()->user->id;

        return array(
            'byuser'=>array(
                'condition'=>'t.user_id='.Yii::app()->user->id,
            ),
            'denied'=>array(
                'condition'=>'t.isdenied=1',
            ),
            'actived'=>array(
                'condition'=>'t.status=1',
            ),
            'getDeniedIO'=>array(
                'condition'=>$deniedios,
                'join'=>'LEFT JOIN {{operation_trail}} AS riotrail ON (riotrail.old_value LIKE CONCAT("%", t.domain, "%") AND riotrail.model="Task")',
            ),
        
            'getDomainNote'=>array(
                'condition'=>'',
                'join'=>"LEFT JOIN (SELECT domain_id, GROUP_CONCAT(notes) AS allnotes FROM {{domain_note}} WHERE isprivate=0 OR (created_by=$cuid AND isprivate=1) GROUP BY domain_id) AS rdnote ON (rdnote.domain_id=t.domain_id)",
            ),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'Belongs To',
			'domain' => 'Domain',
			'domain_id' => 'Domain',
			'category' => 'Category',
			'category_str' => 'Categories',
			'accept_tasktype' => 'Accept Link Type',
			'accept_tasktype_str' => 'Accept Link Types',
			'channel_id' => 'Channel',
			'channel_str' => 'Channel',
			'acquired_channel_id' => 'Acquired',
			'acquireddate' => 'Acquired Date',
			'ispublished' => 'Is Published',
			'last_published' => 'Last Published',
			'ext_backend_acct' => 'Ext Backend Acct',
			'link_on_homepage' => 'HP',//Link On Homepage
			'notes' => 'Notes',
			'status' => 'Active',
			'denied_by' => 'Denied By',
			'denied_by_str' => 'Denied By',
			'isdenied' => 'Is Denied',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',

			'islogin' => 'HP',//Login checkbox
			'isip'    => 'I.P.',//Instant Publish checkbox

			'client_id' => 'Potential Clients',
			'client_str' => 'Potential Clients',

			'owner_channel_id' => 'Owners',
			'owner_channel_str' => 'Owners',
			'probability' => 'Probability',
			'campaign_id' => 'Campaign',
			'campaign_str' => 'Campaign',

			'compete_scaned' => 'Compete Latest Scan',
			'compete_value' => 'Compete',
		);
	}

    protected function beforeSave(){
        if ($this->triggerDomainSave != true) {
            return parent::beforeSave(); 
        }

        //placeholder here!
        $domodel = new Domain;
        $domain = $domodel->find('domain=:domain',array(':domain'=>$this->domain));

        if ($domain) {
            $domain->triggerInventorySave = false;
            $cattrigger = false;
            if ($this->_oldTaskAttributes["category"] != $this->category) {
                $domain->category=$this->category;
                $domain->category_str=$this->category_str;
                $cattrigger = true;
            }
            $this->domain_id = $domain->id;
            if ($this->ispublished == 1) {
                $this->last_published = date('Y-m-d H:i:s');
                if (!in_array($domain->touched_status, array(7,8,15,20))) {
                    $cattrigger = false;
                    $domain->touched_status=20;
                    $domain->save();
                }
            } else {
                /*@2015-01-30
                if (!in_array($domain->touched_status, array(7,8,15,20,6))) {
                    $cattrigger = false;
                    $domain->touched_status=6;
                    $domain->save();
                }
                */

                if (!in_array($domain->touched_status, array(7,8,15,20,6)) 
                    && ($this->_oldTaskAttributes["acquireddate"] != $this->acquireddate || empty($domain->touched))) {
                    $cattrigger = false;
                    $domain->touched_status=6;
                    $domain->save();

                    if (!empty($this->_oldTaskAttributes["acquireddate"]) 
                        && $this->_oldTaskAttributes["acquireddate"] != $this->acquireddate) {
                        $this->acquireddate = $this->_oldTaskAttributes["acquireddate"];
                    }
                }
                /*@2016-01-30
                if (!in_array($domain->touched_status, array(7,8,15,20,6)) 
                    && $this->_oldTaskAttributes["acquireddate"] != $this->acquireddate) {
                    $cattrigger = false;
                    $domain->touched_status=6;
                    $domain->save();

                    if (!empty($this->_oldTaskAttributes["acquireddate"])) {
                        $this->acquireddate = $this->_oldTaskAttributes["acquireddate"];
                    }
                }
                */
            }
            //The date should be updated if we update the owner for now. request by Ching-Li Lin<clin@copypress.com>
            if ($this->_oldTaskAttributes["owner_channel_id"] != $this->owner_channel_id) {
                $this->acquireddate = date("Y-m-d H:i:s");
            }
            if ($cattrigger) {
                $domain->save();
            }
            /*
            $domodel->user_id = $this->user_id;
            $domodel->category = $this->category;
            $domodel->save();
            */
            //$this->setAttribute("domain_id", $domain->id);
            //return true;
        } else {
            $domodel->setIsNewRecord(true);
            $domodel->id=NULL;
            $domodel->domain=$this->domain;
            // $this->stype was used to upload way.
            if ($this->stype) $domodel->stype = $this->stype;
            if ($this->otype) $domodel->otype = $this->otype;
            if ($this->category) {
                $domodel->category = $this->category;
                $domodel->category_str = $this->category_str;
            }
            $tld = array_pop(explode(".", $this->domain));
            $domodel->tld=$tld;
            if ($this->ispublished == 1) {
                $this->last_published = date('Y-m-d H:i:s');
                $domain->touched_status = 20;
            } else {
                //comment out 2016-01-30
                //##if (!empty($this->acquireddate)) $domodel->touched_status = 6;
                $domodel->touched_status = 6;
            }
            $domodel->triggerInventorySave = false;
            if ($domodel->save()) {
                $this->domain_id = $domodel->id;
            } else {
                $this->addErrors(array("domain"=>array('--------- Domain --------- "'.$this->domain.'" may have format issue.')));
                return false;
                //throw new CHttpException(400,'The domain did not stored. Please try it again.');
            }
        }

        return parent::beforeSave();
    }

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        /*
        we should improve the perfermance of this method when we get a chance, cause when we batch upload inventories,
        this will call get types too many times, we should call it one time only.
        */
        if (!empty($this->category)) {
            if (!is_array($this->category) && strpos($this->category, "|") === 0) {
                // for the exception input, such as: |1|..|n|..
                $_tmps = substr($this->category, 1, -1);
                $this->category = explode("|", $_tmps);
            }
            //print_r($this->category);
            //cause we used the refid's value as the dropdown values.
            //$categories = Types::model()->actived()->bytype('category')->findAllByPk(array_values($this->category));
            $categories = Types::model()->actived()->bytype('category')
                                        ->findAllByAttributes(array('refid' => array_values($this->category)));
            //print_r($categories);
            $data = array();
            if ($categories) {
                //$data = CHtml::listData($categories, 'id', 'typename');
                $data = CHtml::listData($categories, 'refid', 'typename');
                if (!empty($data)) $this->category_str = implode(", ", array_values($data));
            }
            $this->category = "|".implode("|", array_values($this->category))."|";
        }
        //$this->category = serialize($this->category);

        if (!empty($this->accept_tasktype)) {
            if (!is_array($this->accept_tasktype) && strpos($this->accept_tasktype, "|") === 0) {
                $_tmps = substr($this->accept_tasktype, 1, -1);
                $this->accept_tasktype = explode("|", $_tmps);
            }
            //cause we used the refid's value as the dropdown values.
            $tasktypes = Types::model()->actived()->bytype('linktask')
                                       ->findAllByAttributes(array('refid' => array_values($this->accept_tasktype)));
            $data = array();
            if ($tasktypes) {
                $data = CHtml::listData($tasktypes, 'refid', 'typename');
                if (!empty($data)) $this->accept_tasktype_str = implode(", ", array_values($data));
            }
            $this->accept_tasktype = "|".implode("|", array_values($this->accept_tasktype))."|";
        }

        if (!empty($this->channel_id)) {
            if (!is_array($this->channel_id) && strpos($this->channel_id, "|") === 0) {
                $_tmps = substr($this->channel_id, 1, -1);
                $this->channel_id = explode("|", $_tmps);
            }
            //cause we used the refid's value as the dropdown values.
            $channels = Types::model()->actived()->bytype('channel')
                                       ->findAllByAttributes(array('refid' => array_values($this->channel_id)));
            $data = array();
            if ($channels) {
                $data = CHtml::listData($channels, 'refid', 'typename');
                if (!empty($data)) $this->channel_str = implode(", ", array_values($data));
            }
            $this->channel_id = "|".implode("|", array_values($this->channel_id))."|";
        }

        if (!empty($this->owner_channel_id)) {
            if (!is_array($this->owner_channel_id) && strpos($this->owner_channel_id, "|") === 0) {
                $_tmps = substr($this->owner_channel_id, 1, -1);
                $this->owner_channel_id = explode("|", $_tmps);
            }
            $channels = Types::model()->actived()->bytype('channel')
                                       ->findAllByAttributes(array('refid' => array_values($this->owner_channel_id)));
            $data = array();
            if ($channels) {
                $data = CHtml::listData($channels, 'refid', 'typename');
                if (!empty($data)) $this->owner_channel_str = implode(", ", array_values($data));
            }
            $this->owner_channel_id = "|".implode("|", array_values($this->owner_channel_id))."|";
        }

        if (!empty($this->client_id)) {
            if (!is_array($this->client_id) && strpos($this->client_id, "|") === 0) {
                $_tmps = substr($this->client_id, 1, -1);
                $this->client_id = explode("|", $_tmps);
            }
            $clients = Client::model()->actived()->findAll();
            $data = array();
            if ($clients) {
                $data = CHtml::listData($clients,'id','company');
                if (!empty($data)) $this->client_str = implode(", ", array_values($data));
            }
            $this->client_id = "|".implode("|", array_values($this->client_id))."|";
        }

        if ($this->isNewRecord) {
            $uid = Yii::app()->user->id;
            $roles = Yii::app()->authManager->getRoles($uid);
            if(isset($roles['Publisher'])){
                $this->user_id = $uid;
            }

            // set the create date, last updated date, then the user doing the creating
            // $this->created = new CDbExpression('NOW()');
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = $uid;
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->modified = new CDbExpression('NOW()');
            $this->modified = date('Y-m-d H:i:s');
            $this->modified_by = Yii::app()->user->id;
        }

        return parent::beforeValidate();
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function simpleSearch()
	{
		$criteria=new CDbCriteria;
        $_acquireddate = $this->acquireddate;
        if ($_acquireddate) $_acquireddate = Utils::smartDateSearch($_acquireddate);
		$criteria->compare('t.acquireddate', $_acquireddate, true);
		//$criteria->compare('t.acquireddate',$this->acquireddate,true);
		$criteria->compare('t.last_published', $this->last_published, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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

        if ($this->category) {
            if (is_array($this->category)) {
                $_condition = "(0 ";
                foreach ($this->category as $v) {
                    //$criteria->addCondition("t.category LIKE '%|".$v."|%'",'OR');
                    //$_condition .= "OR t.category LIKE '%|".$v."|%'";
                    if ($v == -1) {
                        $_condition .= "OR (t.category IS NULL OR t.category='')";
                    } else {
                        $_condition .= "OR t.category LIKE '%|".$v."|%'";
                    }
                }
                $_condition .= ")";
                //$criteria->compare('category',$this->category,true);
                $criteria->addCondition($_condition);//add one more ()
            } else {
                if ($this->category == -1) {
                    $criteria->addCondition("(t.category IS NULL OR t.category='')",'AND');
                } else {
                    $criteria->compare('category',"|".$this->category."|",true);
                }
            }
        }

        if ($this->accept_tasktype) {
            $_condition = "(0 ";
            foreach ($this->accept_tasktype as $v) {
                //$criteria->addCondition("t.accept_tasktype LIKE '%|".$v."|%'",'OR');
                $_condition .= "OR t.accept_tasktype LIKE '%|".$v."|%'";
            }
            $_condition .= ")";
		    //$criteria->compare('category',$this->category,true);
            $criteria->addCondition($_condition);//add one more ()
        }

        if ($this->currentaction == "published") {
            //$criteria->addCondition("((t.channel_id IS NOT NULL) OR (t.channel_id != ''))");
            $criteria->addCondition("(t.ispublished=1)");
        }

        if ($this->channel_id) {
            $_condition = "(0 ";
            foreach ($this->channel_id as $v) {
                //$criteria->addCondition("t.channel_id LIKE '%|".$v."|%'",'OR');
                $_condition .= "OR t.channel_id LIKE '%|".$v."|%'";
            }
            $_condition .= ")";
            //$criteria->compare('t.channel_id',$this->channel_id);
            //die();
            $criteria->addCondition($_condition);//add one more ()
        }

        if ($this->owner_channel_id) {
            if (is_array($this->owner_channel_id)) {
                $_condition = "(0 ";
                foreach ($this->owner_channel_id as $v) {
                    $_condition .= "OR t.owner_channel_id LIKE '%|".$v."|%'";
                }
                $_condition .= ")";
                $criteria->addCondition($_condition);//add one more ()
            } else {
                $criteria->compare('t.owner_channel_id', "|".$this->owner_channel_id."|", true);
            }
        }

        if ($this->client_id) {
            if (is_array($this->client_id)) {
                $_condition = "(0 ";
                foreach ($this->client_id as $v) {
                    $_condition .= "OR t.client_id LIKE '%|".$v."|%'";
                }
                $_condition .= ")";
                $criteria->addCondition($_condition);//add one more ()
            } else {
                $criteria->compare('t.client_id', "|".$this->client_id."|", true);
            }
        }

        //*********************************************//
        //$criteria->with = array('rdomain');
        $criteria->with = array('rdomain.rsummary');
        //*********************************************//

        $className = __CLASS__;
        if ($_GET[$className]) {
            extract($_GET[$className]);

            //domain age 这个部分的操作符要反转
            if ($age) {
                $onlinesince = time() - $age * 86400 * 365;//365 days;
                if (stripos($ageopr, "<") !== false) {
                    $ageopr = str_replace("<", ">", $ageopr);
                } elseif (stripos($ageopr, ">") !== false) {
                    $ageopr = str_replace(">", "<", $ageopr);
                }
                $criteria->compare('rdomain.onlinesince', $ageopr.$onlinesince);
            }

            if ($googlepr) $criteria->compare('rdomain.googlepr',$googlepropr.$googlepr);
            if ($alexarank) $criteria->compare('rdomain.alexarank',$alexarankopr.$alexarank);
            if ($mozrank) $criteria->compare('round(rsummary.mozrank)',$mozrankopr.$mozrank);
            if ($authority) $criteria->compare('round(rsummary.mozauthority)',$authorityopr.$authority);
            if ($stype) $criteria->compare('rdomain.stype',$stype);
            if ($otype) $criteria->compare('rdomain.otype',$otype);

            //if ($islinked && $domain) {
            if ( (in_array($islinked, array(1,2)) && $domain)
              || ($islinked == 3) ) {
                $criteria->with = array('rlink'=>array('together'=>true,'select'=>false));
                if ($islinked == 1) {
                    //$criteria->compare('rlink.targetdomain', "=".$domain, true);
                    $criteria->addCondition("rlink.targetdomain = '{$domain}'");
                } elseif($islinked == 2) {
                    $criteria->addCondition("rlink.targetdomain != '{$domain}'");
                } else {
                    $criteria->addCondition("rlink.targetdomain IS NULL");
                }
            } else {
                $criteria->compare('t.domain',$this->domain,true);
            }

            if ($campaign_include) $criteria->compare("t.campaign_str", $campaign_include, true);
            if ($campaign_exclude) {
                $criteria->addCondition("t.campaign_str NOT LIKE :cmpname OR t.campaign_str IS NULL");
                $campaign_exclude = addcslashes($campaign_exclude, '%_');
                $criteria->params[':cmpname'] = "%$campaign_exclude%";
            }
            /*
            if ($campaign_include || $campaign_exclude) {
                $criteria->with = array('rdomain.rsummary',
                                        'rtask'           => array('together'=>true,'select'=>false),
                                        'rtask.rcampaign' => array('together'=>true,'select'=>false));
                if ($campaign_include) $criteria->compare("rcampaign.name", $campaign_include, true);
                if ($campaign_exclude) $criteria->addCondition("rcampaign.name NOT LIKE '%$campaign_exclude%'");
            }
            */
        }

        //$criteria->compare('t.id',">12163");
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.user_id',$this->user_id);
		$criteria->compare('t.ispublished',$this->ispublished);
		$criteria->compare('t.isdenied',$this->isdenied);
		//##$criteria->compare('t.isdenied',$this->isdenied,true);

        if ($this->denied_by) $this->denied_by = "|".$this->denied_by."|";
		$criteria->compare('t.denied_by',$this->denied_by,true);
		//$criteria->compare('t.denied_by_str',$this->denied_by_str,true);
		//###$criteria->compare('t.domain',$this->domain,true);
		$criteria->compare('t.domain_id',$this->domain_id);
		$criteria->compare('t.category_str',$this->category_str,true);
		$criteria->compare('t.channel_str',$this->channel_str,true);
		$criteria->compare('t.ext_backend_acct',$this->ext_backend_acct,true);
		$criteria->compare('t.link_on_homepage',$this->link_on_homepage);
		$criteria->compare('t.islogin',$this->islogin);
		$criteria->compare('t.isip',$this->isip);
		$criteria->compare('t.notes',$this->notes,true);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('rdomain.status',1);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.acquired_channel_id',$this->acquired_channel_id);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);
		$criteria->compare('t.probability',$this->probability);

		$criteria->compare('t.compete_value',$this->compete_value);
		$criteria->compare('t.compete_scaned',$this->compete_scaned, true);

        $_acquireddate = $this->acquireddate;
        if ($_acquireddate) $_acquireddate = Utils::smartDateSearch($_acquireddate);
        //echo $_acquireddate;
        //exit;
		$criteria->compare('t.acquireddate', $_acquireddate, true);
		//$criteria->compare('t.acquireddate',$this->acquireddate,true);
		$criteria->compare('t.last_published', $this->last_published, true);

        $sort = new CSort();
        $sort->attributes = array(
            //'rdomain.stype'=>array(
            'stype'=>array(
                'asc'=>'rdomain.stype ASC',
                'desc'=>'rdomain.stype DESC',
            ),
            'otype'=>array(
                'asc'=>'rdomain.otype ASC',
                'desc'=>'rdomain.otype DESC',
            ),
            //'rdomain.googlepr'=>array(
            'googlepr'=>array(
                'asc'=>'rdomain.googlepr ASC',
                'desc'=>'rdomain.googlepr DESC',
            ),
            //'rdomain.alexarank'=>array(
            'alexarank'=>array(
                'asc'=>'rdomain.alexarank ASC',
                'desc'=>'rdomain.alexarank DESC',
            ),
            'rdomain.linkingdomains'=>array(
                'asc'=>'rdomain.linkingdomains ASC',
                'desc'=>'rdomain.linkingdomains DESC',
            ),
            'rdomain.inboundlinks'=>array(
                'asc'=>'rdomain.inboundlinks ASC',
                'desc'=>'rdomain.inboundlinks DESC',
            ),
            'rdomain.onlinesince'=>array(
                'asc'=>'rdomain.onlinesince ASC',
                'desc'=>'rdomain.onlinesince DESC',
            ),
            //'rdomain.rsummary.mozrank'=>array(
            'mozrank'=>array(
                'asc'=>'rsummary.mozrank ASC',
                'desc'=>'rsummary.mozrank DESC',
            ),
            //'rdomain.rsummary.mozauthority'=>array(
            'mozauthority'=>array(
                'asc'=>'rsummary.mozauthority ASC',
                'desc'=>'rsummary.mozauthority DESC',
            ),
            '*', // add all of the other columns as sortable
        );

		return new CActiveDataProvider($this, array(
            'sort'=>$sort,
			'criteria'=>$criteria,
		));
	}
}