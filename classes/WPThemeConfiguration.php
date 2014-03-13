<?php namespace Framework;

/*
	This disables 404 when using custom routes instead of wp theme hierarchy
*/

if (!is_admin())
{
	$wp_query = new \WP_Query('author__not_in=999999');
}

class WPThemeConfiguration
{
	protected $hiddenMenuItems = array();
	protected $nav_menu = array();
	protected $adminMessages = array();

	public function __construct()
	{
		/* 
			This disables some kind of strange wp behaviour 
			when wp attempts to guess which file to load before
			routing takes place;
		*/
		global $wp_filter;
		unset($wp_filter['template_redirect'][10]['redirect_canonical']);
		add_action('admin_menu', array(&$this,'removeMenus'));
		add_action( 'init',  array(&$this,'register_main_menu'));
		add_action('admin_notices',  array(&$this,'showAdminMessages')); 
		add_theme_support( 'post-thumbnails' );	
		add_theme_support( 'menus' );	

		

		
	}

	

	

	private function showMessage($message, $errormsg = false)
	{

		if ($errormsg) {
			echo '<div id="message" class="error">';
		}
		else {
			echo '<div id="message" class="updated fade">';
		}

		echo "<p><strong>$message</strong></p></div>"; 
	 
	}


	function register_main_menu()
	{
		foreach ($this->nav_menu as $item) 
		{
		
			register_nav_menu($item['name'], $item['title']);
			wp_create_nav_menu($item['name']);
			\Model::register($item['name'], new \Framework\mainMenuModel($item['name'],array('menu-name'=>$item['name'])));
			$menu = wp_get_nav_menu_items($item['name']);
			if (!$menu && count($menu) == 0)
			{
				$this->showAdminMessage(sprintf('Menu '.$item['name'].' must have items!'));
			}
		}
	}

	/**
	 * 	Callback for admin messages
	 *
	 */
	public function showAdminMessages()
	{
		foreach ($this->adminMessages as $item)
		{
			$this->showMessage($item['message'], $item['error']);
		}
	}


	/**
	*	A callback for removing unnecessary admin menu items
	*	override this function to set up your own configuration
	*	@param array $itemsToHide - array of titles to hide
	*/
	public function removeMenus($itemsToHide = array())
	{	

		\Framework\wpUtils::removeMenus($this->hiddenMenuItems);
	}

	/**
	 *	Removes a menu item with $name
	 *	@param string $name - title of the item to be removed
	 */
	protected function removeAdminMenuItem($name)
	{
		$this->hiddenMenuItems[] = __($name);
	}

	protected function registerNavMenu($name, $title)
	{
		//register_nav_menu($name,__($title));
		$this->nav_menu[] = array('name'=> $name, 'title'=>$title);
	}


	public function showAdminMessage($message, $isError = false)
	{
		$this->adminMessages[] = array('message'=>$message, 'error'=>$isError);
	}


}
