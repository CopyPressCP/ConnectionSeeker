�޸�yiic.bat�ļ��е�
if "%PHP_COMMAND%" == "" set PHP_COMMAND=D:\WEBIDE\Language\php\php.exe

�л�·����
C:\Documents and Settings\Leo>cd K:\NewHtdocs\yii\yii1.1.8.dev\framework
C:\Documents and Settings\Leo>K:
K:\NewHtdocs\yii\yii1.1.8.dev\framework>dir

����������sites��
K:\NewHtdocs\yii\yii1.1.8.dev\framework>yiic webapp ../sites/com.connectionseeker
yes

��com.connectionseeker\protectedĿ¼�´���һ��Ŀ¼modules
��rightsģ�����modules��


����rbacȨ�����ݿ⣺
��framework/web/auth/schema.sql�������ҵ����ݿ���ƶ�����lkm_Ϊ��ʼ�����Ҷ���Сд��ʽ�����Ϊ�˷����ͳһ
������Ҫ�޸����ݿ�ı���
drop table if exists `lkm_auth_assignment`;
drop table if exists `lkm_auth_item_child`;
drop table if exists `lkm_rights`;
drop table if exists `lkm_auth_item`;

create table `lkm_auth_item`
(
   `name`                 varchar(64) not null,
   `type`                 integer not null,
   `description`          text,
   `bizrule`              text,
   `data`                 text,
   primary key (`name`)
) engine InnoDB DEFAULT CHARSET=utf8;

create table `lkm_auth_item_child`
(
   `parent`               varchar(64) not null,
   `child`                varchar(64) not null,
   primary key (`parent`,`child`),
   foreign key (`parent`) references `lkm_auth_item` (`name`) on delete cascade on update cascade,
   foreign key (`child`) references `lkm_auth_item` (`name`) on delete cascade on update cascade
) engine InnoDB DEFAULT CHARSET=utf8;

create table `lkm_auth_assignment`
(
   `itemname`             varchar(64) not null,
   `userid`               varchar(64) not null,
   `bizrule`              text,
   `data`                 text,
   primary key (`itemname`,`userid`),
   foreign key (`itemname`) references `lkm_auth_item` (`name`) on delete cascade on update cascade
) engine InnoDB DEFAULT CHARSET=utf8;

create table `lkm_rights`
(
    `itemname` varchar(64) not null,
    `type` integer not null,
    `weight` integer not null,
     primary key (`itemname`),
     foreign key (`itemname`) references `lkm_auth_item` (`name`) on delete cascade on update cascade
) engine InnoDB DEFAULT CHARSET=utf8;



�޸�com.connectionseeker/protected/main.php�ļ�������Ȩ��
	// application components
	'components'=>array(
	    'user'=>array(
                // enable cookie-based authentication
                'allowAutoLogin'=>true,
                //use the rights module
                'class'=>'RWebUser',
	    ),

        'authManager'=>array(
            'class'=>'RDbAuthManager',
            'itemTable' => 'lkm_auth_item',//auth item table name
            'itemChildTable' => 'lkm_auth_item_child',//auth item child relationship table��֤��ӹ�ϵ
            'assignmentTable' => 'lkm_auth_assignment',//auth item assignment table��֤�Ȩ��ϵ
         ),


######################################more thought about the download & export feature ##################################
About the download & export feature, we have 2 ways,
1.Put the download & export action into the specific/corresponding controller. in other words if we wanna download tasks
then we put the download action into the TaskController, i.e.: Create one method "public function actionDownload()"
so in this case you can access the download feature via http://www.connectionseeker.com/task/download

2.Create one DownloadController, which will handle all of the the download actions in this controller. so if you wanna 
download tasks, then you just need create one publich method in this controller, named like : public function actionTask()
then you can access this download feature via http://www.connectionseeker.com/download/task

######################################more thought about the download & export feature ##################################

