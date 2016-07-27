<?php
/**
 * CMenu class file.
 *
 * @author Jonah Turnquist <poppitypop@gmail.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CMenu displays a multi-level menu using nested HTML lists.
 *
 * The main property of CMenu is {@link items}, which specifies the possible items in the menu.
 * A menu item has three main properties: visible, active and items. The "visible" property
 * specifies whether the menu item is currently visible. The "active" property specifies whether
 * the menu item is currently selected. And the "items" property specifies the child menu items.
 *
 * The following example shows how to use CMenu:
 * <pre>
 * $this->widget('application.extensions.lkmenu.LKMenu', array(
 *     'items'=>array(
 *         // Important: you need to specify url as 'controller/action',
 *         // not just as 'controller' even if default acion is used.
 *         array('label'=>'Home', 'url'=>array('site/index')),
 *         array('label'=>'Products', 'url'=>array('product/index'), 'items'=>array(
 *             array('label'=>'New Arrivals', 'url'=>array('product/new', 'tag'=>'new')),
 *             array('label'=>'Most Popular', 'url'=>array('product/index', 'tag'=>'popular')),
 *         )),
 *         array('label'=>'Login', 'url'=>array('site/login'), 'visible'=>Yii::app()->user->isGuest),
 *     ),
 * ));
 * </pre>
 *
 *
 * @author Jonah Turnquist <poppitypop@gmail.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CMenu.php 3204 2011-05-05 21:36:32Z alexander.makarow $
 * @package zii.widgets
 * @since 1.1
 */
class LKMenu extends CWidget
{
	/**
	 * @var array list of menu items. Each menu item is specified as an array of name-value pairs.
	 * Possible option names include the following:
	 * <ul>
	 * <li>label: string, optional, specifies the menu item label. When {@link encodeLabel} is true, the label
	 * will be HTML-encoded. If the label is not specified, it defaults to an empty string.</li>
	 * <li>url: string or array, optional, specifies the URL of the menu item. It is passed to {@link CHtml::normalizeUrl}
	 * to generate a valid URL. If this is not set, the menu item will be rendered as a span text.</li>
	 * <li>visible: boolean, optional, whether this menu item is visible. Defaults to true.
	 * This can be used to control the visibility of menu items based on user permissions.</li>
	 * <li>items: array, optional, specifies the sub-menu items. Its format is the same as the parent items.</li>
	 * <li>active: boolean, optional, whether this menu item is in active state (currently selected).
	 * If a menu item is active and {@link activeClass} is not empty, its CSS class will be appended with {@link activeClass}.
	 * If this option is not set, the menu item will be set active automatically when the current request
	 * is triggered by {@link url}. Note that the GET parameters not specified in the 'url' option will be ignored.</li>
	 * <li>template: string, optional, the template used to render this menu item.
	 * When this option is set, it will override the global setting {@link itemTemplate}.
	 * Please see {@link itemTemplate} for more details. This option has been available since version 1.1.1.</li>
	 * <li>linkOptions: array, optional, additional HTML attributes to be rendered for the link or span tag of the menu item.</li>
	 * <li>itemOptions: array, optional, additional HTML attributes to be rendered for the container tag of the menu item.</li>
	 * <li>submenuOptions: array, optional, additional HTML attributes to be rendered for the container of the submenu if this menu item has one.
	 * When this option is set, the {@link submenuHtmlOptions} property will be ignored for this particular submenu.
	 * This option has been available since version 1.1.6.</li>
	 * </ul>
	 */
	public $items = array();
    public $activeItem = array();
	/**
	 * @var string the template used to render an individual menu item. In this template,
	 * the token "{menu}" will be replaced with the corresponding menu link or text.
	 * If this property is not set, each menu will be rendered without any decoration.
	 * This property will be overridden by the 'template' option set in individual menu items via {@items}.
	 * @since 1.1.1
	 */
	public $itemTemplate="{menu}";
	/**
	 * @var boolean whether the labels for menu items should be HTML-encoded. Defaults to true.
	 */
	public $encodeLabel=false;
	/**
	 * @var string the CSS class to be appended to the active menu item. Defaults to 'active'.
	 * If empty, the CSS class of menu items will not be changed.
	 */
	public $activeCssClass='active';
	/**
	 * @var boolean whether to automatically activate items according to whether their route setting
	 * matches the currently requested route. Defaults to true.
	 * @since 1.1.3
	 */
	public $activateItems=true;
	/**
	 * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
	 * The activated parent menu items will also have its CSS classes appended with {@link activeCssClass}.
	 * Defaults to false.
	 */
	public $activateParents=false;
	/**
	 * @var boolean whether to hide empty menu items. An empty menu item is one whose 'url' option is not
	 * set and which doesn't contain visible child menu items. Defaults to true.
	 */
	public $hideEmptyItems=true;
	/**
	 * @var array HTML attributes for the menu's root container tag
	 */
	public $htmlOptions=array();
	/**
	 * @var array HTML attributes for the submenu's container tag.
	 */
	public $submenuHtmlOptions=array();
	/**
	 * @var string the HTML element name that will be used to wrap the label of all menu links.
	 * For example, if this property is set as 'span', a menu item may be rendered as
	 * &lt;li&gt;&lt;a href="url"&gt;&lt;span&gt;label&lt;/span&gt;&lt;/a&gt;&lt;/li&gt;
	 * This is useful when implementing menu items using the sliding window technique.
	 * Defaults to null, meaning no wrapper tag will be generated.
	 * @since 1.1.4
	 */
	public $linkLabelWrapper;
	/**
	 * @var string the CSS class that will be assigned to the first item in the main menu or each submenu.
	 * Defaults to null, meaning no such CSS class will be assigned.
	 * @since 1.1.4
	 */
	public $firstItemCssClass;
	/**
	 * @var string the CSS class that will be assigned to the last item in the main menu or each submenu.
	 * Defaults to null, meaning no such CSS class will be assigned.
	 * @since 1.1.4
	 */
	 public $lastItemCssClass;
   
