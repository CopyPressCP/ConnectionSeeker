<?php
/*
 * Project : 
 * Date    : Aug 23, 2011 4:54 PM
 * Author  : leo@infinitenine.com
 * File    : LinkmeGridView.php
 */

Yii::import('zii.widgets.grid.CGridView');

/**
 * Extends CGridView making pagers at the top and bottom default and adding in a pageSize
 * form element.
 *
 * @author leo@infinitenine.com
 * @since Aug 23, 2011
 */
class LinkmeGridView extends CGridView{
    function init(){
        $pageSize = Yii::app()->user->hasState('pageSize') ? Yii::app()->user->getState('pageSize') : 20;
        $this->dataProvider->pagination->pageSize=$pageSize;

        if(count($this->columns)){
            foreach($this->columns as $key=>$column){
                if(isset($column['class'])&& $column['class']==='CButtonColumn' && !isset($column['header'])){
                    $this->columns[$key]['header']=CHtml::dropDownList(
                        'pageSize',
                        $pageSize,
                        array(10=>10,20=>20,50=>50,100=>100),
                        array('onchange'=>"$.fn.yiiGridView.update('".$this->getId()."',{ data:{pageSize: $(this).val() }})")
                    );
                }
            }
        }

		if($this->baseScriptUrl===null)
			$this->baseScriptUrl= isset(Yii::app()->theme) ? Yii::app()->theme->baseUrl.'/css/gridview' : Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview';

        return parent::init();
    }
}

