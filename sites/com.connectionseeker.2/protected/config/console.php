<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	
    'name'=>'ConnectionSeeker Cron',
    'preload'=>array('log'),
 
    'import'=>array(
        'application.components.*',
        'application.models.*',
        'ext.yii-mail.YiiMailMessage',
    ),

    // application components
    'components'=>array(
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron.log',
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron_trace.log',
                    'levels'=>'trace',
                ),
            ),
        ),

        // uncomment the following to use a MySQL database
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=com_linkmev2',
            'emulatePrepare' => true,
            'username' => 'connectionseeker',
            'password' => 'see123ker6',
            'charset' => 'utf8',
            'tablePrefix' => 'lkm_',
        ),

        'cache'  => array(
                //'class'  => 'system.caching.CXCache',
                'class'  => 'system.caching.CFileCache',
        ),

        'mail' => array(
            'class' => 'ext.yii-mail.YiiMail',
            'transportType' => 'smtp',
            'viewPath' => 'application.views.mail',
            'logging' => true,
            'dryRun' => false,
            'transportOptions' => array(
                                    'host' => 'smtp.sendgrid.net',
                                    'username' => 'huamarketing',
                                    'password' => 'poops830',
                                    'port' => '587',
                                  ),
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
            // this is used in contact page
            'adminEmail'=>'leo@infinitenine.com',
            //'contentAPI'=>'https://api.copypress.com',
            'contentAPI'=>'http://apidev.i9cms.com',

        //cron job frequence
        'cronfrq' => array(
            'sonlinesince' => 1,
            'sgooglepr' => 4,
            'salexarank' => 4,
            'smozrank' => 4,
            'sacrank' => 2,
            'sip' => 1,
            'sfacebookshares' => 12,
        ),
    ),

);
