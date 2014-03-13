<?php namespace Framework;


class BaseViewModel implements \Framework\ViewModelInterface
{
	/*
		Wordpress globālie mainīgie, kas varētu būt vajadzīgi visos skatos
	*/
	public $template_directory_uri;
	public $bloginfo_home;
	public $bloginfo_title;
	public $mainMenu = array(
		);



	protected $isModelLoaded = false;

	public function __construct($model = null, $arguments = array('menu_id' => 4,'parent_menu_id'=>-1))
	{
		$this->template_directory_uri = get_template_directory_uri().'/templates';
		$this->bloginfo_home = get_bloginfo('url');
		$this->bloginfo_title = get_bloginfo('title');


		if ($model !== null)
		{
			$this->isModelLoaded = true;

			foreach ($model as $key => $value) 
			{
				$this->{$key} = $value;
			}
			//var_dump($model);
		}
		// ***************** Menus **********************

		// menus are not initialized in admin mode
		if (!is_admin())
		{


			$parent_menu_id = isset($arguments['parent_menu_id']) ? $arguments['parent_menu_id'] : null;


			$locations = get_nav_menu_locations();
			//var_dump($locations);
			foreach ($locations as $name=>$menuId) 
			{
				//var_dump($name, );
				//
				$models = \Model::getModels();
				//if (!isset($models['name'])) continue; // fix this
				$this->{$name} = \Model::getByName($name)->GetItems();
				if ($parent_menu_id !== null)
				{
					$this->parentMenuItem = \Model::$$name()->GetItem($parent_menu_id); // aktualitātes
					$this->mainMenu[$parent_menu_id]['is-current'] = true;
					$this->mainMenu[$parent_menu_id]['is-not-current'] = false;
				}
			} 
		}

		
		

		
	}

	public function __get($name)
	{
		return 'bullsit '.$name;

	}

	public function isModelLoaded()
	{
		return $this->isModelLoaded;
	}

	/*
		iekļauj Templeitā un izsauc wp_footer()
	*/
	public function wp_footer()
	{
		ob_start();
		@wp_footer(); // te rodas klūda ar WP_Query, nezinu kapec
		$result = ob_get_contents();
		ob_clean();
		return $result;
		//var_dump($result);
	}

	public function wp_head()
	{
		ob_start();
		wp_head();
		$result = ob_get_contents();
		ob_clean();
		return $result;
		//var_dump($result);
	}
}
