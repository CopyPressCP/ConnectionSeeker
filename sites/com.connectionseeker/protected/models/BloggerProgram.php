<?php

/**
 * This is the model class for table "{{blogger_program}}".
 *
 * The followings are the available columns in table '{{blogger_program}}':
 * @property integer $id
 * @property string $domain
 * @property string $domain_id
 * @property string $first_name
 * @property string $last_name
 * @property string $mozauthority
 * @property string $category
 * @property string $category_str
 * @property integer $cms_username
 * @property string $cms_username_str
 * @property string $contact_email
 * @property string $per_word_rate
 * @property string $activeprogram
 * @property string $activeprogram_str
 * @property integer $status
 * @property integer $isdelete
 */
class BloggerProgram extends CActiveRecord
{
    public $upfile;
    public $last_published;
    public static $bpstatuses = array(
                           '0' => 'Pilot Assignment',
                           //###'10' => 'Approved (Onboarding)',
                           '10' => 'Onboarding',
                           '15' => 'Onboarded',
                           '20' => 'Active',
                           '30' => 'Inactive',
                           '40' => 'Denied',
                           '50' => 'Failed QA',
                           '60' => 'Do Not Use',);

	/**
	 * Returns the static model of the specified AR class.
	 * @return BloggerProgram the static model class
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
		return '{{blogger_program}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, isdelete, syndication, cms_user_id', 'numerical', 'integerOnly'=>true),
			array('cms_username, domain, contact_email', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('first_name, last_name', 'length', 'max'=>100),
			array('mozauthority, per_word_rate', 'length', 'max'=>9),
			array('category, cms_username, activeprogram', 'length', 'max'=>500),
			array('category_str, activeprogram_str', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, domain_id, first_name, last_name, mozauthority, category, category_str, cms_username, syndication, contact_email, per_word_rate, activeprogram, activeprogram_str, status, isdelete, cms_user_id,last_published', 'safe', 'on'=>'search'),
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
            'rinventory' => array(self::HAS_ONE, 'Inventory', array('domain_id'=>'domain_id')),
            'rprice' => array(self::HAS_MANY, 'BloggerProgramPrice', 'blogger_program_id'),
            'ronenote' => array(self::HAS_ONE, 'BloggerProgramNote', 'blogger_program_id', 'order'=>'ronenote.id DESC'),
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
			'domain_id' => 'Domain',
			'first_name' => 'Contact First Name',
			'last_name' => 'Contact Last Name',
			'mozauthority' => 'DA',
			'category' => 'Category',
			'category_str' => 'Category',
			'cms_username' => 'CMS Username',
			'cms_user_id' => 'CMS User ID',
			'syndication' => 'Syndication',
			'contact_email' => 'Contact Email',
			'per_word_rate' => 'Per Word Rate',
			'activeprogram' => 'Active Program',
			'activeprogram_str' => 'Active Program',
			'status' => 'Status',
			'isdelete' => 'Is Deleted',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        if (!empty($this->category) && (is_array($this->category) || is_numeric($this->category))) {
            if (is_numeric($this->category)) $this->category = array($this->category);
            $categories = Types::model()->actived()->bytype('bloggerprogram')
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
        }

        if (!empty($this->activeprogram) && (is_array($this->activeprogram) || is_numeric($this->activeprogram))) {
            if (is_numeric($this->activeprogram)) $this->activeprogram = array($this->activeprogram);
            $activeprogrames = Types::model()->actived()->bytype('activeprogram')
                                        ->findAllByAttributes(array('refid' => array_values($this->activeprogram)));
            $data = array();
            $this->activeprogram = null;
            if ($activeprogrames) {
                $data = CHtml::listData($activeprogrames, 'refid', 'typename');
                if (!empty($data)) {
                    $this->activeprogram_str = implode(", ", array_values($data));
                    $this->activeprogram = "|".implode("|", array_keys($data))."|";
                }
            }
        }

        /*
        if (!empty($this->cms_username)) {
            $this->cms_username_str = $cms_usernames[$this->cms_username];
        }
        */

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
     * Save domain info into the tbl.domain, tbl.inventory, tbl.domain_summary also at the same time.
     * 
     */
    protected function beforeSave(){
        /*
        Every site we upload or add in the blogger program tab for the very first time, 
        So trigger in the outreach tab as following
        1.Outreach Type should be changed to "Blogger Program"
        2.Status should be changed to "Site Acquired". If its already published, can stay in "Published"
        3.If the domain is off, then turn it on.
        */
        if ($this->isNewRecord) {
            $domodel = new Domain;
            $domain = $domodel->find('domain=:domain',array(':domain'=>$this->domain));

            if ($domain) {
                $this->domain_id = $domain->id;

                $domain->setIsNewRecord(false);
                $domain->setScenario('update');
                $domain->otype = 6;//Set the outreach type as "Blogger Program"
                if ($domain->status == 1) {
                    //if (!in_array($domain->touched_status, array(7,8,15,20,6))) {
                    if (!in_array($domain->touched_status, array(20,6))) {
                        $domain->touched_status=6;
                        $domain->triggerInventorySave = true;
                        //$domain->save();
                    } else {
                        $domain->triggerInventorySave = false;
                    }
                } else {
                    $domain->status=1;
                    $domain->touched_status=6;
                    $domain->triggerInventorySave = true;
                }

                $domain->save();
            } else {
                $domodel->setIsNewRecord(true);
                $domodel->id=NULL;
                $domodel->domain=$this->domain;
                $tld = array_pop(explode(".", $this->domain));
                $domodel->tld=$tld;
                $domodel->touched_status = 6;
                $domodel->otype = 6;//Set the outreach type as "Blogger Program"
                $domodel->triggerInventorySave = true;
                if ($domodel->save()) {
                    $this->domain_id = $domodel->id;
                } else {
                    $this->addErrors(array("domain"=>array('<------ Domain ----> "'.$this->domain.'" may have format issue.')));
                    return false;
                }
            }
        }

        return parent::beforeSave();
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
                    if ($v == -1) {
                        $_condition .= "OR (t.category IS NULL OR t.category='')";
                    } else {
                        $_condition .= "OR t.category LIKE '%|".$v."|%'";
                    }
                }
                $_condition .= ")";
                $criteria->addCondition($_condition);//add one more ()
            } else {
                if ($this->category == -1) {
                    $criteria->addCondition("(t.category IS NULL OR t.category='')",'AND');
                } else {
                    $criteria->compare('category',"|".$this->category."|",true);
                }
            }
        }

        if ($this->activeprogram) {
            if (is_array($this->activeprogram)) {
                $_condition = "(0 ";
                foreach ($this->activeprogram as $v) {
                    if ($v == -1) {
                        $_condition .= "OR (t.activeprogram IS NULL OR t.activeprogram='')";
                    } else {
                        $_condition .= "OR t.activeprogram LIKE '%|".$v."|%'";
                    }
                }
                $_condition .= ")";
                $criteria->addCondition($_condition);//add one more ()
            } else {
                if ($this->activeprogram == -1) {
                    $criteria->addCondition("(t.activeprogram IS NULL OR t.activeprogram='')",'AND');
                } else {
                    $criteria->compare('activeprogram',"|".$this->activeprogram."|",true);
                }
            }
        }

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.domain',$this->domain,true);
		$criteria->compare('t.domain_id',$this->domain_id,true);
		$criteria->compare('t.first_name',$this->first_name,true);
		$criteria->compare('t.last_name',$this->last_name,true);
		$criteria->compare('t.mozauthority',$this->mozauthority,true);
		//$criteria->compare('category',$this->category,true);
		$criteria->compare('t.category_str',$this->category_str,true);
		$criteria->compare('t.syndication',$this->syndication);
		$criteria->compare('t.cms_user_id',$this->cms_user_id);
		$criteria->compare('t.cms_username',$this->cms_username,true);
		$criteria->compare('t.contact_email',$this->contact_email,true);
		$criteria->compare('t.per_word_rate',$this->per_word_rate,true);
		//$criteria->compare('activeprogram',$this->activeprogram,true);
		$criteria->compare('t.activeprogram_str',$this->activeprogram_str,true);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.isdelete',$this->isdelete);

        $criteria->with = array('rinventory');

        if (isset($_GET['BloggerProgram']['last_publishedopr']) && !empty($this->last_published)) {
            if (is_numeric($this->last_published{0})) {
                $this->last_published = $_GET['BloggerProgram']['last_publishedopr'].$this->last_published;
            }
        }
        $criteria->compare('rinventory.last_published',$this->last_published);
        $sort = new CSort();
        $sort->attributes = array(
            'last_published'=>array(
                'asc'=>'rinventory.last_published ASC',
                'desc'=>'rinventory.last_published DESC',
            ),

            '*', // add all of the other columns as sortable
        );

		return new CActiveDataProvider($this, array(
			'sort'=>$sort,
			'criteria'=>$criteria,
		));
	}
}