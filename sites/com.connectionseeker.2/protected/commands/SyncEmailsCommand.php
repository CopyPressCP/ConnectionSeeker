<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php syncTopBacklinks
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncEmails p1 p2
the $args will returns as following
array(
    [0] => p1
    [1] => p2
)

php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php SyncEmails 1 0
This feature will Sync the replied emails from different EMail ISP account.

http://www.jwz.org/doc/threading.html
http://people.dsv.su.se/~jpalme/ietf/message-threading.html
RFC822: http://003317.blog.51cto.com/2005292/611104
http://cn2.php.net/manual/zh/function.imap-list.php
Array
(
    [0] => {imap.gmail.com:993/imap/notls/ssl}INBOX
    [1] => {imap.gmail.com:993/imap/notls/ssl}[Gmail]/All Mail
    [2] => {imap.gmail.com:993/imap/notls/ssl}[Gmail]/Drafts
    [3] => {imap.gmail.com:993/imap/notls/ssl}[Gmail]/Important
    [4] => {imap.gmail.com:993/imap/notls/ssl}[Gmail]/Sent Mail
    [5] => {imap.gmail.com:993/imap/notls/ssl}[Gmail]/Spam
    [6] => {imap.gmail.com:993/imap/notls/ssl}[Gmail]/Starred
    [7] => {imap.gmail.com:993/imap/notls/ssl}[Gmail]/Trash
)
*/

