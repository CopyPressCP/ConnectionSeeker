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
            array('upfile', 'file', 'types'=>'csv, xls', 'on'=>'upload'),
			array('channel_id, link_on_homepage, status, created_by, modified_by, user_id', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('category, accept_tasktype', 'length', 'max'=>500),
			array('category_str, accept_tasktype_str', 'length', 'max'=>1000),
			array('ext_backend_acct, notes, created, modified, stype', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, domain_id, user_id, category, category_str, accept_tasktype, accept_tasktype_str, channel_id, ext_backend_acct, link_on_homepage, notes, status, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
            //$this->setAttribute("domain_id", $domain->id);
            //return true;
        } else {
            $domodel->setIsNewRecord(true);
            $domodel->id=NULL;
            $domodel->domain=$this->domain;
            // $this->stype was used to upload way.
            if ($this->stype) $domodel->stype = $this->stype;
            $tld = array_pop(explode(".", $this->domain));
            $domodel->tld=$tld;
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

        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
            $this->created = $this->modified = date('Y-m-d H:i:s');
            $this->created_by = $this->modified_by = Yii::app()->user->id;
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
            foreach ($this->category as $v) {
                $criteria->addCondition("t.category LIKE '%|".$v."|%'",'OR');
            }
		    //$criteria->compare('category',$this->category,true);
        }

        if ($this->accept_tasktype) {
            foreach ($this->accept_tasktype as $v) {
                $criteria->addCondition("t.accept_tasktype LIKE '%|".$v."|%'",'OR');
            }
		    //$criteria->compare('category',$this->category,true);
        }

        //*********************************************//
        $criteria->with = array('rdomain');
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
		$criteria->compare('t.domain_id',$this->domain_id,true);
		$criteria->compare('t.category_str',$this->category_str,true);
		$criteria->compare('t.channel_id',$this->channel_id);
		$criteria->compare('t.ext_backend_acct',$this->ext_backend_acct,true);
		$criteria->compare('t.link_on_homepage',$this->link_on_homepage);
		$criteria->compare('t.notes',$this->notes,true);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}