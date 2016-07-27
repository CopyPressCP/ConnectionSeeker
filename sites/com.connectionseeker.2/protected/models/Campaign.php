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
			array('client_id, domain_id, status, created_by, modified_by, duedate', 'numerical', 'integerOnly'=>true),
			array('name, domain', 'length', 'max'=>255),
			array('category, category_str, notes, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, domain, client_id, domain_id, category, category_str, notes, status, duedate, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
                'condition'=>'client_id='.$client_id,
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
			'status' => 'Active',
			'duedate' => 'Due Date',
			'created' => 'Created',
			'created_by' => 'Created By',
			'modified' => 'Modified',
			'modified_by' => 'Modified By',
			'percentage' => 'Percentage',
		);
	}

    /**
     * Prepares created, created_by, modified and
     * modified_by id attributes before performing validation.
     */
    protected function beforeValidate() {
        if (!empty($this->category)) {
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

        if (!empty($this->duedate)) $this->duedate = strtotime(str_replace("/", "-", $this->duedate));
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
		//$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.domain',$this->domain,true);
		$criteria->compare('t.client_id',$this->client_id);
		$criteria->compare('t.domain_id',$this->domain_id);
		$criteria->compare('t.category_str',$this->category_str,true);
		$criteria->compare('t.notes',$this->notes,true);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.duedate',$this->duedate);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.created_by',$this->created_by);
		$criteria->compare('t.modified',$this->modified,true);
		$criteria->compare('t.modified_by',$this->modified_by);

        /*
        //we can define these stuff in campaign model,
		$criteria->compare('rcampaigntask.total_count',$this->rcampaigntask->total_count);
		$criteria->compare('rcampaigntask.published_count',$this->rcampaigntask->published_count);
		$criteria->compare('rcampaigntask.percentage_done',$this->rcampaigntask->percentage_done);
        */

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
            'rcampaigntask.percentage_done'=>array(
                'asc'=>'rcampaigntask.percentage_done ASC',
                'desc'=>'rcampaigntask.percentage_done DESC',
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
}