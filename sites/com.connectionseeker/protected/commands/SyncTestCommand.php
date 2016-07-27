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
Yii::import('ext.yii-mail.*');
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ALL);

class SyncTestCommand extends CConsoleCommand {

    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "512M");

        $outip = "199.91.70.156";
        //$outip = "199.91.70.128";
        system("/sbin/iptables -t nat -D POSTROUTING 1");
        system("/sbin/iptables -t nat -I POSTROUTING -o eth0 -j SNAT --to-source ".$outip);

        //Autoload fix & set the X-SMTPAPI
        spl_autoload_unregister(array('YiiBase','autoload'));
        Yii::import('ext.yii-mail.XSmtpHeader', true);
        spl_autoload_register(array('YiiBase','autoload'));


        //###https://github.com/swiftmailer/swiftmailer/issues/341
        $xhdr = new XSmtpHeader();
        $xhdr->setCategory("contact1st");//initial, contact 1st time.

        $message = new YiiMailMessage;
        $headers = $message->getHeaders();

        $message->setSubject("Leo, this is a testing email.")
            ->setTo("leo@infinitenine.com")
            ->setFrom("annie.davis0103@gmail.com", "Annie")
            ->setReplyTo("annie.davis0103@gmail.com",  "Annie")
            ->setBody("Hey, Leo, test it out, please be nice.", 'text/html');

        $m = Yii::app()->mail;
        $m->transportOptions = array(
                                'host' => "ssl://smtp.gmail.com",
                                'username' => "annie.davis0103@gmail.com",
                                'password' => "jan035499",
                                'port' => 465,);
        $c = $m->send($message);
        system("/sbin/iptables -t nat -D POSTROUTING 1");

/*
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

        $imapcriteria = 'ALL SINCE "1 January 2014"';


        $dlbox = "[Gmail]/All Mail";//this one for the mailbox which you wanna download.

        echo $imapcriteria;
        $i = 0;
//        $username = "xusnug1@gmail.com";
//        $password = "xxn1027";
        $username = "haileywrites1117@gmail.com";
        $password = "pepsicola17!";
        $mailbox = new ImapMailbox('{imap.gmail.com:993/imap/notls/ssl}'.$dlbox, $username, $password, $attchs, 'utf-8');

        foreach($mailbox->searchMails($imapcriteria) as $mid) {
        //foreach($mailbox->searchMails('ALL') as $mid) {
            echo $mid." -- ";

            $mail = $mailbox->getMail($mid);
            echo $mail->subject;
        }
        unset($mailbox);//close the imap_open;
*/
    }

}

?>