<?php namespace Framework;



// from this class inherits all other models
class BaseModel implements \Framework\ModelInterface
{

	protected $post_type_name = 'not yet set!';
	private $attributes = array();
 

	public function getLinkedMedia($shortcode, $width = false, $height=false)
	{
		//$shortcode = '[gallery ids=""]';
		preg_match_all("/[0-9]+/", $shortcode, $result);
		if (count($result)==0)
		{
			return array();
		}
		$result = $result[0];
		foreach ($result as &$item) 
		{

			$url = 	wp_get_attachment_image_src($item, 'full');
			$url = $url[0];
			if ($width && $height)
			{
				$item =  wpUtils::resizeImage($url, $width, $height);
			}
			else
			{
				$item = $url;
			}

		}
	//	var_dump($shortcode, $result);
		return $result;
		
	}

	public function getLinkedItems($id_arr, $model,  $loadLinkedObjects = false)
	{

	
		$result = array();
		
		
		///var_dump($id_arr,$model);
		if (trim($id_arr) == '') return array();
		$ids = explode(',',$id_arr);

		foreach ($ids as $id) 
		{
			$result[] = $model->getItem($id, $loadLinkedObjects);
		}
		return $result;
	}
	public function __construct($name,  $attributes = array(), $definition = array())
	{
		$this->post_type_name = $name;
		$this->attributes = $attributes;
		$this->definition = $definition;
	}

	private function content_formatting($content) 
	{
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
	

	// nokodē epastu ar js funkciju, lai pasargātos no spambotiem
	public static function protectEmail($email)
	{
		if (mb_strpos($email,'@')>0)
		{
			$email = explode('@',$email);
			$email = sprintf('<script type="text/javascript">document.write(["<a href=\'mailto:","%s","@","%s","\'>","%s","@","%s","</a>"].join(""));</script>',$email[0], $email[1],$email[0], $email[1]);
		}
		return $email;
	}

	public function count()
	{
		return wp_count_posts($this->post_type_name)->publish;
	}


	/*
		atgriež post meta fields,
		un nodrošina, ka tur ir visi vajadzīgie atribūti..
	*/
	private function get_meta_fields($post_id)
	{
		$fields = get_post_meta($post_id);
		if (is_array($fields))
		{
			foreach ($fields as &$value) 
			{
				$value = $value[0];
			}
		}
		else
		{
			$fields = array();
		}
		$result = array_merge($this->attributes, $fields);
		return $result;
	}

	/*
		saņem post->id vai slug un atgriež ID
		vai false, ja tāda post nav

	*/
	public static function normalizePostId($id, $post_type_name = null)
	{
		$result = false;
		if (is_numeric($id) == $id)
		{
			$result = intval($id);
		}
		else
		{
			$params =array(
			'name'			=> $id,
		    'posts_per_page'  => 99999,
		    'numberposts'     => 99999,
		    'offset'          => null,
		    'category'        => null,
		    'orderby'         => 'post_date',
		    'order'           => 'DESC',
		    'include'         => null,
		    'exclude'         => null,
		    'meta_key'        => null,
		    'meta_value'      => null,
		    'post_type'       => $post_type_name,
		    'post_mime_type'  => null,
		    'post_parent'     => null,
		    'post_status'     => 'publish',
		    'suppress_filters' => true );


			$posts = get_posts($params);
			
			$result = count($posts)>0 ? $posts[0]->ID :false;
		}

		return $result;
	}
	

	/*
		atgriež post ar visiem meta laukiem pēc ID vai SLUG
	*/ 
	public function getItem($itemID, $loadLinkecObjects = true)
	{
		$result = array();
		
		
		$itemID = baseModel::normalizePostId($itemID, $this->post_type_name);
		if ($itemID !== false)
		{
			$post = get_post($itemID,'ARRAY_A');
		//	var_dump($itemID);
			$meta = $this->get_meta_fields($itemID);
			$meta['featured-image'] = wp_get_attachment_url( get_post_thumbnail_id($itemID) );	
					
		}
		else
		{
			return false;
		}
	

		$meta['link'] = get_permalink($itemID);	
		$post['post_date_mysql'] =  $post['post_date'];
		if(! isset($post['post_content'])) $post['post_content'] = '';
		
		$post['post_content'] = $this->content_formatting($post['post_content']);
		$exists = array();
		foreach ($meta as $key => $value) 
		{
			$exists['has-'.$key] = ($value) ? true : false;

		}
		return array_merge($post, $meta,$exists);
		
	}


	// atgriež masīvu ar id->vērtībām
	public function getItemsForSelect()
	{
		$result = array();
		$items = $this->getItems(array(),false);
		foreach ($items as $key => $value) 
		{
			$result[$value['ID']] = $value['post_title'];
		}
		return $result;

	}


	protected function getRawItems($params = array())
	{
		$result = array();
		$defaults = array(
		    'posts_per_page'  => 99999,
		    'numberposts'     => 99999,
		    'offset'          => null,
		    'category'        => null,
		    'orderby'         => 'post_date',
		    'order'           => 'DESC',
		    'include'         => null,
		    'exclude'         => null,
		    'meta_key'        => null,
		    'meta_value'      => null,
		    'post_type'       => $this->post_type_name,
		    'post_mime_type'  => null,
		    'post_parent'     => null,
		    'post_status'     => 'publish',
		    'suppress_filters' => true );
		$args = array_merge($defaults,$params);
	//	var_dump($args);


		
		return get_posts($args);
	}

	/*
		atgriež sarakstu ar visiem post, kas atbilst $params padotajiem parametriem
	*/
	public function getItems($params = array(), $loadLinkecObjects = true)
	{
		$posts = $this->getRawItems($params);
		$result = array();
		foreach ($posts as $value) 
		{
			$result[] = $this->getItem($value->ID, $loadLinkecObjects);
		}
		return $result;
	}
}

?>