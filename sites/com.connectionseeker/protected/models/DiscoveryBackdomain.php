<?php

/**
 * This is the model class for table "{{discovery_backdomain}}".
 *
 * The followings are the available columns in table '{{discovery_backdomain}}':
 * @property string $id
 * @property integer $competitor_id
 * @property integer $discovery_id
 * @property string $domain_id
 * @property string $domain
 * @property integer $hubcount
 * @property integer $max_acrank
 * @property integer $status
 * @property string $fresh_called
 * @property string $historic_called
 */
class DiscoveryBackdomain extends CActiveRecord
{
    public $autorule = null;

	/**
	 * Returns the static model of the specified AR class.
	 * @return DiscoveryBackdomain the static model class
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
		return '{{discovery_backdomain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('competitor_id', 'required'),
			array('competitor_id, discovery_id, mailer_id, hubcount, max_acrank, status', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			array('fresh_called, historic_called, lastcrawled', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, competitor_id, discovery_id, domain_id, domain, hubcount, max_acrank, status, fresh_called, historic_called, mailer_id, lastcrawled', 'safe', 'on'=>'search'),
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
			'rdiscovery' => array(self::BELONGS_TO, 'ClientDiscovery', 'discovery_id'),
			'rdcvdomain' => array(self::BELONGS_TO, 'DiscoveryDomain', 'competitor_id'),
            'rsummary' => array(self::BELONGS_TO, 'Summary', array('domain_id'=>'domain_id')),//###added 4/17/2014
            'rdomainonpage' => array(self::BELONGS_TO, 'DomainOnpage', array('domain_id'=>'domain_id')),//###added 4/25/2014
            'rblforauto' => array(self::HAS_ONE, 'Blacklistforauto', array('domain_id'=>'domain_id')),//###added 9/15/2014
		);
	}


    public function scopes()
    {
        return array(
            'sendable'=>array(
                //'condition'=>'t.status=0',//the same as status=1
                'condition'=>'t.status=0',//the same as status=1
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
			'competitor_id' => 'Competitor',
			'discovery_id' => 'Discovery',
			'mailer_id' => 'Mailer',
			'domain_id' => 'Domain',
			'domain' => 'Domain',
			'hubcount' => 'Hubcount',
			'max_acrank' => 'Max Acrank',
			'status' => 'Status',
			'lastcrawled' => 'Last Crawled',
			'fresh_called' => 'Fresh Called',
			'historic_called' => 'Historic Called',
		);
	}

    public function getRelationCriteria()
    {
        $criteria=new CDbCriteria(array('with'=>array('rdomain.rsummary','rdomain.ronpage')));

        $autorule = $this->autorule;
        if (!empty($autorule->touched_status)) $criteria->compare('rdomain.touched_status',$autorule->touched_status);
        if (!empty($autorule->site_type)) $criteria->compare('rdomain.stype',$autorule->site_type);
        if (!empty($autorule->alexarank)) $criteria->compare('rdomain.alexarank',$autorule->alexarank);

        //*********************************************//
        if (!empty($autorule->semrushkeywords)) {
            $_semrushkws = $autorule->semrushkeywords;
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
        }

        if (!empty($autorule->host_country)) {
            $host_country = trim($autorule->host_country);
            $host_country = strtoupper($host_country);
            $host_country = str_replace(" ", "", $host_country);
            $host_country = explode(",", $host_country);
            $criteria->compare('rdomain.host_country',$host_country);
        }
        //*********************************************//
        $criteria->addCondition("( (rdomain.primary_email IS NOT NULL AND rdomain.primary_email != '') OR (rdomain.primary_email2 IS NOT NULL AND rdomain.primary_email2 != '') OR (ronpage.contactemail IS NOT NULL AND ronpage.contactemail != '' AND ronpage.contactemail != '0') )",'AND');
        if (!empty($autorule->has_owner) && $autorule->has_owner == 1) {
            $criteria->addCondition("(rdomain.owner IS NOT NULL OR rdomain.owner != '')",'AND');
        }
        
        if (!empty($autorule->mozauthority)) $criteria->compare('rsummary.mozauthority',$autorule->mozauthority);
        if (!empty($autorule->mozrank)) $criteria->compare('rsummary.mozrank',$autorule->mozrank);

        return $criteria;
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

        if ($this->autorule) {
            $criteria = $this->getRelationCriteria();
        } else {
		    $criteria=new CDbCriteria;
        }

		$criteria->compare('t.id',$this->id,true);
		$criteria->compare('t.competitor_id',$this->competitor_id);
		$criteria->compare('t.discovery_id',$this->discovery_id);
		$criteria->compare('t.mailer_id',$this->mailer_id);
		$criteria->compare('t.domain_id',$this->domain_id,true);
		$criteria->compare('t.domain',$this->domain,true);
		$criteria->compare('t.hubcount',$this->hubcount);
		$criteria->compare('t.max_acrank',$this->max_acrank);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.lastcrawled',$this->lastcrawled,true);
		$criteria->compare('t.fresh_called',$this->fresh_called,true);
		$criteria->compare('t.historic_called',$this->historic_called,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}