<?php
/*
* Trail all of the user's operation history
*/
class ETrailBehavior extends CActiveRecordBehavior
{
    private $_oldattributes = array();
    public $trackview = false;
 
    public function afterSave($event) {
		try {
			$username = Yii::app()->user->Name;
			$userid = Yii::app()->user->id;
		} catch(Exception $e) { //If we have no user object, this must be a command line program
            parent::afterSave($event);
            return true;
		}

        $actionid = Yii::app()->controller->action->id;

        $ownerclass = get_class($this->Owner);
        $ctrller = $ownerclass;
        $ctrller{0} = strtolower($ctrller{0});//lcfirst;
        $pkid = $this->Owner->getPrimaryKey();
        $detailurl = Yii::app()->createUrl("{$ctrller}/view", array("id"=>$pkid));

        if (!$this->Owner->isNewRecord) {
            // new attributes
            $newattributes = $this->Owner->getAttributes();
            $oldattributes = $this->getOldAttributes();
 
            $_olds = array();
            $_news = array();
            $_fields = array();

            // compare old and new
            foreach ($newattributes as $name => $value) {
                if (!empty($oldattributes)) {
                    $old = $oldattributes[$name];
                } else {
                    $old = '';
                }
 
                if ($value != $old) {
                    $_olds[$name] = $old;
                    $_news[$name] = $value;
                    $_fields[]    = $name;
                }
            }

            if (!empty($_news)) {
                //$changes = $name . ' ('.$old.') => ('.$value.'), ';
                $log = new Trail;
                $_fields_name = implode(",", $_fields);
                /*
                $log->old_value   = $old;
                $log->new_value   = $value;
                */
                $log->old_value   = serialize($_olds);
                $log->new_value   = serialize($_news);
                $log->description = 'User ' . $username 
                                    . ' changed ' . $_fields_name . ' for ' 
                                    . $ownerclass 
                                    . '[<a href="' . $detailurl . '" target="_blank">' . $pkid .'</a>].';
                $log->operation   = 'CHANGE';
                $log->action      = $actionid;
                $log->model       = $ownerclass;
                $log->model_id    = $pkid;
                $log->field       = $_fields_name;
                $log->created     = new CDbExpression('NOW()');
                $log->user_id     = Yii::app()->user->id;
                if ($log->save()) {
                    //do nothing for now;
                } else {
                    //print_r($log->getErrors());
                }
            }

        } else {
            $log = new Trail;
            $log->description = 'User ' . $username
                                . ' created ' . $ownerclass
                                . '[<a href="' . $detailurl . '" target="_blank">' . $pkid .'</a>].';
            $log->operation   = 'CREATE';
            $log->action      = $actionid;
            $log->model       = $ownerclass;
            $log->model_id    = $pkid;
            $log->field       = '';
            $log->created     = new CDbExpression('NOW()');
            $log->user_id     = $userid;
            $log->save();
        }

        parent::afterSave($event);
        return true;
    }
 
    public function afterDelete($event) {
		try {
			$username = Yii::app()->user->Name;
			$userid = Yii::app()->user->id;
		} catch(Exception $e) { //If we have no user object, this must be a command line program
            parent::afterSave($event);
            return true;
		}

        $ownerclass = get_class($this->Owner);
        $ctrller = $ownerclass;
        $ctrller{0} = strtolower($ctrller{0});//lcfirst;
        $pkid = $this->Owner->getPrimaryKey();
        //$detailurl = Yii::app()->createUrl("{$ctrller}/view", array("id"=>$pkid));

        $actionid = Yii::app()->controller->action->id;
        $log = new Trail;
        $log->description = 'User ' . $username . ' deleted ' 
                            . $ownerclass 
                            . '[' . $pkid .'].';
        $log->operation   = 'DELETE';
        $log->action      = $actionid;
        $log->model       = $ownerclass;
        $log->model_id    = $pkid;
        $log->field       = '';
        $log->created     = new CDbExpression('NOW()');
        $log->user_id     = $userid;
        if ($log->save()) {
            //do nothing for now;
        } else {
            //print_r($log->getErrors());
        }

        parent::afterDelete($event);
        return true;
    }
 
    public function afterFind($event) {
        // Save old values
        $this->setOldAttributes($this->Owner->getAttributes());
        return parent::afterFind($event);
    }
 
    public function getOldAttributes() {
        return $this->_oldattributes;
    }
 
    public function setOldAttributes($value) {
        $this->_oldattributes = $value;
    }

    /*
    //if the afterFind() not execute there, then uncomments this function.
    public function beforeSave($event) {
        $attr = $this->getOldAttributes();
        if(!$this->Owner->isNewRecord && empty($attr)) {
            $thisModel = call_user_func(array(get_class($this->Owner), 'model'));
            $this->_oldattributes = $thisModel->findByPk($this->owner->getPrimaryKey())->attributes;
        }
     
        return parent::beforeSave($event);
    }
    */


    public function afterConstruct($event) {
        //echo "afterConstruct";
        //die();
        $now = time();
        //$user_id = Yii::app()->user->id;
		try {
			//$username = Yii::app()->user->Name;
			$user_id = Yii::app()->user->id;
		} catch(Exception $e) { //If we have no user object, this must be a command line program
            parent::afterConstruct($event);
            return true;
		}
        
        $nol = new Online;
        $ol = $nol->findByAttributes(array("date_tracked"=>date("Y-m-d", $now), "user_id"=>$user_id));
        if ($ol && ($now - $ol->last_operation_time < 120)) {//120 means 2 minutes;
            //if you did lots of operation in 2 minutes, then do nothing; no need store the database;
        } else {
            if (!empty($ol)) {
                $ol->setIsNewRecord(false);
                $ol->setScenario('update');
                $brb = $now - $ol->last_operation_time;// no action over 30 minutes
                if ($brb >=  1800) {
                    //if it over 30 minutes no actions, then we only count it as 10 mintes, that's means 600 second
                    $ol->total_online = $ol->total_online + 600;
                } else {
                    $ol->total_online = $ol->total_online + $brb;
                }
                $ol->session_online = $ol->session_online + $brb;
            } else {
                $ol = $nol;
                $ol->setIsNewRecord(true);
                $ol->id=NULL;
                $ol->user_id = Yii::app()->user->id;
                $ol->login_time = $now;
            }
            $ol->last_operation_time = $now;
            $ol->save();
        }
        unset($ol, $nol);

        return parent::afterConstruct($event);
    }

}
