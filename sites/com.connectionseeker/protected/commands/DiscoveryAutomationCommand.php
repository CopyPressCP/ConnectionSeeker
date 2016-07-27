<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php DiscoveryAutomationCommand p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)
*/
date_default_timezone_set('EST');

Yii::import('application.vendors.*');
Yii::import('ext.yii-mail.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class DiscoveryAutomationCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        //Autoload fix & set the X-SMTPAPI
        spl_autoload_unregister(array('YiiBase','autoload'));
        Yii::import('ext.yii-mail.XSmtpHeader', true);
        spl_autoload_register(array('YiiBase','autoload'));
        //$xhdr = new XSmtpHeader();
        //$xhdr->setCategory("contact1st");//initial, contact 1st time.
        //$message = new YiiMailMessage;
        //$headers = $message->getHeaders();
        //##Yii::app()->user->id = 1;

        $num = 5;
        if (!empty($args)) {
            $num = (int) $args[0];
        }

        $offset = 0;
        if (!empty($args) && isset($args[1])) {
            $offset = (int) $args[1];
        }

        echo "#### Start Discovery Email Task ######\r\n";

        $nowtimestamp = time();
        $hour = date("H:i", $nowtimestamp);
        $hourstamp = strtotime($hour.":00");
        $week = date("w", $nowtimestamp);
        $now = date("Y-m-d H:i:s", $nowtimestamp);
        $rules = Yii::app()->db->createCommand()->select()->from('{{client_discovery}}')
            ->where('(status = 1 AND progress>=2 AND progress<5 AND complete_with_automation = 1)')
            //->where('(status = 1 AND progress>=2 AND progress<5 AND complete_with_automation = 1) AND id<200')
            ->order('modified, created DESC')
            ->limit($num)
            ->offset($offset)
            ->queryAll();

        //print_r($rules);
        if (empty($rules)) return false;
        echo "## Start Sending Email Process ##\r\n";
        foreach($rules as $rl) {
            system("/sbin/iptables -t nat -D POSTROUTING 1");

            echo "## EmailTask #".$rl["id"]." ##\r\n";
            if ($rl["progress"] >= 2 && $rl["progress"]<4) {
                //update discovery current progress
                echo $q = "SELECT MAX(domain_id) AS domain_id FROM {{discovery_backdomain}} WHERE discovery_id = '".$rl["id"]."'";
                $latestbackdomain = Yii::app()->db->createCommand($q)->queryRow();
                if ($latestbackdomain && $latestbackdomain["domain_id"]) {
                    echo $q = "SELECT * FROM {{domain_onpage}} WHERE lastcrawled IS NOT NULL AND domain_id = ".$latestbackdomain["domain_id"];
                    $lbdinfo = Yii::app()->db->createCommand($q)->queryRow();
                    $cdarr = array();
                    if ($lbdinfo) {
                        $cdarr["progress"] = 4;
                    } else {
                        $cdarr["progress"] = 3;
                    }
                    //print_r($cdarr);
                    $update = Yii::app()->db->createCommand()
                        ->update('{{client_discovery}}', $cdarr, 'id=:id', array(':id'=>$rl["id"]));
                }
            }

            if (empty($rl["complete_with_automation"]) || empty($rl["automation_setting"])) {
                continue;
            }

            $r = json_decode($rl["automation_setting"], true);
            print_r($r);
            $mailers = $r["mailers"];
            $mailersobj = unserialize($mailers);
            $autocount = count($mailersobj);
            if ($autocount==0) continue;
            if (!empty($r["latest_senttime"])) {
                $timediff = $nowtimestamp - strtotime($r["latest_senttime"]);
                if ($timediff<($r["frequency"]*60)) continue;
            }
            //Check today can send email or not.
            if (!empty($r["days"])) {
                /*
                $_weeks = explode("|", $r["days"]);
                if (!in_array($week, $_weeks)) continue;
                */
                if (!in_array($week, $r["days"])) continue;
            }

            //Check the time is between time start & time end.
            $timestartstamp = strtotime($r["time_start"]);
            $timeendstamp = strtotime($r["time_end"]);
            if ($hourstamp>=$timestartstamp && $hourstamp<=$timeendstamp) {
                //do noting;
            } else {
                continue;
            }

            if (empty($r["domain_queue"])) {
                //get 20 domains from tbl.lkm_domain;
                //$where = "WHERE 1";
                $domainids = genDomainQueue($r+array('discovery_id'=>$rl["id"]));
/*
                if (empty($domainids)) {
                    finishEmailTask($rl["id"]);
                }
*/
            } else {
                //do nothing for now;
                $idstr = $r["domain_queue"];
                $domainids = explode("|", $idstr);
                if ($autocount > count($domainids)) {
                    $newids = genDomainQueue($r+array('discovery_id'=>$rl["id"]));
                    $domainids = array_merge($domainids, $newids);
                }
            }

            print_r($domainids);
            if (empty($domainids)) {
                echo $q = "SELECT COUNT(*) AS nofcralwed FROM {{discovery_backdomain}} WHERE status = 0 AND lastcrawled IS NULL AND discovery_id = '".$rl["id"]."'";
                $_nofc = Yii::app()->db->createCommand($q)->queryRow();
                print_r($_nofc);
                if ($_nofc && $_nofc["nofcralwed"] > 0) {
                } else {
                    finishEmailTask($rl["id"]);
                }
                continue;
            }
            $current_domain_id = array_shift($domainids);
            print_r($domainids);
            $domainobj = Domain::model()->findByPk($current_domain_id);

            //获得当前要使用的mailer_id
            $mids = array_keys($mailersobj);
            if (empty($r["current_mailer_id"])) {
                $current_mailer_id = $mids[0];
            } else {
                $_curr_mkey = array_search($r["current_mailer_id"], $mids);
                $_curr_mkey += 1;
                if ($_curr_mkey == count($mids)) $_curr_mkey = 0;
                $current_mailer_id = $mids[$_curr_mkey];
            }

            //######################4/24/2014 ############################
            $attrs = array();
            $attrs['discovery_mailer'] = $current_mailer_id;
            if (!empty($domainobj->discovery_mailer)) {
                $_attrs = explode(",", $domainobj->discovery_mailer);
                if (in_array($current_mailer_id, $_attrs)) {
                    if (count($mids) == 1) {
                        Yii::app()->db->createCommand()->update('{{discovery_backdomain}}', 
                            array('mailer_id'=>$current_mailer_id),
                            'discovery_id=:dcid AND domain_id=:did', array(':did'=>$domainobj->id, ':dcid'=>$rl["id"]));
                        if ($domainids) {
                            $idstr = implode("|", $domainids);
                            $r["domain_queue"] = $idstr;
                        } else {
                            $r["domain_queue"] = NULL;
                        }
                        $rl["automation_setting"] = json_encode($r);
                        Yii::app()->db->createCommand()->update('{{client_discovery}}', $rl,
                                                    'id=:id', array(':id'=>$rl["id"]));
                        continue;

                    } else {
                        $_curr_mkey += 1;
                        if ($_curr_mkey == count($mids)) $_curr_mkey = 0;
                        $current_mailer_id = $mids[$_curr_mkey];
                        if (!in_array($current_mailer_id, $_attrs)) {
                            $attrs['discovery_mailer'] = $domainobj->discovery_mailer.",".$current_mailer_id;
                        }
                    }
                } else {
                    $attrs['discovery_mailer'] = $domainobj->discovery_mailer.",".$current_mailer_id;
                }
            }
            //######################4/24/2014 ############################

            //check the domain was sent recently or not?
            $yesterday = date("Y-m-d H:i:s", $nowtimestamp - 21600);//one dayago 86400
            /*
            echo $q = "SELECT id FROM {{automation_sent}} WHERE sent >= '$yesterday' ".
                "AND domain_id = '$current_domain_id' AND mailer_id='".$current_mailer_id."'";
                */
            $q = "SELECT id FROM {{automation_sent}} WHERE sent >= '$yesterday' ".
                "AND domain_id = '$current_domain_id'";
            $atm = Yii::app()->db->createCommand($q)->queryRow();
            if (empty($domainobj) || $atm) {
                //do nothing for now;
                if ($domainids) {
                    $idstr = implode("|", $domainids);
                    $r["domain_queue"] = $idstr;
                } else {
                    $r["domain_queue"] = NULL;
                }
                $r["current_domain_id"] = $current_domain_id;

                $rl["automation_setting"] = json_encode($r);
                Yii::app()->db->createCommand()->update('{{client_discovery}}', $rl,
                                            'id=:id', array(':id'=>$rl["id"]));
                continue;
            }
            echo "## Current DomainID #".$current_domain_id." ##\r\n";


            $currmailersobj = $mailersobj[$current_mailer_id];
            $templates = explode("|", $currmailersobj["template"]);
            if (empty($currmailersobj["current_template_id"])) {
                $current_template_id = $templates[0];
            } else {
                $_curr_tkey = array_search($currmailersobj["current_template_id"], $templates);
                $_curr_tkey += 1;
                $_counttpl = count($templates);
                if ($_curr_tkey == $_counttpl) $_curr_tkey = 0;
                $current_template_id = $templates[$_curr_tkey];
            }

            $mobj = null;
            unset($mobj);
            $mobj = Mailer::model()->findByPk($current_mailer_id);
            if (empty($mobj)) continue;

            $tplobj = Template::model()->findByPk($current_template_id);
            if (empty($tplobj)) continue;

            $owner = $domainobj->owner;
            if (empty($owner)) $owner = $domainobj->domain;

            Yii::app()->db->createCommand()->update('{{domain}}', $attrs,
                                    'id=:id', array(':id'=>$domainobj->id));

            Yii::app()->db->createCommand()->update('{{discovery_backdomain}}', array('mailer_id'=>$current_mailer_id),
                                    'discovery_id=:dcid AND domain_id=:did', array(':did'=>$domainobj->id, ':dcid'=>$rl["id"]));

            // ******************** RESET THE Primary Email 2 Crawler Contact Email************************** //
            $primary_email = trim($domainobj->primary_email) ? $domainobj->primary_email : $domainobj->primary_email2;
            $primary_email = trim($primary_email);
            if (empty($primary_email)) {
                $dopobj = DomainOnpage::model()->findByAttributes(array('domain_id'=>$current_domain_id));
                if ($dopobj && !empty($dopobj->contactemail)) {
                    $primary_email = str_ireplace("mailto:", "", trim($dopobj->contactemail));
                } else {
                    //$primary_email = $domainobj->primary_email;
                    echo "E-mail value is empty, Current DomainID:".$current_domain_id;

                    //Reset domain queue;
                    $r["domain_queue"] = NULL;
                    $rl["automation_setting"] = json_encode($r);
                    Yii::app()->db->createCommand()->update('{{client_discovery}}', $rl,
                                                'id=:id', array(':id'=>$rl["id"]));
                    continue;
                }
            }

            echo $primary_email;
            $primary_email = trim($primary_email);
            if(!filter_var($primary_email, FILTER_VALIDATE_EMAIL)){
                $primary_email = extract_email_address($primary_email);
                if (!filter_var($primary_email, FILTER_VALIDATE_EMAIL)) {
                    echo "E-mail $primary_email is not valid";
                    Yii::app()->db->createCommand()->update('{{domain_onpage}}', array('contactemail'=>NULL),
                                    'domain_id=:did', array(':did'=>$domainobj->id));
                    //Reset domain queue;
                    $r["domain_queue"] = NULL;
                    $rl["automation_setting"] = json_encode($r);
                    Yii::app()->db->createCommand()->update('{{client_discovery}}', $rl,
                                                'id=:id', array(':id'=>$rl["id"]));
                    continue;
                }
            }
            // ******************** RESET THE Primary Email 2 Crawler Contact Email************************** //

            if ($domainids) {
                $idstr = implode("|", $domainids);
                $r["domain_queue"] = $idstr;
            } else {
                $r["domain_queue"] = NULL;
            }
            $r["current_domain_id"] = $current_domain_id;

            //New requirement: any task, any domain, anything. We need check for From and To Address have never met before.
            //######################9/10/2014 ############################
            $q = "SELECT id FROM {{email_queue}} WHERE `to` = '$primary_email' AND email_from = '".$mobj->email_from."'";
            $sentever = Yii::app()->db->createCommand($q)->queryRow();
            if ($sentever) {
                $rl["automation_setting"] = json_encode($r);
                Yii::app()->db->createCommand()->update('{{client_discovery}}', $rl,
                                            'id=:id', array(':id'=>$rl["id"]));
                continue;
            }
            //######################9/10/2014 ############################


            $r["current_mailer_id"] = $current_mailer_id;
            //##$r["current_template_id"] = $current_template_id;
            $r["total_sent"] += 1;
            $r["latest_senttime"] = $now;
            $mailersobj[$current_mailer_id]["current_template_id"] = $current_template_id;
            $mailersobj[$current_mailer_id]["latest_senttime"] = $now;
            $r["mailers"] = serialize($mailersobj);

            $content = str_replace('$ownername', $owner, $tplobj->content);

            $attrs = array();
            $attrs['status'] = 1;
            $attrs['send_time'] = $now;
            $attrs['domain_id'] = $current_domain_id;
            $attrs['template_id'] = $current_template_id;
            $attrs['from'] = $current_mailer_id;
            $attrs['to'] = $primary_email;
            $attrs['subject'] = $tplobj->subject;
            $attrs['content'] = $content;
            $attrs['parent_id'] = 0;
            $attrs['email_from'] = $mobj->email_from;
            $rs = Yii::app()->db->createCommand()->insert('{{email_queue}}', $attrs);
            $queue_id = Yii::app()->db->getLastInsertID();
            if($rs) {
                $rl["automation_setting"] = json_encode($r);
                Yii::app()->db->createCommand()->update('{{client_discovery}}', $rl,
                                            'id=:id', array(':id'=>$rl["id"]));
                /*
                Yii::app()->db->createCommand()->update('{{automation_setting}}', $r,
                                            'id=:id', array(':id'=>$r["id"])); */


                if (!empty($mobj->cron_out_ip) && strlen($mobj->cron_out_ip)>6) {
                    system("/sbin/iptables -t nat -I POSTROUTING -o eth0 -j SNAT --to-source ".$mobj->cron_out_ip);
                }

                //###https://github.com/swiftmailer/swiftmailer/issues/341
                $xhdr = new XSmtpHeader();
                $xhdr->setCategory("contact1st");//initial, contact 1st time.

                $message = new YiiMailMessage;
                $headers = $message->getHeaders();

                $xhdr->setUniqueArgs(array('domain_id'=>$current_domain_id, 'template_id'=>$current_template_id, 'queue_id'=>$queue_id, 'mailer_id' => $current_mailer_id));
                $headers->addTextHeader('X-SMTPAPI', $xhdr->asJSON());
                $pixel = "<img src='http://www.connectionseeker.com/index.php?r=email/open&id=".$queue_id."' width='1' height='1' />";

                if ($domainobj) $content = str_ireplace('%DOMAIN%', $domainobj->domain, $content);

                //##$message = new YiiMailMessage;
                $message->setSubject($tplobj->subject)
                    ->setTo($primary_email)
                    ->setFrom($mobj->email_from, $mobj->display_name)
                    ->setReplyTo($mobj->reply_to, $mobj->display_name)
                    ->setBody($content.$pixel, 'text/html');

                $m = Yii::app()->mail;
                $m->transportOptions = array(
                                        'host' => $mobj->smtp_host,
                                        'username' => $mobj->username,
                                        'password' => $mobj->password,
                                        'port' => $mobj->smtp_port,);
                $c = $m->send($message);
                echo $mobj->username . "---------------" . $mobj->email_from . "--------------" . $mobj->reply_to;

                if (!empty($mobj->cron_out_ip) && strlen($mobj->cron_out_ip)>6) {
                    system("/sbin/iptables -t nat -D POSTROUTING 1");
                }

                $message = null;
                $mobj = null;
                $tplobj = null;
                $headers = null;
                $m = null;
                $xhdr = null;
                unset($mobj);
                unset($message);
                unset($tplobj);
                unset($headers);
                unset($m);
                unset($xhdr);
                if ($c) {
                    $attrs = array();
                    $attrs['status'] = 1;
                    $attrs['sent'] = $now;
                    $attrs['automation_id'] = 0;
                    $attrs['domain_id'] = $current_domain_id;
                    $attrs['domain'] = $domainobj->domain;
                    $attrs['primary_email'] = $primary_email;
                    $attrs['owner'] = $owner;
                    $attrs['template_id'] = $current_template_id;
                    $attrs['mailer_id'] = $current_mailer_id;
                    $attrs['queue_id'] = $queue_id;
                    $attrs['type_of_automation'] = 'client_discovery_id';
                    $attrs['client_discovery_id'] = $rl["id"];
                    Yii::app()->db->createCommand()->insert('{{automation_sent}}', $attrs);

                    /*
                    if (!in_array($domainobj->touched_status, array(6,20))) {
                        $attrs = array();
                        $attrs['touched_status'] = 2;
                        $attrs['touched'] = $now;
                        $attrs['touched_by'] = 1;
                        Yii::app()->db->createCommand()->update('{{domain}}', $attrs,
                                                'id=:id', array(':id'=>$domainobj->id));
                    }
                    */
                }
            }
        }//end of foreach

        echo "#### End s 4/14/2014######";

    }//end of function

}


