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


RENAME TABLE `com_linkmev2`.`authassignment` TO `com_linkmev2`.`lkm_auth_assignment` ;
ALTER TABLE `lkm_auth_assignment` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `lkm_auth_assignment` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `lkm_auth_assignment` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


在调用gettopbacklink的时候记得参考：
http://developer-support.majesticseo.com/api/commands/get-top-backlinks.shtml
https://www.majesticseo.com/account/my-subscriptions

cmd:GetTopBackLinks
datasource:Either: "fresh" - to query against fresh index, or "historic" - to query against historic index. defaults to historic
URL:那个你想query的copetitor domain
MaxSourceURLs:返回多少条backlink的结果集，不大于20,000条，参见https://www.majesticseo.com/account/my-subscriptions
app_api_key: API Key


$mjurl = "http://www.majesticseo.com/api_command.php?app_api_key=2F2DE59CC1A7DC7D88149BB6D525FC8C&cmd=GetTopBackLinks&MaxSourceURLs=".$nof_lri."&GetRootDomainData=1&AnalysisResUnits=10000&UseResUnits=1&URL=".urlencode($domain);	




http://developer.majesticseo.com/api_command?app_api_key=2F2DE59CC1A7DC7D88149BB6D525FC8C&cmd=GetTopBackLinks&URL=google.com&MaxSourceURLs=5&ShowDomainInfo=1&GetRootDomainData=1
先处理domainsinfo的资料，然后再处理其他这样就可以取出domain
<?xml version="1.0" encoding="utf-8"?>
<Result Code="OK" ErrorMessage="" FullError="">
		<GlobalVars AnalysisMode="AnalyseAllBackLinks" ChargedAnalysisResUnits="5000" ChargedRetrievalResUnits="5" IndexType="0" RemainingAnalysisResUnits="98270141" RemainingDetailedReportsPerPeriod="942" RemainingRetrievalResUnits="40585513" RemainingStandardReports="942" ServerBuild="2011-10-28 15:08:32" ServerName="DAVE" ServerVersion="1.0.4318.25456"/>
	<DataTables Count="2">
		<DataTable Name="URL" RowsCount="5" Headers="SourceURL|ACRank|AnchorText|Date|FlagRedirect|FlagFrame|FlagNoFollow|FlagImages|FlagDeleted|FlagAltText|FlagMention|TargetURL|DomainID" BackLinkItem="http://google.com" BackLinkType="URL" TotalBackLinks="32526" TotalLines="590" TotalMatches="5">
			<Row>http://hiding-place.info/index.shtml|4|google|2010-08-08|0|0|0|0|0|0|0|http://google.com|0</Row>
			<Row>http://mtkd.tcv.proxydns.com|3|blog at wordpress.com|2010-08-07|0|0|0|0|0|0|0|http://google.com|1</Row>
			<Row>http://mtkd.tcv.proxydns.com|3|wordpress|2010-08-07|0|0|0|0|0|0|0|http://google.com|1</Row>
			<Row>http://i9pg.gov.cn.bocis-c59.com|3|wordpress|2010-08-07|0|0|0|0|0|0|0|http://google.com|2</Row>
			<Row>http://i9pg.gov.cn.bocis-c59.com|3|blog at wordpress.com|2010-08-07|0|0|0|0|0|0|0|http://google.com|2</Row>
		</DataTable>
		<DataTable Name="DomainsInfo" RowsCount="3" Headers="DomainID|Domain|AlexaRank|RefDomains|ExtBackLinks|IndexedURLs|CrawledURLs|FirstCrawled|LastSuccessfulCrawl|IP|SubNet|CountryCode|TLD">
			<Row>0|hiding-place.info|54089|19|216|8300|2|2010-08-08|2010-08-08|69.39.226.204|69.39.226.0|US|info</Row>
			<Row>1|proxydns.com|-1|77|24337|21568|300|2010-08-07|2010-08-07|204.16.173.30|204.16.173.0|US|com</Row>
			<Row>2|bocis-c59.com|-1|125|64314|23704|300|2010-08-07|2010-08-07|216.163.137.3|216.163.137.0|US|com</Row>
		</DataTable>
	</DataTables>
</Result>




http://developer.majesticseo.com/api_command?app_api_key=2F2DE59CC1A7DC7D88149BB6D525FC8C&cmd=GetTopBackLinks&URL=google.com&MaxSourceURLs=5&ShowDomainInfo=1&&GetRootDomainData=1

