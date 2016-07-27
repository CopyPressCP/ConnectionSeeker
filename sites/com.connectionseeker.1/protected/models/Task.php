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

    public static $status = array('0' => 'NaN',
                           '1' => 'Unassigned',
                           '2' => 'Writing',
                           '3' => 'Pending Review',
                           '4' => 'Completed',
                           '5' => 'Link Completed',);

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
			array('inventory_id, campaign_id, tasktype, assignee, duedate, content_article_id, content_campaign_id, content_category_id, send2cpdate, checkouted, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('domain, targeturl, sourceurl, sourcedomain, mapping_id', 'length', 'max'=>255),
			array('domain_id', 'length', 'max'=>20),
			array('taskstatus', 'length', 'max'=>50),
			array('anchortext, title, optional_keywords, notes, created, modified, inventory_ids, client_id, style_guide', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, inventory_id, campaign_id, domain, domain_id, anchortext, targeturl, sourceurl, sourcedomain, title, tasktype, taskstatus, assignee, optional_keywords, mapping_id, notes, duedate, content_article_id, content_campaign_id, content_category_id, send2cpdate, checkouted, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'inventory_id' => 'Inventory',
			'campaign_id' => 'Campaign',
			'domain' => 'Domain',
			'domain_id' => 'Domain',
			'anchortext' => 'Anchortext',
			'targeturl' => 'Targeturl',
			'sourceurl' => 'Sourceurl',
			'sourcedomain' => 'Sourcedomain',
			'title' => 'Title',
			'tasktype' => 'Task Type',
			'taskstatus' => 'Status',
			'assignee' => 'Assignee',
			'optional_keywords' => 'Optional Keywords',
			'mapping_id' => 'Mapping ID',
            'optionalkw1' => 'Optional Keyword 1',
            'optionalkw2' => 'Optional Keyword 2',
            'optionalkw3' => 'Optional Keyword 3',
            'optionalkw4' => 'Optional Keyword 4',
            'style_guide' => 'Style Guide',
			'notes' => 'Notes',
			'duedate' => 'Due Date',
			'content_article_id' => 'ArticleID',
			'content_campaign_id' => 'Content Campaign',
			'content_category_id' => 'Content Category',
			'send2cpdate' => 'Send2cpdate',
			'checkouted' => 'Checkouted',
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
 
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = $this->modified = new CDbExpression('NOW()');
            $this->created = $this->modified = date('Y-m-d H:i:s');
            $this->created_by = $this->modified_by = Yii::app()->user->id;
        } else {
            //not a new record, so just set the last updated time and last updated user id
            //$this->update_time = new CDbExpression('NOW()');
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
		$criteria->compare('inventory_id',$this->inventory_id);
		$criteria->compare('campaign_id',$this->campaign_id);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('anchortext',$this->anchortext,true);
		$criteria->compare('targeturl',$this->targeturl,true);
		$criteria->compare('sourceurl',$this->sourceurl,true);
		$criteria->compare('sourcedomain',$this->sourcedomain,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('tasktype',$this->tasktype);
		$criteria->compare('taskstatus',$this->taskstatus,true);
		$criteria->compare('assignee',$this->assignee);
		$criteria->compare('optional_keywords',$this->optional_keywords,true);
		$criteria->compare('mapping_id',$this->mapping_id,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('duedate',$this->duedate);
		$criteria->compare('content_article_id',$this->content_article_id);
		$criteria->compare('content_campaign_id',$this->content_campaign_id);
		$criteria->compare('content_category_id',$this->content_category_id);
		$criteria->compare('send2cpdate',$this->send2cpdate);
		$criteria->compare('checkouted',$this->checkouted);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}