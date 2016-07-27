<?php  
class CampaignFilter extends CFilter  
{
    public $umodel = null;

    public function filter($fc)  
    {
        if($this->preFilter($fc)){
            $fc->run();
            $this->postFilter($fc);  
        } else {
            $rtn = $fc->controller->raiseErrorByCode(406);

            //@header("{$_SERVER['SERVER_PROTOCOL']} {$rtn['code']} {$rtn['message']}");
            @header("{$_SERVER['SERVER_PROTOCOL']} 200 {$rtn['message']}");
            @header('Content-type: application/json');
            echo CJSON::encode($rtn);
            exit;
        }
    }

    /** 
     * Performs the pre-action filtering. 
     * @param CFilterChain $filterChain/$fc the filter chain that the filter is on. 
     * @return boolean whether the filtering process should continue and the action 
     * should be executed. 
     */  
    protected function preFilter($fc)
    {
        // logic being applied before the action is executed
        $rtn = array();

        if (empty($fc->controller->roles)) {
            return false;
        }

        //if admin, then run it;
        /*
        $cuid = $this->umodel->id;
        $roles = Yii::app()->authManager->getRoles($cuid);
        //print_r($roles);
        //if(isset($roles['Marketer'])){
        */
        if(isset($fc->controller->roles['Marketer'])){
            //$actionid = $this->action->id;
            $cname = $_GET['model'];
            $cname = ucfirst($cname);
            $__cname = strtolower($cname);
            $actionid = $fc->action->id;
            $controllerid = $fc->controller->id;

            if (in_array(strtolower($actionid), array('view','update','delete','cmview'))) {
                //echo $cname;
                //if (strtolower($cname) == "processing") $cname = "Campaign";
                if ($__cname == "processing") $cname = "Campaign";
                $model = new $cname;
                if ($__cname == "client") {
                    if (!isset($_GET['id']) || 
                        (is_numeric($_GET['id']) && $_GET['id']<=0)) {
                        $_GET['id'] = $this->umodel->client_id;
                    }
                }
                $model = $model->findByPk($_GET['id']);

                if ($this->umodel->type == 0) {

                    //echo $this->umodel->client_id;
                    $rtn = false;
                    if ($__cname == "client") {
                        if ($this->umodel->client_id == $_GET['id'] || !is_numeric($_GET['id'])) $rtn = true;
                    } else if (strtolower($cname) == "task") {
                        if ($this->umodel->client_id == $model->rcampaign->client_id) $rtn = true;
                    } else {
                        if ($this->umodel->client_id == $model->client_id) $rtn = true;
                    }
                } else {
                    $cmpids = array();
                    if ($this->umodel->duty_campaign_ids) {
                        $cmpids = unserialize($this->umodel->duty_campaign_ids);
                    }
                    if ($cmpids && in_array($_GET['id'], $cmpids)) {
                        $rtn = true;
                    } else {
                        $rtn = false;
                    }
                }
            } else {
                $rtn = false;
                if (in_array(strtolower($actionid), array('list','create')) ) {
                    $cnamearr = array('clientdomain', 'campaign', 'competitor', 'domain', 'cscategory');
                    if (in_array(strtolower($cname), $cnamearr)) {
                        $rtn = true;
                    }
                }
            }

        } elseif (isset($fc->controller->roles['Admin'])) {
            //do nothing for now!!
            $rtn = true;
        } else {
            $rtn = false;
        }

        return $rtn; // false if the action should not be executed
    }

    /** 
     * Performs the post-action filtering. 
     * @param CFilterChain $filterChain/$fc the filter chain that the filter is on. 
     */
    protected function postFilter ($fc)
    {
        // logic being applied after the action is executed
        //echo "-->CampaignFilter-->post";
        //do nothing for now;
    }
}