<?php

/**
 * This is the model class for table "{{competitor_backdomain}}".
 *
 * The followings are the available columns in table '{{competitor_backdomain}}':
 * @property integer $id
 * @property integer $competitor_id
 * @property integer $domain_id
 * @property integer $fresh_called
 * @property integer $historic_called
 * It Is The CompetitorBackdomain Class Actually!
 */
class Discovery extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Discovery the static model class
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
		return '{{competitor_backdomain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('competitor_id, domain_id', 'required'),
			array('competitor_id, domain_id, fresh_called, historic_called', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, competitor_id, domain_id, hubcount, fresh_called, historic_called', 'safe', 'on'=>'search'),
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
            'rcompetitor' => array(self::BELONGS_TO, 'Competitor', 'competitor_id'),
            //'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id', 'with'=>array('rbacklink'=>array('googlepr','acrank ','anchortext'))),
            //下面也可以直接使用'with'=>'rbacklink'
            //'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id', 'with'=>'rbacklink'),
            'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            //'rbacklink' => array(self::HAS_MANY, 'Backlink', 'domain_id'),
            //'rdomain' => array(self::BELONGS_TO, 'Domain', '{{client_domain_competitor}}(domain_id, competitor_id)'),
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
			'competitor_id' => 'Competitor',
			'domain_id' => 'Domain',
			'fresh_called' => 'Fresh Called',
			'historic_called' => 'Historic Called',
		);
	}

    /*
    public $age = -1;
    public $alexarank = -1;
    public $pagerank = -1;
    */

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
        $criteria=new CDbCriteria;
        $cmpids = $this->competitor_id;

		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
        $yiireq = Yii::app()->request;
        // $yiireq->getQuery('client_domain_id');// this one is totally the same with $_GET['client_domain_id'];
        $client_domain_id = $yiireq->getQuery('client_domain_id');

        /*
        if (empty($client_domain_id) && empty($cmpids)) {
            //we can put the lasted one competitor as the default search parameters when we didn't;
            $lastcpt = Yii::app()->db->createCommand()
                ->select("domain_id, competitor_id, fresh_called, historic_called")
                ->from('{{client_domain_competitor}}')
                //->join('{{client_domain}} cd ', 'cd.id=cdc.domain_id')
                ->where(array("OR", "fresh_called > 0", "historic_called > 0"))->order('id DESC')->limit(1)
                ->queryRow();
            if ($lastcpt) {
                $cmpids = $lastcpt['competitor_id'];
                if ($lastcpt['fresh_called'] > 0) {
                    $this->fresh_called = $lastcpt['fresh_called'];
                } else {
                    $this->historic_called = $lastcpt['historic_called'];
                }
            }
        }
        */

        $clt = array();
        if ($client_domain_id) $clt = ClientDomain::model()->findByPk($client_domain_id);
        if ($clt) {
            $client_id = $clt->client_id;
            //$client_domain_id = $clt->id;
            $use_historic_index = $clt->use_historic_index;
            if ($this->competitor_id) {
                $where = array('and', "competitor_id=".(int)$this->competitor_id, "domain_id={$client_domain_id}");
            } else {
                $where = "domain_id={$client_domain_id}";
            }
            if ($use_historic_index == 0) {
                $where = array("and", $where, "fresh_called > 0");
            } else {
                $where = array("and", $where, "historic_called > 0");
            }
            /*
            if ($use_historic_index == 0) {
                if (is_array($where)) {
                    array_push($where, "fresh_called > 0");
                } else {
                    $where .= "AND fresh_called > 0";
                }
            } else {
            }
            */

            $cpt = Yii::app()->db->createCommand()
                ->select("domain_id, competitor_id, fresh_called, historic_called")
                ->from('{{client_domain_competitor}}')
                ->where($where)
                ->queryAll();
                //->where(array('and', "competitor_id=:competitor_id", "domain_id={$client_domain_id}"), 
                //                     array(':competitor_id'=>$this->competitor_id))
                //->queryRow();
            //print_r($cpt);
            //die();
            /*
            if ($use_historic_index == 0 && $cpt['fresh_called'] > 0) {
                $this->fresh_called = $cpt['fresh_called'];
            } elseif ($use_historic_index && $cpt['historic_called'] > 0) {
                $this->historic_called = $cpt['historic_called'];
            } else {}
            */
            if ($cpt) {
                //reset $cmpids to array()
                $cmpids = array();
                $fcallarr = array();
                $hcallarr = array();
                foreach($cpt as $kc => $vc) {
                    array_push($cmpids, $vc["competitor_id"]);
                    if ($vc["fresh_called"] > 0) $fcallarr = array_merge($fcallarr, array($vc["fresh_called"]));
                    if ($vc["historic_called"] > 0) $hcallarr = array_merge($hcallarr, array($vc["historic_called"]));
                }

                /*
                // 3 month later we may need open this feature.
                if ($use_historic_index == 0) {
                    $criteria->compare('t.fresh_called',$fcallarr);
                } else {
                    $criteria->compare('t.historic_called',$hcallarr);
                }
                */
            }
        }
        //print_r($cmpids);

        if (empty($cmpids)) {
            //return an null CActiveDataProvider result
            $criteria->compare('t.id', "-1");
            return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
            ));
        }

        /*
        如果在要使用跟数据库表字段的引用方式如$this->age,但是在表中{{competitor_backdomain}}却没有这个age字段。
        那么你就要按如下方式使用：1. 先在model类中声明一个对应的public $age, 
            2. 然后在rules方法中声明age为safe在'on'=>'search'。如array('..., domain_id, age, ...,...', 'safe', 'on'=>'search'),
            3. 以下我为了方便，直接引用了$_GET,并没有使用$this->..方式来实现。
        */
        //echo $this->age;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.competitor_id',$cmpids);//the same as addInCondition when the $cmpids is array
		$criteria->compare('t.domain_id',$this->domain_id);
		$criteria->compare('t.hubcount',$yiireq->getQuery('hubcountopr').$this->hubcount);
		$criteria->compare('t.fresh_called',$this->fresh_called);
		$criteria->compare('t.historic_called',$this->historic_called);

        //addSearchCondition()
        $criteria->with = array('rdomain');
        //$criteria->with('rdomain')->join = 'LEFT OUTER JOIN `lkm_competitor_backlink` `rbacklink` ON (`rbacklink`.`domain_id`=`rdomain`.`id`)';

		$criteria->compare('rdomain.googlepr',$yiireq->getQuery('googlepropr').$yiireq->getQuery('googlepr'));
        //############# Not Good! ##########################//
        $acrankopr = $yiireq->getQuery('acrankopr');
        if (in_array($acrankopr, array("=",">",">="))) {
            if ($acrankopr == "=") $acrankopr = ">=";
            $criteria->compare('t.max_acrank', $acrankopr.$yiireq->getQuery('acrank'));
        }

        //echo $_GET['ageopr'];
        //domain age 这个部分的操作符要反转
        if ($_GET['age']) {
            $onlinesince = time() - $yiireq->getQuery('age') * 86400 * 365;//365 days;
            $ageopr = $yiireq->getQuery('ageopr');
            if (stripos($ageopr, "<") !== false) {
                $ageopr = str_replace("<", ">", $ageopr);
            } elseif (stripos($ageopr, ">") !== false) {
                $ageopr = str_replace(">", "<", $ageopr);
            }
		    $criteria->compare('rdomain.onlinesince', $ageopr.$onlinesince);
        }

        $titlemacth = $yiireq->getQuery('titlematch');
        if ($titlemacth) {
            //print_r($_GET);
            /*
            $criteria->join = 'LEFT OUTER JOIN `lkm_competitor_backlink` AS `rbacklink` ON (`rbacklink`.`domain_id`=`t`.`domain_id`)';
            //$criteria->join = 'INNER JOIN `lkm_competitor_backlink` AS `rbacklink` ON (`rbacklink`.`domain_id`=`t`.`domain_id`)';
            $criteria->compare('rbacklink.acrank', $yiireq->getQuery('acrankopr').$yiireq->getQuery('acrank'));
            */
            if ($titlemacth == 1) {
                $criteria->compare('rbacklink.anchortext', $yiireq->getQuery('anchortext'), true);
            } elseif ($titlemacth == 2) {
                if ($yiireq->getQuery('anchortext')) {
                    $anchortext = explode(" ", $yiireq->getQuery('anchortext'));

                    $condition = "(rbacklink.anchortext LIKE '%".implode("%') OR (rbacklink.anchortext LIKE '%",$anchortext)."%')";
                    $criteria->addCondition($condition);
                }
            } elseif ($titlemacth == 3) {
                $criteria->compare('rbacklink.anchortext', "<>".$yiireq->getQuery('anchortext'), true);
            } else {}
        }

        $criteria->group = 't.id';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
    public function ss() {
        $yiireq = Yii::app()->request;
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.competitor_id',$this->competitor_id);
		$criteria->compare('t.domain_id',$this->domain_id);
		$criteria->compare('t.hubcount',$yiireq->getQuery('hubcountopr').$this->hubcount);
		$criteria->compare('t.fresh_called',$this->fresh_called);
		$criteria->compare('t.historic_called',$this->historic_called);
        
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
    }
    */
}