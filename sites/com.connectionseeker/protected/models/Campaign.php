<?php

/**
 * This is the model class for table "{{campaign}}".
 *
 * The followings are the available columns in table '{{campaign}}':
 * @property integer $id
 * @property string $name
 * @property string $domain
 * @property integer $client_id
 * @property integer $domain_id
 * @property string $category
 * @property string $category_str
 * @property string $notes
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Campaign extends CActiveRecord
{
    public $percentage;
    public $duty_campaign_ids = array();
    public $upfile;

    public $summary_total = 0;
    public $summary_qa = 0;
    public $summary_inrepair = 0;
    public $summary_approved = 0;
    public $summary_published = 0;
    public $summary_pending = 0;
    public $summary_remaining = 0;

    public $rct_internal_done = null;
    public $rct_percentage_done = null;
    public $bckw_percentage_done = null;
    public $content_percentage_done = null;

    /**
	 * Returns the static model of the specified AR class.
	 * @return Campaign the static model class
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
		return '{{campaign}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, domain_id, domain, name', 'required'),
			array('client_id, domain_id, status, ishidden, is_fixed_anchortext, always_on_cio, allow_duplicate_url, content, created_by, modified_by, owner', 'numerical', 'integerOnly'=>true),
			array('name, domain, approval_type', 'length', 'max'=>255),
			array('category, category_str, notes, created, modified, duedate, styleguide', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, domain, client_id, domain_id, category, category_str, notes, status, ishidden, duedate, created, created_by, modified, modified_by, rct_internal_done, rct_percentage_done, bckw_percentage_done, content_percentage_done, owner, approval_type, is_fixed_anchortext, always_on_cio, content, allow_duplicate_url', 'safe', 'on'=>'search'),
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
            'rclient' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'rcreatedby' => array(self::BELONGS_TO, 'User', 'created_by'),
			'rcampaigntask' => array(self::HAS_ONE, 'CampaignTask', 'campaign_id'),
		);
	}

    public function scopes()
    {
        $umodel = User::model()->findByPk(Yii::app()->user->id);
        $client_id = $umodel->client_id;

        $onduty = array();
        if ($umodel->type == 0) {
            //do nothing;
        } else {
            if ($umodel->duty_campaign_ids) {
                $cmpids = unserialize($umodel->duty_campaign_ids);
                $onduty = array('condition'=>"id IN(".implode(",", $cmpids).")");
            } else {
                $onduty = array('condition'=>"id = -1");
            }
        }

        return array(
            'byclient'=>array(
                'condition'=>"client_id='{$client_id}'",
            ),
            'unhidden'=>array(
                'condition'=>"ishidden='0'",
            ),
            'hidden'=>array(
                'condition'=>"ishidden='1'",
            ),
            'byduty'=>$onduty,
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
			'name' => 'Name',
			'domain' => 'Domain',
			'client_id' => 'Client',
			'domain_id' => 'Domain ID',
			'category' => 'Category',
			'category_str' => 'Categories',
			'notes' => 'Notes',
			'styleguide' => 'Style Guide',
			'approval_type' => 'Approval',
			'status' => 'Active',
			'ishidden' => 'Hidden',
			'is_fixed_anchortext' => 'Allow Anchor Text Change',
			'always_on_cio' => 'Always display on Content IO',
			'allow_duplicate_url' => 'Allow Duplicate URL',
			'content' => 'Content',//Add one new checkbox called "Content". By Default it is active. If someone unchecks it, these tasks should be hidden in Content IO.
			'duedate' => 'Due Date',
			'owner' => 'Owner',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
			'percentage' => 'Percentage',
			'rct_internal_done' => 'Percentage Done - A',//This for admin/internal team's view
			'rct_percentage_done' => 'Percentage Done - C',//This for admin/internal team's view
			'content_percentage_done' => 'Content Done',//This for admin/internal team's view
			'bckw_percentage_done' => 'O/L',//backwards from the percentage
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        if (!empty($this->category)) {
            if (is_array($this->category)) {
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
        } else {
            $this->category = "";
            $this->category_str = "";
        }
        //$this->category = serialize($this->category);

        if (!empty($this->approval_type) && (is_array($this->approval_type) || is_string($this->approval_type))) {
            if (is_string($this->approval_type)) $this->approval_type = array($this->approval_type);
            $this->approval_type = implode(",", array_values($this->approval_type));
        }

        if (!empty($this->duedate)) {
            $this->duedate = strtotime(str_replace("/", "-", $this->duedate));
            $this->duedate = date("Y-m-d H:i:s", $this->duedate);
        }
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
     * after save a new campaign, we need assign this campaign to the client/user owner.
     * 
     */
    protected function afterSave(){
        if ($this->isNewRecord) {
            $users = User::model()->with('rauthassignment')->marketer()->findAllByAttributes(array('client_id'=>$this->client_id, 'type'=>0));
            if ($users) {
                foreach ($users as $u) {
                    //echo $u->duty_campaign_ids;
                    $cids = unserialize($u->duty_campaign_ids);
                    if ($cids) {
                        array_push($cids, $this->id);
                    } else {
                        $cids = array($this->id);
                    }
                    $cu = User::model()->findByPk($u->id);
                    $cu->duty_campaign_ids = serialize($cids);
                    unset($cu->password);
                    $cu->save();
                    //print_r($cu->getErrors());

                    unset($cids);
                }
            }

            //added 4/30/2014
            $cuid = Yii::app()->user->id;
            $roles = Yii::app()->authManager->getRoles($cuid);
            if(isset($roles['Marketer'])){
                $np = array();
                $np['tos'] = array("neworder@copypress.com");
                $np['cc'] = false;
                $np['content'] = "The Campaign [". $this->name."] has just been created by client. Please check it out here: http://www.connectionseeker.com/index.php?r=campaign/update&id=".$this->id;
                $np['subject'] = "New order was created.";
                $np['format'] = "text/plain";
                $c = Utils::notice($np);
            }

        }

        return parent::afterSave();
    }

    public function getSearchCriteria()
    {
		//$criteria=new CDbCriteria;
		//$criteria=new CDbCriteria(array('with'=>'rcampaigntask'));
		$criteria=new CDbCriteria(array('with'=>array('rcampaigntask','rclient')));

        if ($this->category) {
            foreach ($this->category as $v) {
                $criteria->addCondition("t.category LIKE '%|".$v."|%'",'OR');
            }
		    //$criteria->compare('category',$this->category,true);
        }

        if ($this->duty_campaign_ids && empty($this->id)) {
		    $criteria->compare('t.id',$this->duty_campaign_ids);
        } else {
		    $criteria->compare('t.id',$this->id);
        }

        $_duedate = $this->duedate;
        if ($_duedate) $_duedate = Utils::smartDateSearch($_duedate);

		//$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.domain',$this->domain,true);
		$criteria->compare('t.client_id',$this->client_id);
		$criteria->compare('t.domain_id',$this->domain_id);
		$criteria->compare('t.allow_duplicate_url',$this->allow_duplicate_url);
		$criteria->compare('t.content',$this->content);
		$criteria->compare('t.category_str',$this->category_str,true);
		$criteria->compare('t.notes',$this->notes,true);
		$criteria->compare('t.approval_type',$this->approval_type,true);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.ishidden',$this->ishidden);
		//$criteria->compare('t.duedate',$this->duedate, true);
		$criteria->compare('t.duedate',$_duedate, true);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.owner',$this->owner);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);
        if ($this->rct_internal_done == "") {
            //by default, do nothing;
        } elseif ($this->rct_internal_done == 1) {
		    $criteria->compare('rcampaigntask.internal_done',$this->rct_internal_done);
        } elseif ($this->rct_internal_done == 0){
		    $criteria->compare('rcampaigntask.internal_done',"<1");
        }
        if ($this->rct_percentage_done == "") {
            //by default, do nothing;
        } elseif ($this->rct_percentage_done == 1) {
		    $criteria->compare('rcampaigntask.percentage_done',$this->rct_percentage_done);
        } elseif ($this->rct_percentage_done == 0){
		    $criteria->compare('rcampaigntask.percentage_done',"<1");
        }

        if ($this->content_percentage_done == "") {
            //by default, do nothing;
        } elseif ($this->content_percentage_done == 1) {
		    $criteria->compare('rcampaigntask.content_done',$this->content_percentage_done);
        } elseif ($this->content_percentage_done == 0){
		    $criteria->compare('rcampaigntask.content_done',"<1");
        }

        /*
        //we can define these stuff in campaign model,
		$criteria->compare('rcampaigntask.total_count',$this->rcampaigntask->total_count);
		$criteria->compare('rcampaigntask.published_count',$this->rcampaigntask->published_count);
		$criteria->compare('rcampaigntask.percentage_done',$this->rcampaigntask->percentage_done);
        */

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
        $this->getSummary();

        $criteria = $this->getSearchCriteria();

        $sort = new CSort();
        $sort->attributes = array(
            'rcampaigntask.total_count'=>array(
                'asc'=>'rcampaigntask.total_count ASC',
                'desc'=>'rcampaigntask.total_count DESC',
            ),
            'rcampaigntask.published_count'=>array(
                'asc'=>'rcampaigntask.published_count ASC',
                'desc'=>'rcampaigntask.published_count DESC',
            ),
            'rcampaigntask.pending_count'=>array(
                'asc'=>'rcampaigntask.pending_count ASC',
                'desc'=>'rcampaigntask.pending_count DESC',
            ),
            'rcampaigntask.approved_count'=>array(
                'asc'=>'rcampaigntask.approved_count ASC',
                'desc'=>'rcampaigntask.approved_count DESC',
            ),
            'rcampaigntask.qa_count'=>array(
                'asc'=>'rcampaigntask.qa_count ASC',
                'desc'=>'rcampaigntask.qa_count DESC',
            ),
            'rcampaigntask.inrepair_count'=>array(
                'asc'=>'rcampaigntask.inrepair_count ASC',
                'desc'=>'rcampaigntask.inrepair_count DESC',
            ),
            'rcampaigntask.remaining_count'=>array(
                'asc'=>'rcampaigntask.remaining_count ASC',
                'desc'=>'rcampaigntask.remaining_count DESC',
            ),
            'rcampaigntask.percentage_done'=>array(
                'asc'=>'rcampaigntask.percentage_done ASC',
                'desc'=>'rcampaigntask.percentage_done DESC',
            ),
            'rcampaigntask.internal_done'=>array(
                'asc'=>'rcampaigntask.internal_done ASC',
                'desc'=>'rcampaigntask.internal_done DESC',
            ),
            'rcampaigntask.content_done'=>array(
                'asc'=>'rcampaigntask.content_done ASC',
                'desc'=>'rcampaigntask.content_done DESC',
            ),
            'rct_internal_done'=>array(
                'asc'=>'rcampaigntask.internal_done ASC',
                'desc'=>'rcampaigntask.internal_done DESC',
            ),
            'rct_percentage_done'=>array(
                'asc'=>'rcampaigntask.percentage_done ASC',
                'desc'=>'rcampaigntask.percentage_done DESC',
            ),
            'bckw_percentage_done'=>array(
                'asc'=>'rcampaigntask.percentage_done DESC',
                'desc'=>'rcampaigntask.percentage_done ASC',
            ),
            'content_percentage_done'=>array(
                'asc'=>'rcampaigntask.content_done ASC',
                'desc'=>'rcampaigntask.content_done DESC',
            ),
            'client_id'=>array(
                'asc'=>'rclient.company ASC',
                'desc'=>'rclient.company DESC',
            ),
            '*', // add all of the other columns as sortable
        );


		return new CActiveDataProvider($this, array(
            'sort'=>$sort,
			'criteria'=>$criteria,
		));
	}

    public function getSummary()
    {
        /*
        //$criteria = $this->getSearchCriteria();
        $criteria=new CDbCriteria(array('with'=>array('rcampaign')));
        $criteria->compare('t.client_id',$this->client_id);
        $criteria->compare('t.campaign_id',$this->id);

        $criteria->select = array(
            'SUM(t.total_count) AS summary_total',
            'SUM(t.qa_count) AS summary_qa',
            'SUM(t.approved_count) AS summary_approved',
            'SUM(t.published_count) AS summary_published',
            'SUM(t.remaining_count) AS summary_remaining'
        );

        $ret = CampaignTask::model()->find($criteria);
        if ($ret) {
            $this->summary_total = $ret->summary_total;
            $this->summary_qa = $ret->summary_qa;
            $this->summary_approved = $ret->summary_approved;
            $this->summary_published = $ret->summary_published;
            $this->summary_remaining = $ret->summary_remaining;
        }
        */

        $where = "1";
        if ($this->id) {
            $where .= " AND ct.campaign_id = ".(int)$this->id;
        }
        if ($this->client_id) {
            $where .= " AND c.client_id = ".(int)$this->client_id;
        }
        if ($this->name) {
            $where .= " AND c.name LIKE '%".$this->name."%'";
        }
        if (isset($this->ishidden) && is_numeric($this->ishidden)) {
            //$where .= " AND c.ishidden = ".(int)$this->ishidden;
            if ($this->ishidden == 1) {
                $where .= " AND c.ishidden = 1";
            } else {
                $where .= " AND c.ishidden != 1";
            }
        }
        //->from('{{campaign_task}} ct')->where("$where")

        /*
        if ($this->id) $where .= " AND ct.campaign_id = ':ctid'";
        if ($this->client_id) $where .= " AND c.client_id = ':cid'";
        if ($this->name) $where .= " AND c.name LIKE '%:cname%'";
        $warr = array(":ctid" => (int)$this->id, ":cid"=>(int)$this->client_id, ":cname"=>$this->name);
        */

        $summaries = Yii::app()->db->createCommand()
            ->select('SUM(ct.total_count) AS summary_total, 
                SUM(ct.qa_count) AS summary_qa, SUM(ct.inrepair_count) AS summary_inrepair,
                SUM(ct.approved_count) AS summary_approved,SUM(ct.published_count) AS summary_published,
                SUM(ct.pending_count) AS summary_pending,SUM(ct.remaining_count) AS summary_remaining')
            ->from('{{campaign_task}} ct')->where("$where")
            ->join('{{campaign}} c', 'c.id = ct.campaign_id')->queryRow();

        if ($summaries) {
            $this->summary_total = $summaries["summary_total"];
            $this->summary_qa = $summaries["summary_qa"];
            $this->summary_inrepair = $summaries["summary_inrepair"];
            $this->summary_approved = $summaries["summary_approved"];
            $this->summary_pending = $summaries["summary_pending"];
            $this->summary_published = $summaries["summary_published"];
            $this->summary_remaining = $summaries["summary_remaining"];
        }
    }

}