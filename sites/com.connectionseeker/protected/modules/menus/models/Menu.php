<?php

/**
 * This is the model class for table "{{menus}}".
 *
 * The followings are the available columns in table '{{menus}}':
 * @property integer $id
 * @property string $name
 * @property string $itemname
 * @property string $url
 * @property string $img
 * @property integer $parent_id
 * @property string $mapping
 * @property integer $status
 */
class Menu extends CActiveRecord
{
	const STATUS_NOACTIVE=0;
	const STATUS_ACTIVE=1;
    const TYPE_OPERATION = 0;
    const TYPE_TASK = 1;
    const TYPE_TAB = 2;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Menu the static model class
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
		return '{{menus}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, itemname, url', 'required'),
			array('parent_id, status,type, is_tab', 'numerical', 'integerOnly'=>true),
			array('name, itemname, url, img, mapping', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, itemname, url, img, parent_id, mapping, status,type, is_tab', 'safe', 'on'=>'search'),
			array('name', 'required'),
			//array('url', 'unique',  'message' => Menus::t('Core',"This url already exists.")),
			array('name', 'nameIsAvailable', 'on'=>'create'),
			//array('name', 'newNameIsAvailable', 'on'=>'update'),
		);
	}

     public function scopes()
    {
         return array(
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE,
            ),
            'orderby' => array(
                'order' => 'ordering',
             ),
             'children' => array(
                'condition' =>'parent_id > 0',
             ),
            'notsafe'=>array(
            	'select' => 'id, name, itemname, parent_id, url, img, mapping,type, status, is_tab',
            ),
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
			'id'			=> Menus::t('core', 'ID'),
			'name'			=> Menus::t('core', 'Name'),
			'itemname'	=> Menus::t('core', 'Permission'),
			'url'		=> Menus::t('core', 'URL'),
			'img'			=> Menus::t('core', 'Image'),
			'status'			=> Menus::t('core', 'Status'),
			'parent_id'			=> Menus::t('core', 'Parent'),
			'mapping'			=> Menus::t('core', 'Mapping'),
			'type'			=> Menus::t('core', 'Type'),
		);
	}

	public function nameIsAvailable($attribute, $params)
	{
		// Make sure that an authorization item with the name does not already exist
        
		if( $this->getMenuByName($this->name) !==null )
			$this->addError('name', Menus::t('core', 'An item with this name already exists.', array(':name'=>$this->name)));
	}

	/**
	* Makes sure that the new name is available if the name been has changed.
	* This is the 'newNameIsAvailable' validator as declared in rules().
	*/
	public function newNameIsAvailable($attribute, $params)
	{
        $old_id = $this->id;
        $result = $this->getMenuByName($this->name);
		if( $result !==null && $old_id > 0 && $old_id <> $result->id)
			$this->nameIsAvailable($attribute, $params);
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('itemname',$this->itemname,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('mapping',$this->mapping,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

   function getMenuItem($itemname)
   {
       $result = $this->findByAttributes(array('itemname' => $itemname));
       return empty($result)? null : $result;
   }

    function getMenu($id)
    {
       $result = $this->findByPk($id);
       return empty($result)? null : $result;
    }

    function getMenuByName($name)
    {
       $result = $this->findByAttributes(array('name' => $name));
       return empty($result)? null : $result;
    }

   function getAllMenus()
   {
       $result = $this->findAll();
       $menus = array();
       foreach ($result as $k => $item) {
           $menus[$item->itemname] = $item;
       }
       return $menus;
    }

   function getMenus()
   {
       $criteria=new CDbCriteria;
       $criteria->addCondition('status' , 1);
       $result = $this->findAll($criteria);
       $menus = array();
       foreach ($result as $k => $item) {
           $menus[$item->itemname] = $item;
       }
       return $menus;
    }

//    public function getNameLink()
//	{
//		return CHtml::link($this->name, array('menu/update', 'id'=>$this->id));
//	}

    function getChildren()
    {
         $items = $this->getMenusByParentId($this->id);
         return $items;
    }

    function addItemChild($id, $children)
    {
        $this->attributes = $this->findByPk($children)->attributes;
        $this->parent_id = $id;
        $this->save();
    }

    function getMenusByParentId($parent_id, $is_tab = null)
    {
         $criteria=new CDbCriteria;
         $criteria->condition = 'parent_id=:parent';
         $criteria->params = array(':parent' => $parent_id);
         if ($is_tab !== null) {
             $criteria->condition .= '   AND is_tab=:is_tab';
             $criteria->params[':is_tab'] = $is_tab;
         }
         $criteria->order = 'ordering ASC';
         $result = $this->findAll($criteria);
         $items = array();
         foreach ($result as $row) {
            $items[$row->id] = $row;
         }
         return $items;
    }

    function getTypeMenus($type=null,   $parent_id = null, $sort=true)
    {
        $criteria=new CDbCriteria;
		// We need to sort the items.
		if( $sort===true ) {
             $criteria->condition = '1';
            if (strlen($parent_id)) {
                $criteria->condition .= ' AND parent_id=' . $parent_id;
            }

            if ($type) {
                if (is_array($type)) {
                    $criteria->condition .= '  AND type IN (' .implode(', ', $type) . ')';
                    //$criteria->addInCondition('type', $type);
                } else {
                    $criteria->condition .= '  AND type = ' . $type ;
                    // $criteria->addCondition('type', $type);
                }
            }
            if ($type === null) {
                $criteria->order = ' ordering ASC, type DESC ';
            } else {
                $criteria->order = '  type DESC, ordering ASC ';
            }
		}
        $result = $this->findAll($criteria);
        $items = array();
        foreach ($result as $row) {
            $items[$row->id] = $row;
        }
		return $items;
    }

    function getMenusByParams($param)
    {
        $result = $this->findAllByAttributes($param);
        $menus = array();
        foreach ($result as $k => $item) {
            $menus[$item->id]  = $item;
        }
        return $menus;
    }

	/*public function getMenus($type=null, $userId=null, $sort=true)
	{
		// We need to sort the items.
		if( $sort===true )
		{
			if( $type===null && $userId===null )
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->rightsTable} t2 ON name=itemname
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
			}
			else if( $userId===null )
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->rightsTable} t2 ON name=itemname
					WHERE t1.type=:type
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':type', $type);
			}
			else if( $type===null )
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->assignmentTable} t2 ON name=t2.itemname
					LEFT JOIN {$this->rightsTable} t3 ON name=t3.itemname
					WHERE userid=:userid
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':userid', $userId);
			}
			else
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->assignmentTable} t2 ON name=t2.itemname
					LEFT JOIN {$this->rightsTable} t3 ON name=t3.itemname
					WHERE t1.type=:type AND userid=:userid
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':type', $type);
				$command->bindValue(':userid', $userId);
			}

			$items = array();
			foreach($command->queryAll() as $row)
				$items[ $row['name'] ] = new CAuthItem($this, $row['name'], $row['type'], $row['description'], $row['bizrule'], unserialize($row['data']));
		}
		// No sorting required.
		else
		{
			$items = parent::getAuthItems($type, $userId);
		}

		return $items;
	}*/
}