function genDomainQueue($r) {
    //##$where = "WHERE (t.owner IS NOT NULL OR t.owner != '') AND (t.primary_email IS NOT NULL OR t.primary_email != '')";
    //$where = "WHERE (t.primary_email IS NOT NULL OR t.primary_email != '')";
    $where = "WHERE ldb.status=0 AND ( (t.primary_email IS NOT NULL AND t.primary_email != '') OR (t.primary_email2 IS NOT NULL AND t.primary_email2 != '') OR (dd.contactemail IS NOT NULL AND dd.contactemail != '' AND dd.contactemail != '0'))";

    if (isset($r["category"]) && $r["category"]) {
        $_category = explode("|", $r["category"]);
        $_whr = "";
        foreach ($_category as $v) {
            if ($_whr) $_whr .= " OR ";
            $_whr .= "t.category LIKE '%|".$v."|%'"; 
        }
        $where .= " AND (".$_whr.")";
    }

    if (isset($r["has_owner"])) {
        if ($r["has_owner"] == 1) {
            $where .= " AND (t.owner IS NOT NULL OR t.owner != '')";
        }
    }

    if (isset($r["site_type"]) && !empty($r["site_type"])) {
        $site_type = implode(",", $r["site_type"]);
        $where .= " AND t.stype IN (".$site_type.")";
    }

    if (isset($r["touched_status"]) && $r["touched_status"]) {
        if (is_array($r["touched_status"])) {
            $_status = implode(",", $r["touched_status"]);
        } else {
            $_status = str_replace("|", ",", $r["touched_status"]);
        }
        $where .= " AND t.touched_status IN (".$_status.")";
    }

    if ($r["alexarank"]) {
        if (stripos($r["alexarank"], "between") === false) {
            $alexavalue = $r["alexarank"];
            $op = "";
            if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$alexavalue,$matches)) {
                $alexavalue = $matches[2];
                $op = $matches[1];
            }
            if (empty($op)) $op="=";
            if ($alexavalue) $where .= " AND (t.alexarank ".$op." '".$alexavalue."')";
        } else {
            preg_match_all('/\d+/',$r["alexarank"],$alexaarr);
            $btarr = $alexaarr[0];
            if (count($btarr) == 2) {
                sort($btarr, SORT_NUMERIC);
                $where .= " AND (t.alexarank >= '".$btarr[0]."' AND t.alexarank <= '".$btarr[1]."')";
            } else {
                //Wrong alexa rank value, so we ignore it;
            }
            //#print_r($btarr);
        }
    }

    if ($r["semrushkeywords"]) {
        if ($r["semrushkeywords"] > 0) {
            $where .= " AND (rsummary.semrushkeywords>'0')";
        } elseif ($r["semrushkeywords"] < 0) {
            $where .= " AND (rsummary.semrushkeywords<'0')";
        } else {
            $where .= " AND (rsummary.semrushkeywords IS NULL)";
        }
    }

    if (isset($r["discovery_id"]) && $r["discovery_id"]) {
        $where .= " AND (ldb.discovery_id = '".$r["discovery_id"]."')";
    }

    if (!empty($r["host_country"])) {
        $host_country = trim($r["host_country"]);
        $host_country = strtoupper($host_country);
        $host_country = str_replace(" ", "", $host_country);
        $host_country = str_replace(",", "','", $host_country);
        if ($host_country) $where .= " AND (t.host_country IN ('".$host_country."'))";
    }

    if ($r["mozauthority"]) {
        $mozauthority = $r["mozauthority"];
        $op = "";
        if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$mozauthority,$matches)) {
            $mozauthority = $matches[2];
            $op = $matches[1];
        }
        if (empty($op)) $op="=";
        if ($mozauthority) $where .= " AND (rsummary.mozauthority ".$op." '".$mozauthority."')";
    }


    if ($r["sortby"]) { //if it sort by id desc;
        //#if (!empty($r["current_domain_id"])) $where .= " AND (t.id < ".$r["current_domain_id"].")";
        $where .= " ORDER BY t.id DESC";
    } else { //sort by id asc
        //#if (!empty($r["current_domain_id"])) $where .= " AND (t.id > ".$r["current_domain_id"].")";
        $where .= " ORDER BY t.id ASC";
    }


    if ($r["mozauthority"] || $r["semrushkeywords"]) {
        echo $q = "SELECT DISTINCT ldb.domain_id FROM `lkm_discovery_backdomain` AS ldb 
                   LEFT OUTER JOIN lkm_domain t ON (t.id=ldb.domain_id AND ldb.mailer_id=0)
                   LEFT OUTER JOIN lkm_domain_onpage AS dd ON (t.id = dd.domain_id)
                   LEFT OUTER JOIN lkm_domain_summary rsummary ON (t.id = rsummary.domain_id) ".
                  $where." LIMIT 0, 30";
    } else {
        //###echo $q = "SELECT t.id FROM lkm_domain t ".$where." LIMIT 0, 30";
        echo $q = "SELECT DISTINCT ldb.domain_id FROM `lkm_discovery_backdomain` AS ldb 
                   LEFT OUTER JOIN lkm_domain t ON (t.id=ldb.domain_id AND ldb.mailer_id=0)
                   LEFT OUTER JOIN lkm_domain_onpage AS dd ON (t.id = dd.domain_id) ".$where." LIMIT 0, 30";
    }

    //echo $q;
    //####echo $q = "SELECT t.id FROM lkm_domain t ".$where." LIMIT 0, 30";
    $ids = Yii::app()->db->createCommand($q)->queryAll();
    //print_r($ids);
    $domainids = array();
    if ($ids) {
        $blarr = array("status"=>-9);//means this domain is in our blacklist
        foreach ($ids as $_id) {
            $q = "SELECT id FROM `{{blacklistforauto}}` WHERE domain_id = '".$_id["domain_id"]."'";
            $bld = Yii::app()->db->createCommand($q)->queryRow();
            if ($bld && $bld["id"]) {
                $update = Yii::app()->db->createCommand()
                    ->update('{{discovery_backdomain}}', $blarr, 'discovery_id=:disid AND domain_id=:did',
                              array(':did'=>$_id["domain_id"], ':disid'=>$r["discovery_id"]) );
            } else {
                $domainids[] = $_id["domain_id"];
            }
        }
    }

    return $domainids;
}

function finishEmailTask($id){
    $cdarr = array('progress'=>5);
    $update = Yii::app()->db->createCommand()
        ->update('{{client_discovery}}', $cdarr, 'id=:id', array(':id'=>$id));
}

function extract_email_address($s) {
    $pt="/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";

    preg_match_all($pt, $s, $m);

    if (!empty($m[0])) {
        return $m[0][0];
    } else {
        return "";
    }
}

?>