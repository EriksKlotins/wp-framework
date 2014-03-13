<?php namespace Framework;


class wpUtils
{
	
	/* remove menu items */
	static function removeMenus ($restricted) 
	{
		
		global $menu;
		/*
		$restricted = array(
			//__('Dashboard'),
			//__('Posts'), 
			//__('Media'), 
			__('Links'), 
			//__('Pages')
			//__('Appearance'), 
			//__('Tools'), 
			//__('Users'), 
			//__('Settings'), 
			//__('Comments'), 
			//__('Plugins')
			);
		*/
		end ($menu);
		while (prev($menu))
		{
			$value = explode(' ',$menu[key($menu)][0]);
			if(in_array($value[0] != NULL?$value[0]:"" , $restricted))
			{
				unset($menu[key($menu)]);
			}
		}
	}	


	static function resizeImage($src, $width, $height)
	{

		if (in_array($src,array('', false, null))) $src = sprintf('http://placehold.it/%sx%s',$width, $height );
		return sprintf('%s?src=%s&w=%d&h=%d&format=.png',
			 WPMU_PLUGIN_URL.'/the-framework/image.php',
			$src,
			$width,
			$height);
		
	}

	static function shorten($str, $length)
	{
		if (mb_strlen($str) <= $length)
		{
			return $str;
		} 
		else
		{
			return sprintf('%s...', mb_substr($str, 0, $length));
		}

	}

	static function requireDirectory($path, $recursive = false)
	{
		$files = array();
		if ($handle = opendir($path)) 
		{
		    while (false !== ($entry = readdir($handle))) 
		    {
		    	if ($recursive && is_dir($path.'/'.$entry) && !in_array($entry,['.','..']))
		    	{
		    		wpUtils::requireDirectory($path.'/'.$entry, $recursive);
		    	}
		    	if (preg_match('/.php$/',$entry))
		    	{
		    		$files[] = $entry;
		      	}
		    }
		    closedir($handle);
		}	
		sort($files);
		foreach($files as $file) require_once ($path.'/'.$file);
		//var_dump($files);
	}
}



?>