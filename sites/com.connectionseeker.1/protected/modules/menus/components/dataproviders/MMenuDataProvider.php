<?php
/**
* Authorization item data provider class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.9.10
*/
class MMenuDataProvider extends CDataProvider
{
	public $name;
	public $itemname;
	public $url;
	public $img;
	public $parent_id;
	public $is_tab = null;
	public $parent;
	public $mapping;
	public $type;
	public $ordering;
    public $orderby;
	public $status;
	public $exclude = array();
	public $items;
	public $sortable;

	/**
	* Constructs the data provider.
	* @param string $id the data provider identifier.
	* @param array $config configuration (name=>value) to be applied as the initial property values of this class.
	* @return RightsAuthItemDataProvider
	*/
	public function __construct($id, $config=array())
	{
		$this->setId($id);

		foreach($config as $key=>$value)
			$this->$key=$value;
	}

	/**
	* Fetches the data from the persistent data storage.
	* @return array list of data items
	*/
	public function fetchData()
	{
		if( $this->sortable!==null )
			$this->processSortable();

		if( $this->items===null ) {
            if (empty($this->type) && strlen($this->parent_id)) {
                $this->items = Menus::getMenu()->getMenusByParendId($this->parent_id, $this->is_tab);
            } else {
			    $this->items = Menus::getMenu()->getMenus($this->type, $this->parent, true);
            }
        }

		$data = array();
		foreach( $this->items as $name=>$item )
			$data[] = $item;

		return $data;
	}

	/**
	* Fetches the data item keys from the persistent data storage.
	* @return array list of data item keys.
	*/
	public function fetchKeys()
	{
		$keys = array();
		foreach( $this->getData() as $name=>$item )
			$keys[] = $name;

		return $keys;
	}

	/**
	* Applies jQuery UI sortable on the target element.
	*/
	protected function processSortable()
	{
		if( $this->sortable!==null )
		{
			if( isset($this->sortable['id'])===true && isset($this->sortable['element'])===true && isset($this->sortable['url'])===true )
			{
				// Register the script to bind the sortable plugin to the role table
				Yii::app()->getClientScript()->registerScript($this->sortable['id'],
					"jQuery('".$this->sortable['element']."').rightsSortableTable({
						url:'".$this->sortable['url']."',
						csrfToken:'".Yii::app()->request->csrfToken."'
					});"
				);
			}
		}
	}

	/**
	* Calculates the total number of data items.
	* @return integer the total number of data items.
	*/
	protected function calculateTotalItemCount()
	{
		return count($this->getData());
	}
}
