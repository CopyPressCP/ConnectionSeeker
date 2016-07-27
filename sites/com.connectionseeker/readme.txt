修改yiic.bat文件中的
if "%PHP_COMMAND%" == "" set PHP_COMMAND=D:\WEBIDE\Language\php\php.exe

切换路径：
C:\Documents and Settings\Leo>cd K:\NewHtdocs\yii\yii1.1.8.dev\framework
C:\Documents and Settings\Leo>K:
K:\NewHtdocs\yii\yii1.1.8.dev\framework>dir

创建域名到sites：
K:\NewHtdocs\yii\yii1.1.8.dev\framework>yiic webapp ../sites/com.connectionseeker
yes

在com.connectionseeker\protected目录下创建一个目录modules
把rights模块放入modules下


建立rbac权限数据库：
打开framework/web/auth/schema.sql。由于我的数据库设计都是以lkm_为开始，并且都是小写方式，因此为了风格上统一
我们需要修改数据库的表名
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



修改com.connectionseeker/protected/main.php文件，配置权限
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
            'itemChildTable' => 'lkm_auth_item_child',//auth item child relationship table认证项父子关系
            'assignmentTable' => 'lkm_auth_assignment',//auth item assignment table认证项赋权关系
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

