<?php
/**
 * Seo stuff will be here
 *
 *
 * @copyright    Copyright 2009 PBM Web Development - All Rights Reserved
 * @package      weatherForecast
 * @since        V1.0.0
 * @version      $Revision: 15 $
 * @modifiedby   $LastChangedBy: Chris $
 * @lastmodified $Date: 2009-10-18 11:14:07 +0100 (Sun, 18 Oct 2009) $
 * @license      http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class SeoUtils {

    /**
     * @var array Parameters for the SeoUtils
     */
    public $params;

    /**
    * @var mixed integer: the number of seconds in which the cached forecast will expire; array: (the number of seconds in which the cached forecast will expire, cache dependency object); boolean false: no cacheing
    * @link http://www.yiiframework.com/doc/api/CCache
    * @link http://www.yiiframework.com/doc/api/CCacheDependency
    */
    public $cache = 7776000; // cache 90 days: 3600 sec * 24 hour * 90 days


    public function googleLocality($locality = "google.com") {
        //$se_localitys = Configure::read('searchLocality');
        //in_array($locality, array_keys($se_localitys));
        $api = "http://www.google.com/search?hl=en";

        switch ($locality) {
            case "google.com":
                $api = "https://www.google.com/search?hl=en";
                break;
            case "google.ca":
                $api = "https://www.google.ca/search?hl=en";
                break;
            case "google.uk":
                $api = "https://www.google.uk/search?hl=en";
                break;
            case "google.cn":
                $api = "https://www.google.cn/search?hl=zh-CN";
                break;
            default:
                $api = "https://www.google.com/search?hl=en";
                break;
        }

        return $api;
    }

    public function getDomain($url) {
        if (($pos = stripos($url, 'http://')) === false && ($pos = stripos($url, 'https://')) === false) {
            $url = "http://".$url;
        }

        $pieces = @parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        //maybe we can use the emun way to get the domain, just like:http://www.lampbrother.net/handbooks/PHP/function.parse-url.html
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            //return strtolower($regs['domain']);
            $dm = $regs['domain'];
            if (stripos($regs['domain'], 'www.') === 0) {
                $dm = str_ireplace("www.", "", $dm);
            }
            return strtolower($dm);
        }

        if (stripos($domain, 'www.') === 0) {
            $domain = str_ireplace("www.", "", $domain);
        }

        if (empty($domain)) $domain = $url;
        return strtolower($domain);
    }

    public function getHost($url) {
        if (($pos = stripos($url, 'http://')) === false && ($pos = stripos($url, 'https://')) === false) {
            $url = "http://".$url;
        }
        $pieces = @parse_url($url);
        //print_r($pieces);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            $d = $regs['domain'];
            if (($pos = stripos($domain, $regs['domain'])) === false) {} else {
                $subdomain = substr($domain, 0, $pos);
                if (strtolower($subdomain) == "www.") {
                    $domain = $regs['domain'];
                }
            }
        }

        if (empty($domain)) $domain = $url;
        return strtolower($domain);
    }


    // $number is how many result you want get from google
    public function paserGoogleResults($keyword, $number = 10, $locality = "google.com") {
        if (!function_exists("file_get_html")) {
            include_once("simplehtmldom/simple_html_dom.php");
        }

        set_time_limit(0);
        ini_set("memory_limit", "128M");

        $api = self::googleLocality($locality);

        //$gp = ceil($number / 10);//google page number;

        $rs = array();
        $domianrs = array();
        $rtn = false; // control the return result, if there no result, it will return null

        /*
        $html = @file_get_html($api.'&q='.urlencode($keyword)."&num={$number}&btnG=Search&aq=f&aqi=&oq=");
        //$html = @file_get_html("http://dev.connectionseeker.com/a.txt");
        if (!is_object($html)) return null;
        */

        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (empty($ua)) {
            $ua = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729) FirePHP/0.4\r";
        }
        $api .= '&q='.urlencode($keyword)."&num={$number}&btnG=Search&aq=f&aqi=&oq=";
        // Create a stream
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                      "Referer: https://www.google.com\r\n".
                      "User-Agent: {$ua}\n"
            )
        );

        $context = stream_context_create($opts);
        $html = @file_get_html($api, false, $context);
        //print_r($html);

        $res = $html->find("div[id=ires] ol li h3 a");
        if (!empty($res)) {
            $i = 0;

            //echo $number;
            foreach ($res as $rv) {
                $idx = $i; // array index

                $url = $rv->href;
                if (($pos = stripos($url, 'http://')) === false && ($pos = stripos($url, 'https://')) === false) {
                    continue;
                }
                $domain = self::getDomain($url);
                $domain = strtolower($domain);
                $shorturl = $rv->parent()->parent()->last_child()->children(0)->plaintext;
                if (!empty($shorturl)) $shorturl = str_ireplace(" - Cached","",$shorturl);
                $describe = $rv->parent()->parent()->last_child()->last_child(0)->plaintext;;

                $rs[$domain][] = array('url' => $url,
                                       'domain' => $domain,
                                       'shorturl' => $shorturl,
                                       'title' => $rv->plaintext,
                                       'describe' => $describe);

                $domianrs[$domain]['domain'] = $domain;
                $domianrs[$domain]['hubcount'] += 1;

                //if we don't wanna set these value as below here, we can set it in GoogleSearchController::actionStatus also
                $domianrs[$domain]['googlepr'] = 0;
                $domianrs[$domain]['alexarank'] = 0;
                $domianrs[$domain]['onlinesince'] = 0;
                $domianrs[$domain]['seostatus'] = "";
                //http://www.opensiteexplorer.org/comparisons.html?no_redirect=1&page=1&site=www.seobook.com
                $domianrs[$domain]['inboundlinks'] = 0;
                $domianrs[$domain]['linkingdomains'] = 0;

                $i ++;

            }

            //print_r($rs);
            /*
            foreach ($res as $rv) {
                $idx = $i; // array index
                $rs[$idx]['url'] = $rv->href;
                $rs[$idx]['title'] = $rv->plaintext;
                $domain = self::getDomain($rs[$idx]['url']);
                $domain = strtolower($domain);
                $rs[$idx]['domain'] = $domain;
                $domianrs[$domain] = $domain;
                //echo $rv->href;
                //echo $rv->plaintext;
                $i ++;
                if ($i > 9) break; // cause the google only return 10 results per page.
            }

            $i = 0;
            $res = $html->find("div[id=res] ol li div[class=s]");
            foreach ($res as $rv) {
                $idx = $gpstart + $i; // array index
                //$rv->getElementByTagName('cite')->outertext = "";
                $rs[$idx]['describe'] = $rv->plaintext;
                $i ++;
                if ($i > 9) break; // because the google only return 10 results per page.
            }
            */

            $rtn = true;
            //return $rs;
        }

        // clean up memory
        $html->clear();
        unset($html);

        if ($rtn) {
            return array('cpt_domain' => $domianrs, 'google_results' => $rs, 'number'=>$number);
        }

        return null;
    }

    public function seoInfo($url, $exec = array()) {
        //$exec should be like: array('googlepr','alexainfo','onlinesince');

        if (!class_exists("SEOstats")) {
            require_once("SEOstats/src/class.seostats.php");
        }

        if (empty($url)) {
            return null;
        }
        if (!is_array($exec)) return null;

        $domain = self::getDomain($url);

        $rs = array();
        $s = new SEOstats($url);
        if (empty($exec)) {
            $rs[$domain]['googlepr'] = $s->Google_Page_Rank();
            if (!is_numeric($rs[$domain]['googlepr'])) $rs[$domain]['googlepr'] = -1;
            //$rs[$domain]['alexa'] = $s->Alexa_Global_Rank_Array();
            //$rs[$domain]['seomoz'] = $s->Seomoz_Domainauthority_Array();
            $rs[$domain]['alexainfo'] = self::getAlexaDomainInfo($domain);
            if (empty($rs[$domain]['alexainfo'])) {
                $rs[$domain]['alexarank'] = 0;
            } else {
                $rs[$domain]['alexarank'] = $rs[$domain]['alexainfo']['traffic'];
            }
            $rs[$domain]['domaincreatedon'] = self::getDomainCreatedOn($domain);
            if ($rs[$domain]['domaincreatedon'] == -1) {
                $rs[$domain]['onlinesince'] = 0;
            } else {
                $rs[$domain]['onlinesince'] = strtotime($rs[$domain]['domaincreatedon']);
            }
            $mjinfo = self::getMajesticseExplorer($url);
            if (empty($mjinfo) && !is_array($mjinfo)) {
                $rs[$domain]['inboundlinks'] = -1;
                $rs[$domain]['linkingdomains'] = -1;
            } else {
                $rs[$domain]['inboundlinks'] = $mjinfo['inboundlinks'];
                $rs[$domain]['linkingdomains'] = $mjinfo['linkingdomains'];
            }
        } else {
            $exec = array_values($exec);

            if (in_array('googlepr', $exec)) $rs[$domain]['googlepr'] = $s->Google_Page_Rank();
            if (!is_numeric($rs[$domain]['googlepr'])) $rs[$domain]['googlepr'] = -1;

            if (in_array('alexainfo', $exec)) {
                $rs[$domain]['alexainfo'] = self::getAlexaDomainInfo($domain);
                //$rs[$domain]['alexarank'] = $rs[$domain]['alexainfo']['traffic'];
                if (empty($rs[$domain]['alexainfo'])) {
                    $rs[$domain]['alexarank'] = 0;
                } else {
                    $rs[$domain]['alexarank'] = $rs[$domain]['alexainfo']['traffic'];
                }
            }
            if (in_array('onlinesince', $exec)) {
                $rs[$domain]['domaincreatedon'] = self::getDomainCreatedOn($domain);
                if ($rs[$domain]['domaincreatedon'] == -1) {
                    $rs[$domain]['onlinesince'] = 0;
                } else {
                    $rs[$domain]['onlinesince'] = strtotime($rs[$domain]['domaincreatedon']);
                }
            }

            if (in_array('inboundlinks', $exec)) {
                $mjinfo = self::getMajesticseExplorer($url);
                if (empty($mjinfo) && !is_array($mjinfo)) {
                    $rs[$domain]['inboundlinks'] = -1;
                    $rs[$domain]['linkingdomains'] = -1;
                } else {
                    $rs[$domain]['inboundlinks'] = $mjinfo['inboundlinks'];
                    $rs[$domain]['linkingdomains'] = $mjinfo['linkingdomains'];
                }
            }
        }

        $rs[$domain]['domain'] = $domain;
        $rs[$domain]['seomodified'] = time();

        return $rs;
    }

    public function getGooglePageRank($url) {
        if (empty($url)) return '-1';
        $apiurl = "http://www.google.com/search?client=navclient-auto&features=Rank:&q=info:";
        $apiurl .= $url;
        $apiurl .= "&ch=".self::hashURL($url);

        $pr = @file_get_contents($apiurl);
        if ($pr) {
            $pos = strpos($pr, "Rank_");
            if($pos === false){
                return '-1';
            } else {
                $pr = substr($pr, $pos + 9);
                $pr = trim($pr);
                $pr = str_replace("\n",'',$pr);
                return $pr;
            }
        } else {
            return '-1';
        }
    }// end for getGooglePageRank


    public function getMajesticseExplorer($url, $fresh = true) {
        if (!function_exists("file_get_html")) {
            include_once("simplehtmldom/simple_html_dom.php");
        }

        set_time_limit(0);
        ini_set("memory_limit", "128M");

		$maps = array(		
                'Referring Domains' => 'linkingdomains',
                'External Backlinks' => 'inboundlinks',
                    );

        $url = self::getDomain($url);
        if (empty($url)) return null;
        if ($fresh) {
            //$apiurl = "http://www.majesticseo.com/reports/site-explorer/summary/{$url}?oq={$url}&IndexDataSource=F";
            $apiurl = "http://www.majesticseo.com/reports/site-explorer/summary/{$url}";
        } else {
            $apiurl = "http://www.majesticseo.com/reports/site-explorer/summary/{$url}?oq={$url}&IndexDataSource=H";
        }

        $rtn = false; // control the return result, if there is no result, it will return null

        $html = @file_get_html($apiurl);
        //$html = @file_get_html("http://sites.com/sites/com.connectionseeker/del.txt");
        if (!is_object($html)) return null;

        $rs = array();
        $res = @$html->find("div.contentPanelWhite table", 0)->find("table", 0)->find("td");
        if (!empty($res)) {
            foreach ($res as $rv) {
                $lb = trim($rv->children(0)->plaintext);
                //echo $lbv = $rv->children(1)->plaintext;
                if(!empty($lb) && in_array($lb, array_keys($maps))) {
                //echo $maps[$lb];
                    $rs[$maps[$lb]] = trim(str_replace(",", "", $rv->children(1)->plaintext));
                }
            }
        }

        return $rs;
    }


    // this function/method was discard right now
    public function getSeomozInfo($url, $root = true) {
        if (!function_exists("file_get_html")) {
            include_once("simplehtmldom/simple_html_dom.php");
        }

        set_time_limit(0);
        ini_set("memory_limit", "128M");

		$maps = array(
                'Page Authority' => 'pa',
                'Page MozRank' => 'pmr',
                'Page MozTrust' => 'pmt',
                'Internal Followed Links' => 'pifl',
                'External Followed Links' => 'pefl',
                'Total Internal Links' => 'ptil',
                'Total External Links' => 'ptel',
                'Total Links' => 'ptl',
                'Followed Linking Root Domains' => 'pflrd',
                'Total Linking Root Domains' => 'ptlrd',
                'Linking C Blocks' => 'plcb',
                'Subdomain MozRank' => 'smr',
                'Subdomain MozTrust' => 'smt',
                'External Followed Links' => 'sefl',
                'Total External Links' => 'stel',
                'Total Links' => 'stl',
                'Followed Linking Root Domains' => 'sflrd',
                'Total Linking Root domains' => 'stlrd',
                'Domain Authority' => 'da',
                'Domain MozRank' => 'dmr',
                'Domain MozTrust' => 'dmt',
                'External Followed Links' => 'defl',
                'Total External Links' => 'dtel',
                'Total Links' => 'dtl',
                'Followed Linking Root Domains' => 'dflrd',
                'Total Linking Root Domains' => 'dtlrd',
                'Linking C Blocks' => 'dlcb',
        );


        if (empty($url)) return null;
        if ($root) {
            $url = self::getDomain($url);
            $tableid = "domain-comparisons";
        } else {
            $tableid = "page-comparisons";
        }

        $apiurl = "http://www.opensiteexplorer.org/comparisons.html?no_redirect=1&page=1&site={$url}";
        $rtn = false; // control the return result, if there is no result, it will return null

        //$html = @file_get_html($apiurl);
        //$html = @file_get_html("http://sites.com/sites/com.connectionseeker/seomoz.txt");
        if (!is_object($html)) return null;

        $rs = array();
        $res = $html->find("table[id={$tableid}] tbody tr");
        if (!empty($res)) {
            foreach ($res as $rv) {
                $lb = $rv->children(0)->plaintext;
                //echo $lb = $rv->children(1)->plaintext;
                if(!empty($lb) && in_array($lb, array_keys($map))) {
                    $rs[$maps[$lb]] = str_replace(",", "", $rv->children(1)->plaintext);
                }
            }
        }

        return $rs;

    }// end for getSeomozInfo

    public function getGoogleIndexNumber($cmds, $locality = "google.com") {
        if (!function_exists("file_get_html")) {
            include_once("simplehtmldom/simple_html_dom.php");
        }

        $api = self::googleLocality($locality);
        $html = file_get_html($api.'&q='.urlencode($cmds).'&btnG=Search&aq=f&aqi=&oq=');
        $res = @$html->find("div[id=resultStats]", 0)->plaintext;
        unset($html);
        if (!empty($res)) {
            //echo $res;
            if(preg_match('/([\d|,]+) result(s*)  \(/', $res, $match)) {
                $number = str_replace(",", "", $match[1]);
                return $number;
            } else {
                //echo 'Failed to fetch google result!';
                return 0;
            }
        } else {
            return 0;
        }
    }

    // Calculate signature using HMAC: http://www.faqs.org/rfcs/rfc2104.html
    private function calculate_RFC2104HMAC ($data, $key) {
        return base64_encode (
            pack("H*", sha1((str_pad($key, 64, chr(0x00))
            ^(str_repeat(chr(0x5c), 64))) .
            pack("H*", sha1((str_pad($key, 64, chr(0x00))
            ^(str_repeat(chr(0x36), 64))) . $data))))
         );
    }

    /*
    * Google PageRank Checksum Algorithm (Toolbar for Firefox) V1.3
    * compatible with PHP 5.x,  X86_64 CPU supported
    * e.g.:http://www.google.com/search?client=navclient-auto&features=Rank:&q=info:http://infinitenine.com&ch=85074c1d3
    * Here: ch=85074c1d3, the ch return from HashURL
    */
    private function hashURL($url)
    {
        $seed = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
        $result = 0x01020345;

        for ($i=0; $i<strlen($url); $i++) 
        {
            $result ^= ord($seed{$i%strlen($seed)}) ^ ord($url{$i});
            // AND + SINGED RIGHT SHIFT == UNSIGNED RIGHT SHIFT
            $result = (($result >> 23) & 0x1FF) | $result << 9;
        }

        return sprintf("8%x", $result);
    }


    public function getDomainCreatedOn($domain) {
        if (!function_exists("file_get_html")) {
            include_once("simplehtmldom/simple_html_dom.php");
        }

        $ua = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729) FirePHP/0.4\r";
        if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }

        $api = "http://www.who.is/whois/$domain/";
        // Create a stream
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                      "Referer: http://www.who.is\r\n".
                      "User-Agent: {$ua}\n"
            )
        );

        $context = stream_context_create($opts);
        $html = @file_get_html($api, false, $context);
        //print_r($html);

        if (!is_object($html)) {
            return -2;
        }

        $message = @$html->find("div[id=message]")->plaintext;
        if (stripos($message, "ERROR") !== false) {
            return -99;
        }

        //$res = $html->find("span[id=registry_whois]");
        $res = @$html->find("span[id=registry_whois]", 0)->plaintext;
        if (!empty($res)) {
            if (preg_match('/(?P<name>Creation Date): (?P<onlinesince>[\w|-]+)/', $res, $matches)) {
                //print_r($matches);
                unset($res);
                unset($html);
                return $matches['onlinesince'];
            }
        }

        return -1;
    }

    public function getAlexaTraffic($domain){
        $result = @file_get_contents('http://data.alexa.com/data/+wQ411en8000lA?cli=10&dat=snba&ver=7.0&cdt=alx_vw%3D20%26wid%3D12206%26act%3D00000000000%26ss%3D1680x16t%3D0%26ttl%3D35371%26vis%3D1%26rq%3D4&url=' . urlencode($domain));
        if ($result) {
            if(preg_match('/TEXT=\"(\d+)\"\/>/', $result, $match)) {
                return $match[1];
            } else {
                //echo 'Failed to fetch alexa rank!';
                return 0;
            }
        }

        return 0;
    }

    public function getAlexaDomainInfo($domain) {
        $result = @file_get_contents('http://data.alexa.com/data/+wQ411en8000lA?cli=10&dat=snba&ver=7.0&cdt=alx_vw%3D20%26wid%3D12206%26act%3D00000000000%26ss%3D1680x16t%3D0%26ttl%3D35371%26vis%3D1%26rq%3D4&url=' . urlencode($domain));

        $info = array();
        if( $result )
        {
            $xml = simplexml_load_string( $result );
            $info = array();
            if ($xml->SD->TITLE) {
                foreach($xml->SD->TITLE->attributes() as $k => $v) {
                    if ($k == 'TEXT') {
                        $info['title'] = (string)$v;
                    }
                }
            }

            if ($xml->SD->EMAIL) {
                foreach($xml->SD->EMAIL->attributes() as $k => $v) {
                    if ($k == 'ADDR') {
                        $info['email'] = (string)$v;
                    }
                }
            }

            if ($xml->SD->OWNER) {
                foreach($xml->SD->OWNER->attributes() as $k => $v) {
                    if ($k == 'NAME') {
                        $info['owner'] = (string)$v;
                    }
                }
            }

            if ($xml->SD->ADDR) {
                foreach($xml->SD->ADDR->attributes() as $k => $v) {
                    if ($k == 'STREET') {
                        $info['street'] = (string)$v;
                    } elseif ($k == 'CITY') {
                        $info['city'] = (string)$v;
                    } elseif ($k == 'ZIP') {
                        $info['zip'] = (string)$v;
                    } elseif ($k == 'STATE') {
                        $info['state'] = (string)$v;
                    } elseif ($k == 'COUNTRY') {
                        $info['country'] = (string)$v;
                    }
                }
                $info['address'] = $info['street'] . ", " .$info['city'] . ", " . $info['zip'];
            }

            if ($xml->SD->PHONE) {
                foreach($xml->SD->PHONE->attributes() as $k => $v) {
                    if ($k == 'NUMBER') {
                        $info['number'] = (string)$v;
                    }
                }
            }

            if ($xml->SD->CREATED) {
                foreach($xml->SD->CREATED->attributes() as $k => $v) {
                    if ($k == 'DATE') {
                        $info['date'] = (string)$v;
                    }
                }
            }

            if ($xml->SD->LINKSIN) {
                foreach($xml->SD->LINKSIN->attributes() as $k => $v) {
                    if ($k == 'NUM') {
                        $info['linkin'] = (string)$v;
                    }
                }
            }

            $a = $xml->SD;
            if ($a[1]) {
                $b = $a[1];
                if ($b->POPULARITY) {
                    foreach($b->POPULARITY->attributes() as $k => $v) {
                        if ($k == 'TEXT') {
                            $info['traffic'] = (string)$v;
                        }
                    }
                }

                if ($b->RANK) {
                    foreach($b->RANK->attributes() as $k => $v) {
                        if ($k == 'DELTA') {
                            $info['delta'] = (string)$v;
                        }
                    }
                }
            }

            if (!isset($info['traffic'])) {
                if ($xml->SD) {
                    foreach($xml->SD->attributes() as $k => $v) {
                        $v = (string)$v;
                        $domain = strtolower($domain);
                        if ($k == 'HOST' && $domain == strtolower($v)) {
                            $info['traffic'] = 0;
                        }
                    }
                }
            }
        }

        return $info;
    }

    function onPageAchorCheck($url) {
        if (!function_exists("file_get_html")) {
            include_once("simplehtmldom/simple_html_dom.php");
        }
        $html = @file_get_html($url);
        $res = $html->find("a");
        $domain = self::getDomain($url);

        // find all link
        $hrefs = array();
        $i = 0;
        foreach($html->find('a') as $e) {
            //check if it is outbound link or not.
            if (stripos($e->href, "http://") !== false || stripos($e->href, "https://") !== false) {
                $outdomain = self::getDomain($e->href);
                if (strtolower($domain) != strtolower($outdomain)) {
                    $hrefs[$i]['anchor'] = $e->plaintext;
                    $hrefs[$i]['href'] = $e->href;
                    $hrefs[$i]['rel'] = $e->rel;
                    $hrefs[$i]['target'] = $e->target;
                    $hrefs[$i]['title'] = $e->title;
                    if (empty($e->rel)) $hrefs[$i]['rel'] = "follow";
                    if (empty($e->target)) $hrefs[$i]['target'] = "_self";
                    $i++;
                }
            }
            //echo $e->plaintext . '<br>';
            //echo $e->href . '<br>';
            //echo $e->rel . '<br>'. '<br>';
        }
        unset($html);

        if ($hrefs) {
            foreach ($hrefs as $k => $v) {
                $html = @file_get_html($v['href']);
                // get title
                //echo $v['anchor'];
                $hrefs[$k]['outlinktitle'] = @$html->find('title', 0)->innertext;
                if (stripos($hrefs[$k]['outlinktitle'], $v['anchor']) === false) {
                    $hrefs[$k]['keywordmatch'] = false;
                } else {
                    $hrefs[$k]['keywordmatch'] = true;
                }
                unset($html);
            }
        }

        return $hrefs;
    }

    public function googleAjaxSER($query, $number = 10, $locality = "google.com")
    {
        $pp = 8;//per page;
        $ttpages = ceil($number / $pp); // total pages
        $result = array ();
        //$ttpages = 1;
        $delay = 1000000;
        $rs = array();
        $rscount = 0;
        $query = urlencode($query);

        for($start=0;$start<$ttpages;$start++)
        {
            $st = $start * 8;
            $cbk = "CBK.".time();
            $slen = strlen($cbk);
            $url = "https://ajax.googleapis.com/ajax/services/search/web?v=1.0&callback={$cbk}&q={$query}&rsz={$pp}&start={$st}";
            //exec("wget -O {$url}");

            $serstr = file_get_contents($url);
            //$serstr = SEOstats::cURL($url);
            //echo $serstr;

            $serstr = substr($serstr, 0, -1);
            $serstr = substr($serstr, ($slen + 1));
            //$a = json_decode($serstr);
            $serrs = CJSON::decode($serstr);

            if (!empty($serrs) && $serrs['responseStatus'] == 200) {
                $rpdrs = $serrs['responseData']['results'];
                if (!empty($rpdrs)) {
                    foreach($rpdrs as $v){
                        $url = $v['unescapedUrl'];
                        if (($pos = stripos($url, 'http://')) === false && ($pos = stripos($url, 'https://')) === false) {
                            continue;
                        }
                        $domain = self::getDomain($url);
                        $domain = strtolower($domain);

                        $rs[$domain][] = array('url' => $url,
                                               'domain' => $domain,
                                               'shorturl' => $v['visibleUrl'],
                                               'safeurl' => $v['url'],
                                               'title' => $v['titleNoFormatting'],
                                               'describe' => $v['content']);

                        $domianrs[$domain]['domain'] = $domain;
                        $domianrs[$domain]['hubcount'] += 1;

                        //if we don't wanna set these value as below here, we can set it in GoogleSearchController::actionStatus also
                        $domianrs[$domain]['googlepr'] = 0;
                        $domianrs[$domain]['alexarank'] = 0;
                        $domianrs[$domain]['onlinesince'] = 0;
                        $domianrs[$domain]['seostatus'] = "";
                        //http://www.opensiteexplorer.org/comparisons.html?no_redirect=1&page=1&site=www.seobook.com
                        $domianrs[$domain]['inboundlinks'] = 0;
                        $domianrs[$domain]['linkingdomains'] = 0;

                        //$i ++;
                    }

                    if(!$rscount) {
                        $rscount = $serrs['responseData']['cursor']['estimatedResultCount'];
                    }
                }

            } else {
                //$serrs = array();
                break;
            }

            //usleep($delay);
            //print_r($serrs);
        }

        //if ($rtn) {
        return array('cpt_domain' => $domianrs,
                    'google_results' => $rs,
                    'number'=>$number,
                    'estimated_result_count'=>$rscount);
        //}
        //print_r($serrs);

    }


}

?>