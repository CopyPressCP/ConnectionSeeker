<?php
/*
 * Project : 
 * Date    : 5/30/2013 11:13 PM
 * Author  : leo@infinitenine.com
 * File    : LinkmeButtonColumn.php
 */

Yii::import('zii.widgets.grid.CButtonColumn');

/**
 * ButtonColumn class file.
 * Extends {@link CButtonColumn}
 * 
 * Allows additional evaluation for the fields/attributes of buttons tags
 * 
 * @version $Id$
 * 
 */
class LinkmeButtonColumn extends CButtonColumn
{
    /**
     * @var boolean whether the fields/attributes in the button tags should be evaluated.
     */
    public $evaluateFields = array();
 
    /**
     * Renders the button cell content.
     * This method renders the view, update and delete buttons in the data cell.
     * Overrides the method 'renderDataCellContent()' of the class CButtonColumn
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    public function renderDataCellContent($row, $data)
    {
        $tr=array();
        ob_start();
        foreach($this->buttons as $id=>$button) {
            if($this->evaluateFields) {
                foreach ($this->evaluateFields as $ev) {
                    if (isset($button[$ev])) {
                        foreach ($button[$ev] as $k => $v) {
                            if (!is_numeric($v)) 
                                $button[$ev][$k] = $this->evaluateExpression($v, array('row'=>$row,'data'=>$data));
                        }
                    }
                }
            }
 
            $this->renderButton($id,$button,$row,$data);
            $tr['{'.$id.'}']=ob_get_contents();
            ob_clean();
        }
        ob_end_clean();
        echo strtr($this->template,$tr);
    }

}