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

	
	/**
	 * Parāda karti ar iespeju izveleties vietu!
	 * @param  [type] $name   [description]
	 * @param  [type] $title  [description]
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	private function location($name, $title, $object)
	{
		/************************/
		$vieta = $object->{$name.'-adrese'};					// adreses strings (var nebūt)
		$koordinatasStr = $object->$name;
		
		$koordinatas = explode(',',$koordinatasStr);
		if (count($koordinatas) != 2)
		{
			$koordinatas = array(56.9462031, 24.1042);
		}
	?>
		 <p>
	        <label for="vieta"><?php echo $title;?></label>
	        <br />
	        <input class="widefat" style="width:80%;" type="text" name="<?php echo $name; ?>-adrese" id="<?php echo $name; ?>-adrese" value="<?php echo $vieta; ?>" size="30" />
	        <input type="button" style="width:15%; float:right;" id="getCoords" value="Atrast kartē"/>
	        <input type="hidden" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $koordinatasStr;?>" />
	       	<div id="map_canvas" class="widefat" style="height:200px;"></div>
	       	 <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
	        <script>
	        	jQuery(document).ready(function()
	        	{

	        		var mapOptions = {
				        zoom: 12,
				        center: new google.maps.LatLng(<?php echo $koordinatas[0].', '.$koordinatas[1];?>),
				        mapTypeId: google.maps.MapTypeId.ROADMAP
				      }
				      var map = new google.maps.Map(document.getElementById('map_canvas'),  mapOptions);
	        		  var marker = new google.maps.Marker({
					          position:new google.maps.LatLng(<?php echo $koordinatas[0].', '.$koordinatas[1];?>),
					          map: map,
					          draggable: true,
					          animation: google.maps.Animation.DROP
					        });
	        		 google.maps.event.addListener(marker, 'dragend', function() {
               			jQuery('#<?php echo $name; ?>').val(marker.position.lat()+', '+marker.position.lng());
               			jQuery('#<?php echo $name; ?>-adrese').val('').attr('placeholder','Vieta ievadīta norādot to kartē');

           			 });

	        		jQuery('#getCoords').click(function()
	        		{
	        			var value = jQuery('#<?php echo $name; ?>-adrese').val();
	        			var geocoder = new google.maps.Geocoder();
	        			geocoder.geocode({'address': value}, function(results,status)
						{
							if (results.length == 0)
							{
								alert('Tāda vieta nav atrasta');
								return;
							}
							var pos = results[0].geometry.location;
						  	jQuery('#<?php echo $name; ?>').val(pos.lat()+', '+pos.lng());
						  	if (marker) marker.setMap(null);
						  	map.setCenter(pos);
						  	marker = new google.maps.Marker({
					          position:pos,
					          map: map,
					          draggable: true,
					          animation: google.maps.Animation.DROP
					        });
					        google.maps.event.addListener(marker, 'dragend', function() {
		               			jQuery('#<?php echo $name; ?>').val(marker.position.lat()+', '+marker.position.lng());
		               			jQuery('#<?php echo $name; ?>-adrese').val('').attr('placeholder','Vieta ievadīta norādot to kartē');

		           			 });

						 // console.log(results[0].geometry.location.lat()+', '+results[0].geometry.location.lng());
						}); 

	        		});
					
	        	});
	        	


	        		  
	        	
	        </script>	        
	    </p>
	    <?php

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


	// public function ImageField($name, $label, $object)
	// {
	// 	$default = get_post_meta( $object->ID, $name, true  );
	// 	$src = '';
	// 	if ($default && $default > 0)
	// 	{
	// 		$src = \Framework\wpUtils::resizeImage(wp_get_attachment_url( $default ), 150, 150);
	// 	}
	// 	//var_dump('default', $default, $src);

	// 	return
	// 		'<p>'.
	// 			sprintf('<label>%s</label>',$label).'<br />'.    
	// 			sprintf('<input type="hidden"class="image-field" name="%s" value="%s"/>', $name, $default).


	// 		  	sprintf('<img class="image-field empty" data-domain="%s" src="%s"/>',  $name, $src).'</br>'.
	// 			sprintf('<a class="image-field select" data-domain="%s" href="#">Select image</a>', $name).
	// 			sprintf('<a class="image-field remove" data-domain="%s" href="#">Remove image</a>', $name).
	// 		'</p>';
	// }


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
	public function ImageField($name, $label, $object)
	{
		
		$src = Resize::image($object->$name, 264,264);

		return 
		'<p class="'.$name.'">'.
		   		sprintf('<label for="%s">%s</label>', $name, $label).
		//sprintf('<div class="form-field"><label for="%s">%s</label>', $name, $title). 
			'<br/>
			<div id="image_preview_container_'.$name.'" class="image_preview" style="">
				<img src="'.$src.'" id="image_preview_'.$name.'" style="width:264px"></div>
			<input 
			id="'.$name.'" 
			class="" 
			size="60" 
			maxlength="400" 
			type="hidden" 
			name="'.$name.'" 
			value="'.$object->$name.'" readonly>
			<a 
				id="select_image_'.$name.'" 
				href="#" 
				class=""
				data-uploader-type="1" 
				data-enable_external_source="0">Izveleties</a>
			<a 
				id="select_image_'.$name.'_remove" 
				href="#" 
			>Aizvākt</a>


				

				<script> jQuery(document).ready(function() { setAPFImageUploader("'.$name.'", false, false,"'.$label.'");}); </script></p>';



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
		   		sprintf('<input type="hidden" name="%s" value="" />', $name). // triks ar overraidoÅanu
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
		// bilžu izvele
		
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