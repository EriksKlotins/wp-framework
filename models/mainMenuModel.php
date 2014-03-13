<?php namespace Framework;
	

	// apraksta galveno navigāciju
	// 
	class mainMenuModel extends BaseModel
	{
		private $items = array();
		private $activeMenuItem = false;


		public function __construct($name, $attributes)
		{
			parent::__construct($name, $attributes);
			
			
			//var_dump($_SERVER);
			$this->init($attributes['menu-name']);

			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$requestUri = $protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$hasActive = false;
			
			// uzstādām visādus parametrus

			foreach ($this->items as $key => &$item) 
			{
				//$item['url'] = get_bloginfo('home').$item['url'];
				
				if(count($item['submenu']) > 0) $item['has-submenu'] = 1;

				if($hasActive) continue;
				
				foreach($item['submenu'] as $subkey => &$subitem){

					if (mb_strpos($requestUri, $subitem['url']) !== false) {
						$subitem['is-current-sub'] = true;
						$item['is-current'] = true;
						$hasActive = true;
						
						$item['url'] = get_bloginfo('home').$item['url'];

						$this->activeMenuItem = $key;
						break; // šo apli nevajag pabeigt
					}
				}
				if($hasActive) continue;

				if ($item['url']!='')
				{
					$url =  $protocol.$_SERVER['HTTP_HOST'] .$item['url'];
					if ($requestUri == $url)
					{
						$item['is-current'] = true;
						$hasActive = true;
						$this->activeMenuItem = $key;
					}
				}
				
				
			}

		}


		public function getItem($itemID, $loadLinkecObjects = true)
		{
			return isset($this->items[$itemID]) ? $this->items[$itemID] : false;
		}
		
		public function getItems($params = array(), $loadLinkecObjects = true)
		{
			return $this->items;
		}

		public function getItemsForSelect()
		{
			$result = array();
			foreach($this->items as $key=>$item)
			{
				$result[$key] = $item['title'];
			}
			return $result;
		}

		public function getItemsForMustache()
		{
			$result = array();
			foreach ($this->items as $key => $value) 
			{
				$result[$value['name']] = $value['url'];
			}
			return $result;
		}

		public function getActiveMenuItem(){
			return $this->activeMenuItem;
		}

		private function AttachToParent($tree, $item)
		{
			if ($item['parent'] == 0)
			{
				$tree[] = $item;
				return $tree;
			}
			foreach ($tree as &$node) 
			{

				if ($node['id'] == $item['parent'])
				{
					$node['submenu'][] = $item;
					return $tree;
				}
			}
			return $tree;
		}
	

		public function init($menu_name)
		{

			//$menu_name = 'lddk-menu';
			$locations = get_nav_menu_locations();

			$menuObject = wp_get_nav_menu_object( $locations[ $menu_name ] );
			$menu = wp_get_nav_menu_items( $menuObject->term_id);
			//var_dump($locations,$menu_name,$menuObject);

		
			$result = array();
			if ($menu)
			{
				foreach ($menu as $item) 
				{
					$absoluteUrl = $item->url;
					if (strpos($item->url, 'http') !== 0)
					{

						$absoluteUrl = /*get_bloginfo('url') . */ $item->url;
					}

					$current = array(
						'id'		=>$item->ID, 
						'name'	 	=> $item->post_name, 
						'title'		=>$item->title, 
						'url'		=> $absoluteUrl, 
						'parent'	=> $item->menu_item_parent, 'submenu'=>array());
					$result = $this->AttachToParent($result, $current);

					//$result[] = $current;
					//var_dump($item->title);
				}
			}
			
			
			$this->items = $result;
		}



	}
	


?>