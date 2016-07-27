<?php
//http://www.yiiframework.com/wiki/91/implementing-cron-jobs-with-yii/
//http://www.yiiframework.com/forum/index.php?/topic/26551-setting-up-cronjob/
//How to call this script in crontab: php /path/to/cron.php GetDomainType
//http://hudeyong926.iteye.com/blog/1283125 路径引用总结
/*
I:\xampp\php>php I:\htdocs\connectionseeker\cron.php GetDomainType p1
the $args will returns as following
array(
    [0] => p1
)
Please query this sql before start to run this cronjob
#added by nancy xu 2012-08-31 11:19
ALTER TABLE `lkm_domain` ADD `sitetype` VARCHAR( 255 ) NULL AFTER `street` ,
ADD `scaned` DATETIME NULL AFTER `sitetype` ;
#end
*/
Yii::import('application.vendors.*');
require_once(Yii::app()->BasePath . "/vendors/simplehtmldom/simple_html_dom.php");
class GetDomainTypeCommand extends CConsoleCommand {
    public function run($args) {
        //ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set("memory_limit", "128M");
//        echo isBlogDomain('luxuryrealestate.com');
//        return false;
//        $html = str_get_html('<html><body><a href="http://www.google.com">Hello!</a><a href="http://www.google.com">2Hello!</a></body></html>');
//        print_r($html->find('a',0)->find('text',0));
//        return false;
//        echo isWordPressDomain('asiapacificscreenacademy.com');return false;
//        isBlogDomain('engadget.com');
//        return false;
//        echo isDrupalDomain('soso.com');
//        return false;
//        echo isDrupalDomain('drupal.org'); return false;
        $num = 10;
        if (!empty($args)) {
            $num = (int) $args[0];
        }

        //'id, domain, googlepr, onlinesince, alexarank'
        $domains = Yii::app()->db->createCommand()->select()->from('{{domain}}')
            ->where('(scaned IS NULL) AND stype = 8')
            ->limit($num)
            ->queryAll();
            //->queryRow();

        $now = date("Y-m-d H:i:s");
        if (!empty($domains)) {
            foreach ($domains as $dv) {
                $dcols = array();
                $domain = $dv['domain'];
                /*if (checkdnsrr($domain)) {
         
                }*/
                echo $domain . "\n";

                $dcols['stype'] = 1;
                if (isDrupalDomain($domain)) {
                    $dcols['cmsbuilder'] = 4;//Drupal
                } else if (isWordPressDomain($domain)) {
                    $dcols['cmsbuilder'] = 2;//wordpress
                } else if (isJoomlaDomain($domain)) { 
                    $dcols['cmsbuilder'] = 3;//Joomla
                } else if (isBlogDomain($domain)) {
                    $dcols['cmsbuilder'] = 1;//Others CMS SYSTEM
                } else {
                    unset($dcols['stype']);
                }
                $dcols['scaned'] = $now;
                //print_r($dcols);
                //stype = 8 means alexa
                Yii::app()->db->createCommand()->update('{{domain}}', $dcols, 'id=:id', array(':id'=>$dv['id']));
            }

        }
        //print_r($domains);
    }

}
function isWordPressDomain($domain)
{
    $url = $domain . '/wp-admin/';
    if (!$content = getUrlContent($url)) {
        if ($content = getUrlContent($domain)) {
            $html = str_get_html($content);
            $a = $html->find('meta[content^=WordPress]',0);
            if (is_object($a)) return true;
        }
        return false;
    }
    if (!empty($content)) {
         $html = str_get_html($content);
         $a = $html->find('a[title=Powered by WordPress]', 0);
         if (!is_object($a)) $a = $html->find('a[title$=WordPress.]', 0);
         if (!is_object($a)) $a = $html->find('a[title$=WordPress]', 0);
         if (is_object($a)) return true;
    }
    return false;
}

function isJoomlaDomain($domain)
{
    $url = $domain . '/administrator';
    if (!$content = getUrlContent($url)) {
        if (!$content = getUrlContent($domain))
            return false;
    }
    if (!empty($content)) {
         $html = str_get_html($content);
         $obj = $html->find('meta[content^=Joomla!]',0);
         if (is_object($obj)) {
             return true;
         }
    }
    return false;
}
function isDrupalDomain($domain)
{
    if (!$content = getUrlContent($domain . '/misc/drupal.js')) {
        if (!$content = getUrlContent($domain)) {
            return false;
        }
        if (!empty($content)) {
             $html = str_get_html($content);
             $obj = $html->find('meta[content^=Drupal]',0);
             if (is_object($obj)) {
                 return true;
             }
        }
    } else if (preg_match('/[ ]+Drupal[ ]+/ims', $content)) {
        return true;
    }
    return false;
}
function isBlogDomain($domain)
{
    $max_link = 20;
    if ($content = getUrlContent($domain)) {
        $html = str_get_html($content);
        $a = $html->find('a[href*=' . $domain . ']');
        if (empty($a)) $a = $html->find('a[href!=http]');
        if (!empty($a)) {
            $i = 1;
            foreach ($a as $k => $obj) {
                //echo $obj->innertext . "\n" ;
                if (!preg_match('/<img[^>]+\>/ims', $obj->innertext) && preg_match('/blog/ims',$obj->plaintext)) return true;
                if ($i>=$max_link) return false;
                $i++;
            }
        }
    }
    return false;
}
function getUrlContent($url)
{
    if ($fp = curl_init($url)) {
        curl_setopt($fp, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727; InfoPath.1; CIBA)");
        curl_setopt($fp, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($fp, CURLOPT_FOLLOWLOCATION,1);
        curl_exec($fp);
        $content = false;
        $code = curl_getinfo($fp, CURLINFO_HTTP_CODE);
        if ($code == 200) { 
            $content = curl_multi_getcontent($fp);
        }
        curl_close($fp);
        return $content;
    }
    return false;
}
?>