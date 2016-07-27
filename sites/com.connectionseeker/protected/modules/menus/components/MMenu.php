<?php
/**
* Rights authorizer component class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.5
*/
class MMenu extends CApplicationComponent
{
	/**
	* @property string the name of the superuser role.
	*/
	public $superuserName;
	/**
	 * @property RDbAuthManager the authorization manager.
	 */
	private $_menu;

	/**
	* Initializes the authorizer.
	*/
	public function init()
	{
		parent::init();

		$this->_menu = new Menu;
	}


	/**
	* Returns the a list of all roles.
	* @param boolean $includeSuperuser whether to include the superuser.
	* @param boolean $sort whether to sort the items by their weights.
	* @return the roles.
	*/
	public function getRoles($includeSuperuser=true, $sort=true)
	{
		$exclude = $includeSuperuser===false ? array($this->superuserName) : array();
	 	$roles = $this->getAuthItems(CAuthItem::TYPE_ROLE, null, null, $sort, $exclude);
	 	$roles = $this->attachMenuBehavior($roles);
	 	return $roles;
	}

	/**
	* Creates an authorization item.
	* @param string $name the item name. This must be a unique identifier.
	* @param integer $type the item type (0: operation, 1: task, 2: role).
	* @param string $description the description for the item.
	* @param string $bizRule business rule associated with the item. This is a piece of
	* PHP code that will be executed when {@link checkAccess} is called for the item.
	* @param mixed $data additional data associated with the item.
	* @return CAuthItem the authorization item
	*/
	public function createAuthItem($name, $type, $description='', $bizRule=null, $data=null)
	{
		$bizRule = $bizRule!=='' ? $bizRule : null;

		if( $data!==null )
			$data = $data!=='' ? $this->sanitizeExpression($data.';') : null;

		return $this->_authManager->createAuthItem($name, $type, $description, $bizRule, $data);
	}

	/**
	* Updates an authorization item.
	* @param string $oldName the item name. This must be a unique identifier.
	* @param integer $name the item type (0: operation, 1: task, 2: role).
	* @param string $description the description for the item.
	* @param string $bizRule business rule associated with the item. This is a piece of
	* PHP code that will be executed when {@link checkAccess} is called for the item.
	* @param mixed $data additional data associated with the item.
	*/
	public function updateAuthItem($oldName, $name, $description='', $bizRule=null, $data=null)
	{
		$authItem = $this->_authManager->getAuthItem($oldName);
		$authItem->name = $name;
		$authItem->description = $description!=='' ? $description : null;
		$authItem->bizRule = $bizRule!=='' ? $bizRule : null;

		// Make sure that data is not already serialized.
		if( @unserialize($data)===false )
			$authItem->data = $data!=='' ? $this->sanitizeExpression($data.';') : null;

		$this->_authManager->saveAuthItem($authItem, $oldName);
	}

	/**
	 * Returns the authorization items of the specific type and user.
	 * @param mixed $types the item type (0: operation, 1: task, 2: role). Defaults to null,
	 * meaning returning all items regardless of their type.
	 * @param mixed $userId the user ID. Defaults to null, meaning returning all items even if
	 * they are not assigned to a user.
	 * @param Menu $parent the item for which to get the select options.
	 * @param boolean $sort sort items by to weights.
	 * @param array $exclude the items to be excluded.
	 * @return array the authorization items of the specific type.
	 */
	public function getMenus($types=null, Menu $parent=null,  $sort=true,  $exclude=array())
	{
		// We have none or a single type.
        if( $types === null &&($parent || strlen($parent)) ) {
            $pid = ($parent instanceof Menu)===false ? $parent : $parent->id;
            $types = null;
            $items = $this->_menu->getTypeMenus($types, $pid, $sort);
        }elseif( $types!==(array)$types ) {
            $pid = $parent === null ? $parent: (($parent instanceof Menu)===false ? $parent : $parent->id);
            $items = $this->_menu->getTypeMenus($types, $pid, $sort);
		}// We have multiple types.
		else
		{
			$typeItemList = array();
			foreach( $types as $type ) {
				$typeItemList[ $type ] = $this->_menu->getTypeMenus($type,  null, $sort);
            }

			// Merge the authorization items preserving the keys.
			$items = array();
			foreach( $typeItemList as $typeItems ) {
				$items = $this->mergeMenus($items, $typeItems);
            }
		}
 
		$items = $this->excludeInvalidMenus($items, $parent, $exclude);
		$items = $this->attachMenuBehavior($items,  $parent);

		return $items;
	}