     public $stylesheet = "menu_default.css";
     
     private $_baseUrl = null ;
     private $_imageUrl = null;


	/**
	 * Initializes the menu widget.
	 * This method mainly normalizes the {@link items} property.
	 * If this method is overridden, make sure the parent implementation is invoked.
	 */
	public function init()
	{
		$this->htmlOptions['id']=$this->getId();
		$route=$this->getController()->getRoute();
		$this->items=$this->normalizeItems($this->items,$route,$hasActiveChild);
	}

	/**
	 * Calls {@link renderMenu} to render the menu.
	 */
	public function run()
	{
        $this->registerClientScripts();
		$this->renderMenu($this->items);
	}
  /**
   * Registers the clientside widget files (css & js)
   */
      public function registerClientScripts() {
        // Get the resources path
        $resources = dirname(__FILE__).DIRECTORY_SEPARATOR.'resources';
        // publish the files
        $this->_baseUrl = Yii::app()->assetManager->publish($resources);        
        $this->_imageUrl = $this->_baseUrl.'/images/';
      }

	/**
	 * Renders the menu items.
	 * @param array $items menu items. Each menu item will be an array with at least two elements: 'label' and 'active'.
	 * It may have three other optional elements: 'items', 'linkOptions' and 'itemOptions'.
	 */
	protected function renderMenu($items)
	{
		if(count($items))
		{
			echo '<table cellspacing="0" cellpadding="0" border="0" align="center">'."\n";
            echo '<tr>'."\n";
			$this->renderMenuRecursive($items);
			echo '</tr>' . "\n";
			echo '</table>' . "\n";
		}
	}

	/**
	 * Recursively renders the menu items.
	 * @param array $items the menu items to be rendered recursively
	 */
	protected function renderMenuRecursive($items)
	{
		$count=0;
		$n=count($items);
		foreach($items as $item)
		{
			$count++;
			$options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
			$class=array();
			if($item['active'] && $this->activeCssClass!='')
				$class[]=$this->activeCssClass;
			if($count===1 && $this->firstItemCssClass!='')
				$class[]=$this->firstItemCssClass;
			if($count===$n && $this->lastItemCssClass!='')
				$class[]=$this->lastItemCssClass;
			if($class!==array())
			{
				if(empty($options['class']))
					$options['class']=implode(' ',$class);
				else
					$options['class'].=' '.implode(' ',$class);
			}
            $options['height'] = 65;
            $options['width'] = 159;
            $options['align'] = 'center';
			echo CHtml::openTag('td', $options);

			$menu=$this->renderMenuItem($item);
			if(isset($this->itemTemplate) || isset($item['template']))
			{
				$template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
				echo strtr($template,array('{menu}'=>$menu));
			}

			echo CHtml::closeTag('td')."\n";
            if ($n > $count) {
                echo '<td width="12" align="center"><img height="35" width="2" border="0" style="display:block" src="' . $this->_imageUrl . 'border.png"></td>' . "\n";
            }
		}
	}

