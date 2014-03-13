<?php

/**
 * Base class for meta boxes
 * contains static definitions for various input fields
 */
class WpCustomAdmin
{

	private static $jsToLoad = array();
	private static $cssToLoad = array();
	// predefined templates for most used form elements
 	public function TextField($name, $label, $object)
	{
		$default = get_post_meta( $object->ID, $name, true  );
		return
			'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label>', $name, $label).
		        '<br />'.    
		   		sprintf('<input class="widefat" type="text" name="%s" id="%s" value="%s" />', $name, $name, $default).
		   '</p>';
	} 
	public function RadioGroup($name, $label,$values, $object, $defaultValue = null)
	{	
		$name = trim($name,'[]');
		$default = get_post_meta( $object->ID, $name, true  );
		if (($default == '') && ($defaultValue != null))
		{
			$default = $defaultValue;
			
		}
		$valuesHTML = '';
		foreach ($values as $key => $value) 
		{
			$valuesHTML .= sprintf('<input type="radio" value="%s" name="%s"%s>&nbsp;%s</option><br/>',$key,$name,  $key == $default ? 'checked' : '', $value);
		}
		return 
			'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label><br/>', $name, $label).
				sprintf('%s', $valuesHTML).
			'</p>';
	}


	public function Datepicker($name, $label, $object)
	{
		$default = get_post_meta( $object->ID, $name, true  );
		return
			'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label>', $name, $label).
		        '<br />'.    
		   		sprintf('<input class="widefat datepicker" type="text" name="%s" id="%s" value="%s" />', $name, $name, $default).
		   '</p>';
	}


	public function Timepicker($name, $label, $object)
	{
		$default = get_post_meta( $object->ID, $name, true  );
		return
			'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label>', $name, $label).
		        '<br />'.    
		   		sprintf('<input class="widefat timepicker" type="text" name="%s" id="%s" value="%s" />', $name, $name, $default).
		   '</p>';
	}


	public function ImageField($name, $label, $object)
	{
		$default = get_post_meta( $object->ID, $name, true  );
		$src = '';
		if ($default && $default > 0)
		{
			$src = \Framework\wpUtils::resizeImage(wp_get_attachment_url( $default ), 150, 150);
		}
		//var_dump('default', $default, $src);

		return
			'<p>'.
				sprintf('<label>%s</label>',$label).'<br />'.    
				sprintf('<input type="hidden"class="image-field" name="%s" value="%s"/>', $name, $default).


			  	sprintf('<img class="image-field empty" data-domain="%s" src="%s"/>',  $name, $src).'</br>'.
				sprintf('<a class="image-field select" data-domain="%s" href="#">Select image</a>', $name).
				sprintf('<a class="image-field remove" data-domain="%s" href="#">Remove image</a>', $name).
			'</p>';
	}


	public function GalleryField($name, $label, $object)
	{
		$default = get_post_meta( $object->ID, $name, true  );
		return 
		 '<p class="'.$name.'">'.'<label>'.$label.'</label><br/>'.
		 '<a href="#" class="button custom-media" media-domain="'.$name.'" title="Izveleties"><span class="wp-media-buttons-icon"></span>Select Media</a>'.
		 "<input type='hidden' name='".$name."' value='".$default."'/>".
		 '</p>';
	}
	public function TextArea($name, $label, $object)
	{
		$default = get_post_meta( $object->ID, $name, true  );
		return
			'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label>', $name, $label).
		        '<br />'.    
		   		sprintf('<textarea class="widefat"  name="%s" id="%s">%s</textarea>', $name, $name, $default).
		   '</p>';
	} 


	public function Multiselect($name, $label, $values, $object)
	{
		//$name = trim($name,'[]');
		$default = get_post_meta( $object->ID, $name, true  );

		$default = explode(',', $default);
		$valuesHTML = '';

		foreach ($values as $key => $value) 
		{
			$valuesHTML .= sprintf('<option value="%s" %s>%s</option>', $key, in_array($key, $default) ? 'selected' : '', $value);
		}
		return 

			'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label>', $name, $label).
		   		sprintf('<input type="hidden" name="%s" value="" />', $name). // triks ar overraido≈anu
				sprintf('<select class="widefat select2" id="%s" name="%s[]" multiple>',$name, $name).$valuesHTML.'</select>'.
			'</p>';
	} 

	public function Select($name, $label, $values, $object, $defaultValue = null)
	{
		$name = trim($name,'[]');
		$default = get_post_meta( $object->ID, $name, true  );
		if (($default == '') && ($defaultValue != null))
		{
			$default = $defaultValue;
			
		}
		$valuesHTML = '';
		foreach ($values as $key => $value) 
		{
			$valuesHTML .= sprintf('<option value="%s" %s>%s</option>', $key, $key == $default ? 'selected' : '', $value);
		}
		return 
			'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label>', $name, $label).
				sprintf('<select class="widefat" name="%s">', $name).$valuesHTML.'</select>'.
			'</p>';
	} 
	//=========================================================================================


	public function includeJS($url)
	{
		
		
		WpCustomAdmin::$jsToLoad[] = $url;
	}

	public function includeCSS($url)
	{
		WpCustomAdmin::$cssToLoad[] = $url;
	}


	public function renderCSS()
	{
			foreach (WpCustomAdmin::$cssToLoad as  $url) 
			{
				echo '<link rel="stylesheet" href="'.$url.'"" type="text/css" />';
			}
	}

	public function renderJavascripts()
	{
		


			foreach (WpCustomAdmin::$jsToLoad as  $url) 
			{
				echo '<script type="text/javascript" src="'. $url . '"></script>';
			}

			echo '<script type="text/javascript">
				jQuery(document).ready(function()
				{

						jQuery("select.select2").select2();
						jQuery("input.datepicker").datepicker();
						jQuery("input.timepicker").timepicker({ "timeFormat": "H:i"});
					
				});

			</script>';
		
	}
}