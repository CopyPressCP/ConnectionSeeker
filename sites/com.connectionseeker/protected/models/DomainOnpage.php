<?php

/**
 * This is the model class for table "{{domain_onpage}}".
 *
 * The followings are the available columns in table '{{domain_onpage}}':
 * @property string $id
 * @property string $domain_id
 * @property string $domain
 * @property string $contacttwitter
 * @property string $contactfacebook
 * @property string $wordpress_registration
 * @property string $wordpress
 * @property string $writeforus
 * @property string $drupal
 * @property string $contacturl
 * @property string $contactemail
 * @property string $magento
 * @property string $blog
 * @property string $sitemap
 * @property string $english
 * @property string $lastcrawled
 */
class DomainOnpage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DomainOnpage the static model class
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
		return '{{domain_onpage}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain_id', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			array('contacttwitter, contactfacebook, wordpress_registration, wordpress, writeforus, contacturl, contactemail, magento, blog, sitemap', 'length', 'max'=>1000),
			array('drupal', 'length', 'max'=>2000),
			array('english', 'length', 'max'=>50),
			array('lastcrawled', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, domain, contacttwitter, contactfacebook, wordpress_registration, wordpress, writeforus, drupal, contacturl, contactemail, magento, blog, sitemap, english, lastcrawled', 'safe', 'on'=>'search'),
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
			'domain_id' => 'Domain',
			'domain' => 'Domain',
			'contacttwitter' => 'Contacttwitter',
			'contactfacebook' => 'Contactfacebook',
			'wordpress_registration' => 'Wordpress Registration',
			'wordpress' => 'Wordpress',
			'writeforus' => 'Writeforus',
			'drupal' => 'Drupal',
			'contacturl' => 'Contacturl',
			'contactemail' => 'Contactemail',
			'magento' => 'Magento',
			'blog' => 'Blog',
			'sitemap' => 'Sitemap',
			'english' => 'English',
			'lastcrawled' => 'Lastcrawled',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('contacttwitter',$this->contacttwitter,true);
		$criteria->compare('contactfacebook',$this->contactfacebook,true);
		$criteria->compare('wordpress_registration',$this->wordpress_registration,true);
		$criteria->compare('wordpress',$this->wordpress,true);
		$criteria->compare('writeforus',$this->writeforus,true);
		$criteria->compare('drupal',$this->drupal,true);
		$criteria->compare('contacturl',$this->contacturl,true);
		$criteria->compare('contactemail',$this->contactemail,true);
		$criteria->compare('magento',$this->magento,true);
		$criteria->compare('blog',$this->blog,true);
		$criteria->compare('sitemap',$this->sitemap,true);
		$criteria->compare('english',$this->english,true);
		$criteria->compare('lastcrawled',$this->lastcrawled,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}