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
    public $iodate_end;
    public $client_request;
    public $duty_campaign_ids = array();
    public $total_spent = null;
    public $campaign_exclude;
    public $campaign_approval_type;

    public $triggerTitleSave = true; //prevent afterSave/beforeSave loop, Please Pay Attention to this one!!!!!!

    //all of the io statuses
    public static $iostatuses = array('0' => 'Initial',
                           '1' => 'Current',
                           '2' => 'Accepted',
                           '3' => 'Approved',
                           '4' => 'Denied',
                           '5' => 'IO Completed',
                           '21' => 'Pending',
                           '31' => 'Completed - Pre QA',
                           '32' => 'Completed - In Repair',);

    //copypress content status; map to "taskstatus" field, acctually it should be named "contentstatus"
    public static $status = array('0' => 'NaN',
                           '1' => 'Unassigned',
                           '2' => 'Writing',
                           '3' => 'Pending Review',
                           '4' => 'Completed',
                           '5' => 'Link Completed',);//We will add another value 99 for no article content;

    //task progess status;
    /*
    public static $pgstatus = array('0' => 'Initial',
                           '1' => 'In progress',
                           '2' => 'In production',
                           '3' => 'QA',
                           '4' => 'Completed',);
    */

    private $_campaignTotalCount = 0;
    private $_campaignCompletedCount = 0;

    private $_oldTaskAttributes;
    public function afterFind()
    {
        $this->_oldTaskAttributes = $this->attributes;
        return parent::afterFind();
    }

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
			array('inventory_id, campaign_id, tasktype, assignee, content_article_id, content_campaign_id, content_category_id, send2cpdate, checkouted, created_by, modified_by, publication_pending, passed_iqa, siteonly, rebuild,always_on_cio', 'numerical', 'integerOnly'=>true),
			array('targeturl, sourceurl', 'length', 'max'=>2000),
			array('domain, sourcedomain, mapping_id', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('taskstatus, iostatus', 'length', 'max'=>50),
			array('anchortext, title, optional_keywords, notes, qa_comments, channel_id, desired_domain_id, desired_domain, rewritten_title, blog_title, tierlevel, tierlevel_built, blog_url, created, modified, inventory_ids, client_id, style_guide, livedate, sentdate, iodate, other, client_comments, duedate, content_step, step_date, content_step_editor, assisted_by', 'safe'),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, inventory_id, campaign_id, domain, domain_id, anchortext, targeturl, sourceurl, sourcedomain, title, tasktype, taskstatus, iostatus, assignee, optional_keywords, mapping_id, notes, tierlevel, tierlevel_built, qa_comments, channel_id, desired_domain_id, desired_domain, rewritten_title, blog_title, blog_url, duedate, content_article_id, content_campaign_id, content_category_id, send2cpdate, checkouted, created, created_by, livedate_end, iodate_end, modified, modified_by, spent, other, sentdate, siteonly, content_step, step_date, content_step_editor, campaign_name, rebuild, passed_iqa, publication_pending, campaign_exclude, assisted_by, client_id, always_on_cio, campaign_approval_type', 'safe', 'on'=>'search'),
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
            'rrating' => array(self::HAS_ONE,    'TaskRating', 'task_id'),
            'rstep' => array(self::HAS_ONE,    'ContentStep', 'task_id'),
            'rstepnote2' => array(self::HAS_ONE, 'StepNote', 'task_id', 'on'=>'rstepnote2.type=2'),//Writer Note(Client Comment)
            'rstepnote3' => array(self::HAS_ONE, 'StepNote', 'task_id', 'on'=>'rstepnote3.type=3'),//Extra Writer Note
            //'rsummary' => array(self::BELONGS_TO, 'Summary', '', 'joinType'=>'INNER JOIN', 'join'=>'lkm_inventory_building_task as t', 'on'=>'rsummary.domain_id = t.desired_domain_id'),
		);
	}

    public function scopes()
    {
        return array(
            'haverating'=>array(
                'condition'=>"rrating.rating > '0'",
            ),
            'hidden'=>array(
                'condition'=>"rcampaign.ishidden = '1'",
            ),
            'unhidden'=>array(
                'condition'=>"rcampaign.ishidden != '1'",
            ),
            'contentio'=>array(
                'condition'=>"(rcampaign.ishidden != '1' AND (rcampaign.content = '1') AND (t.iostatus IN (3, 4, 5, 31, 32))) OR (rcampaign.always_on_cio=1) OR (t.always_on_cio=1)",
            ),
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
			'tierlevel' => 'Tier: Order',
			'tierlevel_built' => 'Tier: Built',
			'spent' => 'Spent',
			'siteonly' => 'Site Only',
			'rebuild' => 'Rebuild',
			'assisted_by' => 'Assisted By',

			'qa_comments' => 'QA Comments',
			'client_comments' => 'Client Comments',
			'client_request' => 'Client Request',
			'client_id' => 'Client',
			'other' => 'Other',
			'livedate' => 'Live Date',
			'sentdate' => 'Content Sent',
			'iodate' => 'IO Date',
			'target_stype' => 'Type of Site',
			'publication_pending' => 'Publication Pending',
			'passed_iqa' => 'Passed Internal QA?',
			'channel_id'  => 'Channel',
			'desired_domain_id' => 'Desired Placement',
			'desired_domain' => 'Desired Placement',
			'rewritten_title' => 'Title',//Rewritten Title
			'blog_title' => 'Blog Article Title',
			'blog_url' => 'Blog Article URL',
			'always_on_cio' => 'Always display on Content IO',

			'content_step' => 'Step',
			'step_date' => 'Date Of Step',
			'content_step_editor' => 'Editor',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
 
        if (!empty($this->duedate)) {
            $this->duedate = strtotime(str_replace("/", "-", $this->duedate));
            $this->duedate = date("Y-m-d H:i:s", $this->duedate);
        }

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

    protected function beforeSave(){
        /*
        * IF Desired Placement != a domain(Options for desired placement are a domain, and channel, or ----- which equals null) 
        * AND IO status == Completed AND Progress Status == Less the Pre QA, Update IO status to Initial, 
        * and KEEP all the data filled in.  The only thing to change is the IO status.
        *
        * IF Desired Placement != null AND IO status == Completed AND Progress Status == Less the Pre QA,
        * Update Progress Status to Pre QA.  
        */
        if ($this->isNewRecord) {
            //do nothing for now;
        } else {
            if (empty($this->desired_domain_id) && in_array($this->iostatus, array(31,5)) ) {
                $this->iostatus = 0;
            }
            if ($this->iostatus == 2 && $this->_oldTaskAttributes["iostatus"] != 2) {
                $this->sentdate = null;
            }
            if ($this->iostatus == 2 && $this->_oldTaskAttributes["iostatus"] == 32) {
                $this->rewritten_title = "[Needs Content]";
                $this->sourceurl = "";
                $tknote = "Star Over Again:\r\nOriginal Title: %s\r\nOriginal Publish URL: %s\r\nOriginal Desired Domain: %s\r\n";
                $tknote = sprintf($tknote, $this->_oldTaskAttributes["rewritten_title"],$this->_oldTaskAttributes["sourceurl"],$this->_oldTaskAttributes["desired_domain"]);
                $this->_autoNote($tknote);
            }

            //Sync the Content IO title with IO title and Campaign Title.
            if ($this->_oldTaskAttributes["content_step"] == 5 && $this->content_step == 6 && empty($this->rewritten_title)) {
                $cstep = ContentStep::model()->findByAttributes(array('task_id' => $this->id));
                if ($cstep) {
                    $this->rewritten_title = $cstep->step_title;
                }
            } elseif ($this->_oldTaskAttributes["content_step"] == 0 && $this->content_step == 1) {
                //It should automatically check if Client Title Approval Column is Yes or No.  
                //If column is No, it should skip Idea Approval and go directly to "To Order" tab.
                $cmpmodel = Campaign::model()->findByPk($this->campaign_id);
                if ($cmpmodel && stripos($cmpmodel->approval_type, "TA") === false ) {
                    $this->content_step = 2;
                }
            } elseif ($this->_oldTaskAttributes["content_step"] == 3 && $this->content_step == 4) {
                //On Content Approval check Client Content Approval Column, IF column is No, skip to Delivered
                $cmpmodel = Campaign::model()->findByPk($this->campaign_id);
                if ($cmpmodel && stripos($cmpmodel->approval_type, "CA") === false ) {
                    $this->content_step = 5;
                }
            }

            if ($this->_oldTaskAttributes["content_step"] != $this->content_step) {
                $this->step_date = date("Y-m-d H:i:s");
            }

        }

        return parent::beforeSave();
    }

    private function _autoNote($note) {
        if (!empty($note)) {
            $notemodel = new TaskNote;
            $notemodel->setIsNewRecord(true);
            $notemodel->id = NULL;
            $notemodel->task_id = $this->id;
            $notemodel->notes = $note;
            $notemodel->save();
        }
    }

    /**
     * Update the Content IO history when content IO items are moved to each tab (tbl.lkm_contentio_historic_reporting)
     * 
     */
    private function updateContentHistory() {

        if ( ($this->isNewRecord || $this->_oldTaskAttributes["content_step"] != $this->content_step) && 
            ( ($this->rcampaign->ishidden != 1 && $this->rcampaign->content==1 && in_array((int)$this->iostatus, array(3,4,5,31,32)) ) 
                || ($this->rcampaign->always_on_cio == 1) || ($this->always_on_cio == 1) ) ) {
            $iohmdl = new ContentioHistoricReporting;
            $_iohmdl = $iohmdl->find('task_id=:task_id',array(':task_id'=>$this->id));
            if ($_iohmdl) {
                $iohmdl = $_iohmdl;
                $iohmdl->setIsNewRecord(false);
                $iohmdl->setScenario('update');

                if ($this->content_step) {
                    $datelabel = strtolower("date_step".$this->content_step);
                    $timelabel = strtolower("time2step".$this->content_step);
                    if ($this->content_step>0) {
                        $prevstep = $this->_oldTaskAttributes["content_step"];
                        $predatelabel = strtolower("date_step".$prevstep);

                        if (empty($iohmdl->$predatelabel)) $iohmdl->$predatelabel = $this->created;
                        $iohmdl->$datelabel  = $this->modified;
                        $iohmdl->$timelabel  = strtotime($this->modified) - strtotime($iohmdl->$predatelabel);
                    }
                }
            } else {
                $iohmdl->setIsNewRecord(true);
                $iohmdl->id       = NULL;
                $iohmdl->date_step0 = ($this->modified_by > 0) ? $this->modified : $this->created;
                $iohmdl->time2step0 = strtotime($iohmdl->date_step0) - strtotime($this->created);
                $iohmdl->task_id = $this->id;
            }

            $iohmdl->campaign_id = $this->campaign_id;
            $iohmdl->tierlevel = $this->tierlevel;
            $iohmdl->channel_id  = $this->channel_id;
            //print_r($iohmdl->attributes);
            $iohmdl->save();
        }
    }

    /**
     * Update the campaign task's published_count & percentage when you set it complete.
     * 
     */
    protected function afterSave(){

        /*
        * Link Complete,if you wanna the status can be changed anyway,
        * no matter it was complete or not,then comment's out this line: if ($this->progressstatus == 4) {
        * $pcount = Task::model()->countByAttributes(array('campaign_id'=>$this->campaign_id, 'progressstatus'=>4));
        */
        $this->updateCampaignTaskCount();
        if ($this->isNewRecord || $this->_oldTaskAttributes["iostatus"] != $this->iostatus) {
            //$this->updateCampaignTaskCount();

            $iohmdl = new IoHistoricReporting;
            $_iohmdl = $iohmdl->find('task_id=:task_id',array(':task_id'=>$this->id));
            if ($_iohmdl) {
                $iohmdl = $_iohmdl;
                //$this->_oldTaskAttributes
                $iohmdl->setIsNewRecord(false);
                $iohmdl->setScenario('update');

                //if ($this->_oldTaskAttributes["iostatus"]) {
                if ($this->iostatus) {
                    if ($this->iostatus == 4) {
                        $iohmdl->date_denied = $this->modified;
                        $iohmdl->time2denied = strtotime($this->modified) - strtotime($this->created);
                    } elseif ($this->iostatus == 32) {
                        $iohmdl->date_inrepair = $this->modified;
                        $iohmdl->time2inrepair = strtotime($this->modified) - strtotime($iohmdl->time2approved);
                    } else {
                        /*
                        if ($this->iostatus == 5) {
                            $iolabel = "completed";
                        } else {
                            $iolabel = self::$iostatuses[$this->iostatus];
                        }
                        */
                        switch ($this->iostatus) {
                            case 5:
                                $iolabel = "completed";
                                break;
                            case 31:
                                $iolabel = "preqa";
                                break;
                            case 32:
                                $iolabel = "inrepair";
                                break;
                            default:
                                $iolabel = self::$iostatuses[$this->iostatus];
                        }

                        $datelabel = strtolower("date_".$iolabel);
                        $timelabel = strtolower("time2".$iolabel);

                        $flowstr = ",initial,current,accepted,pending,approved,preqa,completed";
                        $preflow = substr($flowstr, 0, stripos($flowstr, ",$iolabel"));
                        $prelabel = substr(strrchr($preflow, ","), 1);
                        $predatelabel = strtolower("date_".$prelabel);
                        
                        //echo $iolabel . $prelabel;
                        if ($iolabel && $prelabel) {
                            if (empty($iohmdl->$predatelabel)) $iohmdl->$predatelabel = $this->created;
                            $iohmdl->$datelabel  = $this->modified;
                            $iohmdl->$timelabel  = strtotime($this->modified) - strtotime($iohmdl->$predatelabel);
                        }
                    }
                }
            } else {
                $iohmdl->setIsNewRecord(true);
                $iohmdl->id       = NULL;
                $iohmdl->date_initial = $this->created;
                $iohmdl->task_id = $this->id;
            }

            $iohmdl->campaign_id = $this->campaign_id;
            $iohmdl->tierlevel = $this->tierlevel;
            $iohmdl->channel_id  = $this->channel_id;
            //print_r($iohmdl->attributes);
            $iohmdl->save();

            $domodel = array();
            if (is_numeric($this->desired_domain_id)) {
                $domodel = Domain::model()->with(array('rsummary'))->findByPk($this->desired_domain_id);
            }
            if ($this->_oldTaskAttributes["iostatus"] == 21 && $this->iostatus == 3 
              && !empty($this->tierlevel) && $domodel) {
                //Create one note for this.
                $_types = Types::model()->bytype("tierlevel")->findAll();
                $_gtps = CHtml::listData($_types, 'refid', 'typename', 'type');
                $tiers = $_gtps['tierlevel'];
                
                $tknote = "From: System\r\nBody: \r\nTier = %s\r\nAlexa Rank = %s\r\nMozrank = %s\r\nDA = %s\r\nDesired Domain: %s\r\n";
                $tknote = sprintf($tknote,$tiers[$this->tierlevel],$domodel->alexarank,$domodel->rsummary->mozrank,$domodel->rsummary->mozauthority,$this->desired_domain);
                $this->_autoNote($tknote);
            }
        }

        $this->updateContentHistory();

        //added 7/15/2013 for tracking denied;
        if ($this->_oldTaskAttributes["iostatus"] != $this->iostatus && in_array($this->iostatus, array(1,4)) ) {
            $old_domain_id = $this->_oldTaskAttributes["desired_domain_id"];
            if ($old_domain_id > 0) {
                $ivt = Inventory::model()->findByAttributes(array('domain_id'=>$old_domain_id));
                if ($ivt) {
                    $ivt->setIsNewRecord(false);
                    $ivt->setScenario('update');
                    $ivt->isdenied = 1;
                    $ivt->status = 1;
                    if ($ivt->denied_by) {
                        if (strpos($ivt->denied_by, "|".Yii::app()->user->id."|") === false) {
                            $ivt->denied_by .= Yii::app()->user->id."|";
                            $ivt->denied_by_str .= Yii::app()->user->name."|";
                        }
                    } else {
                        $ivt->denied_by = "|".Yii::app()->user->id."|";
                        $ivt->denied_by_str = "|".Yii::app()->user->name."|";
                    }
                    $ivt->save();
                }
            }
        }


        //When move this task from Accept into Pending;
        if ($this->_oldTaskAttributes["iostatus"] == 2 && $this->iostatus == 21) {
            $ivt = Inventory::model()->findByAttributes(array('domain'=>$this->desired_domain));
            if ($ivt) {
                $ivt->setIsNewRecord(false);
                $ivt->setScenario('update');
                $ivt->acquireddate = date('Y-m-d H:i:s');
                $ivt->status = 1;
                $ivt->save();
            }
        }

        if ($this->_oldTaskAttributes["iostatus"] != $this->iostatus && $this->iostatus == 5) {
            $campaigns = Yii::app()->db->createCommand()->select('t.campaign_id, c.name')
                ->from('{{inventory_building_task}} t')->join('{{campaign}} c', 'c.id=t.campaign_id')
                ->where("t.desired_domain_id='".$this->desired_domain_id."' AND t.iostatus=5")
                ->group("t.campaign_id")->queryAll();
            if ($campaigns) {
                $cmpids = array();
                $cmpnames = array();
                foreach ($campaigns as $cmp) {
                    $cmpids[] = $cmp["campaign_id"];
                    $cmpnames[] = $cmp["name"];
                }
                $cmpidstr = implode("|", $cmpids);
                $cmpidstr = "|" . $cmpidstr . "|";
                $cmpnamestr = implode(", ", $cmpnames);
                $ivtarr = array();
                $ivtarr["campaign_id"] = $cmpidstr;
                $ivtarr["campaign_str"] = $cmpnamestr;
                $ivtarr["ispublished"] = 1;
                $ivtarr["status"] = 1;
                $ivtarr["last_published"] = date('Y-m-d H:i:s');
                Yii::app()->db->createCommand()->update('{{inventory}}', $ivtarr, 'domain_id=:domain_id', array(':domain_id'=>$this->desired_domain_id));
                unset($cmpids);
                unset($cmpnames);
                unset($campaigns);
            }

            //add a basic check AFTER clicking "Approve" on Complete - Pre QA, that checks the campaign to see if the current task being moved is the task that makes the campaign 100% completed. 
            if ($this->_oldTaskAttributes["iostatus"] == 31 && $this->_campaignTotalCount == $this->_campaignCompletedCount) {
                //IF no, do nothing. If yes, trigger an email.
                $np = array();
                //$np['tos'] = array("rwhitney@copypress.com","lseedhouse@copypress.com","twyher@copypress.com");
                $np['tos'] = array("CSCampaignComplete@copypress.com");
                $np['cc'] = false;
                $np['content'] = $this->rcampaign->name." has just been finished. ";
                $np['content'] .= "Download tasks click here: http://dev.connectionseeker.com/index.php?r=download/task&Task%5Bcampaign_id%5D=".$this->campaign_id;
                $np['subject'] = "Good Job! Another campaign is completed.";
                $np['format'] = "text/plain";
                $c = Utils::notice($np);
            }
        }

        //4/30/2014 notice content editor when there is new idea task assign to he.
        /* //### comment out 7/17/2014, We get rid of the assign editor part when we rebuild the new content IO part. 
        if ($this->_oldTaskAttributes["content_step"] == 2 && $this->content_step == 3 && $this->content_step_editor > 0) {
            $_ueditor = User::model()->findByPk($this->content_step_editor);
            if ($_ueditor && $_ueditor->email) {
                $np = array();
                $np['tos'] = array($this->content_step_editor);
                $np['cc'] = false;
                $np['content'] = "You have been assigned Task#.  Please complete it <a href='http://www.connectionseeker.com/index.php?r=contentStep/step3' target='_blank'>here</a>.";
                $np['subject'] = "You have a new ideation task";
                $np['format'] = "text/html";
                $c = Utils::notice($np);
            }
        }
        */

        if ($this->triggerTitleSave == true && $this->_oldTaskAttributes["rewritten_title"] != $this->rewritten_title) {
            $csmdl = ContentStep::model()->findByAttributes(array('task_id'=>$this->id));
            if ($csmdl) {
                $csmdl->setIsNewRecord(false);
                $csmdl->setScenario('update');
                $csmdl->triggerTitleSave = false;
                $csmdl->step_title = $this->rewritten_title;
                $csmdl->save();
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
     * After Delete the task's we need update the rcampaigntask.total_count amount first.
     * 
     */
    protected function afterDelete(){
        $this->updateCampaignTaskCount();

        return parent::afterDelete();
    }

    private function updateCampaignTaskCount() {
        $summaries = Yii::app()->db->createCommand()
            ->select("COUNT( if(iostatus='31',true,null) ) AS qa_count,
                    COUNT( if(iostatus='32',true,null) ) AS inrepair_count,
                    COUNT( if(iostatus='5',true,null) ) AS published_count,
                    COUNT( if(iostatus='21',true,null) ) AS pending_count,
                    COUNT( if(iostatus='3',true,null) ) AS approved_count")
            ->from('{{inventory_building_task}}')
            ->where("`campaign_id` = '".$this->campaign_id."'")
            ->queryRow();

        $cptmodel = CampaignTask::model()->findByAttributes(array('campaign_id'=>$this->campaign_id));
        if ($cptmodel && $summaries) {
            $this->_campaignTotalCount = $cptmodel->total_count;
            $this->_campaignCompletedCount = $summaries["published_count"];

            $cptmodel->setIsNewRecord(false);
            $cptmodel->setScenario('update');
            $qa_count = $summaries["qa_count"];
            $inrepair_count = $summaries["inrepair_count"];
            $published_count = $summaries["published_count"];
            $approved_count = $summaries["approved_count"];
            $pending_count = $summaries["pending_count"];

            $cptmodel->qa_count = $qa_count;
            $cptmodel->published_count = $published_count;
            $cptmodel->approved_count = $approved_count;
            $cptmodel->inrepair_count = $inrepair_count;
            $cptmodel->pending_count = $pending_count;

            //Percentage Complete =  IO Approved + Pre QA + In Repair + Post QA  --- 8/7/2013
            //##$ongoing = $qa_count + $published_count + $approved_count + $pending_count + $inrepair_count;
            $ongoing = $qa_count + $published_count + $approved_count + $inrepair_count;

            $cptmodel->remaining_count = $cptmodel->total_count - $ongoing;
            if ($cptmodel->total_count > 0) {
                //We are not using percentage_done any more, the internal_done will be the finnal decision
                $cptmodel->internal_done = round($ongoing / $cptmodel->total_count, 3);
                $cptmodel->percentage_done = round($cptmodel->published_count / $cptmodel->total_count, 3);
                /*
                if ($this->iostatus == 5 || $this->iostatus == 31) {
                    $cptmodel->percentage_done = round($cptmodel->published_count / $cptmodel->total_count, 3);
                }
                */

                //###7/9/2014### greater than IO Approved AND title not null OR not = []
                //Please Pay Attention to \\\[ AND \\\] in PHP, one "\" for PHP, one for mysql parser, one for pattern lib.
                    //->select("*")->from('{{inventory_building_task}}')
                $cnttdone = Yii::app()->db->createCommand()
                    ->select("COUNT(*) AS content_count")->from('{{inventory_building_task}}')
                    ->where("`campaign_id`='".$this->campaign_id."' AND iostatus IN (3,31,32,5) AND LENGTH(rewritten_title)>5 AND `rewritten_title` NOT REGEXP '(\\\[.*\\\])+' ")
                    ->queryRow();
                //##print_r($cnttdone);
                if ($cnttdone && $cnttdone["content_count"] > 0 && $ongoing > 0) {
                    $cptmodel->content_done = round($cnttdone["content_count"] / $ongoing, 3);;
                } else {
                    $cptmodel->content_done = 0.00;
                }
            }
            $cptmodel->save();
        }
    }

    public function getSearchCriteria()
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

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.inventory_id',$this->inventory_id);
        if ($this->duty_campaign_ids && empty($this->campaign_id)) {
		    $criteria->compare('t.campaign_id',$this->duty_campaign_ids);
        } else {
            if (is_numeric($this->campaign_id) || is_array($this->campaign_id)) {
                $criteria->compare('t.campaign_id',$this->campaign_id);
            } else {
                $criteria->compare('rcampaign.name',$this->campaign_id, true);
            }
        }

        if ($this->campaign_name) {
            if (is_numeric($this->campaign_name)) {
                //http://www.yiiframework.com/wiki/199/creating-a-parameterized-like-query/
                $criteria->addCondition("t.campaign_id = :cmpid OR rcampaign.name LIKE :cmpname");
                $_cmpid = addcslashes($this->campaign_name, '%_');
                $criteria->params[':cmpid'] = $_cmpid;
                $criteria->params[':cmpname'] = "%$_cmpid%";
            } elseif(is_array($this->campaign_name)) {
                $criteria->compare('t.campaign_id',$this->campaign_name);
            } else {
                $criteria->compare('rcampaign.name',$this->campaign_name, true);
            }
        }

        if (is_numeric($this->campaign_approval_type)) {
            if ($this->campaign_approval_type == 1) {
                //####$criteria->compare('rcampaign.approval_type',$this->campaign_approval_type);
                $criteria->addCondition("rcampaign.approval_type > '0'");
            } elseif ($this->campaign_approval_type == 0) {
                $criteria->addCondition("(rcampaign.approval_type IS NULL) OR (rcampaign.approval_type = '')");
            }
        }


        if ($this->campaign_exclude) {
            if (is_numeric($this->campaign_exclude)) {
                //http://www.yiiframework.com/wiki/199/creating-a-parameterized-like-query/
                $criteria->addCondition("t.campaign_id != :cmpid OR rcampaign.name NOT LIKE :cmpname");
                $_cmpid = addcslashes($this->campaign_exclude, '%_');
                $criteria->params[':cmpid'] = $_cmpid;
                $criteria->params[':cmpname'] = "%$_cmpid%";
            } elseif(is_array($this->campaign_exclude)) {
                $criteria->addNotInCondition('t.campaign_id',$this->campaign_exclude);
            } else {
                $criteria->addCondition("rcampaign.name NOT LIKE :cmpname");
                $campaign_exclude = addcslashes($this->campaign_exclude, '%_');
                $criteria->params[':cmpname'] = "%$campaign_exclude%";
            }
        }

        if ($this->iostatus == 500) {
            $this->rebuild = 0;
            $criteria->compare('t.iostatus',5);
        } elseif ($this->iostatus == 501) {
            $this->rebuild = 1;
            $criteria->compare('t.iostatus',5);
        } else {
            $criteria->compare('t.iostatus',$this->iostatus);
        }

		$criteria->compare('t.domain',$this->domain,true);
		$criteria->compare('t.domain_id',$this->domain_id,true);
		$criteria->compare('t.anchortext',$this->anchortext,true);
		$criteria->compare('t.targeturl',$this->targeturl,true);
		$criteria->compare('t.sourceurl',$this->sourceurl,true);
		$criteria->compare('t.sourcedomain',$this->sourcedomain,true);
		$criteria->compare('t.title',$this->title,true);
		$criteria->compare('t.tasktype',$this->tasktype);
		$criteria->compare('t.taskstatus',$this->taskstatus);
		//##$criteria->compare('t.iostatus',$this->iostatus);
		$criteria->compare('t.assignee',$this->assignee);
		$criteria->compare('t.optional_keywords',$this->optional_keywords,true);
		$criteria->compare('t.mapping_id',$this->mapping_id,true);
		$criteria->compare('t.notes',$this->notes,true);
		$criteria->compare('t.content_article_id',$this->content_article_id);
		$criteria->compare('t.content_campaign_id',$this->content_campaign_id);
		$criteria->compare('t.content_category_id',$this->content_category_id);
		$criteria->compare('t.send2cpdate',$this->send2cpdate);
		$criteria->compare('t.checkouted',$this->checkouted);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);
		$criteria->compare('t.other',$this->other, true);
		//$criteria->compare('t.iodate',$this->iodate,true);
		//$criteria->compare('livedate',$this->livedate,true);
		$criteria->compare('t.siteonly',$this->siteonly);
		$criteria->compare('t.rebuild',$this->rebuild);
		$criteria->compare('t.publication_pending',$this->publication_pending);
		$criteria->compare('t.passed_iqa',$this->passed_iqa);
		$criteria->compare('t.assisted_by',$this->assisted_by);

        $_duedate = $this->duedate;
        if ($_duedate) $_duedate = Utils::smartDateSearch($_duedate);
		$criteria->compare('t.duedate', $_duedate, true);
		//$criteria->compare('t.duedate',$this->duedate, true);

        if(!empty($this->livedate) && !empty($this->livedate_end)) {
            $criteria->addBetweenCondition('t.livedate', $this->livedate, $this->livedate_end, "AND");
            //$criteria->params[':lstart'] = $this->livedate;
            //$criteria->params[':lend'] = $this->livedate_end;
            //$criteria->addCondition('livedate BETWEEN :lstart AND :lend');
            //$criteria->addCondition('(livedate >= :lstart AND livedate <= :lend)');
        } else {
		    $criteria->compare('t.livedate',$this->livedate,true);
        }

        if(!empty($this->iodate) && !empty($this->iodate_end)) {
            $criteria->addBetweenCondition('t.iodate', $this->iodate, $this->iodate_end, "AND");
        } else {
		    $criteria->compare('t.iodate',$this->iodate,true);
        }

		if ($this->tierlevel) {
            if ($_GET["Task"]["tieropr"]) $this->tierlevel = $_GET["Task"]["tieropr"].$this->tierlevel;
            $criteria->compare('t.tierlevel',$this->tierlevel);
        }
		if ($this->tierlevel_built) {
            if ($_GET["Task"]["tierbuiltopr"]) $this->tierlevel_built = $_GET["Task"]["tierbuiltopr"].$this->tierlevel_built;
            $criteria->compare('t.tierlevel_built',$this->tierlevel_built);
        }
        //$criteria->compare('tierlevel',$this->tierlevel);
		//$criteria->compare('tierlevel_built',$this->tierlevel_built);
		$criteria->compare('t.qa_comments',$this->qa_comments,true);
		$criteria->compare('t.channel_id',$this->channel_id);
        if (is_numeric($this->desired_domain_id)) {
		    $criteria->compare('t.desired_domain_id',$this->desired_domain_id);
        } else {
		    $criteria->compare('t.desired_domain',$this->desired_domain_id, true);
        }
		$criteria->compare('t.desired_domain',$this->desired_domain,true);
		$criteria->compare('t.rewritten_title',$this->rewritten_title,true);
		$criteria->compare('t.blog_title',$this->blog_title,true);
		$criteria->compare('t.blog_url',$this->blog_url,true);

		$criteria->compare('t.content_step',$this->content_step);
		$criteria->compare('t.step_date',$this->step_date,true);
		$criteria->compare('t.content_step_editor',$this->content_step_editor);

        if ($this->client_id) {
		    $criteria->compare('rcampaign.client_id',$this->client_id);
        }

        /*
        * - If campaign is hidden and the task is in IO Current, Accepted, or Pending, do not display on Content IO tasks.
        */
        /*
        //Moved into scopes already...
        if (strtolower(Yii::app()->controller->id) == "contentstep") {
		    //##$criteria->compare('rcampaign.ishidden',0);
		    $criteria->compare('t.iostatus',array(3,4,5,31,32));
        }
        */

        return $criteria;
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		return new CActiveDataProvider($this, array(
			//'criteria'=>$criteria,
            'criteria'=>$this->getSearchCriteria(),
            'sort'=>array(
                'attributes'=>array(
                    'campaign_name' => array(
                        'asc' => 'rcampaign.name ASC',
                        'desc' => 'rcampaign.name DESC',
                    ),
                    'client_id' => array(
                        'asc' => 'rcampaign.client_id ASC',
                        'desc' => 'rcampaign.client_id DESC',
                    ),
                    'campaign_approval_type' => array(
                        'asc' => 'rcampaign.approval_type ASC',
                        'desc' => 'rcampaign.approval_type DESC',
                    ),
                    '*',
                ),
            ),
		));
	}

    public function totalspent()
    {
        $criteria = $this->getSearchCriteria();
        $criteria->select = 'SUM(t.spent) AS total_spent';
        unset($criteria->with);

        if (($this->campaign_id && !is_numeric($this->campaign_id) && !is_array($this->campaign_id)) 
            || $this->client_id || $this->campaign_name || $this->campaign_exclude) {
            $criteria->join = "LEFT JOIN {{campaign}} rcampaign ON (rcampaign.id = t.campaign_id)";
        }

        $rs = Task::model()->find($criteria);
        if ($rs) {
            return $rs->total_spent;
        } else {
            return null;
        }

        //The queryScalar Way is only for singal table, 
        //if there are relationships in The AR model/criteria, then it wouldn't works
        //return $this->commandBuilder->createFindCommand($this->getTableSchema(),$criteria)->queryScalar();
    }

	/**
     * For reporting/channels
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
    public $init_count;
    public $qa_count;
    public $inrepair_count;
    public $published_count;
    public $accepted_count;
    public $pending_count;
    public $approved_count;
    public $current_count;
    public $total_count;
    public $remaining_count;
    //public $completed_count;
    public $denied_count;
    public $campaign_name;

	public function report()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
        $criteria=new CDbCriteria;
        //$criteria=new CDbCriteria(array('with'=>array('rcampaign')));

        if ($this->duty_campaign_ids && empty($this->campaign_id)) {
		    $criteria->compare('campaign_id',$this->duty_campaign_ids);
        } else {
            if (is_numeric($this->campaign_id) || is_array($this->campaign_id)) {
                $criteria->compare('campaign_id',$this->campaign_id);
            } else {
                $criteria->compare('rcampaign.name',$this->campaign_id, true);
            }
        }
        $criteria->compare('channel_id',$this->channel_id);

        if (isset($_REQUEST["groupby"]) && $_REQUEST["groupby"] == "campaign") {
            $select = "campaign_id, rcampaign.name AS campaign_name, ";
        } else {
            $select = "channel_id, ";
        }

        //COUNT( if(iostatus='5',true,null) ) AS completed_count, 
        $criteria->select = "$select COUNT( if(iostatus='0',true,null) ) AS init_count, COUNT(*) AS total_count,
            COUNT( if(iostatus='31',true,null) ) AS qa_count, COUNT( if(iostatus='32',true,null) ) AS inrepair_count,
            COUNT( if(iostatus='2',true,null) ) AS accepted_count, COUNT( if(iostatus='21',true,null) ) AS pending_count,
            COUNT( if(iostatus='4',true,null) ) AS denied_count, COUNT( if(iostatus='5',true,null) ) AS published_count,
            COUNT( if( (iostatus='0' OR iostatus='1' OR iostatus='2') ,true,null)) AS remaining_count,
            COUNT( if(iostatus='3',true,null) ) AS approved_count, COUNT( if(iostatus='1',true,null) ) AS current_count";

        //added 5/15/2014 for calling unhidden() of channels view. 
        $criteria->join = 'LEFT JOIN {{campaign}} rcampaign on rcampaign.id = t.campaign_id';
        if (isset($_REQUEST["groupby"]) && $_REQUEST["groupby"] == "campaign") {
            $criteria->group = 'campaign_id';
            //$criteria->join = 'LEFT JOIN {{campaign}} c on c.id = t.campaign_id';//comment out 5/15/2014
        } else {
            $criteria->group = 'channel_id';
        }

        //CSqlDataProvider
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'attributes'=>array(
                    'campaign_name' => array(
                        'asc' => 'campaign_name ASC',
                        'desc' => 'campaign_name DESC',
                    ),
                    'total_count' => array(
                        'asc' => 'total_count ASC',
                        'desc' => 'total_count DESC',
                    ),
                    'init_count' => array(
                        'asc' => 'init_count ASC',
                        'desc' => 'init_count DESC',
                    ),
                    'qa_count' => array(
                        'asc' => 'qa_count ASC',
                        'desc' => 'qa_count DESC',
                    ),
                    'inrepair_count' => array(
                        'asc' => 'inrepair_count ASC',
                        'desc' => 'inrepair_count DESC',
                    ),
                    'published_count' => array(
                        'asc' => 'published_count ASC',
                        'desc' => 'published_count DESC',
                    ),
                    'accepted_count' => array(
                        'asc' => 'accepted_count ASC',
                        'desc' => 'accepted_count DESC',
                    ),
                    'pending_count' => array(
                        'asc' => 'pending_count ASC',
                        'desc' => 'pending_count DESC',
                    ),
                    'approved_count' => array(
                        'asc' => 'approved_count ASC',
                        'desc' => 'approved_count DESC',
                    ),
                    'current_count' => array(
                        'asc' => 'current_count ASC',
                        'desc' => 'current_count DESC',
                    ),
                    'denied_count' => array(
                        'asc' => 'denied_count ASC',
                        'desc' => 'denied_count DESC',
                    ),
                    /*
                    'completed_count' => array(
                        'asc' => 'completed_count ASC',
                        'desc' => 'completed_count DESC',
                    ),
                    */
                    'remaining_count' => array(
                        'asc' => 'remaining_count ASC',
                        'desc' => 'remaining_count DESC',
                    ),
                    '*',
                ),
            ),
		));
	}
}