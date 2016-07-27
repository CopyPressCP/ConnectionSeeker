使用方法: 
activeMenus设置该菜单的子菜单的连接或则controller。如果该项设置了连接，并且当前连接就在activeMenus里面，则这个菜单就处于激活状态；如果设置是controller，那么检查当前连接的controller是否和activeMenus里面的controller相匹配，匹配则这个菜单就处于激活状态。
比如当前访问连接profileField/index，profileField/index在client菜单的activeMenus项，那么页面显示时候client菜单处于激活状态；而profilefield在user菜单的activeMenus项, 但当前连接profileField/index时，user菜单处于非激活状态。也就是activeMenus项设置连接的优先级高于设置controller
  $items = array(
	array('img'=> array( 'name'=> 'client_icon.png',  'htmlOptions'=> array('id' => 'client_icon', 'name'=> 'client_icon')), 'url'=>array('/client'), 'activeMenus' => array('site', 'profileField/index')),
	array('img' => array('name'=>'outsearch_icon.png', 'htmlOptions'=>array('id' => 'outsearch_icon', 'name'=> 'outsearch_icon')), 'url'=>array('/outsearch')),
	array('img' => array('name'=>'campaign_icon.png', 'htmlOptions'=>array('id' => 'campaign_icon', 'name'=> 'campaign_icon')), 'url'=>array('/campaign')),
	array('img' => array('name'=> 'user_icon.png', 'htmlOptions'=>array('id' => 'user_icon', 'name'=> 'user_icon')), 'url'=>array('/user'), 'activeMenus' => array('profilefield')),
	array('img' => array('name'=>'email_iconnav.png', 'htmlOptions'=>array('id' => 'email_iconnav', 'name'=> 'email_iconnav')) , 'url'=>array('/email')));
$this->widget('application.extensions.lkmenu.LKMenu', array( 'items' => $items));
