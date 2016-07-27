<?php
/**
* Auth item child form class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.9
*/
class MenuChildForm extends CFormModel
{
	public $id;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Menus::t('core', 'ID'),
		);
	}
}
