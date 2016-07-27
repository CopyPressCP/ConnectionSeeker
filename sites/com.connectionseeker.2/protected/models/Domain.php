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
    public $semrushor;

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
                           '6' => 'Site Acquired',
                           '7' => 'Site Denied',
                           '8' => 'Not Interested',
                           '10' => 'Replied',
                           '11' => 'Contact Form',
                           '15' => 'Delete',
                           '16' => 'Ready For Outreach',);


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
			array('googlepr, onlinesince, linkingdomains, inboundlinks, indexedurls, alexarank, touched_by, created_by, modified_by, stype, otype, technorati_category, touched_status', 'numerical', 'integerOnly'=>true),
			array('domain, title, owner, primary_email, telephone, rootdomain', 'length', 'max'=>255),
			array('tld', 'length', 'max'=>10),
			array('ip, subnet', 'length', 'max'=>32),
			array('country, host_country, zip', 'length', 'max'=>64),
			array('state, city, host_city', 'length', 'max'=>128),
			array('category', 'length', 'max'=>500),
			array('category_str, technorati_category_str, email', 'length', 'max'=>1000),
			array('street, stype, otype, touched, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, rootdomain, tld, googlepr, onlinesince, linkingdomains, inboundlinks, indexedurls, alexarank, ip, subnet, title, owner, email, telephone, country, host_country, state, city, zip, street, stype, otype, category, category_str, technorati_category, technorati_category_str, touched_status, touched, touched_by, created, created_by, modified, modified_by, semrushor, mozrank, mozauthority', 'safe', 'on'=>'search'),
		);
	}

    public function scopes()
    {
        return array(
            'touched'=>array(
                'condition'=>'touched_status > 0 AND touched_status != 6',
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
            'rcrawler' => array(self::HAS_ONE, 'Crawler', 'domain_id'),
            'touchedby' => array(self::BELONGS_TO, 'User', 'touched_by'),
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
            'owner' => 'Owner',
            'email' => 'Email',
            'primary_email' => 'Primary Email',
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

            //the following for the lkm_domain_summary
            'mozrank' => 'Domain Moz Rank',
            'mozauthority' => 'Authority',
            'semrushor' => 'SEM',
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
            if ($categories) {
                //$data = CHtml::listData($categories, 'id', 'typename');
                $data = CHtml::listData($categories, 'refid', 'typename');
                if (!empty($data)) $this->category_str = implode(", ", array_values($data));
            }
            $this->category = "|".implode("|", array_values($this->category))."|";
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
            if (isset($this->mozrank)) $sumodel->mozrank = $this->mozrank;
            //if (isset($this->acrank)) $sumodel->acrank = $this->acrank;
        } else {
            // insert new data;
            $sumodel->setIsNewRecord(true);
            $sumodel->attributes = $this->attributes;
            $sumodel->id=NULL;
            $sumodel->domain_id=$this->id;
            //print_r($sumodel->attributes);

            //insert a new record into tbl.lkm_domain_craler
            $clmodel = new Crawler;
            $clmodel->setIsNewRecord(true);
            $clmodel->id=NULL;
            $clmodel->domain_id=$this->id;
            $clmodel->domain=$this->domain;
            $clmodel->save();
        }

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

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        //*********************************************//
        if ($this->mozrank || is_numeric($this->semrushor) || $this->mozauthority) {
            $criteria->with = array('rsummary');
            if (is_numeric($this->semrushor)) {
                $semrushor = (int)$this->semrushor;
                if ($semrushor > 0) {
                    $criteria->compare('rsummary.semrushor', ">0");
                } elseif ($semrushor < 0) {
                    //die($this->semrushor);
                    $criteria->compare('rsummary.semrushor', "<0");
                } elseif ($semrushor == 0) {
                    $criteria->compare('rsummary.semrushor', "=0");
                }
            }

            //$criteria->addBetweenCondition('rsummary.mozrank',$this->mozrank);
            $criteria->compare('rsummary.mozrank',$this->mozrank);
        }
        //*********************************************//

        if ($this->category) {
            if (is_array($this->category)) {
                $_cat_whr = "";
                foreach ($this->category as $v) {
                    //$criteria->addCondition("t.category LIKE '%|".$v."|%'",'OR');
                    if ($_cat_whr) $_cat_whr .= " OR ";
                    $_cat_whr .= "t.category LIKE '%|".$v."|%'"; 
                }
                $_cat_whr = "(".$_cat_whr.")";
                $criteria->addCondition($_cat_whr,'AND');
            } else {
                //!!##Please Pay attention to here "OR" / "AND"
                $criteria->addCondition("t.category LIKE '%|".$this->category."|%'",'AND');
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


        $criteria->compare('t.id',$this->id);
        $criteria->compare('t.domain',$this->domain,true);
        $criteria->compare('t.rootdomain',$this->rootdomain,true);
        $criteria->compare('t.tld',$this->tld,true);
        $criteria->compare('t.googlepr',$this->googlepr);
        $criteria->compare('t.onlinesince',$this->onlinesince);
        $criteria->compare('t.linkingdomains',$this->linkingdomains);
        $criteria->compare('t.inboundlinks',$this->inboundlinks);
        $criteria->compare('t.indexedurls',$this->indexedurls);
        $criteria->compare('t.alexarank',$this->alexarank);
        $criteria->compare('t.ip',$this->ip,true);
        $criteria->compare('t.subnet',$this->subnet,true);
        $criteria->compare('t.title',$this->title,true);
        $criteria->compare('t.owner',$this->owner,true);
        $criteria->compare('t.email',$this->email,true);
        $criteria->compare('t.telephone',$this->telephone,true);
        $criteria->compare('t.country',$this->country,true);
        $criteria->compare('t.host_country',$this->host_country,true);
        $criteria->compare('t.host_city',$this->host_city,true);
        $criteria->compare('t.state',$this->state,true);
        $criteria->compare('t.city',$this->city,true);
        $criteria->compare('t.zip',$this->zip,true);
        $criteria->compare('t.street',$this->street,true);
        $criteria->compare('t.stype',$this->stype);
        $criteria->compare('t.otype',$this->otype);
        $criteria->compare('t.touched_status',$this->touched_status);
        $criteria->compare('t.touched',$this->touched,true);
        $criteria->compare('t.touched_by',$this->touched_by);
        $criteria->compare('t.created',$this->created,true);
        $criteria->compare('t.created_by',$this->created_by);
        $criteria->compare('t.modified',$this->modified,true);
        $criteria->compare('t.modified_by',$this->modified_by);

        //$criteria->compare('technorati_category',$this->technorati_category);
        //$criteria->compare('technorati_category_str',$this->technorati_category_str,true);

        $sort = new CSort();
        $sort->attributes = array(
            'mozrank'=>array(
                'asc'=>'rsummary.mozrank ASC',
                'desc'=>'rsummary.mozrank DESC',
            ),
            /*
            'rsummary.mozauthority'=>array(
                'asc'=>'rsummary.mozauthority ASC',
                'desc'=>'rsummary.mozauthority DESC',
            ),
            */
            'semrushor'=>array(
                'asc'=>'rsummary.semrushor ASC',
                'desc'=>'rsummary.semrushor DESC',
            ),
            '*', // add all of the other columns as sortable
        );

        return new CActiveDataProvider($this, array(
            'sort'=>$sort,
            'criteria'=>$criteria,
        ));
    }
}