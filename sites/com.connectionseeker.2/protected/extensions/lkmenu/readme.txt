ʹ�÷���: 
activeMenus���øò˵����Ӳ˵������ӻ���controller������������������ӣ����ҵ�ǰ���Ӿ���activeMenus���棬������˵��ʹ��ڼ���״̬�����������controller����ô��鵱ǰ���ӵ�controller�Ƿ��activeMenus�����controller��ƥ�䣬ƥ��������˵��ʹ��ڼ���״̬��
���統ǰ��������profileField/index��profileField/index��client�˵���activeMenus���ôҳ����ʾʱ��client�˵����ڼ���״̬����profilefield��user�˵���activeMenus��, ����ǰ����profileField/indexʱ��user�˵����ڷǼ���״̬��Ҳ����activeMenus���������ӵ����ȼ���������controller
  $items = array(
	array('img'=> array( 'name'=> 'client_icon.png',  'htmlOptions'=> array('id' => 'client_icon', 'name'=> 'client_icon')), 'url'=>array('/client'), 'activeMenus' => array('site', 'profileField/index')),
	array('img' => array('name'=>'outsearch_icon.png', 'htmlOptions'=>array('id' => 'outsearch_icon', 'name'=> 'outsearch_icon')), 'url'=>array('/outsearch')),
	array('img' => array('name'=>'campaign_icon.png', 'htmlOptions'=>array('id' => 'campaign_icon', 'name'=> 'campaign_icon')), 'url'=>array('/campaign')),
	array('img' => array('name'=> 'user_icon.png', 'htmlOptions'=>array('id' => 'user_icon', 'name'=> 'user_icon')), 'url'=>array('/user'), 'activeMenus' => array('profilefield')),
	array('img' => array('name'=>'email_iconnav.png', 'htmlOptions'=>array('id' => 'email_iconnav', 'name'=> 'email_iconnav')) , 'url'=>array('/email')));
$this->widget('application.extensions.lkmenu.LKMenu', array( 'items' => $items));
