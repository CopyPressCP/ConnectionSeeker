<?php

/**
 * This is the model class for table "{{domain}}".
 *
 * The followings are the available columns in table '{{domain}}':
 * @property integer $id
 * @property string $domain
 * @property string $tld
 * @property integer $googlepr
 * @property integer $onlinesince
 * @property integer $linkingdomains
 * @property integer $inboundlinks
 * @property integer $indexedurls
 * @property integer $alexarank
 * @property string $ip
 * @property string $subnet
 * @property string $title
 * @property string $owner
 * @property string $email
 * @property string $telephone
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $zip
 * @property string $street
 * @property integer $stype
 * @property integer $otype
 * @property string $touched
 * @property integer $touched_by
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Domain extends CActiveRecord
{
    public $upfile;
    public $mozrank;
    public $mozauthority;
    public $semrushkeywords;
    public $price;
    public $tierlevel;

    public $triggerInventorySave = false; //prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!

    /*
    //Work In Progress(contacted, bounced, opened, clicked)
    public static $status = array('0' => 'Unchecked',
                           '1' => 'Site Untouched',
                           '2' => 'Contacted',
                           '3' => 'Bounced',
                           '4' => 'Opened',
                           '5' => 'Clicked',
                           '6' => 'Site Acquired',
                           '7' => 'Site Denied',
                           '8' => 'Not Interested',
                           '9' => 'Queued',
                           '10' => 'Replied',
                           '11' => 'Contact Form',
                           '12' => 'No Form Found',
                           '13' => 'Internal Outreach',
                           '14' => 'Internal Upload',
                           '15' => 'Delete',
                           '16' => 'Ready For Outreach',);
    */

    public static $status = array('1' => 'Site Untouched',
                           '2' => 'Contacted',
                           '3' => 'Bounced Email',
                           '6' => 'Site Acquired',
                           '20' => 'Published',
                           '7' => 'CP Not Interested', //Site Denied
                           '8' => 'Blogger Not Interested', //Not Interested
                           '10' => 'Replied',
                           '11' => 'Follow Up 1',
                           '19' => 'Follow Up 1 - Sent',
                           '18' => 'Follow Up 2',
                           '12' => 'Website Down',
                           '15' => 'Delete',
                           '16' => 'Ready For Outreach',
                           '17' => 'Required Log In',);


    private $_oldAttributes;
    public function afterFind()
    {
        //$this->oldRecord = clone $this;
        $this->_oldAttributes = $this->attributes;
        //print_r($this->_oldAttributes);
        return parent::afterFind();
    }


	/**
	 * Returns the static model of the specified AR class.
	 * @return Domain the static model class
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
		return '{{domain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('domain', 'required'),
            array('domain', 'unique'),
			array('googlepr, onlinesince, linkingdomains, inboundlinks, indexedurls, alexarank, touched_by, created_by, modified_by, stype, otype, technorati_category, touched_status, status', 'numerical', 'integerOnly'=>true),
			array('domain, title, owner, primary_email, owner2, primary_email2, last_sent_email, telephone, rootdomain', 'length', 'max'=>255),
			array('tld', 'length', 'max'=>10),
			array('ip, subnet', 'length', 'max'=>32),
			array('country, host_country, zip', 'length', 'max'=>64),
			array('state, city, host_city', 'length', 'max'=>128),
			array('category, discovery_mailer', 'length', 'max'=>500),
			array('category_str, technorati_category_str, email, meta_keywords, meta_description', 'length', 'max'=>1000),
			array('street, stype, otype, touched, created, modified, spa_twitter_username, spa_twitter_followers, spa_twitter_url, spa_facebook_username, spa_facebook_likes, spa_facebook_url, spa_ggplus_username, spa_ggplus_plusone, spa_ggplus_circles, spa_ggplus_url, spa_linkedin_username, spa_linkedin_url,last_sent_email,mozauthority,mozrank', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, rootdomain, tld, googlepr, onlinesince, linkingdomains, inboundlinks, indexedurls, alexarank, ip, subnet, title, owner, email, telephone, country, host_country, state, city, zip, street, stype, otype, category, category_str, technorati_category, technorati_category_str, touched_status, touched, touched_by, created, created_by, modified, modified_by, semrushkeywords, mozrank, mozauthority, meta_keywords, meta_description, tierlevel, spa_twitter_username, spa_twitter_followers, spa_twitter_url, spa_facebook_username, spa_facebook_likes, spa_facebook_url, spa_ggplus_username, spa_ggplus_plusone, spa_ggplus_circles, spa_ggplus_url, spa_linkedin_username, spa_linkedin_url, price, owner2, primary_email2,last_sent_email,discovery_mailer,status', 'safe', 'on'=>'search'),
		);
	}

    public function scopes()
    {
        return array(
            'touched'=>array(
                'condition'=>'(t.touched_status > 0 AND t.touched_status != 6 AND t.touched_status != 20)',
            ),
            'actived'=>array(
                'condition'=>'t.touched_status != 15',
            ),
            'undeleted'=>array(
                'condition'=>'t.status = 1',
            ),
            /*
            'hasemail'=>array(
                'condition'=>"(t.owner IS NOT NULL OR t.owner != '') AND (t.primary_email IS NOT NULL OR t.primary_email != '')",
            ),
            */
            'hasowner'=>array(
                'condition'=>"(t.owner IS NOT NULL OR t.owner != '')",
            ),
            'hasemail'=>array(
                'condition'=>"(t.primary_email IS NOT NULL OR t.primary_email != '')",
            ),
            //Usage: $clients = Types::model()->actived()->findAll();
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
            'rbacklink' => array(self::HAS_MANY, 'Backlink', 'domain_id'),
            'rinventory' => array(self::HAS_ONE, 'Inventory', 'domain_id'),
            'rsummary' => array(self::HAS_ONE, 'Summary', 'domain_id'),
            'ronpage' => array(self::HAS_ONE, 'DomainOnpage', 'domain_id'),
            'rcrawler' => array(self::HAS_ONE, 'Crawler', 'domain_id'),
            'rprice' => array(self::HAS_MANY, 'DomainPrice', 'domain_id'),
            'touchedby' => array(self::BELONGS_TO, 'User', 'touched_by'),
            'ronenote' => array(self::HAS_ONE, 'Note', 'domain_id'),
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
            'domain' => 'Domain',
            'rootdomain' => 'Root Domain',
            'tld' => 'Tld',
            'googlepr' => 'PR',
            'onlinesince' => 'Onlinesince',
            'linkingdomains' => 'Linkingdomains',
            'inboundlinks' => 'Inboundlinks',
            'indexedurls' => 'Indexedurls',
            'alexarank' => 'Alexa Rank',
            'ip' => 'Ip',
            'subnet' => 'Subnet',
            'title' => 'Title',
            'email' => 'Email',
            'owner' => 'Contact Name',
            'primary_email' => 'Primary Email',
            'owner2' => 'Owner2',
            'primary_email2' => 'Primary Email2',
            'last_sent_email' => 'Last Sent Email',

            'telephone' => 'Telephone',
            'country' => 'Country',
            'host_country' => 'Host Country',
            'host_city' => 'City',
            'state' => 'State',
            'city' => 'City',
            'zip' => 'Zip',
            'street' => 'Street',
            'stype' => 'Site Type',
            'otype' => 'Outreach Type',
            'category' => 'Category',
            'category_str' => 'Categories',
            'technorati_category' => 'Technorati Category',
            'technorati_category_str' => 'Technorati Category',
            'touched' => 'Touched',
            'touched_status' => 'Status',
            'touched_by' => 'Touched By',
            'created' => 'Added',
            'created_by' => 'Added By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
            'meta_keywords' => 'Keywords',
            'meta_description' => 'Description',
            'tierlevel' => 'Tier Level',
            'discovery_mailer' => 'Discovery Mailer',

            //the following for the lkm_domain_summary
            'mozrank' => 'Domain Moz Rank',
            'mozauthority' => 'Domain Authority',

            //The SEOCandy Social Pages API, Please refer http://www.seocandy.co.uk/social-pages-api/
            'spa_twitter_username' => 'Twitter Username',
            'spa_twitter_followers' => 'Twitter Followers',
            'spa_twitter_url' => 'Twitter URL',
            'spa_facebook_username' => 'Facebook Username',
            'spa_facebook_likes' => 'Facebook Likes',
            'spa_facebook_url' => 'Facebook URL',

            'spa_ggplus_username' => 'Google+ Username',
            'spa_ggplus_plusone' => 'Google+ Plus1',
            'spa_ggplus_circles' => 'Google+ Circles',
            'spa_ggplus_url' => 'Google+ URL',
            'spa_linkedin_username' => 'LinkedIn Username',
            'spa_linkedin_url' => 'LinkedIn URL',
        );
    }

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
         if (!empty($this->category) && (is_array($this->category) || is_numeric($this->category))) {
            if (is_numeric($this->category)) $this->category = array($this->category);
            //print_r($this->category);
            //cause we used the refid's value as the dropdown values.
            //$categories = Types::model()->actived()->bytype('category')->findAllByPk(array_values($this->category));
            $categories = Types::model()->actived()->bytype('category')
                                        ->findAllByAttributes(array('refid' => array_values($this->category)));
            //print_r($categories);
            $data = array();
            $this->category = null;
            if ($categories) {
                //$data = CHtml::listData($categories, 'id', 'typename');
                $data = CHtml::listData($categories, 'refid', 'typename');
                if (!empty($data)) {
                    $this->category_str = implode(", ", array_values($data));
                    $this->category = "|".implode("|", array_keys($data))."|";
                }
            }
            //##$this->category = "|".implode("|", array_values($this->category))."|";
        }

        #################8/21/2012 start ###################
        if (!empty($this->awis_category) && is_numeric($this->awis_category)) {
            $categories = Types::model()->actived()->bytype('awis')
                                        ->findAllByAttributes(array('refid' => $this->awis_category));
            if ($categories) {
                $this->awis_category_str = $categories->typename;
            }
        }

        if (!empty($this->technorati_category) && is_numeric($this->technorati_category)) {
            $categories = Types::model()->actived()->bytype('technorati')
                                        ->findAllByAttributes(array('refid' => $this->technorati_category));
            if ($categories) {
                $this->technorati_category_str = $categories->typename;
            }
        }
        
        if (empty($this->rootdomain)) {
            Yii::import('application.vendors.*');
            $this->rootdomain = SeoUtils::getDomain($this->domain);
        }

        if (empty($this->tld)) $this->tld = substr(strrchr($this->domain, "."), 1);

        /*
        if (!empty($this->awis_category) && (is_array($this->awis_category) || is_numeric($this->awis_category))) {
            if (is_numeric($this->awis_category)) $this->awis_category = array($this->awis_category);
            $categories = Types::model()->actived()->bytype('awis')
                                        ->findAllByAttributes(array('refid' => array_values($this->awis_category)));
            //print_r($categories);
            $data = array();
            if ($categories) {
                //$data = CHtml::listData($categories, 'id', 'typename');
                $data = CHtml::listData($categories, 'refid', 'typename');
                if (!empty($data)) $this->awis_category_str = implode(", ", array_values($data));
            }
            $this->awis_category = "|".implode("|", array_values($this->awis_category))."|";
        }

        if (!empty($this->technorati_category) && (is_array($this->technorati_category) || is_numeric($this->technorati_category))) {
            if (is_numeric($this->technorati_category)) $this->technorati_category = array($this->technorati_category);
            $categories = Types::model()->actived()->bytype('technorati')
                                        ->findAllByAttributes(array('refid' => array_values($this->technorati_category)));
            //print_r($categories);
            $data = array();
            if ($categories) {
                //$data = CHtml::listData($categories, 'id', 'typename');
                $data = CHtml::listData($categories, 'refid', 'typename');
                if (!empty($data)) $this->technorati_category_str = implode(", ", array_values($data));
            }
            $this->technorati_category = "|".implode("|", array_values($this->technorati_category))."|";
        }
        */
        #################8/21/2012 end #########################

        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
            $this->created = $this->modified = date('Y-m-d H:i:s');
            $this->created_by = $this->modified_by = Yii::app()->user->id;
            //$this->salt = $this->generateSalt();
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
            $this->modified = date('Y-m-d H:i:s');
            $this->modified_by = Yii::app()->user->id;
            //if (empty($this->password)) unset($this->password);
        }

        return parent::beforeValidate();
    }

    /*
    * Keep tracking the changing of the domain touched status.
    */
    protected function beforeSave(){
        ##!!Please be care for!!!, we couldn't inactive domain on the live page !!!
        ##So we can always put status = 1 here!!!, cause we set lots of domain status = 0 manully!!!, we need active these domains
        $this->status = 1;


        if ($this->isNewRecord) {
            //do nothing for now;
        } else {
            //echo $this->_oldAttributes["touched_status"];
            //$_domodel = self::findByPk($this->id);
            if ($this->_oldAttributes["id"] == $this->id && 
                $this->_oldAttributes["touched_status"] != $this->touched_status) {

                /*
                If we couldn't find out the record on OutreachTracking table,
                that means the record may be changed by cronjob,Such as AutoUpdateStatus cronjob
                */
                $dsmdl = new OutreachTracking;
                $dsmdl->setIsNewRecord(true);
                $dsmdl->id=NULL;
                $dsmdl->domain = $this->domain;
                $dsmdl->domain_id = $this->id;
                $dsmdl->before_value = $this->_oldAttributes["touched_status"];
                $dsmdl->after_value = $this->touched_status;
                $dsmdl->save();
            }
        }

        return parent::beforeSave();
    }

    /**
     * Save domain info into the tbl.domain_summary also at the same time.
     * 
     */
    protected function afterSave(){
        //placeholder here!
        $sumodel = new Summary;
        $sudomain = $sumodel->find('domain_id=:domain_id',array(':domain_id'=>$this->id));
        if ($sudomain) {
            /*
            saveAttributes(): Saves a selected list of attributes. Unlike save, this method only saves the specified attributes of an existing row dataset and does NOT call either beforeSave or afterSave.Also note that this method does neither attribute filtering nor validation. So do not use this method with untrusted data (such as user posted data). 
            */
            // update the data;
            $sumodel = $sudomain;
            $sumodel->setIsNewRecord(false);
            $sumodel->setScenario('update');
            if (isset($this->googlepr)) $sumodel->googlepr = $this->googlepr;
            if (isset($this->onlinesince)) $sumodel->onlinesince = $this->onlinesince;
            if (isset($this->linkingdomains)) $sumodel->linkingdomains = $this->linkingdomains;
            if (isset($this->inboundlinks)) $sumodel->inboundlinks = $this->inboundlinks;
            if (isset($this->indexedurls)) $sumodel->indexedurls = $this->indexedurls;
            if (isset($this->alexarank)) $sumodel->alexarank = $this->alexarank;
            if (isset($this->mozrank) && !empty($this->mozrank)) $sumodel->mozrank = $this->mozrank;
            if (isset($this->mozauthority) && !empty($this->mozauthority)) $sumodel->mozauthority = $this->mozauthority;
            //if (isset($this->acrank)) $sumodel->acrank = $this->acrank;
        } else {
            // insert new data;
            $sumodel->setIsNewRecord(true);
            $sumodel->attributes = $this->attributes;
            $sumodel->id=NULL;
            $sumodel->domain_id=$this->id;

            if (isset($this->mozrank)) $sumodel->mozrank = $this->mozrank;
            if (isset($this->mozauthority)) $sumodel->mozauthority = $this->mozauthority;
        }

        $clmodel = Crawler::model()->findByAttributes(array('domain_id' => $this->id));
        if (!$clmodel) {
            //insert a new record into tbl.lkm_domain_craler
            $clmodel = new Crawler;
            $clmodel->setIsNewRecord(true);
            $clmodel->id=NULL;
            $clmodel->domain_id=$this->id;
            $clmodel->domain=$this->domain;
            $clmodel->save();
        }

        if ($this->isNewRecord) {
            $opmodel = new DomainOnpage;
            $opmodel->setIsNewRecord(true);
            $opmodel->id=NULL;
            $opmodel->domain_id=$this->id;
            $opmodel->domain=$this->domain;
            $opmodel->save();
        }

        //#####if ($this->triggerInventorySave && in_array($this->touched_status, array(6,20)) 
        //#### && ($this->isNewRecord || $this->_oldAttributes["touched_status"] != $this->touched_status)) {...}
        if ($this->triggerInventorySave && in_array($this->touched_status, array(6,20))) {
            $ivtmdl = new Inventory;
            $ivtmodel = $ivtmdl->findByAttributes(array('domain_id' => $this->id));

            if ($ivtmodel) {
                if (isset($this->_oldAttributes["touched_status"]) && $ivtmodel->status == 1
                 && in_array($this->_oldAttributes["touched_status"], array(6,20))) {//updated by leo @2015-12-03
                 //&& in_array($this->_oldAttributes["touched_status"], array(20))) {
                    //do nothing;
                } else {
                    $ivtmodel->setIsNewRecord(false);
                    $ivtmodel->setScenario('update');

                    if ($this->touched_status == 20) {
                        $ivtmodel->ispublished = 1;
                        $ivtmodel->last_published = date('Y-m-d H:i:s');
                    } else {
                        $ivtmodel->ispublished = 0;
                    }
                    //###prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!
                    $ivtmodel->triggerDomainSave = false;
                    $ivtmodel->status = 1;
                    unset($ivtmodel->modified, $ivtmodel->modified_by);
                    $ivtmodel->save();//we need do a transaction here.
                }
            } else {
                $ivtmodel = $ivtmdl;
                $ivtmodel->setIsNewRecord(true);
                $ivtmodel->attributes = $this->attributes;
                $ivtmodel->id=NULL;
                $umodel = User::model()->findByPk(Yii::app()->user->id);
                if (!empty($umodel->channel_id)) {
                    $ivtmodel->user_id = Yii::app()->user->id;
                    $ivtmodel->acquired_channel_id = $umodel->channel_id;
                } else {
                    $ivtmodel->user_id = 2;//2 is reserved for outreach
                    $ivtmodel->acquired_channel_id = 5;
                }
                $ivtmodel->acquireddate = date('Y-m-d H:i:s');
                $ivtmodel->domain_id = $this->id;
                if ($this->category) {
                    $_tmps = explode("|", $this->category);
                    array_pop($_tmps);
                    array_shift($_tmps);
                    $ivtmodel->category = $_tmps;
                }

                if ($this->touched_status == 20) {
                    $ivtmodel->ispublished = 1;
                    $ivtmodel->last_published = date('Y-m-d H:i:s');
                } else {
                    $ivtmodel->ispublished = 0;//2016-02-17
                }

                $ivtmodel->status = 1;
                //###prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!
                $ivtmodel->triggerDomainSave = false;
                unset($ivtmodel->modified, $ivtmodel->modified_by);
                $ivtmodel->save();//we need do a transaction here.
            }
        }

        if (!$this->isNewRecord && $this->triggerInventorySave && $this->_oldAttributes["category"] != $this->category) {
            $ivtmdl = new Inventory;
            $ivtmodel = $ivtmdl->findByAttributes(array('domain_id' => $this->id));
            if ($ivtmodel) {
                $ivtmodel->setIsNewRecord(false);
                $ivtmodel->setScenario('update');

                $ivtmodel->status = 1;
                $ivtmodel->category = $this->category;
                //###prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!
                $ivtmodel->triggerDomainSave = false;
                unset($ivtmodel->modified, $ivtmodel->modified_by);
                $ivtmodel->save();//we need do a transaction here.
            }
        }

		//#####################################2015/12/18############################################//
		if ($this->status == 1 && !in_array($this->touched_status, array(6,20))) {
            $ivtmdl = new Inventory;
            $ivtmodel = $ivtmdl->findByAttributes(array('domain_id' => $this->id));
            if ($ivtmodel) {
                $ivtmodel->setIsNewRecord(false);
                $ivtmodel->setScenario('update');

                $ivtmodel->status = 0;
                //###prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!
                $ivtmodel->triggerDomainSave = false;
                unset($ivtmodel->modified, $ivtmodel->modified_by);
                $ivtmodel->save();//we need do a transaction here.
            }
		}
		//#####################################2015/12/18############################################//


        if (!$sumodel->save()) {
            $this->addErrors(array("domain"=>array('Domain: "'.$this->domain.'" may have format issue.')));
            parent::afterSave();
            return false;
        } else {
            parent::afterSave();
            return true;
        }

        //return parent::afterSave();
    }

    public function getSearchCriteria()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->with = array('rsummary');

        //*********************************************//
        if ($this->mozrank || $this->mozauthority || $this->semrushkeywords != "") {
            $criteria->with = array('rsummary');

            $_semrushkws = $this->semrushkeywords;
            if (trim($_semrushkws) != "") {
                if (is_numeric($_semrushkws)) {
                    $semrushkeywords = (int)$_semrushkws;
                    if ($semrushkeywords > 0) {
                        $criteria->compare('rsummary.semrushkeywords', ">0");
                    } elseif ($semrushkeywords < 0) {
                        $criteria->compare('rsummary.semrushkeywords', "<0");
                    } elseif ($semrushkeywords == 0) {
                        $criteria->addCondition('rsummary.semrushkeywords IS NULL', 'AND');
                    }
                } else {
                    if (strpos($_semrushkws, ",") === false) {
                        $criteria->addCondition("rsummary.semrushkeywords LIKE '%".trim($_semrushkws)."%'", 'AND');
                    } else {
                        $_semrusharr = explode(",", $_semrushkws);
                        $_sem_whr = "";
                        foreach ($_semrusharr as $v) {
                            if ($_sem_whr) $_sem_whr .= " OR ";
                            $_sem_whr .= "rsummary.semrushkeywords LIKE '%".trim($v)."%'"; 
                        }
                        $_sem_whr = "(".$_sem_whr.")";
                        $criteria->addCondition($_sem_whr,'AND');
                    }
                }
            }

            //$criteria->addBetweenCondition('rsummary.mozrank',$this->mozrank);
            $criteria->compare('rsummary.mozrank',$this->mozrank);
            $criteria->compare('rsummary.mozauthority',$this->mozauthority);
        }
        //*********************************************//

        if (!empty($this->price)) {
            if ($this->price > 0) {
                $criteria->join = "INNER JOIN {{domain_price}} rprice ON (rprice.domain_id = t.id)";
                $criteria->addCondition("rprice.price IS NOT NULL", 'AND');
            } else {
                $criteria->join = "LEFT OUTER JOIN {{domain_price}} rprice ON (rprice.domain_id = t.id)";
                $criteria->addCondition("rprice.price IS NULL", 'AND');
            }
        }


        if ($this->category) {
            if (is_array($this->category)) {
                $_cat_whr = "";
                foreach ($this->category as $v) {
                    //$criteria->addCondition("t.category LIKE '%|".$v."|%'",'OR');
                    if ($_cat_whr) $_cat_whr .= " OR ";
                    //$_cat_whr .= "t.category LIKE '%|".$v."|%'";
                    if ($v == -1) {
                        $_cat_whr .= "(t.category IS NULL OR t.category='')";
                    } else {
                        $_cat_whr .= "t.category LIKE '%|".$v."|%'";
                    }
                }
                $_cat_whr = "(".$_cat_whr.")";
                $criteria->addCondition($_cat_whr,'AND');
            } else {
                //!!##Please Pay attention to here "OR" / "AND"
                if ($this->category == -1) {
                    $criteria->addCondition("(t.category IS NULL OR t.category='')",'AND');
                } else {
                    $criteria->addCondition("t.category LIKE '%|".$this->category."|%'",'AND');
                }
            }
        }

        if (!empty($_GET["Domain"]["excludecategory"])) {
            $excludecategory = $_GET["Domain"]["excludecategory"];
            if (is_array($excludecategory)) {
                $_cat_whr = "";
                foreach ($excludecategory as $v) {
                    if ($_cat_whr) $_cat_whr .= " AND ";
                    $_cat_whr .= "t.category NOT LIKE '%|".$v."|%'"; 
                }
                $_cat_whr = "(".$_cat_whr.") OR (t.category IS NULL) OR (t.category='')";
                $criteria->addCondition($_cat_whr,'AND');
            } else {
                //!!##Please Pay attention to here "OR" / "AND"
                $criteria->addCondition("(t.category NOT LIKE '%|".$excludecategory."|%') OR (t.category IS NULL) OR (t.category='')",'AND');
            }
        }

        if ($this->awis_category) {
            if (is_array($this->awis_category)) {
                /*
                foreach ($this->awis_category as $v) {
                    $criteria->addCondition("t.awis_category LIKE '%|".$v."|%'",'OR');
                }
                */
                $_cat_whr = "";
                foreach ($this->awis_category as $v) {
                    //$criteria->addCondition("t.awis_category LIKE '%|".$v."|%'",'OR');
                    if ($_cat_whr) $_cat_whr .= " OR ";
                    $_cat_whr .= "t.awis_category LIKE '%|".$v."|%'"; 
                }
                $_cat_whr = "(".$_cat_whr.")";
                $criteria->addCondition($_cat_whr,'AND');
            } else {
                $criteria->addCondition("t.awis_category LIKE '%|".$this->awis_category."|%'",'AND');
            }
        }

        if ($this->technorati_category) {
            if (is_array($this->technorati_category)) {
                $_cat_whr = "";
                foreach ($this->technorati_category as $v) {
                    //$criteria->addCondition("t.technorati_category LIKE '%|".$v."|%'",'OR');
                    if ($_cat_whr) $_cat_whr .= " OR ";
                    $_cat_whr .= "t.technorati_category LIKE '%|".$v."|%'"; 
                }
                $_cat_whr = "(".$_cat_whr.")";
                $criteria->addCondition($_cat_whr,'AND');
            } else {
                $criteria->addCondition("t.technorati_category LIKE '%|".$this->technorati_category."|%'",'AND');
            }
        }

        if ($this->alexarank) $this->alexarank = str_replace(",", "", $this->alexarank);

        if ($this->host_country) {
            $_host_country = trim($this->host_country);
            if (strpos($_host_country, ",") !== false) {
                $_host_country = preg_replace("/,\s*/", ",", $_host_country);
                $_host_country = explode(",", $_host_country);
            }
            $criteria->compare('t.host_country',$_host_country,true);
        }

        if ($this->domain) {
            if (strpos($this->domain, ",") !== false) {
                $_domain_str = explode(",", $this->domain);
                $_domain_whr = "";
                foreach ($_domain_str as $v) {
                    if (!empty($v)) {
                        if ($_domain_whr) $_domain_whr .= " OR ";
                        $_domain_whr .= "t.domain LIKE '%".trim($v)."%'";
                    }
                }
                if ($_domain_whr) {
                    $_domain_whr = "(".$_domain_whr.")";
                    $criteria->addCondition($_domain_whr,'AND');
                }
            } else {
                Yii::import('application.vendors.*');
                $this->domain = SeoUtils::getDomain($this->domain);
                $criteria->compare('t.domain',$this->domain,true);
            }
        }

        if ($this->rootdomain) {
            if (strpos($this->rootdomain, ",") !== false) {
                $_domain_str = explode(",", $this->rootdomain);
                $_domain_whr = "";
                foreach ($_domain_str as $v) {
                    if (!empty($v)) {
                        if ($_domain_whr) $_domain_whr .= " OR ";
                        $_domain_whr .= "t.rootdomain LIKE '%".$v."%'";
                    }
                }
                if ($_domain_whr) {
                    $_domain_whr = "(".$_domain_whr.")";
                    $criteria->addCondition($_domain_whr,'AND');
                }
            } else {
                $criteria->compare('t.rootdomain',$this->rootdomain,true);
            }
        }

        if ($this->rootdomain) {
            $_rootdomain_str = $this->rootdomain;
            if (strpos($_rootdomain_str, ",") !== false) $_rootdomain_str = explode(",", $_rootdomain_str);
            $criteria->compare('t.rootdomain',$_rootdomain_str,true);
        }

        //##### 1/18/2013 added for tier level #####//
        if (isset($this->tierlevel) && (is_numeric($this->tierlevel) || is_array($this->tierlevel))) {

            $_tier = Types::model()->actived()->bytype('tierlevel')
                                        ->findAllByAttributes(array('refid' => $this->tierlevel));
            if ($_tier) {
                $criteria->with = array('rsummary');

                //$_search = array("googlepr","mozrank","alexarank");
                //$_replace = array("rsummary.googlepr","rsummary.mozrank","rsummary.alexarank");

                $_search = array("googlepr","mozrank","alexarank");
                $_replace = array("t.googlepr","rsummary.mozrank","t.alexarank");
                $_outils = "";
                foreach ($_tier as $_tv) {
                    if (!empty($_outils)) $_outils .= ") OR (";
                    $_outils .= str_replace($_search, $_replace, $_tv->outils);
                }
                $_outils = "(".$_outils.")";
                //##$_outils = str_replace($_search, $_replace, $_tier->outils);
                $criteria->addCondition($_outils, 'AND');
                //echo $_tier->outils;
                //var_dump($criteria->with);
            }

        } else {
            $criteria->compare('t.alexarank',$this->alexarank);
            $criteria->compare('t.googlepr',$this->googlepr);
        }
        //##### 1/18/2013 ended #####//

        if (!isset($this->status)) {
            $this->status = 1;
        }
        $criteria->compare('t.id',$this->id);
        $criteria->compare('t.status',$this->status);
        //$criteria->compare('t.domain',$this->domain,true);
        //$criteria->compare('t.rootdomain',$this->rootdomain,true);
        $criteria->compare('t.tld',$this->tld,true);
        $criteria->compare('t.onlinesince',$this->onlinesince);
        $criteria->compare('t.linkingdomains',$this->linkingdomains);
        $criteria->compare('t.inboundlinks',$this->inboundlinks);
        $criteria->compare('t.indexedurls',$this->indexedurls);
        $criteria->compare('t.ip',$this->ip,true);
        $criteria->compare('t.subnet',$this->subnet,true);
        $criteria->compare('t.title',$this->title,true);
        $criteria->compare('t.owner',$this->owner,true);
        $criteria->compare('t.email',$this->email,true);
        $criteria->compare('t.telephone',$this->telephone,true);
        $criteria->compare('t.country',$this->country,true);
        //#####$criteria->compare('t.host_country',$this->host_country,true);
        $criteria->compare('t.host_city',$this->host_city,true);
        $criteria->compare('t.state',$this->state,true);
        $criteria->compare('t.city',$this->city,true);
        $criteria->compare('t.zip',$this->zip,true);
        $criteria->compare('t.street',$this->street,true);
        $criteria->compare('t.stype',$this->stype);
        $criteria->compare('t.otype',$this->otype);
        $criteria->compare('t.touched_status',$this->touched_status);
        if ($this->discovery_mailer > 0) {
            //$criteria->compare('t.discovery_mailer',$this->discovery_mailer);
            $criteria->compare('t.discovery_mailer', ">0");
        } else {
            $criteria->compare('t.discovery_mailer',$this->discovery_mailer);
        }
        $criteria->compare('t.touched',$this->touched,true);
        $criteria->compare('t.touched_by',$this->touched_by);
        $criteria->compare('t.created',$this->created,true);
        $criteria->compare('t.created_by',$this->created_by);
        $criteria->compare('t.modified',$this->modified,true);
        $criteria->compare('t.modified_by',$this->modified_by);
        $criteria->compare('t.owner2',$this->owner2,true);
        $criteria->compare('t.primary_email2',$this->primary_email2,true);

        $criteria->compare('t.meta_keywords',$this->meta_keywords,true);
        $criteria->compare('t.meta_description',$this->meta_description,true);

        $criteria->compare('t.spa_facebook_username',$this->spa_facebook_username,true);
        $criteria->compare('t.spa_twitter_username',$this->spa_twitter_username,true);

        //$criteria->compare('technorati_category',$this->technorati_category);
        //$criteria->compare('technorati_category_str',$this->technorati_category_str,true);

        return $criteria;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = $this->getSearchCriteria();
        $sort = new CSort();
        $sort->attributes = array(
            '__mozrank'=>array(
                'asc'=>'rsummary.mozrank ASC, t.id ASC',
                'desc'=>'rsummary.mozrank DESC, t.id ASC',
            ),
            /*
            'rsummary.mozauthority'=>array(
                'asc'=>'rsummary.mozauthority ASC',
                'desc'=>'rsummary.mozauthority DESC',
            ),
            'semrushkeywords'=>array(
                'asc'=>'rsummary.semrushkeywords ASC, t.id ASC',
                'desc'=>'rsummary.semrushkeywords DESC, t.id ASC',
            ),
            */
            '*', // add all of the other columns as sortable
        );

        $speedUpByIn = false;
        if (isset($_REQUEST["sort"])) {
             $_currsort = trim(strtolower($_REQUEST["sort"]));
             if ($_currsort == "semrushkeywords" || $_currsort == "mozrank") {
                 $speedUpByIn = true;
             }
        }

        //###$sort->applyOrder($criteria);
        return new CActiveDataProvider($this, array(
            'sort'=>$sort,
            'criteria'=>$criteria,
            'speedUpByIn'=>$speedUpByIn,
        ));
    }
}