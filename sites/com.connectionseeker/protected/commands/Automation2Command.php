<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php Automation p1 p2
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

class Automation2Command extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        //Autoload fix & set the X-SMTPAPI
        spl_autoload_unregister(array('YiiBase','autoload'));
        Yii::import('ext.yii-mail.XSmtpHeader', true);
        $xhdr = new XSmtpHeader();
        spl_autoload_register(array('YiiBase','autoload'));
        $xhdr->setCategory("contact1st");//initial, contact 1st time.
        $message = new YiiMailMessage;
        $headers = $message->getHeaders();
        //##Yii::app()->user->id = 1;

        $num = 5;
        if (!empty($args)) {
            $num = (int) $args[0];
        }

        $offset = 0;
        if (!empty($args) && isset($args[1])) {
            $offset = (int) $args[1];
        }

        $nowtimestamp = time();
        $hour = date("H:i", $nowtimestamp);
        $hourstamp = strtotime($hour.":00");
        $week = date("w", $nowtimestamp);
        $now = date("Y-m-d H:i:s", $nowtimestamp);
        $rules = Yii::app()->db->createCommand()->select()->from('{{automation_setting}}')
            ->where('(status = 1) AND id=11')
            ->order('modified ASC')
            ->limit($num)
            ->offset($offset)
            ->queryAll();

print_r($rules);
        if (empty($rules)) return false;
        foreach($rules as $r) {
            $mailers = $r["mailers"];
            $mailersobj = unserialize($mailers);
            $autocount = count($mailersobj);
            if ($autocount==0) continue;
            if (!empty($r["latest_senttime"])) {
echo                $timediff = $nowtimestamp - strtotime($r["latest_senttime"]);
                if ($timediff<($r["frequency"]*60)) continue;
            }
echo "-----";
            //Check today can send email or not.
            if (!empty($r["days"])) {
               $_weeks = explode("|", $r["days"]);
print_r($_weeks);
//                if (!in_array($week, $_weeks)) continue;
            }
print_r($mailers);

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
                $domainids = genDomainQueue($r);
            } else {
                //do nothing for now;
                $idstr = $r["domain_queue"];
                $domainids = explode("|", $idstr);
                if ($autocount > count($domainids)) {
                    $newids = genDomainQueue($r);
                    $domainids = array_merge($domainids, $newids);
                }
            }

            if (empty($domainids)) continue;
            $current_domain_id = array_shift($domainids);
            $domainobj = Domain::model()->findByPk($current_domain_id);
            echo $domainobj->primary_email;
            if (filter_var($domainobj->primary_email, FILTER_VALIDATE_EMAIL)) {
                //echo $domainobj->primary_email;
            } else {
                echo $current_domain_id;
                //$domainobj->primary_email = NULL;
                //$domainobj->save();
                Yii::app()->db->createCommand()->update('{{domain}}', array("primary_email"=>NULL),
                                                'id=:id', array(':id'=>$current_domain_id));
                Yii::app()->db->createCommand()->update('{{automation_setting}}', array("domain_queue" => NULL),
                                            'id=:id', array(':id'=>$r["id"]));
                continue;
            }

            //check the domain was sent recently or not?
            $yesterday = date("Y-m-d H:i:s", $nowtimestamp - 86400);//one dayago
            $q = "SELECT id FROM {{automation_sent}} WHERE sent >= '$yesterday' AND domain_id = '$current_domain_id'";
            $atm = Yii::app()->db->createCommand($q)->queryRow();
