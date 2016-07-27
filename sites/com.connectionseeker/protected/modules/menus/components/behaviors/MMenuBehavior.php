<?php
/**
* Menus authorization item behavior class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.9.11
*/
class MMenuBehavior extends CBehavior
{
	/**
	* @property integer the id of the user to whom this item is assigned.
	*/
	public $userId;
	/**
	* @property CAuthItem the parent item.
	*/
	public $parent;
	/**
	* @property integer the amount of children this item has.
	*/
	public $childCount;

	/**
	* Constructs the behavior.
	* @param integer $userId the id of the user to whom this item is assigned
	* @param CAuthItem $parent the parent item.
	*/
	public function __construct( Menu $parent=null)
	{
		$this->parent = $parent;
	}

	/**
	* Returns the item name.
	* @return string the markup.
	*/
	public function getNameText()
	{
		return $this->owner->name ;
	}

	/**
	* Returns the link to update the item.
	* @return string the markup.
	*/
	public function getNameLink()
	{
		return CHtml::link($this->getNameText(), array(
			'tab/update',
			'id'=>urlencode($this->owner->id),
		));
	}
	
	/**
	* Returns the markup for the name link to displayed in the grid.
	* @return string the markup. 
	*/
	public function getGridNameLink()
	{
		$markup = CHtml::link($this->owner->name, array(
			'tab/update',
			'id'=>urlencode($this->owner->id),
		));

		$markup .= $this->childCount();
		$markup .= $this->sortableId();

		return $markup;
	}

	/**
	* Returns the markup for the child count.
	* @return string the markup.
	*/
	public function childCount()
	{
		if( $this->childCount===null )
			$this->childCount = count($this->owner->getChildren());
            $link = CHtml::link($this->childCount, array(
			'tab/menus',
			'pid'=>urlencode($this->owner->id),
		));
		return $this->childCount>0 ? ' [ <span class="child-count">'.$link.'</span> ]' : '';
	}

	/**
	* Returns the markup for the id required by jui sortable.
	* @return string the markup.
	*/
	public function sortableId()
	{
	 	return ' <span class="auth-item-name" style="display:none;">'.$this->owner->id.'</span>';
	}

	/**
	* Returns the markup for the item type.
	* @return string the markup.
	*/
	public function getTypeText()
	{
		return Menus::getMenuTypeName($this->owner->type);
	}

	/**
	* Returns the markup for the delete operation link.
	* @return string the markup.
	*/
	public function getDeleteLink()
	{
        $html = '';
        if ($this->owner->status == 1) {
            $html =  CHtml::linkButton(Menus::t('core', 'Remove'), array(
                'submit'=>array('tab/remove', 'id'=>urlencode($this->owner->id)),
                'confirm'=>Menus::t('core', 'Are you sure you want to remove this menu?'),
                'class'=>'delete-link',
                'csrf'=>Yii::app()->request->enableCsrfValidation,
            ));
        } else {
            $html = CHtml::linkButton(Menus::t('core', 'Active'), array(
                'submit'=>array('tab/active', 'id'=>urlencode($this->owner->id)),
                'confirm'=>Menus::t('core', 'Are you sure you want to re-active this menu?'),
                'class'=>'delete-link',
                'csrf'=>Yii::app()->request->enableCsrfValidation,
            ));
        }

        return $html;
	}

    public function getAddMenuLink()
    {
        $html = '';
        if ($this->owner->parent_id == 0) {
            $html = '&nbsp;' . CHtml::linkButton(Menus::t('core', 'Add Menu'), array(
                'submit'=>array('tab/create', 'pid'=>urlencode($this->owner->id)),
                // 'confirm'=>Menus::t('core', 'Are you sure you want to Add Custom menu for?'),
                'class'=>'add-link',
                'csrf'=>Yii::app()->request->enableCsrfValidation,
            ));
        }
        return $html;
    }

