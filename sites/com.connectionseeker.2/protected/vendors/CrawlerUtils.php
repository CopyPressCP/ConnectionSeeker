<?php
/**
 * the following methods will be relatd the Crawler of Domain profile
 *
 *
 * @copyright    Copyright 2012 infinitenine.com Web Development - All Rights Reserved
 * @package      CrawlerUtils
 * @since        V1.0.1
 * @version      $Revision: 18 $
 * @modifiedby   $LastChangedBy: Leo $
 * @lastmodified $Date: 10/29/2012 $
 * @license      http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class CrawlerUtils {

    /**
     * @var array Parameters for the CrawlerUtils
     */
    public $params;

    function sip($domain){
        error_reporting(NULL);

        $domain  = preg_replace('#^https?://#', '', $domain);
        $da = dns_get_record($domain, DNS_A);
        /*
        try {
            $da = dns_get_record($domain, DNS_A);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return array();
        }
        */

        $dv = array();
        if ($da) {
            foreach ($da as $d) {
                if ($d["type"] == "A") {
                    $dv["ip"] = $ip = $d["ip"];
                    $hostips = SeoUtils::getCountryByIP($ip);
                    if ($hostips) {
                        $dv["host_country"] = trim($hostips["scn"]);
                        $dv["host_city"] = trim($hostips["city"]);
                    }
                    break;
                } else {
                    continue;
                }
            }
        }

        return $dv;
    }


    function sgooglepr($domain){
        //if (!class_exists("SEOstats")) {
            require_once(Yii::app()->BasePath . "/vendors/SEOstats/src/class.seostats.php");
        //}

        $dv = array();
        $rootdomain = SeoUtils::getDomain($domain);
        if ($domain == $rootdomain) {
            $homepage = "http://www." . $domain;
        } else {
            $homepage = "http://" . $domain;
        }
        $s = new SEOstats($homepage);
        $googlepr = $s->Google_Page_Rank();
        if (is_numeric($googlepr)) {
            $dv['googlepr'] = $googlepr;
        }

        return $dv;
    }

    function sonlinesince($domain){
        $rootdomain = SeoUtils::getDomain($domain);
        $onlinesince = SeoUtils::getDomainCreatedOn($rootdomain);
        if (is_numeric($onlinesince)) {
            $dv['onlinesince'] = $onlinesince;
        } else {
            $dv['onlinesince'] = strtotime($onlinesince);
        }

        return $dv;
    }

    function salexarank($domain){
        $dcols = array();
        $alexa = SeoUtils::getAlexaDomainInfo($domain);
        //print_r($alexa);

        if (!empty($alexa)) {
            $dv = Domain::model()->findByAttributes(array("domain"=>$domain));

            if (isset($alexa['title'])) $dcols['title'] = $alexa['title'];
            if (isset($alexa['email']) && empty($dv['email'])) $dcols['email'] = $alexa['email'];
            if (isset($alexa['number']) && empty($dv['telephone'])) $dcols['telephone'] = $alexa['number'];
            if (isset($alexa['owner'])) $dcols['owner'] = $alexa['owner'];
            if (isset($alexa['street'])) $dcols['street'] = $alexa['street'];
            if (isset($alexa['city'])) $dcols['city'] = $alexa['city'];
            if (isset($alexa['zip'])) $dcols['zip'] = $alexa['zip'];
            if (isset($alexa['state'])) $dcols['state'] = $alexa['state'];
            if (isset($alexa['country'])) $dcols['country'] = $alexa['country'];
            if (isset($alexa['traffic'])) $dcols['alexarank'] = $alexa['traffic'];

            if (isset($alexa['date']) && (empty($dv['onlinesince']) || $dv['onlinesince'] == 658454400 || $dv['onlinesince'] == -1)) $dcols['onlinesince'] = strtotime($alexa['date']);

            if (isset($alexa['cats'])) {
                $dcols['awis_category_str'] = implode(",", $alexa['cats']);
                $cscats = Utils::categoryMapping($alexa['cats']);

                if ($dv['category']) {
                    $_existcat = substr($dv['category'], 1, -1);
                    //echo $_existcat;
                    if (!empty($_existcat)) {
                        $_es = explode("|", $_existcat);
                        //print_r($cscats);
                        //print_r($_es);
                        $cscats = array_merge($cscats, $_es);
                        $cscats = array_unique($cscats);
                        //print_r($cscats);
                    }
                }
                if ($cscats) {
                    $dcols['category'] = "|".implode("|", $cscats)."|";
                    $_types = Types::model()->bytype(array("category"))->findAllByAttributes(array("refid"=>$cscats));
                    $_cats = CHtml::listData($_types, 'refid', 'typename');
                    $dcols['category_str'] = implode(",", $_cats);
                }
            }
        }

        return $dcols;
    }

    function smozrank($domain){
        $accessID = "member-41b9bb683d";
        $secretKey = "5e07bbb01774fee92ed680bc62e56c8d";
        $expires = time() + 300;
        $stringToSign = $accessID."\n".$expires;
        $binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
        $urlSafeSignature = urlencode(base64_encode($binarySignature));
        $cols = "68719591424";//please reference http://apiwiki.seomoz.org/url-metrics#urlmetricsbitflags

        $domain  = preg_replace('#^https?://#', '', $domain);

        $req = "http://lsapi.seomoz.com/linkscape/url-metrics/"
               .urlencode($domain)."?Cols=".$cols."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
        //echo $req;
        $mozrs = file_get_contents($req, true);
        if ($mozrs) {
            $moz = json_decode($mozrs, true);
            //print_r($moz);
            
            $dv["mozauthority"] = $moz{"pda"};
            $subdomain = SeoUtils::getDomain($domain);
            if ($subdomain == $domain) {
                $dv["mozrank"] = $moz{"pmrp"};
            } else {
                $dv["mozrank"] = $moz{"fmrp"};
            }

            return $dv;
        }
    }

    function sacrank($domain){
        //do nothing for now;
    }

    function ssemrushor($domain){
        //##http://www.semrush.com/api.html
        //$domain = SeoUtils::getDomain($domain);

        $dv = array();
        $ctx = stream_context_create(array(
           'http' => array(
               'timeout' => 3600
               )
           )
        );

        $key = "fb2e3c1d78b42415e87fa8424ed72f43";
        $columns = "Or";

        $req = "http://us.api.semrush.com/?action=report&type=domain_rank&key=".$key.
               "&export=api&export_columns=".$columns."&domain=".$domain;

        //echo $semrush = file_get_contents($req, true, $ctx);
        $semrush = file_get_contents($req, true, $ctx);

        /*
        if(stripos($semrush,'NOTHING FOUND') !== false) {
            $dv["semrushor"] = 0;
        } else */
        if(stripos($semrush,'ERROR') !== false) {
            preg_match("/^ERROR (?P<errorcode>\d+) :: [\s\S]*/imu", $semrush, $matches);
            $dv["semrushor"] = 0 - (int)$matches["errorcode"];
            //Send email to us;
            //Utils::notice(array('content'=>"SemRush for '$domain': ".$semrush));
        } else {
            if (preg_match("/^Organic Keywords(?:\r?\n){1,}(?P<semrushor>\d+)/imu", $semrush, $matches)) {
                $dv["semrushor"] = (int)$matches["semrushor"];
            } else {
                //send a notice email to us
                $dv["semrushor"] = -1;
                //Utils::notice(array('content'=>"SemRush for '$domain': unexpected ERROR comes up."));
            }
        }
        //print_r($matches);
        unset($semrush);

        return $dv;
    }
}

?>