print_r($atm);
            if (empty($domainobj) || $atm) {
                //do nothing for now;
                if ($domainids) {
                    $idstr = implode("|", $domainids);
                    $r["domain_queue"] = $idstr;
                } else {
                    $r["domain_queue"] = NULL;
                }
                $r["current_domain_id"] = $current_domain_id;
print_r($r);
                Yii::app()->db->createCommand()->update('{{automation_setting}}', $r,
                                            'id=:id', array(':id'=>$r["id"]));
                continue;
            }


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

            $mobj = Mailer::model()->findByPk($current_mailer_id);
            if (empty($mobj)) continue;

            $tplobj = Template::model()->findByPk($current_template_id);
            if (empty($tplobj)) continue;

            $owner = $domainobj->owner;
            $primary_email = $domainobj->primary_email;
            if (empty($owner)) $owner = $primary_email;

            if ($domainids) {
                $idstr = implode("|", $domainids);
                $r["domain_queue"] = $idstr;
            } else {
                $r["domain_queue"] = NULL;
            }
            $r["current_domain_id"] = $current_domain_id;
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
            $attrs['to'] = trim($primary_email);
            $attrs['subject'] = $tplobj->subject;
            $attrs['content'] = $content;
            $attrs['parent_id'] = 0;
            $attrs['email_from'] = $mobj->email_from;
            print_r($attrs);
            $rs = Yii::app()->db->createCommand()->insert('{{email_queue}}', $attrs);
            $queue_id = Yii::app()->db->getLastInsertID();
            if($rs) {
                Yii::app()->db->createCommand()->update('{{automation_setting}}', $r,
                                            'id=:id', array(':id'=>$r["id"]));

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
                if ($c) {
                    $attrs = array();
                    $attrs['status'] = 1;
                    $attrs['sent'] = $now;
                    $attrs['automation_id'] = $r["id"];
                    $attrs['domain_id'] = $current_domain_id;
                    $attrs['domain'] = $domainobj->domain;
                    $attrs['primary_email'] = $primary_email;
                    $attrs['owner'] = $owner;
                    $attrs['template_id'] = $current_template_id;
                    $attrs['mailer_id'] = $current_mailer_id;
                    $attrs['queue_id'] = $queue_id;
                    Yii::app()->db->createCommand()->insert('{{automation_sent}}', $attrs);

                    if (!in_array($domainobj->touched_status, array(6,20))) {
                        $attrs = array();
                        $attrs['touched_status'] = 2;
                        $attrs['touched'] = $now;
                        $attrs['touched_by'] = 1;
                        Yii::app()->db->createCommand()->update('{{domain}}', $attrs,
                                                'id=:id', array(':id'=>$domainobj->id));
                    }
                }
            }
        }//end of foreach

    }//end of function

}


function genDomainQueue($r) {
    //##$where = "WHERE (t.owner IS NOT NULL OR t.owner != '') AND (t.primary_email IS NOT NULL OR t.primary_email != '')";
    $where = "WHERE (t.primary_email IS NOT NULL OR t.primary_email != '') AND (t.status=1)";
    if ($r["category"]) {
        $_category = explode("|", $r["category"]);
        $_whr = "";
        foreach ($_category as $v) {
            if ($_whr) $_whr .= " OR ";
            $_whr .= "t.category LIKE '%|".$v."|%'"; 
        }
        $where .= " AND (".$_whr.")";
    }

    if ($r["has_owner"]) {
        if ($r["has_owner"] == 1) {
            $where .= " AND (t.owner IS NOT NULL OR t.owner != '')";
        }
    }

    if ($r["touched_status"]) {
        $_status = str_replace("|", ",", $r["touched_status"]);
        $where .= " AND t.touched_status IN (".$_status.")";
    }

    if ($r["alexarank"]) {
        $alexavalue = $r["alexarank"];
        $op = "";
        if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/',$alexavalue,$matches)) {
            $alexavalue = $matches[2];
            $op = $matches[1];
        }
        if (empty($op)) $op="=";
        if ($alexavalue) $where .= " AND (t.alexarank ".$op." '".$alexavalue."')";
    }

    if ($r["semrushkeywords"]) {
        if ($r["semrushkeywords"] > 0) {
            $where .= " AND (rsummary.semrushkeywords>'0')";
        } elseif ($r["semrushkeywords"] < 0) {
            $where .= " AND (rsummary.semrushkeywords<'0')";
        } else {
            $where .= " AND (rsummary.semrushkeywords IS NULL')";
        }
    }

    if (!empty($r["host_country"])) {
        $host_country = trim($r["host_country"]);
        $host_country = strtoupper($host_country);
        if ($host_country) $where .= " AND (t.host_country = '".$host_country."')";
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
        if (!empty($r["current_domain_id"])) $where .= " AND (t.id < ".$r["current_domain_id"].")";
        $where .= " ORDER BY t.id DESC";
    } else { //sort by id asc
        if (!empty($r["current_domain_id"])) $where .= " AND (t.id > ".$r["current_domain_id"].")";
        $where .= " ORDER BY t.id ASC";
    }

    if ($r["mozauthority"] || $r["semrushkeywords"]) {
        echo $q = "SELECT t.id FROM lkm_domain t LEFT OUTER JOIN lkm_domain_summary rsummary ON (t.id = rsummary.domain_id) ".
                  $where." LIMIT 0, 30";
    } else {
        echo $q = "SELECT t.id FROM lkm_domain t ".$where." LIMIT 0, 30";
    }

    //####echo $q = "SELECT t.id FROM lkm_domain t ".$where." LIMIT 0, 30";
    $ids = Yii::app()->db->createCommand($q)->queryAll();
    //print_r($ids);
    $domainids = array();
    if ($ids) {
        foreach ($ids as $_id) {
            $domainids[] = $_id["id"];
        }
    }

    return $domainids;
}


?>