    function getMenusByParendId($parent_id, $is_tab=null, $sort = true)
    {
        $items = $this->_menu->getMenusByParentId($parent_id, $is_tab);
        $items = $this->attachMenuBehavior($items, null);
        return $items;
    }

	/**
	* Merges two arrays with authorization items preserving the keys.
	* @param array $array1 the items to merge to.
	* @param array $array2 the items to merge from.
	* @return array the merged items.
	*/
	protected function mergeMenus($array1, $array2)
	{
		foreach( $array2 as $itemName=>$item )
			if( isset($array1[ $itemName ])===false )
				$array1[ $itemName ] = $item;

		return $array1;
	}

	/**
	* Excludes invalid authorization items.
	* When an item is provided its parents and children are excluded aswell.
	* @param array $items the authorization items to process.
	* @param CAuthItem $parent the item to check valid authorization items for.
	* @param array $exclude additional items to be excluded.
	* @return array valid authorization items.
	*/
	protected function excludeInvalidMenus($items, Menu $parent=null, $exclude=array())
	{
		// We are getting authorization items valid for a certain item
		// exclude its parents and children aswell.
		if( $parent!==null )
		{
		 	$exclude[] = $parent->id;
		 	foreach( $parent->getChildren() as $childId=>$child )
		 		$exclude[] = $childId;

		 	// Exclude the parents recursively to avoid inheritance loops.
		 	$parentIds = array_keys($this->getMenuParents($parent->id));
		 	$exclude = array_merge($parentIds, $exclude);
		}
        

		// Unset the items that are supposed to be excluded.
        if (is_array($exclude)) {
            foreach( $exclude as $id )
                if( isset($items[ $id ]) ) unset($items[ $id ]);
        }

		return $items;
	}

	/**
	* Returns the parents of the specified authorization item.
	* @param mixed $item the item name for which to get its parents.
	* @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to null,
	* meaning returning all items regardless of their type.
	* @param string $parentName the name of the item in which permissions to search.
	* @param boolean $direct whether we want the specified items parent or all parents.
	* @return array the names of the parent items.
	*/
	public function getMenuParents($item, $type=null, $parentId=null, $direct=false)
	{
		if( ($item instanceof Menu)===false )
			$item = $this->_menu->getMenu($item);
		$parents = $this->_menu->getMenusByParams(array('id' => $item->parent_id));
		$parents = $this->attachMenuBehavior($parents,  $item);

		if( $type!==null )
			foreach( $parents as $parentName=>$parent )
				if( (int)$parent->type!==$type )
					unset($parents[ $parentName ]);

		return $parents;
	}

	/**
	* Returns the parents of the specified authorization item recursively.
	* @param string $itemName the item name for which to get its parents.
	* @param array $items the items to process.
	* @param boolean $direct whether we want the specified items parent or all parents.
	* @return the names of the parents items recursively.
	*/
	private function getMenuParentsRecursive($menuId, $items, $direct)
	{
		$parents = array();
		foreach( $items as $childName=>$children )
		{
		 	if( $children!==array() )
		 	{
		 		if( isset($children[ $menuId ]) )
		 		{
		 			if( isset($parents[ $childName ])===false )
		 				$parents[ $childName ] = $childName;
				}
				else
				{
		 			if( ($p = $this->getMenuParentsRecursive($menuId, $children, $direct))!==array() )
		 			{
		 				if( $direct===false && isset($parents[ $childName ])===false )
		 					$parents[ $childName ] = $childName;

		 				$parents = array_merge($parents, $p);
					}
				}
			}
		}

		return $parents;
	}

