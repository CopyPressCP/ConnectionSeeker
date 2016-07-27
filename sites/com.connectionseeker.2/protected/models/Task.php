<?php

/**
 * This is the model class for table "{{inventory_building_task}}".
 *
 * The followings are the available columns in table '{{inventory_building_task}}':
 * @property integer $id
 * @property integer $inventory_id
 * @property integer $campaign_id
 * @property string $domain
 * @property string $domain_id
 * @property string $anchortext
 * @property string $targeturl
 * @property string $sourceurl
 * @property string $sourcedomain
 * @property string $title
 * @property integer $tasktype
 * @property string $taskstatus
 * @property integer $assignee
 * @property string $optional_keywords
 * @property string $mapping_id
 * @property string $notes
 * @property integer $duedate
 * @property integer $content_article_id
 * @property integer $content_campaign_id
 * @property integer $content_category_id
 * @property integer $send2cpdate
 * @property integer $checkouted
 * @property date $livedate
 * @property datetime $iodate
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Task extends CActiveRecord
{
    public $client_id;
    public $inventory_ids;
    public $optionalkw1;
    public $optionalkw2;
    public $optionalkw3;
    public $optionalkw4;
    public $style_guide;
    public $livedate_end;
    public $client_request;
    public $duty_campaign_ids = array();

    //all of the io statuses
    public static $iostatuses = array('0' => 'Initial',
                           '1' => 'Current',
                           '2' => 'Accepted',
                           '3' => 'Approved',
                           '4' => 'Denied',
                           '5' => 'IO Completed',
                           '21' => 'Pending',);

    //copypress content status; map to "taskstatus" field, acctually it should be named "contentstatus"
    public static $status = array('0' => 'NaN',
                           '1' => 'Unassigned',
                           '2' => 'Writing',
                           '3' => 'Pending Review',
                           '4' => 'Completed',
                           '5' => 'Link Completed',);

    //task progess status;
    public static $pgstatus = array('0' => 'Initial',
                           '1' => 'In progress',
                           '2' => 'In production',
                           '3' => 'QA',
                           '4' => 'Completed',);

	/**
	 * Returns the static model of the specified AR class.
	 * @return Task the static model class
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
		return '{{inventory_building_task}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('inventory_id, campaign_id, tasktype, assignee', 'required'),
			array('inventory_id, campaign_id, tasktype, assignee, duedate, content_article_id, content_campaign_id, content_category_id, send2cpdate, checkouted, created_by, modified_by, publication_pending', 'numerical', 'integerOnly'=>true),
			array('domain, targeturl, sourceurl, sourcedomain, mapping_id', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('taskstatus, progressstatus, iostatus', 'length', 'max'=>50),
			array('anchortext, title, optional_keywords, notes, qa_comments, channel_id, desired_domain_id, desired_domain, rewritten_title, blog_title, tierlevel, blog_url, created, modified, inventory_ids, client_id, style_guide, livedate, iodate', 'safe'),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, inventory_id, campaign_id, domain, domain_id, anchortext, targeturl, sourceurl, sourcedomain, title, tasktype, taskstatus, iostatus, assignee, optional_keywords, mapping_id, notes, tierlevel, qa_comments, channel_id, desired_domain_id, desired_domain, rewritten_title, blog_title, blog_url, duedate, content_article_id, content_campaign_id, content_category_id, send2cpdate, checkouted, created, created_by, livedate_end, modified, modified_by', 'safe', 'on'=>'search'),
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
            'rcampaign' => array(self::BELONGS_TO, 'Campaign', 'campaign_id'),
            'rcontent' => array(self::BELONGS_TO, 'Content', 'content_article_id'),
            'rassignee' => array(self::BELONGS_TO, 'User', 'assignee'),
            'rsummary' => array(self::BELONGS_TO, 'Summary', array('desired_domain_id'=>'domain_id')),
            //'rsummary' => array(self::BELONGS_TO, 'Summary', '', 'joinType'=>'INNER JOIN', 'join'=>'lkm_inventory_building_task as t', 'on'=>'rsummary.domain_id = t.desired_domain_id'),
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
			'iostatus' => 'IO Status',
			'inventory_id' => 'Inventory',
			'campaign_id' => 'Campaign',
			'domain' => 'Domain',
			'domain_id' => 'Domain',
			'anchortext' => 'Anchor Text',
			'targeturl' => 'Target URL',
			'sourceurl' => 'Posted URL',
			'sourcedomain' => 'Source Domain',
			'title' => 'Title',
			'tasktype' => 'Content Needed',
			'taskstatus' => 'Content Status',
			'assignee' => 'Assignee',
			'optional_keywords' => 'Optional Keywords',
			'mapping_id' => 'Mapping ID',
            'optionalkw1' => 'Optional Keyword 1',
            'optionalkw2' => 'Optional Keyword 2',
            'optionalkw3' => 'Optional Keyword 3',
            'optionalkw4' => 'Optional Keyword 4',
            'style_guide' => 'Style Guide',
			'notes' => 'Title Notes',
			'duedate' => 'Due Date',
			'content_article_id' => 'Article ID',
			'content_campaign_id' => 'Content Campaign',
			'content_category_id' => 'Content Category',
			'send2cpdate' => 'Send2cpdate',
			'checkouted' => 'Checkouted',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Last Updated',
			'tierlevel' => 'Tier Level',

			'qa_comments' => 'QA Comments',
			'client_comments' => 'Client Comments',
			'client_request' => 'Client Request',
			'livedate' => 'Live Date',
			'iodate' => 'IO Date',
			'target_stype' => 'Type of Site',
			'publication_pending' => 'Publication Pending',
			'channel_id'  => 'Channel',
			'desired_domain_id' => 'Desired Placement',
			'desired_domain' => 'Desired Placement',
			'rewritten_title' => 'Title',//Rewritten Title
			'blog_title' => 'Blog Article Title',
			'blog_url' => 'Blog Article URL',
			'progressstatus' => 'Progress Status',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
 
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
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

    /**
     * Update the campaign task's published_count & percentage when you set it complete.
     * 
     */
    protected function afterSave(){
        /*
        * Link Complete,if you wanna the status can be changed anyway,
        * no matter it was complete or not,then coment's out this line: if ($this->progressstatus == 4) {
        */
        if ($this->progressstatus == 4) {
            $cptmodel = CampaignTask::model()->findByAttributes(array('campaign_id'=>$this->campaign_id));
            if ($cptmodel) {
                $cptmodel->setIsNewRecord(false);
                $cptmodel->setScenario('update');
                $pcount = Task::model()->countByAttributes(array('campaign_id'=>$this->campaign_id, 'progressstatus'=>4));
                $cptmodel->published_count = $pcount;
                if ($cptmodel->total_count > 0) {
                    $cptmodel->percentage_done = round($cptmodel->published_count / $cptmodel->total_count, 3);
                }
                $cptmodel->save();
            }
        }

        return parent::afterSave();
    }

    /**
     * Before Delete the task's we need update the rcampaigntask.total_count amount first.
     * 
     */
    protected function beforeDelete(){
        $cptmodel = CampaignTask::model()->findByAttributes(array('campaign_id'=>$this->campaign_id));
        if ($cptmodel && $cptmodel->total_count > 0) {
            $cptmodel->total_count = $cptmodel->total_count - 1;

            #####################################
            $ky = unserialize($cptmodel->keyword);
            $urls = unserialize($cptmodel->targeturl);
            if (count($ky)) {
                foreach ($ky as $k => $v) {
                    if (($v['targeturl'] == $this->targeturl && $v['keyword'] == $this->anchortext 
                                                             && $v['tierlevel'] == $this->tierlevel)
                      || (isset($v['taskids']) && in_array($this->id, $v['taskids'])) ) {
                        if ($v['kwcount'] == 1) {
                            unset($ky[$k]);
                            unset($urls[$k]);
                        } else {
                            $v['kwcount'] = $v['kwcount'] - 1;
                            $ky[$k] = $v;
                        }
                        break;
                    }
                }
                $cptmodel->keyword = serialize($ky);
                $cptmodel->targeturl = serialize($urls);
            }
            #####################################


            $cptmodel->save();
        }

        return parent::beforeDelete();
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
        $criteria=new CDbCriteria(array('with'=>array('rcampaign')));
        /*
        if ($this->client_id) {
		    $criteria=new CDbCriteria(array('with'=>array('rcampaign')));
        } else {
		    $criteria=new CDbCriteria;
        }
        */

		$criteria->compare('id',$this->id);
		$criteria->compare('inventory_id',$this->inventory_id);
        if ($this->duty_campaign_ids && empty($this->campaign_id)) {
		    $criteria->compare('campaign_id',$this->duty_campaign_ids);
        } else {
            if (is_numeric($this->campaign_id) || is_array($this->campaign_id)) {
                $criteria->compare('campaign_id',$this->campaign_id);
            } else {
                $criteria->compare('rcampaign.name',$this->campaign_id, true);
            }
        }
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('anchortext',$this->anchortext,true);
		$criteria->compare('targeturl',$this->targeturl,true);
		$criteria->compare('sourceurl',$this->sourceurl,true);
		$criteria->compare('sourcedomain',$this->sourcedomain,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('tasktype',$this->tasktype);
		$criteria->compare('taskstatus',$this->taskstatus);
		$criteria->compare('iostatus',$this->iostatus);
		$criteria->compare('assignee',$this->assignee);
		$criteria->compare('optional_keywords',$this->optional_keywords,true);
		$criteria->compare('mapping_id',$this->mapping_id,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('t.duedate',$this->duedate);
		$criteria->compare('content_article_id',$this->content_article_id);
		$criteria->compare('content_campaign_id',$this->content_campaign_id);
		$criteria->compare('content_category_id',$this->content_category_id);
		$criteria->compare('send2cpdate',$this->send2cpdate);
		$criteria->compare('checkouted',$this->checkouted);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		$criteria->compare('iodate',$this->iodate,true);
		//$criteria->compare('livedate',$this->livedate,true);

        if(!empty($this->livedate) && !empty($this->livedate_end)) {
            $criteria->addBetweenCondition('livedate', $this->livedate, $this->livedate_end, "AND");
            //$criteria->params[':lstart'] = $this->livedate;
            //$criteria->params[':lend'] = $this->livedate_end;
            //$criteria->addCondition('livedate BETWEEN :lstart AND :lend');
            //$criteria->addCondition('(livedate >= :lstart AND livedate <= :lend)');
        } else {
		    $criteria->compare('livedate',$this->livedate,true);
        }

		$criteria->compare('tierlevel',$this->tierlevel);
		$criteria->compare('qa_comments',$this->qa_comments,true);
		$criteria->compare('channel_id',$this->channel_id);
        if (is_numeric($this->desired_domain_id)) {
		    $criteria->compare('desired_domain_id',$this->desired_domain_id);
        } else {
		    $criteria->compare('desired_domain',$this->desired_domain_id, true);
        }
		$criteria->compare('desired_domain',$this->desired_domain,true);
		$criteria->compare('rewritten_title',$this->rewritten_title,true);
		$criteria->compare('blog_title',$this->blog_title,true);
		$criteria->compare('blog_url',$this->blog_url,true);
		$criteria->compare('progressstatus',$this->progressstatus);

        if ($this->client_id) {
		    $criteria->compare('rcampaign.client_id',$this->client_id);
        }

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}