<?php

/**
 * This is the model class for table "{{inventory_link}}".
 *
 * The followings are the available columns in table '{{inventory_link}}':
 * @property integer $id
 * @property integer $inventory_id
 * @property string $sourceurl
 * @property integer $campaign_id
 * @property string $targeturl
 * @property string $targetdomain
 * @property string $anchortext
 * @property integer $category_id
 * @property integer $tasktype_id
 * @property integer $status
 * @property integer $checked
 * @property string $notes
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Link extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Link the static model class
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
		return '{{inventory_link}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('inventory_id', 'required'),
			array('inventory_id, campaign_id, category_id, tasktype_id, status, checked, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('sourceurl, targeturl', 'length', 'max'=>500),
			array('targetdomain', 'length', 'max'=>255),
			array('anchortext, notes, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, inventory_id, sourceurl, campaign_id, targeturl, targetdomain, anchortext, category_id, tasktype_id, status, checked, notes, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
            'rinventory' => array(self::BELONGS_TO, 'Invneotry', 'inventory_id'),
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
			'inventory_id' => 'Inventory',
			'sourceurl' => 'Source URL',
			'campaign_id' => 'Campaign',
			'targeturl' => 'Target URL',
			'targetdomain' => 'Target Domain',
			'anchortext' => 'Anchor Text',
			'category_id' => 'Category',
			'tasktype_id' => 'Tasktype',
			'status' => 'Status',
			'checked' => 'Checked',
			'notes' => 'Notes',
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
            $created = time();
            $this->created = $this->modified = date('Y-m-d H:i:s', $created);
            if (!$this->added) $this->added = $created;
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
		$criteria->compare('sourceurl',$this->sourceurl,true);
		$criteria->compare('campaign_id',$this->campaign_id);
		$criteria->compare('targeturl',$this->targeturl,true);
		$criteria->compare('targetdomain',$this->targetdomain,true);
		$criteria->compare('anchortext',$this->anchortext,true);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('tasktype_id',$this->tasktype_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('checked',$this->checked);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}