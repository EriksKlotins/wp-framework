<?php namespace Framework;

class MVCGeneratorViewModel extends BaseViewModel
{
	public function __construct($model = null, $arguments = array('menu_id' => 4,'parent_menu_id'=>-1))
	{
		parent::__construct($model);
		$this->template_directory_uri = FRAMEWORK_URL.'templates/';
	}
	
	
}