	/**
	* Returns the children for the specified authorization item recursively.
	* @param mixed $item the item for which to get its children.
	* @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to null,
	* meaning returning all items regardless of their type.
	* @return array the names of the item's children.
	*/
	public function getMenuChildren($item, $type=null)
	{
		if( ($item instanceof Menu)===false )
			$item = $this->_menu->getMenu($item);

		$childrenIds = array();
		foreach( $item->getChildren() as $childName=>$child )
			if( $type===null || (int)$child->type===$type )
				$childrenIds[] = $childName;
        //print_r($childrenIds);exit();
		$children = $this->_menu->getMenusByParams(array('id' => $childrenIds));
		$children = $this->attachMenuBehavior($children, $item);
		return $children;
	}

	/**
	* Attaches the rights authorization item behavior to the given item.
	* @param mixed $items the item or items to which attach the behavior.
	* @param int $userId the ID of the user to which the item is assigned.
	* @param CAuthItem $parent the parent of the given item.
	* @return mixed the item or items with the behavior attached.
	*/
	public function attachMenuBehavior($items, Menu $parent=null)
	{
        //return $items;
		// We have a single item.
		if( $items instanceof Menu )
		{
			$items->attachBehavior('menus', new MMenuBehavior($parent));
		}
		// We have multiple items.
		else if( $items===(array)$items )
		{
			foreach( $items as $item )
				$item->attachBehavior('menus', new MMenuBehavior($parent));
		}

		return $items;
	}

    function updateItemOrdering($ids)
    {
        foreach ($ids as $ordering => $id)
        {
            $item = $this->_menu->getMenu($id);
            $item->ordering = $ordering;
            $item->save();
        }
    }


	/**
	* Returns the permissions for a specific authorization item.
	* @param string $itemName the name of the item for which to get permissions. Defaults to null,
	* meaning that the full permission tree is returned.
	* @return the permission tree.
	*/
	public function getPermissions($menuid=null)
	{
		$permissions = array();

		if( $menuid!==null )
		{
			$item = $this->_menu->getMenu($menuid);
			$permissions = $this->getPermissionsRecursive($item);
		}

		return $permissions;
	}

	/**
	* Returns the permissions for a specific authorization item recursively.
	* @param CAuthItem $item the item for which to get permissions.
	* @return array the section of the permissions tree.
	*/
	private function getPermissionsRecursive(Menu $item)
	{
		$permissions = array();
	 	foreach( $item->getChildren() as $childName=>$child )
	 	{
	 		$permissions[ $childName ] = array();
	 		if( ($grandChildren = $this->getPermissionsRecursive($child))!==array() )
				$permissions[ $childName ] = $grandChildren;
		}

		return $permissions;
	}

	/**
	* Returns the permission type for an authorization item.
	* @param string $itemName the name of the item to check permission for.
	* @param string $parentName the name of the item in which permissions to look.
	* @param array $permissions the permissions.
	* @return integer the permission type (0: None, 1: Direct, 2: Inherited).
	*/
	public function hasPermission($itemName, $parentName=null, $permissions=array())
	{
		if( $parentName!==null )
		{
			if( $parentName===$this->superuserName )
				return 1;

			$permissions = $this->getPermissions($parentName);
		}

		if( isset($permissions[ $itemName ]) )
			return 1;

		foreach( $permissions as $children )
			if( $children!==array() )
				if( $this->hasPermission($itemName, null, $children)>0 )
					return 2;

		return 0;
	}

	/**
	* Tries to sanitize code to make it safe for execution.
	* @param string $code the code to be execute.
	* @return mixed the return value of eval() or null if the code was unsafe to execute.
	*/
	protected function sanitizeExpression($code)
	{
		// Language consturcts.
		$languageConstructs = array(
			'echo',
			'empty',
			'isset',
			'unset',
			'exit',
			'die',
			'include',
			'include_once',
			'require',
			'require_once',
		);

		// Loop through the language constructs.
		foreach( $languageConstructs as $lc )
			if( preg_match('/'.$lc.'\ *\(?\ *[\"\']+/', $code)>0 )
				return null; // Language construct found, not safe for eval.

		// Get a list of all defined functions
		$definedFunctions = get_defined_functions();
		$functions = array_merge($definedFunctions['internal'], $definedFunctions['user']);

		// Loop through the functions and check the code for function calls.
		// Append a '(' to the functions to avoid confusion between e.g. array() and array_merge().
		foreach( $functions as $f )
			if( preg_match('/'.$f.'\ *\({1}/', $code)>0 )
				return null; // Function call found, not safe for eval.

		// Evaluate the safer code
		$result = @eval($code);

		// Return the evaluated code or null if the result was false.
		return $result!==false ? $result : null;
	}

