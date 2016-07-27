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

	/**
	 * tbl.domain.stype means the site type, this one will used in the upload way.
	 */
    public $stype;

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
			array('link_on_homepage, status, created_by, modified_by, user_id', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('category, accept_tasktype, channel_id', 'length', 'max'=>500),
			array('category_str, accept_tasktype_str, channel_str', 'length', 'max'=>1000),
			array('ext_backend_acct, notes, created, modified, stype', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, domain_id, user_id, category, category_str, accept_tasktype, accept_tasktype_str, channel_id, channel_str, ext_backend_acct, link_on_homepage, notes, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
            'rlink' => array(self::HAS_MANY, 'Link', 'inventory_id'),
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
        return array(
            'byuser'=>array(
                'condition'=>'user_id='.Yii::app()->user->id,
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
			'ext_backend_acct' => 'Ext Backend Acct',
			'link_on_homepage' => 'HP',//Link On Homepage
			'notes' => 'Notes',
			'status' => 'Active',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
		);
	}

    protected function beforeSave(){
        //placeholder here!
        $domodel = new Domain;
        $domain = $domodel->find('domain=:domain',array(':domain'=>$this->domain));

        if ($domain) {
            $this->domain_id = $domain->id;
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
            if ($this->category) {
                $domodel->category = $this->category;
                $domodel->category_str = $this->category_str;
            }
            $tld = array_pop(explode(".", $this->domain));
            $domodel->tld=$tld;
            $domodel->touched_status=6;
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
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

        if ($this->category) {
            $_condition = "(0 ";
            foreach ($this->category as $v) {
                //$criteria->addCondition("t.category LIKE '%|".$v."|%'",'OR');
                $_condition .= "OR t.category LIKE '%|".$v."|%'";
            }
            $_condition .= ")";
		    //$criteria->compare('category',$this->category,true);
            $criteria->addCondition($_condition);//add one more ()
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

            //if ($islinked && $domain) {
            if ( (in_array($islinked, array(1,2)) && $domain)
              || ($islinked == 3) ) {
                $criteria->with = array('rlink'=>array('together'=>true));
                if ($islinked == 1) {
                    //$criteria->compare('rlink.targetdomain', "=".$domain, true);
                    $criteria->addCondition("rlink.targetdomain = '{$domain}'");;
                } elseif($islinked == 2) {
                    $criteria->addCondition("rlink.targetdomain != '{$domain}'");;
                } else {
                    $criteria->addCondition("rlink.targetdomain IS NULL");;
                }
            } else {
                $criteria->compare('t.domain',$this->domain,true);
            }
        }

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.user_id',$this->user_id);
		//###$criteria->compare('t.domain',$this->domain,true);
		$criteria->compare('t.domain_id',$this->domain_id);
		$criteria->compare('t.category_str',$this->category_str,true);
		$criteria->compare('t.channel_str',$this->channel_str,true);
		$criteria->compare('t.ext_backend_acct',$this->ext_backend_acct,true);
		$criteria->compare('t.link_on_homepage',$this->link_on_homepage);
		$criteria->compare('t.notes',$this->notes,true);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);


        $sort = new CSort();
        $sort->attributes = array(
            'rdomain.stype'=>array(
                'asc'=>'rdomain.stype ASC',
                'desc'=>'rdomain.stype DESC',
            ),
            'rdomain.googlepr'=>array(
                'asc'=>'rdomain.googlepr ASC',
                'desc'=>'rdomain.googlepr DESC',
            ),
            'rdomain.alexarank'=>array(
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
            'rdomain.rsummary.mozrank'=>array(
                'asc'=>'rsummary.mozrank ASC',
                'desc'=>'rsummary.mozrank DESC',
            ),
            'rdomain.rsummary.mozauthority'=>array(
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