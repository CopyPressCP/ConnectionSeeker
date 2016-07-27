<?php
/**
* SendGrid X-SMTP API implement: XSmtpHeader
*
*
* @link http://docs.sendgrid.com/documentation/api/smtp-api/php-example/
* @link http://docs.sendgrid.com/documentation/api/event-api/
* @package Yii-Mail
*/

/**
* XSmtpHeader is a plugin for the Yii-Mail before we are sending the email out.
*
* You can configure it in the config/main.php, and you can just import it when you wanna call this api
*
* Example usage:
* <pre>
* //Autoload fix
* spl_autoload_unregister(array('YiiBase','autoload'));
* Yii::import('ext.yii-mail.XSmtpHeader', true);
* $xhdr = new XSmtpHeader();
* spl_autoload_register(array('YiiBase','autoload'));
*
* $message = new YiiMailMessage;
* $message->setBody('Message content here with HTML', 'text/html');
* $message->subject = 'My Subject';
* $message->addTo('johnDoe@domain.com');
* $message->from = Yii::app()->params['adminEmail'];
* Yii::app()->mail->send($message);
*
* $headers = $message->getHeaders();
* $headers->addTextHeader('X-SMTPAPI', $xhdr->asJSON());
* </pre>
*/
class XSmtpHeader
{
    public $data;

    public function addTo($tos) {
        if (!isset($this->data['to'])) {
            $this->data['to'] = array();
        }
        $this->data['to'] = array_merge($this->data['to'], (array)$tos);
    }

    public function addSubVal($var, $val) {
        if (!isset($this->data['sub'])) {
            $this->data['sub'] = array();
        }

        if (!isset($this->data['sub'][$var])) {
            $this->data['sub'][$var] = array();
        }
        $this->data['sub'][$var] = array_merge($this->data['sub'][$var], (array)$val);
    }

    public function setUniqueArgs($val) {
        if (!is_array($val)) return;
        // checking for associative array
        $diff = array_diff_assoc($val, array_values($val));
        if(((empty($diff)) ? false : true)) {
            $this->data['unique_args'] = $val;
        }
    }

    public function setCategory($cat)
    {
        $this->data['category'] = $cat;
    }

    public function addFilterSetting($filter, $setting, $value)
    {
        if (!isset($this->data['filters'])) {
            $this->data['filters'] = array();
        }

        if (!isset($this->data['filters'][$filter])) {
            $this->data['filters'][$filter] = array();
        }

        if (!isset($this->data['filters'][$filter]['settings'])) {
            $this->data['filters'][$filter]['settings'] = array();
        }
        $this->data['filters'][$filter]['settings'][$setting] = $value;
    }

    public function asJSON() {
        $json = json_encode($this->data);
        // Add spaces so that the field can be folded
        $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);
        return $json;
    }

    public function toString() {
        $json = $this->asJSON();
        $str = "X-SMTPAPI: " . wordwrap($json, 76, "\n ");
        return $str;
    }
}