	/**
	* @return RAuthManager the authorization manager.
	*/
	public function getAuthManager()
	{
		return $this->_authManager;
	}

    function checkURLVisible($permission, $user_id = null)
    {
        if ($user_id === null)  {
            $user_id = Yii::app()->user->id;
        }
        $params = array('userid' => $user_id);
        $taskPermission = $this->getTaskPermision($permission);
        $visible =  Yii::app()->user->checkAccess($taskPermission, $params) || Yii::app()->user->checkAccess($permission, $params);
        //$visible =  Yii::app()->user->checkAccess($permission, $params);
         return $visible;
     }

    public function getTabs()
    {
        $result = Menu::model()->active()->orderby()->findAllByAttributes(array('parent_id' => 0, 'is_tab'=>1));
        $arr = Menu::model()->active()->orderby()->children()->findAllByAttributes(array( 'is_tab'=>0));
        $children = $activeMenus = array();
        
        foreach ($arr as $child) {
           $visible =  $this->checkURLVisible($child->itemname);
           $parent_id = $child->parent_id;
           $url = trim(strtolower($child->url), '/');
           if ($visible) {
               if (!isset($children[$parent_id])) {
                   $children[$parent_id] = $child;
                   $activeMenus[$parent_id] = array($url);
               } else {
                   $activeMenus[$parent_id][] = $url;
               }
           }
        }
        $tabs = array();
        foreach ($result as $k => $item) {
            $permission = $item->itemname;
            $check =  $this->checkURLVisible($permission);
            $itemId = $item->id;
            if (!$check) {
                if (isset($children[$itemId])) {
                    $firstItem = $children[$itemId];
                    $permission = $firstItem->itemname;
                    $check = $this->checkURLVisible($permission);
                } else {
                    $firstItem = null;
                }
            } else {
                unset($firstItem);// this is very important!!!
            }

            if ($check) {
                $arr = array();
                $arr['activeMenus'] = !empty($item->mapping) ? explode(',', strtolower($item->mapping)) : array();
                if (isset($activeMenus[$itemId])) {
                    $arr['activeMenus'] += $activeMenus[$itemId];
                    $arr['activeMenus'] = array_unique($arr['activeMenus']);
                }
                $arr['activeMenus'][] =  trim(strtolower($item->url), '/');
                $arr['name'] = $item->name;
                $url = isset($firstItem) ? $firstItem->url : $item->url;
                $arr['url'] = array($url);
                if (!empty($item->img)) {
                    $tmp = explode(".", $item->img);
                    $arr['img'] = array('name'=> $item->img, 'htmlOptions' => array('id'=> $tmp[0], 'name' => $tmp[0]));
                } else {
                    $img = str_replace('.', '', $permission);
                    $arr['img'] = array('name'=>'none.png', 'htmlOptions' => array('id'=> $img, 'name' => $img));
                }
                $tabs[] = $arr;
            }
        }
        return $tabs;
    }
    public function getSubMenus()
    {
        $url =  Yii::app()->request->getUrl();
        $url_arr = parse_url($url);
        if (isset($url_arr['query'])) {
            parse_str($url_arr['query'], $qry);
            if (!isset($qry['r'])) $qry['r'] = 'site';
            $url = '/'. $qry['r'];
        }
        
        $result = Menu::model()->active()->orderby()->findByAttributes(array('url' => $url));
        //print_r($result->attributes);
        if (empty($result)) {
            $url_array = explode('/', trim($url,'/'));
            if (count($url_array) > 1) {
                $temp_url = '/' . $url_array[0];
                $result = Menu::model()->active()->orderby()->findByAttributes(array('url' => $temp_url));
                if (empty($result)) {
                    $temp_url = '/' . $url_array[0] . '/' . $url_array[1];
                    $result = Menu::model()->active()->orderby()->findByAttributes(array('url' => $temp_url));
                }
            }
            if (empty($result)) {
                $result = Menu::model()->active()->orderby()->findByAttributes(array('parent_id' => 0));
            }
        }
        $menus = array();
        //print_r($result->attributes);

        if ($result) {
            $id =$result->parent_id > 0 ?  $result->parent_id : $result->id;
            $result = Menu::model()->active()->orderby()->findAllByAttributes(array('parent_id' => $id));

            $action = Yii::app()->controller->action->id;
            $action = strtolower($action);
            if (empty($result) && in_array($action, array("view", "update", "processing"))) {
                if (Yii::app()->controller->id == "task" && $action == "processing") {
                    $url = "/campaign/index";
                } else {
                    $url = preg_replace('/(\w+)\/(\w+)$/', '$1/index', $url);
                }
                //die($url);
                //get the signal menu's attributes
                $result = Menu::model()->active()->orderby()->findByAttributes(array('url' => $url));
                if ($result) {
                    $id =$result->parent_id > 0 ?  $result->parent_id : $result->id;
                    //get the menu's parent's submenus
                    $result = Menu::model()->active()->orderby()->findAllByAttributes(array('parent_id' => $id));
                }
            }

            //fixed parent menu is empty
            $count = count(explode("/", $url));
            $u1 = preg_replace('/(\w+)(\/(\w+))+/', '$1', $url);
            //exception for Right module & Menu module & template, this exception just for Connection Seeker.
            if ($u1 == '/template') $u1 = "/mailer";

            if (empty($result) && $count > 1) {
                //$url = preg_replace('/(\w+)(\/(\w+))+/', '$1', $url);
                //get the signal menu's attributes
                $result = Menu::model()->active()->orderby()->findByAttributes(array('url' => $u1));
                if ($result) {
                    $id =$result->parent_id > 0 ?  $result->parent_id : $result->id;
                    //get the menu's parent's submenus
                    $result = Menu::model()->active()->orderby()->findAllByAttributes(array('parent_id' => $id));
                }
            }

            if ($result) {
                foreach ($result as $k => $item) {
                    if ($this->checkURLVisible($item->itemname)) {
                        //exception for Right module & Menu module & template, this exception just for Connection Seeker. 
                        //$u1 = preg_replace('/(\w+)(\/(\w+))+/', '$1', $url);
                        $u2 = preg_replace('/(\w+)(\/(\w+))+/', '$1', $item->url);
                        //if ($item->url == $url)  {
                        if ($item->url == $url 
                         || (in_array($u1, array('/rights','/menus')) && $u1 == $u2) 
                         || ($u1 == '/mailer' && $u1 == $u2))  {//exception for connection seeker system!!!
                            $menus[] = array(
                                'label' => $item->name,
                                'url' => array($item->url),
                                'active' => true,
                            );
                        } else {
                            $menus[] = array(
                                'label' => $item->name,
                                'url' => array($item->url),
                            );
                        }
                    }
                }
            }
        }
        //print_r($menus);

        return $menus;
    }

