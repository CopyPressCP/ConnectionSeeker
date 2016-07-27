<?php

/**
 * This is the model class for table "{{automation_setting}}".
 *
 * The followings are the available columns in table '{{automation_setting}}':
 * @property integer $id
 * @property string $name
 * @property string $category
 * @property string $touched_status
 * @property string $mailers
 * @property string $current_domain_id
 * @property string $total_sent
 * @property string $total
 * @property integer $frequency
 * @property string $sortby
 * @property string $alexarank
 * @property string $mozauthority
 * @property integer $semrushkeywords
 * @property string $domain_queue
 * @property string $days
 * @property string $time_start
 * @property string $time_end
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Automation extends CActiveRecord
{
    public $mailer;
    public $template;
    //###public $frequency;
    //###public $latest_senttime;
    public $current_template_id;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Automation the static model class
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
		return '{{automation_setting}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_by', 'required'),
			array('semrushkeywords, has_owner, status, created_by, modified_by, frequency, current_mailer_id', 'numerical', 'integerOnly'=>true),
			array('host_country', 'length', 'max'=>64),
			array('name, alexarank, mozauthority', 'length', 'max'=>255),
			array('category, touched_status, stype, otype', 'length', 'max'=>2000),
			array('current_domain_id, total_sent, total', 'length', 'max'=>20),
			array('sortby', 'length', 'max'=>500),
			array('days', 'length', 'max'=>1000),
			array('mailers, domain_queue, time_start, time_end, created, modified, latest_senttime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, category, touched_status, mailers, current_domain_id, current_mailer_id, total_sent, total, sortby, alexarank, mozauthority, semrushkeywords, domain_queue, days, time_start, time_end, status, created, created_by, modified, modified_by, frequency, latest_senttime, has_owner, host_country, stype, otype', 'safe', 'on'=>'search'),
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
			'rcreatedby' => array(self::BELONGS_TO, 'User', 'created_by'),
		);
	}

	/**
	 * @return array named scopes.
     * Usage: $users = Automation::model()->actived()->findAll(); 
	 */
    public function scopes()
    {
        return array(
            'actived'=>array(
                'condition'=>'status=1',
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
			'name' => 'Name',
			'category' => 'Category',
			'touched_status' => 'Touched Status',
			'mailers' => 'Mailers',
			'current_domain_id' => 'Current Domain',
			'current_mailer_id' => 'Current Mailer',
			'latest_senttime' => 'Latest Senttime',
			'total_sent' => 'Total Sent',
			'total' => 'Total',
			'frequency' => 'Frequency(Minutes)',
			'sortby' => 'Sortby',
			'alexarank' => 'Alexa Rank',
			'mozauthority' => 'Domain Authority',
			'semrushkeywords' => 'Semrushkeywords',
			'has_owner' => 'Primary Name Must Be Exist',
            'host_country' => 'Host Country',
			'domain_queue' => 'Domain Queue',
			'days' => 'Days',
			'stype' => 'Site Type',
			'otype' => 'Outreach Type',
			'time_start' => 'Time Start',
			'time_end' => 'Time End',
			'status' => 'Status',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
		);
	}


    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        if (!empty($this->category) && (is_array($this->category) || is_numeric($this->category))) {
            if (is_numeric($this->category)) $this->category = array($this->category);
            $this->category = implode("|", array_values($this->category));
        }
        if (!empty($this->days) && (is_array($this->days) || is_numeric($this->days))) {
            if (is_numeric($this->days)) $this->days = array($this->days);
            $this->days = implode("|", array_values($this->days));
        }
        /*
        if (!empty($this->mailer) && (is_array($this->mailer) || is_numeric($this->mailer))) {
            if (is_numeric($this->mailer)) $this->mailer = array($this->mailer);
            $this->mailer = implode("|", array_values($this->mailer));
        }
        if (!empty($this->template) && (is_array($this->template) || is_numeric($this->template))) {
            if (is_numeric($this->template)) $this->template = array($this->template);
            $this->template = implode("|", array_values($this->template));
        }
        */
        if (!empty($this->touched_status) && (is_array($this->touched_status) || is_numeric($this->touched_status))) {
            if (is_numeric($this->touched_status)) $this->touched_status = array($this->touched_status);
            $this->touched_status = implode("|", array_values($this->touched_status));
        }

        if (!empty($this->stype) && (is_array($this->stype) || is_numeric($this->stype))) {
            if (is_numeric($this->stype)) $this->stype = array($this->stype);
            $this->stype = implode("|", array_values($this->stype));
        }

        if (!empty($this->otype) && (is_array($this->otype) || is_numeric($this->otype))) {
            if (is_numeric($this->otype)) $this->otype = array($this->otype);
            $this->otype = implode("|", array_values($this->otype));
        }

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

    /*
    * Keep tracking the changing of the domain touched status.
    */
    protected function beforeSave(){
        /*
        if ($this->isNewRecord) {
        } else {
            //do nothing for now;
        }
        */

        $this->current_domain_id = 0;

        //##$where = "(t.owner IS NOT NULL OR t.owner != '') AND (t.primary_email IS NOT NULL OR t.primary_email != '')";
        $where = "(t.primary_email IS NOT NULL OR t.primary_email != '')";
        if ($this->has_owner) {
            $where .= " AND (t.owner IS NOT NULL OR t.owner != '')"; 
        }
        if ($this->current_domain_id) {
            /*
            if ($this->sortby) { //if it sort by id desc;
                $where .= " AND (id < '".$this->current_domain_id."')";
            } else { //sort by id asc
                $where .= " AND (id > '".$this->current_domain_id."')";
            }
            */
        }
        if ($this->category) {
            if (!is_array($this->category)) {
                $categories = explode("|", $this->category);
            }
            $__whr = "";
            foreach ($categories as $v) {
                if ($__whr) $__whr .= " OR ";
                $__whr .= "t.category LIKE '%|".$v."|%'"; 
            }
            $where .= " AND (".$__whr.")";
        }

        /*
        if ($this->touched_status) {
            if (!is_array($this->touched_status)) {
                $statuses = explode("|", $this->touched_status);
            }
            $__whr = "";
            foreach ($statuses as $v) {
                if ($__whr) $__whr .= " OR ";
                $__whr .= "t.touched_status = '".$v."'"; 
            }
            $where .= " AND (".$__whr.")";
        }
        */
        if ($this->touched_status) {
            if (!is_array($this->touched_status)) {
                $_status = str_replace("|", ",", $this->touched_status);
            } else {
                $_status = implode(",", $this->touched_status);
            }
            $where .= " AND t.touched_status IN (".$_status.")";
        }

        if ($this->stype) {
            if (!is_array($this->stype)) {
                $_stypes = str_replace("|", ",", $this->stype);
            } else {
                $_stypes = implode(",", $this->stype);
            }
            $where .= " AND t.stype IN (".$_stypes.")";
        }

        if ($this->otype) {
            if (!is_array($this->otype)) {
                $_otypes = str_replace("|", ",", $this->otype);
            } else {
                $_otypes = implode(",", $this->otype);
            }
            $where .= " AND t.otype IN (".$_otypes.")";
        }


        if ($this->alexarank) {
            $alexavalue = $this->alexarank;
            $op = "";
            if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$alexavalue,$matches)) {
                $alexavalue = $matches[2];
                $op = $matches[1];
            }
            if (empty($op)) $op="=";
            if ($alexavalue) $where .= " AND (t.alexarank ".$op." '".$alexavalue."')";
        }

        if ($this->semrushkeywords) {
            if ($this->semrushkeywords > 0) {
                $where .= " AND (rsummary.semrushkeywords>'0')";
            } elseif ($this->semrushkeywords < 0) {
                $where .= " AND (rsummary.semrushkeywords<'0')";
            } else {
                $where .= " AND (rsummary.semrushkeywords IS NULL)";
            }
        }

        if ($this->mozauthority) {
            $mozauthority = $this->mozauthority;
            $op = "";
            if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$mozauthority,$matches)) {
                $mozauthority = $matches[2];
                $op = $matches[1];
            }
            if (empty($op)) $op="=";
            if ($mozauthority) $where .= " AND (rsummary.mozauthority ".$op." '".$mozauthority."')";
        }

        if ($this->semrushkeywords || $this->mozauthority) {
            $this->total = Domain::model()->with("rsummary")->count($where);
        } else {
            $this->total = Domain::model()->count($where);
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

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
        if ($this->category) {
            if (is_array($this->category)) {
                $_cat_whr = "";
                foreach ($this->category as $v) {
                    if ($_cat_whr) $_cat_whr .= " OR ";
                    $_cat_whr .= "CONCAT('|',t.category,'|') LIKE '%|".$v."|%'"; 
                }
                $_cat_whr = "(".$_cat_whr.")";
                $criteria->addCondition($_cat_whr,'AND');
            } else {
                //!!##Please Pay attention to here "OR" / "AND"
                $criteria->addCondition("CONCAT('|',t.category,'|') LIKE '%|".$this->category."|%'",'AND');
            }
        }
		//##$criteria->compare('category',$this->category,true);
		$criteria->compare('touched_status',$this->touched_status,true);
		$criteria->compare('stype',$this->stype,true);
		$criteria->compare('otype',$this->otype,true);
		$criteria->compare('mailers',$this->mailers,true);
		$criteria->compare('current_domain_id',$this->current_domain_id,true);
		$criteria->compare('current_mailer_id',$this->current_mailer_id,true);
		$criteria->compare('total_sent',$this->total_sent,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('frequency',$this->frequency);
		$criteria->compare('sortby',$this->sortby,true);
		$criteria->compare('alexarank',$this->alexarank,true);
		$criteria->compare('mozauthority',$this->mozauthority,true);
		$criteria->compare('semrushkeywords',$this->semrushkeywords);
		$criteria->compare('has_owner',$this->has_owner);
		$criteria->compare('domain_queue',$this->domain_queue,true);
		$criteria->compare('days',$this->days,true);
		$criteria->compare('time_start',$this->time_start,true);
		$criteria->compare('time_end',$this->time_end,true);
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