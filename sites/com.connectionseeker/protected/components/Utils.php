<?php
/**
 * Utils is a common function/method Class.
 * This class will provide lots of functions which can handle sperate features.
 */
class Utils
{
	/**
	 * Dump the array into a string, this string will use the $sperator to sperate it.
	 * This function is very useful in the grid view layout.
     * @params $arr array
     * @params $sperator string
	 */
	public function array2String($arr = array(), $sperator = ","){
        if (empty($sperator)) $sperator = ",";

        $arrstr = "array(";
        if ($arr) {
            foreach ($arr as $k => $v) {
                //$arrstr .= "'".$k."'=>'".$v."',";
                if (is_array($v)) {
                    $_v = Utils::array2String($v);
                    $arrstr .= "'{$k}'=>{$_v}{$sperator}";
                } else {
                    $arrstr .= "'{$k}'=>'{$v}'{$sperator}";
                }
            }
        }

        $arrstr .= ")";

        return $arrstr;
    }

    public function getValue($arr, $key, $isreturn = false){
        if ($isreturn) {
            return $arr[$key];
        } else {
            echo $arr[$key];
        }
    }

    public function getTierWordCount($tier, $isreturn = false){
        switch($tier) {
            case 10;
                $wc = 500;
                break;
            case 20;
                $wc = 675;
                break;
            case 30;
                $wc = 675;
                break;
            case 40;
                $wc = 1000;
                break;
            default;
                $wc = 1000;
                break;
        }

        if ($isreturn) {
            return $wc;
        } else {
            echo $wc;
        }
    }

    public function getUploadError($errorcode){
        //please check it out:http://www.php.net/manual/en/features.file-upload.errors.php

        $errors = array("0"=>"There is no error, the file uploaded with success.",
            "1"=>"The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            "2"=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
            "3"=>"The uploaded file was only partially uploaded.",
            "4"=>"No file was uploaded.",
            "5"=>"Defined by ourself, maybe file is empty.",
            "6"=>"Missing a temporary folder.",
            "7"=>"Failed to write file to disk.",
            "8"=>"System stopped the file upload. system does not provide a way to ascertain which extension caused the file upload to stop.",);

        return $errors[$errorcode];
    }

    /**
    * Returns the coresponding excel column. (A-Z, 26 latin characters).
    * 
    * @param int $index
    * @return string
    */
    public function columnName($index)
    {
        --$index;
        if($index >= 0 && $index < 26)
            return chr(ord('A') + $index);
        else if ($index > 25)
            return ($this->columnName($index / 26)).($this->columnName($index%26 + 1));
        else
            throw new Exception("Invalid Column # ".($index + 1));
    }


    public function preference($attr = 'styleguide')
    {

        $preffile = dirname(dirname(__FILE__))."/config/preferences.xml";
        $prefs = @simplexml_load_file($preffile, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$prefs) {
            return false;
        } else {
            $xpathivts = $prefs->xpath('//inventories');
            return (string)$xpathivts[0]->$attr;
        }
    }