    function getTaskPermision($name)
    {
        $pos = strpos($name, '*');
         if ($pos===false) {
            $arr = explode('.',  $name);
            $name = $arr[0]. '.*';
            return $name;
         }
         return null;
    }

    function generateUrl($name)
    {
        $arr = explode('.',  trim($name, '.*'));
        foreach ($arr as $k => $v) {
            $v = trim($v);
            if ($v=='*') {
                unset($arr[$k]);
            } else {
                //lcfirst only for php5.3, so we couldn't use this function.
                /*
                if ( false === function_exists('lcfirst') ):
                    function lcfirst( $str ){
                        return (string)(strtolower(substr($str,0,1)).substr($str,1));
                    }
                endif; 
                */
                //$arr[$k] = lcfirst($v);
                $v{0} = strtolower($v{0});
                $arr[$k] = $v;
            }
        }
        return '/'. implode('/' , $arr);
    }

    function generatePermission($url)
    {
        $pos = strpos($url, '&');
        if ($pos!==false) {
            $url = substr($url, 0, strpos($url, '&'));
        }
        $name = ucwords(trim(str_replace("/", '.',  $url), '.'));
        $arr = explode('.', $name);
        if (count($arr) == 1) {
            $name .= '.*';
        } else {
            foreach ($arr as $k=> $v) {
                $arr[$k] = ucwords($v);
            }
            $name = implode('.', $arr);
        }
        return $name;
    }
}