	/**
	 * Renders the content of a menu item.
	 * Note that the container and the sub-menus are not rendered here.
	 * @param array $item the menu item to be rendered. Please see {@link items} on what data might be in the item.
	 * @return string
	 * @since 1.1.6
	 */
	protected function renderMenuItem($item)
	{
		if(isset($item['url']))
		{
            if (isset($item['label'])) {
			    $label=$this->linkLabelWrapper===null ? $item['label'] : '<'.$this->linkLabelWrapper.'>'.$item['label'].'</'.$this->linkLabelWrapper.'>';
            } else {
                $name = $item['img']['name'];
                $arr = explode('.', $name);
                $hover_img = $arr[0]. '-hover' . '.' . $arr[1];
                if ($item['active']) {
                    $item['img']['name'] = $hover_img;
                }
                if (!isset($item['img']['htmlOptions']['height'])) $item['img']['htmlOptions']['height'] = 65;
                if (!isset($item['img']['htmlOptions']['width'])) $item['img']['htmlOptions']['width'] = 159;
                if (!isset($item['img']['htmlOptions']['border'])) $item['img']['htmlOptions']['border'] = 0;
                $label = CHtml::image($this->_imageUrl . $item['img']['name'], (isset($item['img']['alt'])? $item['img']['alt'] : ''), $item['img']['htmlOptions']);
                if (empty($item['linkOptions'])) {
                    $item['linkOptions'] = array('onmouseover' => "MM_swapImage('" . $item['img']['htmlOptions']['id'] . "','','" . $this->_imageUrl . $hover_img. "',1)", 'onmouseout' => 'MM_swapImgRestore()');
                }
            }
			return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
		}
	}

	/**
	 * Normalizes the {@link items} property so that the 'active' state is properly identified for every menu item.
	 * @param array $items the items to be normalized.
	 * @param string $route the route of the current request.
	 * @param boolean $active whether there is an active child menu item.
	 * @return array the normalized menu items
	 */
	protected function normalizeItems($items,$route,&$active)
	{
		foreach($items as $i=>$item)
		{
			if(isset($item['visible']) && !$item['visible'])
			{
				unset($items[$i]);
				continue;
			}
			if(!isset($item['label']))
				$item['label']='';
			if($this->encodeLabel)
				$items[$i]['label']=CHtml::encode($item['label']);
			$hasActiveChild=false;
			if(isset($item['items']))
			{
				$items[$i]['items']=$this->normalizeItems($item['items'],$route,$hasActiveChild);
				if(empty($items[$i]['items']) && $this->hideEmptyItems)
					unset($items[$i]['items']);
			}
			if(!isset($item['active']))
			{
                if (isset($item['activeMenus'])) {
                    foreach ($item['activeMenus'] as $subk => $v) {
                        $item['activeMenus'][$subk] = strtolower($v);
                    }
                }
				if($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item,$route)) {
					$active=$items[$i]['active']=true;
                    if (!empty($this->activeItem)) {
                        $items[$this->activeItem['index']]['active'] = false;
                    }
                    $this->activeItem= array('index' => $i, 'activeMenus' => $item['activeMenus']);
				} else {
					$items[$i]['active']=false;
                }
			}
			else if($item['active'])
				$active=true;
		}
		return array_values($items);
	}

	/**
	 * Checks whether a menu item is active.
	 * This is done by checking if the currently requested URL is generated by the 'url' option
	 * of the menu item. Note that the GET parameters not specified in the 'url' option will be ignored.
	 * @param array $item the menu item to be checked
	 * @param string $route the route of the current request
	 * @return boolean whether the menu item is active
	 */
	protected function isItemActive($item,$route)
	{
		if(isset($item['url']) && is_array($item['url']))
		{
            $lowerRoute = strtolower($route);
            // added by nancy xu 2012-04-16
            if (!empty($this->activeItem) && is_array($this->activeItem['activeMenus'])) {
                if (in_array($lowerRoute, $this->activeItem['activeMenus'])) {
                    return false;
                }
            }// end
            $arr = explode('/', $lowerRoute);
            $currentController = $arr[0];
            $itemUrl = trim($item['url'][0],'/');
            $activeMenus = isset($item['activeMenus']) ? $item['activeMenus'] : array();
            $result = false;
            if (!empty($activeMenus)) {
                if (($routeCheck = in_array($lowerRoute, $activeMenus)) || ($conCheck = in_array($currentController, $activeMenus))) {
                    if (!empty($this->activeItem)) {
                        $activeCheck = in_array($lowerRoute, $this->activeItem['activeMenus']);
                        $activeConCheck = in_array($currentController, $this->activeItem['activeMenus']);
                        if ($routeCheck) {
                            $result = $activeCheck ? false : true;
                        } else if ($conCheck) {
                            $result = $activeCheck ? false : ($activeConCheck ? false : true);
                        }  else {
                            $result = false;
                        }
                    } else {
                        $result  = true;
                    }
                }
            }
            if (!strcasecmp($itemUrl, $currentController)) {                
                $result = true;
            }
            if ($result) {
                if(count($item['url'])>1)
                {
                    foreach(array_splice($item['url'],1) as $name=>$value)
                    {
                        if(!isset($_GET[$name]) || $_GET[$name]!=$value)
                            $result =  false;
                    }
                }
            }
            return $result;
		}
		return false;
	}
}