<?php

/**
 * This is the model class for table "{{competitor}}".
 *
 * The followings are the available columns in table '{{competitor}}':
 * @property integer $id
 * @property string $domain
 * @property integer $googlepr
 * @property integer $onlinesince
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 * @property integer $last_call_api_time
 */
class Competitor extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Competitor the static model class
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
		return '{{competitor}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('googlepr, onlinesince, created_by, modified_by, fresh_called, historic_called', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, domain_id, googlepr, onlinesince, created, created_by, modified, modified_by, fresh_called, historic_called', 'safe', 'on'=>'search'),
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
            'rdomain' => array(self::MANY_MANY, 'ClientDomain', '{{client_domain_competitor}}(competitor_id, domain_id)'),
            'rtbldomain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
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
			'domain_id' => 'Domain #',
			'googlepr' => 'Googlepr',
			'onlinesince' => 'Onlinesince',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
			'fresh_called' => 'Last Call Fresh Api Time',
			'historic_called' => 'Last Call Historic Api Time',
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
		$criteria->compare('googlepr',$this->googlepr);
		$criteria->compare('onlinesince',$this->onlinesince);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);
		$criteria->compare('fresh_called',$this->fresh_called);
		$criteria->compare('historic_called',$this->historic_called);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}