<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php NoticeIOEmail p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class NoticeIOEmailCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        /*
        $num = 10;
        $offset = 0;
        if (!empty($args)) $num = (int) $args[0];
        if (!empty($args)) $offset = (int) $args[1];
        */

        $now = time();
        $onedayago = $now - 86400;
        $yesterday = date("Y-m-d H:i:s", $onedayago);

        $redstart = $now - 86400 * 36;
        $redend = $now - 86400 * 35;//35 days ago
        $redstartday = date("Y-m-d H:i:s", $redstart);
        $redendday = date("Y-m-d H:i:s", $redend);

        $q = "SELECT aa.userid, aa.itemname, u.email, u.username 
            FROM lkm_user AS u LEFT JOIN lkm_auth_assignment AS aa ON (aa.userid = u.id) 
            WHERE aa.itemname = 'InternalOutreach'";
        $outreachers = Yii::app()->db->createCommand($q)->queryAll();
        $orarr = array();
        if ($outreachers) {
            foreach ($outreachers as  $ov) {
                $orarr[$ov["email"]] = $ov["username"];
            }
        }

        /*
        $tasks = Yii::app()->db->createCommand()->select("id, anchortext, channel_id")
            ->from('{{inventory_building_task}}')
            ->where('((modified >= :modified) AND (iostatus IN (1, 3)) AND (channel_id > 0))', array(':modified'=>$yesterday))
            ->queryAll();
        */

        $q = "SELECT t.id, t.anchortext, t.channel_id, io.oldiostatus, io.iostatus, aa.userid, aa.itemname, u.email 
            FROM lkm_io_history AS io
            LEFT JOIN lkm_inventory_building_task AS t ON (t.id = io.task_id) 
            LEFT JOIN lkm_user AS u ON (t.channel_id = u.channel_id) 
            LEFT JOIN lkm_auth_assignment AS aa ON (aa.userid = u.id) 
            WHERE io.created >= '{$yesterday}' AND io.iostatus IN (1, 3, 32) AND aa.itemname = 'InternalOutreach'";

        $tasks = Yii::app()->db->createCommand($q)->queryAll();

        $ioslink = "http://dev.connectionseeker.com/index.php?r=ios";
        $ctablink = "The Following tasks are now in your <a href='{$ioslink}/current' target='_blank'>Current Tab</a>:<br />";
        $atablink = "The Following tasks are now in your <a href='{$ioslink}/approved' target='_blank'>Approved Tab</a>:<br />";
        $ptablink = "The Following tasks are now in your <a href='{$ioslink}/pending' target='_blank'>Pending Tab</a>:<br />";
        $rtablink = "The Following tasks are now in your <a href='{$ioslink}/inrepair' target='_blank'>In Repair Tab</a>:<br />";


        // AND t.isdenied=0 AND t.ispublished=0
        $q = "SELECT t.id, t.acquired_channel_id, t.domain, t.domain_id, aa.userid, aa.itemname, u.email, u.username
            FROM lkm_inventory AS t
            LEFT JOIN lkm_user AS u ON (t.acquired_channel_id = u.channel_id) 
            LEFT JOIN lkm_auth_assignment AS aa ON (aa.userid = u.id) 
            WHERE t.acquireddate >= '{$yesterday}' AND t.created >= '{$yesterday}' AND aa.itemname = 'InternalOutreach'";
        $acquireddomains = Yii::app()->db->createCommand($q)->queryAll();
        $acquirestr = "";
        if (!empty($acquireddomains)) {
            $acquirestr = "The following domains are newly acquired<br />";
            foreach ($acquireddomains as $acqv) {
                $acquirestr .= $acqv["domain"] . " - " . $acqv["username"] . "<br />";
            }
            unset($acquireddomains);
        }

        $q = "SELECT t.id, t.anchortext, t.desired_domain, t.desired_domain_id, aa.userid, aa.itemname, u.email, u.username
            FROM lkm_inventory_building_task AS t  
            LEFT JOIN lkm_inventory AS i ON (t.desired_domain_id = i.domain_id) 
            LEFT JOIN lkm_user AS u ON (t.channel_id = u.channel_id) 
            LEFT JOIN lkm_auth_assignment AS aa ON (aa.userid = u.id) 
            WHERE t.livedate >= '{$yesterday}' AND aa.itemname = 'InternalOutreach' AND i.channel_str NOT LIKE '%,%'
            GROUP BY t.desired_domain_id";
        $publisheddomains = Yii::app()->db->createCommand($q)->queryAll();
        $pubstr = "";
        if (!empty($publisheddomains)) {
            $pubstr = "The following domains are newly published on<br />";
            foreach ($publisheddomains as $pubv) {
                $pubstr .= $pubv["desired_domain"] . " - " . $pubv["username"] . "<br />";
            }
            unset($publisheddomains);
        }


        $q = "SELECT t.id, t.anchortext, t.desired_domain, t.desired_domain_id, t.channel_id, t.targeturl, c.name, aa.itemname, u.email 
            FROM lkm_inventory_building_task AS t  
            LEFT JOIN lkm_campaign AS c ON (t.campaign_id = c.id) 
            LEFT JOIN lkm_user AS u ON (t.channel_id = u.channel_id) 
            LEFT JOIN lkm_auth_assignment AS aa ON (aa.userid = u.id) 
            WHERE t.livedate >= '{$redstartday}' AND t.livedate <= '{$redendday}' 
                AND aa.itemname = 'InternalOutreach' AND c.client_id='88'";//client_id = 88 means for Red Ventures
        $forredventures = Yii::app()->db->createCommand($q)->queryAll();
        $redvtstr = "";
        $redchannels = array();
        if (!empty($forredventures)) {
            $redvtstr = "The following domains were published for Red Ventures 35 days ago:<br />";
            foreach ($forredventures as $redv) {
                if (!isset($redchannels[$redv["email"]])) {
                    $redchannels[$redv["email"]] = "The following domains were published for Red Ventures 35 days ago:<br />";
                }
                //$redchannels[$redv["email"]] = $redv["email"];
                $redvtstr .= $redv["name"]." - ".$redv["desired_domain"]." - ".$redv["anchortext"]." - ".$redv["targeturl"]."<br />";
                $redchannels[$redv["email"]] .= $redv["name"]." - ".$redv["desired_domain"]." - ".$redv["anchortext"]." - ".$redv["targeturl"]."<br />";
            }
            unset($forredventures);
        }

        //## 30 day Live Link Update (not pass)
        //## Task# - Desired URL - Anchor Text - Target URL - Date Completed - Response to crawler
        $couplemonths = date("Y-m-d H:i:s", $now - 86400 * 120);
        $q = "SELECT t.id, t.anchortext, t.channel_id, t.iostatus, t.sourceurl, t.targeturl, t.livedate,t.desired_check,u.email, u.username  
            FROM lkm_inventory_building_task t
            LEFT JOIN lkm_user AS u ON (t.channel_id = u.channel_id) 
            LEFT JOIN lkm_auth_assignment AS aa ON (aa.userid = u.id) 
            WHERE t.iostatus = 5 AND t.livedate > 0 AND t.desired_check > 0 AND t.livedate >= '$couplemonths' 
            AND MOD( DATEDIFF('$yesterday', t.livedate), 30 )=0 AND aa.itemname = 'InternalOutreach'";
        $notpasstasks = Yii::app()->db->createCommand($q)->queryAll();
        if (!empty($notpasstasks)) {
            $warnings = array("1"=>"The anchor text was found however the targeturl link was not.",
                "2"=>"The anchor text was not found however the targeturl link was.",
                "3"=>"Both the targeturl and anchortext are missing.");
            $notpassstr = "30 day Live Link Update:<br />";
            foreach ($notpasstasks as $npv) {
                if (!isset($npchannels[$npv["email"]])) {
                    $npchannels[$npv["email"]] = "30 day Live Link Update:<br />";
                }
                //$npchannels[$npv["email"]] .= $npv["name"]." - ".$npv["desired_domain"]." - ".$npv["anchortext"]." - ".$npv["targeturl"]."<br />";
                //$npchannels[$npv["email"]] .= "Task#%s - %s - %s - %s - %s - %s";
                $npchannels[$npv["email"]] .= "Task#".$npv["id"]." - ".$npv["sourceurl"]." - ".$npv["anchortext"]." - ".$npv["targeturl"]." - ".$npv["livedate"]." - ".$warnings[$npv["desired_check"]];
                $notpassstr .= "Task#".$npv["id"]." - ".$npv["sourceurl"]." - ".$npv["anchortext"]." - ".$npv["targeturl"]." - ".$npv["livedate"]." - ".$warnings[$npv["desired_check"]];
            }
            unset($notpasstasks);

            //added 12/19/2013 by Leo for task "Update crawler code to be separate from daily emails"
            Utils::notice(array('content'=>$notpassstr, 'tos'=>"twyher@copypress.com", 'cc'=>false,
                                'subject'=>'Warning: 30 day Live Link Update(not pass)'));
        }

        /*
        1) IF task is in Approved for > 48 hours, and Content Sent = null, alert them in the following format. "Task # $taskid was approved on $date-io-approved and Content Sent has not been entered yet. Please update this task(Link to io/approved page).
        2) If task is in Approved for > 120 hours, AND Content Sent != null AND Live Date = null, alert them in the following format. "Task # $taskid was approved on $date-io-approved and there is still no live date. Please update this task(Link to io/approved page).
        */
        $twodaysago = date("Y-m-d H:i:s", $now - 3600 * 48);
        $q = "SELECT t.id, t.iodate, t.iostatus, t.sentdate, t.livedate, u.email, u.username  
            FROM lkm_inventory_building_task t
            LEFT JOIN lkm_user AS u ON (t.channel_id = u.channel_id) 
            LEFT JOIN lkm_auth_assignment AS aa ON (aa.userid = u.id) 
            WHERE t.iostatus = 3 AND t.iodate <= '$twodaysago' AND aa.itemname = 'InternalOutreach'";
        $approvedtasks = Yii::app()->db->createCommand($q)->queryAll();
        if (!empty($approvedtasks)) {
            foreach ($approvedtasks as $aprv) {
                $lastiodate = strtotime($aprv["iodate"]);
                if (empty($aprv["sentdate"])) {
                    if (!isset($approvedors[$aprv["email"]])) {
                        $approvedors[$aprv["email"]] = "<br />";
                    }
                    $approvedors[$aprv["email"]] .= "Task # ".$aprv["id"]." was approved on ".$aprv["iodate"]." and Content Sent has not been entered yet. Please update <a href='http://dev.connectionseeker.com/index.php?r=ios/approved' target='_blank'>this task</a>.<br />";
                } else {
                    if ($lastiodate > ($now - 432000) || !empty($aprv["livedate"])) {//3600 * 120 hours
                        continue;
                    }
                    if (!isset($approvedors[$aprv["email"]])) {
                        $approvedors[$aprv["email"]] = "<br />";
                    }
                    $approvedors[$aprv["email"]] .= "Task # ".$aprv["id"]." was approved on ".$aprv["iodate"]." and there is still no live date. Please update <a href='http://dev.connectionseeker.com/index.php?r=ios/approved' target='_blank'>this task</a>.<br />";
                }
            }
            unset($approvedtasks);
        }


        if (!empty($tasks)) {
            $arr = array();
            foreach ($tasks as $v) {
                $id         = $v["id"];
                $email      = $v["email"];
                $iostatus   = $v["iostatus"];
                $anchortext = $v["anchortext"];
                $arr[$email][$iostatus][$id] = $anchortext;
                //$arr[$email][$iostatus][$id] = $anchortext;
                unset($orarr[$email]);//protected don't send notice email to one user more than one time.
            }
            unset($tasks);

            //print_r($arr);
            if ($arr) {
                foreach ($arr as $ke => $va) {
                    $content = $ctablink;
                    if (isset($va[1])) {
                        foreach ($va[1] as $ki => $vi) {
                            $content .= "#".$ki." ".$vi."<br />";
                        }
                    }

                    $content .= "<br />" .$atablink;
                    if (isset($va[3])) {
                        foreach ($va[3] as $ki => $vi) {
                            $content .= "#".$ki.". ".$vi."<br />";
                        }
                    }

                    //Completed In Repair
                    $content .= "<br />" .$rtablink;
                    if (isset($va[32])) {
                        foreach ($va[32] as $ki => $vi) {
                            $content .= "#".$ki.". ".$vi."<br />";
                        }
                    }

                    if ($acquirestr) {
                        $content .= "<br />" . $acquirestr;
                    }

                    if ($pubstr) {
                        $content .= "<br />" . $pubstr;
                    }

                    if ($redvtstr && isset($redchannels[$ke])) {
                        //###$content .= "<br />" . $redvtstr;
                        $content .= "<br />" . $redchannels[$ke];
                        unset($redchannels[$ke]);//#############!!!!!!!!!!!!!!!!!!!!!
                    }

                    if ($notpassstr && isset($npchannels[$ke])) {
                        $content .= "<br />" . $npchannels[$ke];
                        unset($npchannels[$ke]);//#############!!!!!!!!!!!!!!!!!!!!!
                    }

                    if ($approvedors && isset($approvedors[$ke])) {
                        $content .= "<br />" . $approvedors[$ke];
                        unset($approvedors[$ke]);//#############!!!!!!!!!!!!!!!!!!!!!
                    }

                    //####Utils::notice(array('content'=>$content, 'tos'=>$ke, 'cc'=>'kzipp@copypress.com',
                    Utils::notice(array('content'=>$content, 'tos'=>$ke, 'cc'=>false,
                                        'subject'=>'IO Tasks Notification Within The Past 24 Hours'));
                }
            }
            unset($arr);
        }

        if (!empty($npchannels)) {//still have not pass channel result:
            foreach ($npchannels as $ke => $va) {
                $content = "<br />" . $va;

                if ($acquirestr) {
                    $content .= "<br />" . $acquirestr;
                }

                if ($pubstr) {
                    $content .= "<br />" . $pubstr;
                }

                if (isset($redchannels[$ke])) {
                    $content .= "<br />" . $redchannels[$ke];
                    unset($redchannels[$ke]);//#############!!!!!!!!!!!!!!!!!!!!!
                }

                Utils::notice(array('content'=>$content, 'tos'=>$ke, 'cc'=>false,
                                    'subject'=>'IO Tasks Notification Within The Past 24 Hours'));
            }
        }

        if (!empty($redchannels)) {
            foreach ($redchannels as $ke => $va) {
                $content = "<br />" . $va;

                if ($acquirestr) {
                    $content .= "<br />" . $acquirestr;
                }

                if ($pubstr) {
                    $content .= "<br />" . $pubstr;
                }

                Utils::notice(array('content'=>$content, 'tos'=>$ke, 'cc'=>false,
                                    'subject'=>'IO Tasks Notification Within The Past 24 Hours'));
            }
        }

        $q = "SELECT t.id, t.anchortext, io.iostatus 
            FROM lkm_io_history AS io
            LEFT JOIN lkm_inventory_building_task AS t ON (t.id = io.task_id) 
            WHERE io.created >= '{$yesterday}' AND io.iostatus = '21'";
        $tasks = Yii::app()->db->createCommand($q)->queryAll();
        if ($tasks) {
            $arr = array();
            $content = $ptablink;
            foreach ($tasks as $v) {
                $content .= "#".$v["id"]." ".$v["anchortext"]."<br />";
            }

            if ($acquirestr) {
                $content .= "<br />" . $acquirestr;
            }

            if ($pubstr) {
                $content .= "<br />" . $pubstr;
            }

            if ($redvtstr) {
                $content .= "<br />" . $redvtstr;
            }

            if ($notpassstr) {
                $content .= "<br />" . $notpassstr;
            }
            Utils::notice(array('content'=>$content, 'tos'=>"csnotifications@copypress.com",
                                'subject'=>'IO Tasks Notification Within The Past 24 Hours'));
        }

        //For the other Outreacher users to get the acquired & published & red ventrues notice domains.
        /*
        if ($orarr && ($acquirestr || $pubstr || $redvtstr) ) {
            foreach ($orarr as $ko => $vo) {
                $content = "";
                if ($acquirestr) {
                    $content .= "<br />" . $acquirestr;
                }

                if ($pubstr) {
                    $content .= "<br />" . $pubstr;
                }

                if ($redvtstr && isset($redchannels[$ko])) {//!!!!!#############
                    $content .= "<br />" . $redvtstr;
                }

                if (!empty($ko) && !empty($content)) {
                    Utils::notice(array('content'=>$content, 'tos'=>$ko, 'cc'=>false, 
                                        'subject'=>'IO Tasks Notification Within The Past 24 Hours'));
                }
            }
        }
        */

    }

}

?>