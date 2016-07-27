<?php
/*
 * Project : 
 * Date    : Aug 23, 2011 4:54 PM
 * Author  : leo@infinitenine.com
 * File    : LinkmeGridView.php
 */

Yii::import('zii.widgets.grid.CCheckBoxColumn');

/**
 * Extends CCheckBoxColumn making the checkbox of each row, which can be controled the checkbox display or hidden
 * form element.
 *
 * @author leo@infinitenine.com
 * @since Mar 23, 2012
 */
class LinkmeCheckBoxColumn extends CCheckBoxColumn{
    /**
    * a PHP expression that is evaluated for every checkbox of data cell 
    * and whose result is used as a switch, it it is true then diplay the checkbox for the data cell, false will hidden it.
    */
    public $displayRow;

    /**
    * a PHP expression that is evaluated for every checkbox of data cell 
    * and whose result is used as a switch, it it is true then diplay the checkbox for the data cell, false will hidden it.
    */
    public $expressCBHtmlOptions;

	/**
	 * Renders the data cell content.
	 * This method renders a checkbox in the data cell.
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data associated with the row
	 */
	protected function renderDataCellContent($row,$data)
	{
        if ($this->expressCBHtmlOptions!==null && is_array($this->expressCBHtmlOptions)) {
            foreach ($this->expressCBHtmlOptions as $n => $v) {
                $this->checkBoxHtmlOptions[$n] = $this->evaluateExpression($v,array('data'=>$data,'row'=>$row));
            }
        }
        if ($this->displayRow!==null) {
            $ctr=$this->evaluateExpression($this->displayRow,array('data'=>$data,'row'=>$row));
            if ($ctr) {
                parent::renderDataCellContent($row,$data);
            } else {
                echo "";
            }
        } else {
            parent::renderDataCellContent($row,$data);
        }
	}
}

