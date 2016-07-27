<?php

/**
 * This is the model class for table "{{blogger_program_price}}".
 *
 * The followings are the available columns in table '{{blogger_program_price}}':
 * @property integer $id
 * @property string $blogger_program_id
 * @property string $domain_id
 * @property string $domain
 * @property string $price
 * @property string $memo
 * @property string $created
 * @property integer $created_by
 */
class BloggerProgramPrice extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return BloggerProgramPrice the static model class
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
		return '{{blogger_program_price}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_by', 'numerical', 'integerOnly'=>true),
			array('blogger_program_id, domain_id', 'length', 'max'=>20),
			array('domain, price', 'length', 'max'=>255),
			array('memo, created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, blogger_program_id, domain_id, domain, price, memo, created, created_by', 'safe', 'on'=>'search'),
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
            'rdomain' => array(self::BELONGS_TO, 'Domain', 'domain_id'),
            'rbloggerprogram' => array(self::BELONGS_TO, 'BloggerProgram', 'blogger_program_id'),
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
			'blogger_program_id' => 'Blogger Program',
			'domain_id' => 'Domain',
			'domain' => 'Domain',
			'price' => 'Price',
			'memo' => 'Memo',
			'created' => 'Created',
			'created_by' => 'Created By',
		);
	}

    /**
     * Prepares salt, create_time, create_user_id, update_time and
     * update_user_ id attributes before performing validation.
     */
    protected function beforeValidate() {
 
        if ($this->isNewRecord) {
            // set the create date, last updated date, then the user doing the creating
            // $this->created = new CDbExpression('NOW()');
            $this->created =  date('Y-m-d H:i:s');
            $this->created_by = Yii::app()->user->id;
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
		$criteria->compare('blogger_program_id',$this->blogger_program_id,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('memo',$this->memo,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}