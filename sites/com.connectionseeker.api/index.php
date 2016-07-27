<?php
$_isapicall = stripos($_SERVER["REQUEST_URI"], "/api/");
if ($_isapicall !== 0) {
    header('Location: http://www.connectionseeker.com');
    exit;
}

// change the following paths if necessary
$yii=dirname(__FILE__).'/../../framework/yii.php';
$config=dirname(__FILE__).'/../com.connectionseeker/protected/config/restapi.php';
//$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