<?xml version="1.0" encoding="utf-8"?>
<Result Code="OK" ErrorMessage="" FullError="">
		<GlobalVars AnalysisMode="AnalyseAllBackLinks" ChargedAnalysisResUnits="10000" ChargedRetrievalResUnits="10" IndexType="0" RemainingAnalysisResUnits="98265141" RemainingDetailedReportsPerPeriod="942" RemainingRetrievalResUnits="40585513" RemainingStandardReports="942" ServerBuild="2011-10-28 15:08:32" ServerName="DAVE" ServerVersion="1.0.4318.25456"/>
	<DataTables Count="3">
		<DataTable Name="URL" RowsCount="5" Headers="SourceURL|ACRank|AnchorText|Date|FlagRedirect|FlagFrame|FlagNoFollow|FlagImages|FlagDeleted|FlagAltText|FlagMention|TargetURL|DomainID" BackLinkItem="http://google.com" BackLinkType="URL" TotalBackLinks="32526" TotalLines="590" TotalMatches="5">
			<Row>http://hiding-place.info/index.shtml|4|google|2010-08-08|0|0|0|0|0|0|0|http://google.com|0</Row>
			<Row>http://mtkd.tcv.proxydns.com|3|blog at wordpress.com|2010-08-07|0|0|0|0|0|0|0|http://google.com|1</Row>
			<Row>http://mtkd.tcv.proxydns.com|3|wordpress|2010-08-07|0|0|0|0|0|0|0|http://google.com|1</Row>
			<Row>http://i9pg.gov.cn.bocis-c59.com|3|wordpress|2010-08-07|0|0|0|0|0|0|0|http://google.com|2</Row>
			<Row>http://i9pg.gov.cn.bocis-c59.com|3|blog at wordpress.com|2010-08-07|0|0|0|0|0|0|0|http://google.com|2</Row>
		</DataTable>
		<DataTable Name="RootDomain" RowsCount="5" Headers="SourceURL|ACRank|AnchorText|Date|FlagRedirect|FlagFrame|FlagNoFollow|FlagImages|FlagDeleted|FlagAltText|FlagMention|TargetURL|DomainID" BackLinkItem="google.com" BackLinkType="RootDomain" TotalBackLinks="4693250" TotalLines="530000" TotalMatches="5">
			<Row>http://www.joomla.org|8|cms|2010-08-08|0|0|0|0|0|0|0|http://groups.google.com/group/joomla-dev-general|3</Row>
			<Row>http://www.joomla.org|8|framework|2010-08-08|0|0|0|0|0|0|0|http://groups.google.com/group/joomla-dev-framework|3</Row>
			<Row>http://www.joomla.org|8|general|2010-08-08|0|0|0|0|0|0|0|http://groups.google.com/group/joomla-dev-general|3</Row>
			<Row>http://www.joomla.org|8|jbs mailing list|2010-08-08|0|0|0|0|0|0|0|http://groups.google.com/group/joomlabugsquad|3</Row>
			<Row>http://www.joomla.org|8|bug squad|2010-08-08|0|0|0|0|0|0|0|http://groups.google.com/group/joomlabugsquad|3</Row>
		</DataTable>
		<DataTable Name="DomainsInfo" RowsCount="4" Headers="DomainID|Domain|AlexaRank|RefDomains|ExtBackLinks|IndexedURLs|CrawledURLs|FirstCrawled|LastSuccessfulCrawl|IP|SubNet|CountryCode|TLD">
			<Row>0|hiding-place.info|54089|19|216|8300|2|2010-08-08|2010-08-08|69.39.226.204|69.39.226.0|US|info</Row>
			<Row>1|proxydns.com|-1|77|24337|21568|300|2010-08-07|2010-08-07|204.16.173.30|204.16.173.0|US|com</Row>
			<Row>2|bocis-c59.com|-1|125|64314|23704|300|2010-08-07|2010-08-07|216.163.137.3|216.163.137.0|US|com</Row>
			<Row>3|joomla.org|248|1094|66840|5418|906|2010-06-12|2010-08-08|72.9.243.251|72.9.243.0|US|org</Row>
		</DataTable>
	</DataTables>
</Result>


-------------------------------------------------
tbl.lkm_competitor_bldomain
id
competitor_id
competitor_domain
mj_domain_id
domain
alexa_rank
ref_domains
ext_backlinks
indexed_urls
crawled_urls
first_crawled
last_successful_crawl
ip
subnet
countrycode
tld
googlepr
onlinesince
hubcount

tbl.lkm_competitor_backlink
id
domain_id
mj_domain_id
source_url
ac_rank
anchor_text
date
flag_redirect
flag_frame
flag_nofollow
flag_images
flag_deleted
flag_alttext
flag_mention
target_url
domainid
-------------------------------------------------



[root@myhost a]# cat a.sh
#!/bin/sh
if [ ! -d "$1" ]; then
mkdir "$1"
fi
cd "$1"
wget -O "$2" "http://developer.majesticseo.com/api_command?app_api_key=2F2DE59CC1A7DC7D88149BB6D525FC8C&cmd=GetTopBackLinks&URL=google.com&MaxSourceURLs=2&ShowDomainInfo=1&datasource=fresh"
 


2F2DE59CC1A7DC7D88149BB6D525FC8C

