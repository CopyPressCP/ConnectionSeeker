<?php
/**
* Authorization item form class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.5
*/
class MenuForm extends CFormModel
{
	public $id;
	public $name;
	public $itemname;
	public $url;
	public $img;
	public $parent_id;
	public $is_tab;
	public $mapping;
	public $type;
	public $status;

	/**
	* Declares the validation rules.
	*/
	public function rules()
	{
        //return Menu::model()->rules();
		return array(
			array('name, itemname, url', 'required'),
			array('parent_id, status,type,is_tab', 'numerical', 'integerOnly'=>true),
			array('name, itemname, url, img, mapping', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, itemname, url, img, parent_id, mapping, status, type', 'safe', 'on'=>'search'),
			array('name', 'required'),
			array('name', 'nameIsAvailable', 'on'=>'create'),
			array('name', 'newNameIsAvailable', 'on'=>'update'),
			// array('name', 'isSuperuser', 'on'=>'update'),
		);
	}

	/**
	* Declares attribute labels.
	*/
	public function attributeLabels()
	{
        //return Menu::model()->attributeLabels();
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

	/**
	* Makes sure that the name is available.
	* This is the 'nameIsAvailable' validator as declared in rules().
	*/
	public function nameIsAvailable($attribute, $params)
	{
		// Make sure that an authorization item with the name does not already exist
        return Menu::model()->nameIsAvailable($attribute, $params);
	}

	/**
	* Makes sure that the new name is available if the name been has changed.
	* This is the 'newNameIsAvailable' validator as declared in rules().
	*/
	public function newNameIsAvailable($attribute, $params)
	{
        return Menu::model()->newNameIsAvailable($attribute, $params);
	}
}