    function sendCmd2SSSAPI($cmd, $p = array()) {
        $cmd = strtolower($cmd);
        if (!in_array($cmd, array('createcampaign', 'createarticle', 'downloadarticles', 'getarticlestatus', 'approvearticle', 'cancelarticle', 'fuzzytitle')) || empty($p)) {
            return false;
        }
        $vars = array();
        switch ($cmd) {
            case 'createcampaign':
                $vars['sssdata'] = genCCCFormat($p);
                break;
            case 'createarticle':
                $vars['sssdata'] = genCCAFormat($p);
                break;
            case 'downloadarticles':
                $p['action'] = 'downloadarticle';
                $vars['sssdata'] = genDownloadArticlesFormat($p);
                break;
            case 'getarticlestatus':
                $p['action'] = 'getarticlestatus';
                $vars['sssdata'] = genDownloadArticlesFormat($p);
                break;
            case 'approvearticle':
                $p['action'] = 'approvearticle';
                $vars['sssdata'] = genDownloadArticlesFormat($p);
                break;
            case 'cancelarticle':
                $p['action'] = 'cancelarticle';
                $vars['sssdata'] = genCancelArticlesFormat($p);
                break;
            case 'fuzzytitle':
                $p['action'] = 'fuzzytitle';
                $vars['sssdata'] = genDownloadArticlesFormat($p);
                break;
            default:
                $vars['sssdata'] = genCCCFormat($p);
                break;
        }

        if (empty($vars['sssdata'])) return false;

        /*
        $url = Yii::app()->params['contentAPI'];
        require_once 'HTTP/Client.php';   //pear HTTP_Client
        $client = & new HTTP_Client();  //instance HTTP_Client
        $status = $client->post($url, $vars);  //submit form
        return $response = $client->currentResponse();
        //$responsebody = $response['body'];
        */
        Yii::import('ext.EHttpClient.*');
        //$uri = "http://www.thenanogreen.com/alimama.php";
        $uri = Yii::app()->params['contentAPI'];
        $config = array('timeout' => 1200,
                        'sslverify' => false,
                        'adapter' => 'EHttpClientAdapterCurl');
        $client = new EHttpClient($uri, $config);
        $client->setParameterPost($vars);
        return $response = $client->request('POST');

        /*
        if($response->isSuccessful())
           $rtn = $response->getBody();
        else
           $rtn = $response->getRawBody();

        return $rtn;
        */
    }

    public function notice($p = array()){
        if (empty($p)) {
            return false;
        }
        extract($p);
        if (!isset($content)) return false;

        if (!isset($displayname)) $displayname = "Do Not Reply";
        //if (!isset($mfrom)) $mfrom = "noreply@steelcast.com";
        if (!isset($mfrom)) $mfrom = "csdata@steelcast.com";
        if (!isset($subject)) $subject = "Notification From Connection Seeker";
        if (!isset($format)) $format = "text/html";//'text/plain'
        if (!isset($tos)) $tos = array('technical@steelcast.com');//'twang@steelcast.com','apineda@steelcast.com'...,
        //if (!isset($cc)) $cc = array('leo@infinitenine.com');
        $message = new YiiMailMessage;

        if ($cc) {
            $message->setSubject($subject)->setTo($tos)->setFrom($mfrom, $displayname)
                //->setReplyTo($replyto)
                ->setCc($cc)
                ->setBody($content, $format);
        } else {
            $message->setSubject($subject)->setTo($tos)->setFrom($mfrom, $displayname)
                ->setBody($content, $format);
        }

        $m = Yii::app()->mail;
        $m->transportOptions = array(
                                'host' => "ssl://smtp.gmail.com",
                                //'username' => "noreply@steelcast.com",
                                'username' => "csdata@steelcast.com",
                                'password' => "!Steel99",
                                'port' => "465",);
        $c = $m->send($message);
        if ($c) {
            return true;
        } else {
            return false;
        }
    }


