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
        if (!in_array($cmd, array('createcampaign', 'createarticle', 'downloadarticles', 'getarticlestatus', 'approvearticle', 'cancelarticle')) || empty($p)) {
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
        if (!isset($mfrom)) $mfrom = "noreply@steelcast.com";
        if (!isset($subject)) $subject = "Notification From Connection Seeker";
        if (!isset($format)) $format = "text/html";//'text/plain'
        if (!isset($tos)) $tos = array('technical@steelcast.com');//'twang@steelcast.com','apineda@steelcast.com'...,
        $message = new YiiMailMessage;

        $message->setSubject($subject)
            ->setTo($tos)
            ->setFrom($mfrom, $displayname)
            //->setReplyTo($replyto)
            ->setCc(array('leo@infinitenine.com'))
            ->setBody($content, $format);

        $m = Yii::app()->mail;
        $m->transportOptions = array(
                                'host' => "ssl://smtp.gmail.com",
                                'username' => "noreply@steelcast.com",
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
            $dparr = array("progressstatus","tierlevel","anchortext","targeturl","desired_domain_id","channel_id","rewritten_title","notes","qa_comments","client_comments");
        } elseif ($displaymode == 3) {//Outreach Stuff
            $dparr = array("progressstatus","tasktype","tierlevel","anchortext","targeturl","desired_domain_id","rewritten_title");
        } elseif ($displaymode == 4) {//Content
            $dparr = array("progressstatus","tasktype","tierlevel","anchortext","targeturl","rewritten_title","content_article_id","duedate");
        } elseif ($displaymode == 5) {//Admin
            $dparr = array("progressstatus","tierlevel","anchortext","targeturl","desired_domain_id","channel_id","rewritten_title","sourceurl");
        } elseif ($displaymode == 6) {//Client
            $dparr = array("progressstatus","tierlevel","anchortext","targeturl","desired_domain_id","rewritten_title","livedate","sourceurl","target_stype","googlepr","mozrank","alexarank","client_comments");
        } elseif ($displaymode == 999 || $displaymode == 0) {//display all of these stuff;
            $dparr = "ALL";
        } else {//by default show admin model;
            $dparr = array("progressstatus","tierlevel","anchortext","targeturl","desired_domain_id","channel_id","rewritten_title","sourceurl");
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
 <user>linkquest</user>
 <apikey>linkme-r3AGl9zJFwjhCaePirgS</apikey>
 <apisignature>8d764752fb610b63e725b7c16fb1f107</apisignature>
 <createcampaign>
   <campaignid>$campaignid</campaignid>
   $__ups
 </createcampaign>
</sssRequest>
XML;
    } else {
    $sssdata = <<<XML
<sssRequest>
 <user>linkquest</user>
 <apikey>linkme-r3AGl9zJFwjhCaePirgS</apikey>
 <apisignature>8d764752fb610b63e725b7c16fb1f107</apisignature>
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
 <user>linkquest</user>
 <apikey>linkme-r3AGl9zJFwjhCaePirgS</apikey>
 <apisignature>8d764752fb610b63e725b7c16fb1f107</apisignature>
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
    }
    if ($ids & is_array($ids)) {
        $dlstr = "";
        foreach ($ids as $v) {
            if ($v > 0) {
                $dlstr .= "<{$action}><articleid>$v</articleid></{$action}>";
            }
        }
    }

    if (empty($dlstr)) {
        return false;
    }

    $sssdata = <<<XML
<sssRequest>
 <user>linkquest</user>
 <apikey>linkme-r3AGl9zJFwjhCaePirgS</apikey>
 <apisignature>8d764752fb610b63e725b7c16fb1f107</apisignature>
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
 <user>linkquest</user>
 <apikey>linkme-r3AGl9zJFwjhCaePirgS</apikey>
 <apisignature>8d764752fb610b63e725b7c16fb1f107</apisignature>
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