    public function getTabLink()
    {
        if ($this->owner->parent_id == 0) {
            if ($this->owner->is_tab == 1) {
                return CHtml::linkButton(Menus::t('core', 'Remove Tab'), array(
                    'submit'=>array('tab/removetab', 'id'=>urlencode($this->owner->id)),
                    'confirm'=>Menus::t('core', 'Are you sure you want to remove this menu from tab?'),
                    'class'=>'delete-link',
                    'csrf'=>Yii::app()->request->enableCsrfValidation,
                ));
            } else {
                return CHtml::linkButton(Menus::t('core', 'Add Tab'), array(
                    'submit'=>array('tab/addtab', 'id'=>urlencode($this->owner->id)),
                    'confirm'=>Menus::t('core', 'Are you sure you want to add this menu to tab?'),
                    'class'=>'delete-link',
                    'csrf'=>Yii::app()->request->enableCsrfValidation,
                ));
            }
        }
    }

	/**
	* Returns the markup for the remove child link.
	* @return string the markup.
	*/
	public function getRemoveChildLink()
	{
		return CHtml::linkButton(Menus::t('core', 'Remove'), array(
			'submit'=>array('tab/removeChild', 'id'=>urlencode($this->parent->id), 'child'=>urlencode($this->owner->id)),
			'class'=>'remove-link',
			'csrf'=>Yii::app()->request->enableCsrfValidation,
		));
	}

	/**
	* Returns the markup for the revoke assignment link.
	* @return string the markup.
	*/
	public function getRevokeAssignmentLink()
	{
		return CHtml::linkButton(Menus::t('core', 'Revoke'), array(
			'submit'=>array('assignment/revoke', 'id'=>$this->userId, 'name'=>urlencode($this->owner->name)),
			'class'=>'revoke-link',
			'csrf'=>Yii::app()->request->enableCsrfValidation,
		));
	}
	
	/**
	* Returns the markup for the revoke permission link.
	* @param CAuthItem $role the role the permission is for.
	* @return string the markup.
	*/
	public function getRevokePermissionLink(CAuthItem $role)
	{
		$csrf = Menus::getDataCsrf();
		
		return CHtml::link(Menus::t('core', 'Revoke'), '#', array(
			'onclick'=>"
				jQuery.ajax({
					type:'POST',
					url:'".Yii::app()->controller->createUrl('authItem/revoke', array(
						'name'=>urlencode($role->name), 
						'child'=>urlencode($this->owner->name),
					))."',
					data:{ ajax:1 $csrf },
					success:function() {
						$('#permissions').load('".Yii::app()->controller->createUrl('authItem/permissions')."', { ajax:1 $csrf });
					}
				});

				return false;				
			",
			'class'=>'revoke-link',
		));
	}

	/**
	* Returns the markup for the assign permission link.
	* @param CAuthItem $role the role the permission is for.
	* @return string the markup.
	*/
	public function getAssignPermissionLink(CAuthItem $role)
	{
		$csrf = Menus::getDataCsrf();
		
		return CHtml::link(Menus::t('core', 'Assign'), '#', array(
			'onclick'=>"
				jQuery.ajax({
					type:'POST',
					url:'".Yii::app()->controller->createUrl('authItem/assign', array(
						'name'=>urlencode($role->name), 
						'child'=>urlencode($this->owner->name),
					))."',
					data:{ ajax:1 $csrf },
					success:function() {
						$('#permissions').load('".Yii::app()->controller->createUrl('authItem/permissions')."', { ajax:1 $csrf });
					}
				});

				return false;				
			",
			'class'=>'assign-link',
		));
	}
	
	/**
	* Returns the markup for a inherited permission.
	* @param array $parents the parents for this item.
	* @param boolean $displayType whether to display the parent item type.
	* @return string the markup.
	*/
	public function getInheritedPermissionText($parents, $displayType=false)
	{
		$items = array();
		foreach( $parents as $itemName=>$item )
		{
			$itemMarkup = $item->getNameText();

			if( $displayType===true )
				$itemMarkup .= ' ('.Menus::getAuthItemTypeName($item->type).')';

			$items[] = $itemMarkup;
		}

		return '<span class="inherited-item" title="'.implode('<br />', $items).'">'.Menus::t('core', 'Inherited').' *</span>';
	}
}