    public function taskDisplayMode($dpm = ""){
        if (!empty($dpm)) {
            $displaymode = $dpm;
        } else {
            $cuid = Yii::app()->user->id;
            $usermodel = User::model()->findByPk($cuid);
            $roles = Yii::app()->authManager->getRoles($cuid);
            if (isset($roles['Marketer'])) {
                $displaymode = 6;
            } else {
                $displaymode = $usermodel->display_mode;
            }
        }

        if ($displaymode == 1) {//pre-content
            $dparr = array("tasktype","anchortext","targeturl","rewritten_title","blog_title","blog_url","notes");
        } elseif ($displaymode == 2) {//QA Stuff
            $dparr = array("tierlevel","anchortext","targeturl","desired_domain_id","sentdate","channel_id","rewritten_title","notes","qa_comments","client_comments","always_on_cio");
        } elseif ($displaymode == 3) {//Outreach Stuff
            $dparr = array("tasktype","tierlevel","tierlevel_built","anchortext","sentdate","targeturl","desired_domain_id","rewritten_title","spent","always_on_cio");
        } elseif ($displaymode == 4) {//Content
            $dparr = array("tasktype","tierlevel","anchortext","targeturl","rewritten_title","content_article_id","duedate");
        } elseif ($displaymode == 5) {//Admin
            $dparr = array("tierlevel","tierlevel_built","anchortext","targeturl","desired_domain_id","channel_id","rewritten_title","sourceurl","spent","always_on_cio");
        } elseif ($displaymode == 6) {//Client
            $dparr = array("tierlevel","tierlevel_built","anchortext","targeturl","desired_domain_id","rewritten_title","livedate","iodate","sourceurl","target_stype","googlepr","mozrank","alexarank","client_comments","other");
        } elseif ($displaymode == 999 || $displaymode == 0) {//display all of these stuff;
            $dparr = "ALL";
        } else {//by default show admin model;
            $dparr = array("tierlevel","anchortext","tierlevel_built","targeturl","desired_domain_id","channel_id","rewritten_title","sourceurl");
        }

        return $dparr;
    }

    public function categoryMapping($cats = array()){
        $mapping = array(
            "Arts"      => 1,
            "Business"  => 4,
            "Computers" => 33,
            "Games"     => 12,
            "Home"      => 15,
            "Health"    => 13,
            "Kids_and_Teens" => 9,
            "News"      => 26,
            "Recreation" => 17,
            "Reference" => 24,
            "Regional"  => 18,
            "Science"   => 38,
            "Shopping"  => 9,
            "Society"   => 9,
            "Sports"    => 17,
            "World"     => 26,
            "Adult"     => 9,
            "Travel"    => 18,
        );

        $maps = array();
        if ($cats) {
            foreach ($cats as $v) {
                $maps[] = $mapping[$v];
            }
        }

        return $maps;
    }

    function mbCheckEncoding($s){
        $charset = "auto";
        if(mb_check_encoding($s, 'UTF-8')) $charset = 'UTF-8';
        elseif(mb_check_encoding($s, 'Shift_JIS')) $charset = 'Shift_JIS';
        elseif(mb_check_encoding($s, 'GBK')) $charset = 'GBK';
        elseif(mb_check_encoding($s, 'GB2312')) $charset = 'GB2312';

        return $charset;
    }

    function smartDateSearch($d){
        preg_match('/^([<|>]*[=]*)?/', $d, $matches);//parse the opration such as >=, >, <, <=
        $opr = trim($matches[0]);
        if ($opr != "") {
            $d = substr($d, strlen($opr));
        }

        //do nothing;
        if (stripos($d, "-") !== false) {
            $ac = substr_count($d, '-');
        } else {
            $ac = substr_count($d, '/');
        }

        $marr = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'sept', 'oct', 'nov', 'dec', "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");

        $narr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '01', '02', '03', '04', '05', '06', '07', '08', '09', '09', '10', '11', '12', "01", "02", "03", "04", "05", "05", "07", "08", "09", "10", "11", "12");

        if ($ac == 0) {
            if (!is_numeric($d)) {
                $d = str_ireplace($marr, $narr, $d);
                $d = "-{$d}-";
            }
        } elseif ($ac == 1) {
            $d = str_ireplace($marr, $narr, $d);
            $d = str_replace("/", "-", $d);
            $darr = explode("-", $d);
            if (strlen($darr[0]) == 1) $darr[0] = "0" . $darr[0];
            if (strlen($darr[1]) == 1) $darr[1] = "0" . $darr[1];
            $d = implode("-", $darr);
            if (strlen($darr[0]) == 2) $d = "-" . $d;
            /*
            if (stripos($d, "-") === 1) {
                $d = "-0".$d;
            } else {
                $d = "-".$d;
            }
            */
        } elseif($ac == 2) {
            $d = str_replace("/", "-", $d);
            $d = date("Y-m-d", strtotime($d));
        } else {
            $d = "";
        }

        return $opr.$d;
    }

    //transfer sql condition to logic. such as if()
    function sqlCondition2Logic($condition){
        if (empty($condition)) return false;
        $condition = '(' . $condition . ')';
        $condition = preg_replace('/\s\s+/', ' ', $condition);

        //transfer BETWEEN ... AND 1st
        $pattern = '/\((\w+) BETWEEN (\d+) AND (\d+)\)/i';
        $replacement = '($1 >= $2 && $1 <= $3)';
        $condition = preg_replace($pattern, $replacement, $condition);
        $condition = str_ireplace(array(" AND ", " OR "), array(" && ", " || "), $condition);

        return $condition;
    }
}//end of the Class Utils