Yii::import('application.vendors.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class SyncEmailsCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $num = 10;
        if (!empty($args)) {
            $num = (int) $args[0];
        }

        $offset = 0;
        if (!empty($args)) {
            $offset = (int) $args[1];
        }

        //$attchs = dirname(dirname(__FILE__)) . DS . "runtime" . DS;
        $attchs = false;

        $accounts = Yii::app()->db->createCommand()->select()->from('{{mailer_account}}')
            ->where('(status = 1) AND (pop3_host IS NOT NULL) AND (pop3_port IS NOT NULL) AND (pop3_host != "")')
            //->order('synced ASC')
            ->order('synced DESC')
            ->limit($num)
            ->offset($offset)
            ->queryAll();
        //print_r($accounts);

        $now = time();
        $today = date("Y-m-d H:i:s", $now);
        if (!empty($accounts)) {
            //echo count($accounts);
            foreach ($accounts as $av) {
                $dcols = array();
                print_r($av);

                if (empty($av['pop3_host']) || empty($av['pop3_port'])) {
                    continue;
                }
                $host = $av['pop3_host'];
                $port = $av['pop3_port'];
                $username = $av['username'];
                $password = $av['password'];
                if (stripos($host, "imap.") === false) {
                    $ptl = "pop";
                } else {
                    $ptl = "imap";
                }
                echo $ptl;

                if ($av['synced']) {
                    //$imapcriteria = 'ALL SINCE "8 August 2012"';
                    $imapcriteria = 'ALL SINCE "'.date("j F Y", $av['synced']).'"';
                } else {
                    $imapcriteria = 'ALL';
                }

                $dlbox = $av['mailbox'] ? $av['mailbox'] : "INBOX";//this one for the mailbox which you wanna download.

                echo $imapcriteria;
                $i = 0;

                //$mailbox = new ImapMailbox('{'."$host:$port/$ptl".'/notls/ssl}INBOX', $username, $password, $attchs, 'utf-8');
                //$mailbox = new ImapMailbox('{'."$host:$port/$ptl".'/notls/ssl}[Gmail]/Sent Mail', $username, $password, $attchs, 'utf-8');
                $mailbox = new ImapMailbox('{'."$host:$port/$ptl".'/notls/ssl}'.$dlbox, $username, $password, $attchs, 'utf-8');

                //foreach($mailbox->searchMails('SUBJECT "HOWTO be Awesome" SINCE "8 August 2012"') as $mid) {
                //foreach($mailbox->searchMails('SINCE "11 October 2012"') as $mid) {
                foreach($mailbox->searchMails($imapcriteria) as $mid) {
                //foreach($mailbox->searchMails('ALL') as $mid) {
                    echo $mid." -- ";
                    //###break;
                    //Check this email is downloaded or not.
                    $dlalready = Yii::app()->db->createCommand()->select()->from('{{email_queue}}')
                        ->where('(`from` = :mailer) AND (mid = :mid)', array(':mailer'=>$av["id"],':mid'=>$mid))->queryRow();
                    if ($dlalready) {
                        continue;
                    }

                    $mail = $mailbox->getMail($mid);
                    if (!isset($mail->to) || empty($mail->to)) {//for the draft Box
                        continue;
                    }
                    //print_r($mail);
                    //echo $mail->mId.") (";
                    //continue;
                    //$mails[] = $mail;
                    $emlqueue = array();

                    $emlqueue["mid"] = $mid;
                    $emlqueue["message_id"] = $mail->messageId;

                    #################################
                    $parentmail = array();
                    if (isset($mail->inReplyTo)) {
                        $emlqueue["is_reply"] = 1;
                        $inreplyto = $mail->inReplyTo;
                        $parentmail = Yii::app()->db->createCommand()->select()->from('{{email_queue}}')
                            ->where('(message_id = :msgid)', array(':msgid'=>$inreplyto))->queryRow();
                        if ($parentmail) {
                            $emlqueue["parent_id"] = $parentmail["id"];
                            $emlqueue["domain_id"] = $parentmail["domain_id"];
                            $emlqueue["template_id"] = $parentmail["template_id"];
                            $emlqueue["from"] = $parentmail["from"];
                        }
                    }

                    if (isset($mail->xSmtpApi)) {
                        $xsmtpapi = json_decode($mail->xSmtpApi, true);
                        $unqargs = $xsmtpapi["unique_args"];
                        if ($unqargs) {
                            if ($unqargs["template_id"]) $emlqueue["template_id"] = $unqargs["template_id"];
                            //if ($unqargs["domain_id"]) $emlqueue["domain_id"] = $unqargs["domain_id"];
                            if (isset($unqargs["queue_id"])) {
                                //$emlqueue["domain_id"] = $unqargs["domain_id"];
                                Yii::app()->db->createCommand()->update('{{email_queue}}', $emlqueue,
                                                                        'id=:id', array(':id'=>$unqargs["queue_id"]));
                                continue;
                            }
                        }
                    }
                    #################################

                    $emlqueue["subject"] = $mail->subject;
                    $emlqueue["content"] = $mail->textHtml;
                    if (empty($emlqueue["content"])) {
                        $emlqueue["content"] = $mail->textPlain;
                    }
                    $tos     = $mail->to;
                    //get the first elements of the array as the "to"
                    foreach ($tos as $teml => $talias) {
                        break;
                    }

                    $emlqueue["send_time"]  = $synced  = $mail->date;
                    $synced = strtotime($synced);
                    $emlqueue["email_from"] = $mail->fromAddress;
                    $emlqueue["to"]   = $teml;
                    $emlqueue["status"] = 1;

                    if (empty($talias)) $talias = $teml;
                    if (isset($mail->inReplyTo) && $parentmail) {
                        Yii::app()->db->createCommand()->insert('{{email_queue}}', $emlqueue);
                        continue;
                    }


                    $existacct = Yii::app()->db->createCommand()->select()->from('{{mailer_account}}')
                        ->where('(display_name = :ualias) AND (email_from = :efrom OR reply_to = :reply)', 
                                array(':ualias'=>$talias,':efrom'=>$teml,':reply'=>$teml))->queryRow();
                    print_r($existacct);
                    if ($existacct) {
                        $acountid = $existacct["id"];
                        $emlqueue["from"] = $acountid;
                        $everqueue = Yii::app()->db->createCommand()->select()->from('{{email_queue}}')
                            ->where('(`from` = :from) AND (`to` = :eto) AND (email_from = :efrom) AND (send_time IS NOT NULL)', 
                                    array(':from'=>$acountid,':eto'=>$emlqueue["email_from"],':efrom'=>$teml))->queryRow();
                    } else {
                        $subject = preg_replace("/^Re: /i", "", $emlqueue["subject"]);

                        $everqueue = Yii::app()->db->createCommand()->select()->from('{{email_queue}}')
                            ->where('(subject = :subject) AND (`to` = :eto) AND (email_from = :efrom) AND (send_time IS NOT NULL)', 
                                    array(':subject'=>$subject,':eto'=>$emlqueue["email_from"],':efrom'=>$teml))->queryRow();
                        if ($everqueue) {
                            $acountid = $everqueue["from"];
                            $emlqueue["from"] = $acountid;
                        } else {
                            $emlqueue["from"] = $av["id"];
                            $everqueue = Yii::app()->db->createCommand()->select()->from('{{email_queue}}')
                                ->where('(`from` = :from) AND (`to` = :eto) AND (email_from = :efrom) AND (send_time IS NOT NULL)', 
                                        array(':from'=>$av["id"],':eto'=>$emlqueue["email_from"],':efrom'=>$teml))->queryRow();
                        }
                        echo $subject;
                    }

                    if ($everqueue) {
                        $emlqueue["parent_id"] = $everqueue["id"];
                        $emlqueue["domain_id"] = $everqueue["domain_id"];
                        $emlqueue["template_id"] = $everqueue["template_id"];
                    } else {
                        $emlqueue["parent_id"] = 0;
                        $emlqueue["template_id"] = 0;//here we should pay attention to
                        $emlqueue["domain_id"] = 0;//here we should pay attention to
                        $domainexist = Yii::app()->db->createCommand()->select()->from('{{domain}}')
                            ->where('(primary_email=:pem) OR (email=:pem)', array(':pem'=>$emlqueue["email_from"]))->queryRow();
                        if ($domainexist) {
                            $emlqueue["domain_id"] = $domainexist["id"];
                        }
                    }
                    //$emlqueue["is_reply"] = 1;

                    //$emlqueue["from"]     = //??????;
                    //$emlqueue["parent_id"]= //??????;
                    print_r($emlqueue);
                    Yii::app()->db->createCommand()->insert('{{email_queue}}', $emlqueue);

                    if ($i % 200 == 0) {
                        if (!isset($synced)) $synced = $now - 86400;//minus 1 day for different timezone.
                        $dcols['synced'] = $synced;
                        Yii::app()->db->createCommand()->update('{{mailer_account}}', $dcols, 'id=:id', array(':id'=>$av['id']));
                    }
                    $i++;
                }
                unset($mailbox);//close the imap_open;

                if (!isset($synced)) $synced = $now - 86400;//minus 1 day for different timezone.
                $dcols['synced'] = $synced;
                //print_r($dcols);
                Yii::app()->db->createCommand()->update('{{mailer_account}}', $dcols, 'id=:id', array(':id'=>$av['id']));
            }

        }
        //print_r($domains);
    }

}

?>