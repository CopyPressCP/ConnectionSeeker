<?php

/**
 * This is the model class for table "{{ioblacklist}}".
 *
 * The followings are the available columns in table '{{ioblacklist}}':
 * @property integer $id
 * @property string $domain_id
 * @property string $domain
 * @property integer $isallclient
 * @property string $clients
 * @property string $clients_str
 * @property string $notes
 * @property integer $isblacklist
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Ioblacklist extends CActiveRecord
{
    public $upfile;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Ioblacklist the static model class
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
		return '{{ioblacklist}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('isallclient, isblacklist, created_by, modified_by', 'numerical', 'integerOnly'=>true),
			array('domain_id', 'length', 'max'=>20),
			array('domain', 'length', 'max'=>255),
			array('clients', 'length', 'max'=>500),
			array('clients_str', 'length', 'max'=>1000),
			array('notes, created, modified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain_id, domain, isallclient, clients, clients_str, notes, isblacklist, created, created_by, modified, modified_by', 'safe', 'on'=>'search'),
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
            //'rclient' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'rcreatedby' => array(self::BELONGS_TO, 'User', 'created_by'),
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
			'domain_id' => 'Domain ID',
			'domain' => 'Domain',
			'isallclient' => 'For All Client',
			'clients' => 'Clients',
			'clients_str' => 'Clients',
			'notes' => 'Reason',
			'isblacklist' => 'Is Blacklist',
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
        if (!empty($this->domain)) {
            Yii::import('application.vendors.*');
            $domain = SeoUtils::getSubDomain($this->domain);
            $domodel = new Domain;
            $dmdl = $domodel->findByAttributes(array('domain' => $domain));
            if (!empty($dmdl)) {
                $this->domain_id = $dmdl->id;
            } else {
                $domodel->domain = $domain;
                if ($domodel->save()) {
                    $this->domain_id = $domodel->id;
                }
            }
        }

        if (!empty($this->clients)) {
            if (is_array($this->clients)) {
                //cause we used the refid's value as the dropdown values.
                //$clients = Client::model()->actived()->findAllByPk(array_values($this->clients));
                $clients = Client::model()->findAllByPk(array_values($this->clients));
                $data = array();
                if ($clients) {
                    $data = CHtml::listData($clients, 'id', 'company');
                    if (!empty($data)) $this->clients_str = implode(", ", array_values($data));
                }
                $this->clients = "|".implode("|", array_values($this->clients))."|";
            }
        } else {
            $this->clients = "";
            $this->clients_str = "";
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
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('domain_id',$this->domain_id,true);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('isallclient',$this->isallclient);
		$criteria->compare('clients',$this->clients,true);
		$criteria->compare('clients_str',$this->clients_str,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('isblacklist',$this->isblacklist);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('modified_by',$this->modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}