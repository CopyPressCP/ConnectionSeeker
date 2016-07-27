<?php

/**
 * This is the model class for table "{{copypress_content}}".
 *
 * The followings are the available columns in table '{{copypress_content}}':
 * @property integer $id
 * @property string $title
 * @property integer $length
 * @property string $text
 * @property string $html
 * @property integer $downloaded
 */
class Content extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Content the static model class
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
		return '{{copypress_content}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, length, downloaded', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>500),
			array('text, html', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, length, text, html, downloaded', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'length' => 'Length',
			'text' => 'Text',
			'html' => 'Html',
			'downloaded' => 'Downloaded',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('length',$this->length);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('html',$this->html,true);
		$criteria->compare('downloaded',$this->downloaded);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}