//generate create content campaign submit format.
function genCCCFormat($p) {
    extract($p);
    if (isset($campaignid) && $campaignid) {
        $__ups = "";
        if (!empty($campaignname)) $__ups .= "<name><![CDATA[$campaignname]]></name>";
        if (!empty($vertical)) $__ups .= "<vertical>$vertical</vertical>";
        if (!empty($contentcategory_id)) $__ups .= "<categoryid>$contentcategory_id</categoryid>";
        if (!empty($campaignrequirement)) $__ups .= "<campaignrequirement><![CDATA[$campaignrequirement]]></campaignrequirement>";
        if (!empty($datestart)) $__ups .= "<datestart>$datestart</datestart>";
        if (!empty($dateend)) $__ups .= "<dateend>$dateend</dateend>";
        $sssdata = <<<XML
<sssRequest>
 <user>connection</user>
 <apikey>linkme-saRitZPsDNwjq4yK69Ia</apikey>
 <apisignature>3b9807be9e351496daa7cae56d8376be</apisignature>
 <createcampaign>
   <campaignid>$campaignid</campaignid>
   $__ups
 </createcampaign>
</sssRequest>
XML;
    } else {
    $sssdata = <<<XML
<sssRequest>
 <user>connection</user>
 <apikey>linkme-saRitZPsDNwjq4yK69Ia</apikey>
 <apisignature>3b9807be9e351496daa7cae56d8376be</apisignature>
 <createcampaign>
   <name><![CDATA[$campaignname]]></name>
   <vertical>2</vertical>
   <categoryid>$contentcategory_id</categoryid>
   <campaignrequirement><![CDATA[$campaignrequirement]]></campaignrequirement>
   <datestart>$datestart</datestart>
   <dateend>$dateend</dateend>
 </createcampaign>
</sssRequest>
XML;
    }

    return $sssdata;
}

function genCCAFormat($p) {
    $articletype = Utils::preference("articletype");
    if (empty($articletype)) {
        $articletype = 9;
    }

    $optionalkws = unserialize($p['optional_keywords']);
    //print_r($optionalkws);
    $datestart = date('Y-m-d', $p['created']);
    $dateend = date('Y-m-d', $p['duedate']);
    extract($p);
    extract($optionalkws);
    $optlkw1 = addslashes($optlkw1);
    $sssdata = <<<XML
<sssRequest>
 <user>connection</user>
 <apikey>linkme-saRitZPsDNwjq4yK69Ia</apikey>
 <apisignature>3b9807be9e351496daa7cae56d8376be</apisignature>
 <createnewarticle>
  <articletype>$articletype</articletype>
  <vertical>2</vertical>
  <keyword><![CDATA[$anchortext]]></keyword>
  <title><![CDATA[$title]]></title>
  <length>0</length>
  <styleguide><![CDATA[$instructions]]></styleguide>
  <datestart>$datestart</datestart>
  <dateend>$dateend</dateend>
  <mappingid><![CDATA[$mapping_id]]></mappingid>
  <optional1><![CDATA[$optlkw1]]></optional1>
  <optional2><![CDATA[$optlkw2]]></optional2>
  <optional3><![CDATA[$optlkw3]]></optional3>
  <optional4><![CDATA[$optlkw4]]></optional4>
  <campaignid>$content_campaign_id</campaignid>
 </createnewarticle>
</sssRequest>
XML;

    return $sssdata;
}

