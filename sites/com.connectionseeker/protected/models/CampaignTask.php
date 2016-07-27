<?php

/**
 * This is the model class for table "{{campaign_task}}".
 *
 * The followings are the available columns in table '{{campaign_task}}':
 * @property integer $id
 * @property integer $campaign_id
 * @property string $keyword
 * @property string $targeturl
 */
class CampaignTask extends CActiveRecord
{
    public $kwcount;
    public $kwexistcount;
    public $tierlevel = 20;
    public static $tier = array('30' => 'Attention',//Authority
                           '20' => 'Traffic',//Tier 1
                           '10' => 'Budget',//Tier 2
                           '40' => 'Authority',//new one
                           '50' => 'Tier 1',//start from 50, there are added 7/18/2014.
                           '60' => 'Tier 2',
                           '70' => 'Private Network',
                           '80' => 'Social Bookmarking',
                           '90' => 'Test Tier',);

    /*
    public $tierlevel = 1;
    public static $tier = array('0' => 'Attention',//Authority
                           '1' => 'Traffic',//Tier 1
                           '2' => 'Budget',//Tier 2
                           '3' => 'Authority',);//new one
    */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CampaignTask the static model class
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
		return '{{campaign_task}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('campaign_id', 'required'),
			array('campaign_id, total_count, published_count', 'numerical', 'integerOnly'=>true),
			array('keyword, targeturl, kwcount, tierlevel, percentage_done, qa_count, published_count, inrepair_count, remaining_count, approved_count, content_done', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, campaign_id, keyword, targeturl, total_count, percentage_done, qa_count, published_count, inrepair_count, remaining_count, approved_count, content_done', 'safe', 'on'=>'search'),
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
			'campaign_id' => 'Campaign',
			'keyword' => 'Anchor Text',
			'targeturl' => 'Target URL',
			'kwcount' => 'Count',
			'tierlevel' => 'Tier Level',
			'total_count' => 'Total Count',
			'published_count' => 'Published Count',
			'approved_count' => 'Site Acquired',
			'pending_count' => 'Pending',
			'qa_count' => 'Pre QA',
			'inrepair_count' => 'In Repair',
			'remaining_count' => 'Remaining',
			'percentage_done' => 'Percentage Done',//this for client view
			'internal_done' => 'Percentage Done',//This for admin/internal team's view
			'content_done' => 'Content Done',//This for admin/internal team's view
		);
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
		$criteria->compare('campaign_id',$this->campaign_id);
		$criteria->compare('total_count',$this->total_count);
		$criteria->compare('published_count',$this->published_count);
		$criteria->compare('pending_count',$this->pending_count);
		$criteria->compare('approved_count',$this->approved_count);
		$criteria->compare('qa_count',$this->qa_count);
		$criteria->compare('inrepair_count',$this->inrepair_count);
		$criteria->compare('remaining_count',$this->remaining_count);
		$criteria->compare('percentage_done',$this->percentage_done);
		$criteria->compare('content_done',$this->content_done);
		$criteria->compare('internal_done',$this->internal_done);
		$criteria->compare('keyword',$this->keyword,true);
		$criteria->compare('targeturl',$this->targeturl,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}