function genDownloadArticlesFormat($p) {
    extract($p);
    if (is_numeric($ids)) {
        //$dlstr = "<downloadarticle><articleid>$ids</articleid></downloadarticle>";
        $dlstr = "<{$action}><articleid>$ids</articleid></{$action}>";
    } elseif ($ids & is_array($ids)) {
        $dlstr = "";
        foreach ($ids as $v) {
            if (is_numeric($v) && $v > 0) {
                $dlstr .= "<{$action}><articleid>$v</articleid></{$action}>";
            } elseif ($v && is_string($v)) {
                $dlstr .= "<{$action}><articleid><![CDATA[$v]]></articleid></{$action}>";
            }
        }
    } elseif ($ids && is_string($ids)) {
        $dlstr = "<{$action}><articleid><![CDATA[$ids]]></articleid></{$action}>";
    }

    if (empty($dlstr)) {
        return false;
    }

    $sssdata = <<<XML
<sssRequest>
 <user>connection</user>
 <apikey>linkme-saRitZPsDNwjq4yK69Ia</apikey>
 <apisignature>3b9807be9e351496daa7cae56d8376be</apisignature>
 $dlstr
</sssRequest>
XML;

    return $sssdata;
}

function genCancelArticlesFormat($p) {
    extract($p);
    if (is_numeric($ids)) {
        $dlstr = "<{$action}><articleid>$ids</articleid><memo>Client Cancel</memo></{$action}>";
    }
    if ($ids & is_array($ids)) {
        $dlstr = "";
        foreach ($ids as $v) {
            if ($v > 0) {
                $dlstr .= "<{$action}><articleid>$v</articleid><memo>Client Cancel</memo></{$action}>";
            }
        }
    }

    if (empty($dlstr)) {
        return false;
    }

    $sssdata = <<<XML
<sssRequest>
 <user>connection</user>
 <apikey>linkme-saRitZPsDNwjq4yK69Ia</apikey>
 <apisignature>3b9807be9e351496daa7cae56d8376be</apisignature>
 $dlstr
</sssRequest>
XML;

    return $sssdata;
}


function isVisible($field, $dparr) {
    if (is_array($dparr)) {
        if (in_array($field, $dparr)) {
            return true;
        } else {
            return false;
        }
    } else {
        //means ALL
        return true;
    }
}

function domain2URL($domain, $aslink = false, $lnparams = array()) {
    if (empty($domain)) return $domain;
    $domaintxt = $domain;
    
    if (($pos = stripos($domain, 'http://')) === 0 || ($pos = stripos($domain, 'https://')) === 0) {
        //do nothing;
    } else {
        if (($pos = stripos($domain, 'http;//')) === 0) {
            $domain = substr($domain, 7);
            $domain = "http://".$domain;
        } elseif (($pos = stripos($domain, 'https;//')) === 0) {
            $domain = substr($domain, 8);
            $domain = "https://".$domain;
        } elseif(filter_var("http://".$domain, FILTER_VALIDATE_URL)) {
            $domain = "http://".$domain;
        }
    }

    if (empty($lnparams)) {
        $lnparams = array("target"=>"_blank");
    }

    if ($aslink) {
        if (filter_var($domain, FILTER_VALIDATE_URL)) {
            return CHtml::link(CHtml::encode($domaintxt), $domain, $lnparams);
        }
    }

    return $domain;
}

//This function is for Fixing Rebuilt IO Completed status display.
function fixRebuildIOStatus($strs, $s, $r, $isreturn = false){
    if ($s == 5 && $r == 1) {
        $s = 501;
    } elseif ($s == 5 && $r == 0) {
        $s = 500;
    }
    if ($isreturn) {
        return Utils::getValue($strs, $s, $isreturn);
    } else {
        Utils::getValue($strs, $s, $isreturn);
    }
}
