<?php


/*
	pieliek custom meta box pie custom post type rediģēšanas
	var izsaukt vairākas reizes vienam post-tipam
*/
class WPCustomMetabox extends WpCustomAdmin
	{
		private $typeName = '';
		private $boxTitle = '';
		protected $options = array();
		protected $post = null;
	
		
		public function __construct($typeName, $boxTitle, $options = array())
		{
			if (!is_admin() ) return; // šo nevajag, ja nav admin panelis

			//if (!isset($_GET['post']) && empty($_POST) || !isset($_GET['post_type'])) return; //nekas nav..
		//	add_action( 'admin_enqueue_scripts', [&$this,'theme_admin_scripts'] );

			if (isset($_GET['post_type']))
			{
				$post_type = $_GET['post_type'];
			}
			else
			{
				if (empty($_GET))
				{
					
					$id = isset($_POST['post_ID']) ? $_POST['post_ID'] : null;
				}
				else
				{
					$id = isset($_GET['post']) ? $_GET['post'] : null;
				}
				$this->post = get_post($id);
				
				$post_type = ($this->post) ? $this->post->post_type : '';
				
			}
			
			

			
		//var_dump($id,$post,$post->post_type , $typeName,$post->post_type == $typeName,'-----------');
			
			if ($post_type == $typeName)
			{
				
				$defaults =  array('user_field_prefix' => 'not-yet-set!', 'column'=>true, 'columns'=>true, 'box'=>true, 'save'=>true);
				$options = array_merge($defaults, $options);
				$this->typeName = $typeName;
				$this->boxTitle = $boxTitle;
				$this->options = $options;
					//var_dump($typeName, $boxTitle, $options );
				$this->includeJS(FRAMEWORK_URL.'/lib/select2/select2.js');
				$this->includeCSS(FRAMEWORK_URL.'/lib/select2/select2.css');
				$this->includeJS(FRAMEWORK_URL.'/lib/gallery/gallery.js');

		
				$this->includeJS('http://code.jquery.com/ui/1.10.2/jquery-ui.js');
				$this->includeCSS('http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');
				$this->includeJS(FRAMEWORK_URL.'/lib/timepicker/jquery.timepicker.min.js');
				$this->includeCSS(FRAMEWORK_URL.'/lib/timepicker/jquery.timepicker.css');



				$this->InitActions();
				add_action('admin_footer', array(&$this,'renderJavascripts'));
				add_action('admin_head', array(&$this,'renderCSS'));
				add_action( 'admin_enqueue_scripts', [&$this,'theme_admin_scripts'] );
				$this->OnInitialize();
			}
		}

		public function theme_admin_scripts()
		{
			wp_enqueue_media();
			//var_dump(content_url() . '/mu-plugins/the-framework/lib/gallery/media.js');
			wp_enqueue_script('the-framework-media-js', content_url() . '/mu-plugins/the-framework/lib/gallery/media.js');
		}

		public function InitActions()
		{	
			//var_dump('InitActions for ',$this->typeName);
			if ($this->options['column']) 	add_action('manage_posts_custom_column', array(&$this,'callOnLoadColumn'), 99999, 2);
			if ($this->options['columns']) 	add_filter('manage_edit-'.$this->typeName.'_columns', array(&$this,'callOnLoadColumns'),99999);
			if ($this->options['save']) 	add_action('save_post', array(&$this,'callOnFormSubmit'), 10, 2 );
			if ($this->options['box']) 		add_action('load-post.php', array(&$this,'AddMetaBox') );
			if ($this->options['box']) 		add_action('load-post-new.php',array(&$this,'AddMetaBox') );
		}



		public function callOnFormSubmit($post_id, $post)
		{

			if (!empty($_POST))
			{
				$post_type = get_post_type_object( $post->post_type );
				if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) return $post_id;
				//var_dump($_POST);
				//die();
				if ($post->post_type == $this->typeName)
				{
					
					return $this->OnFormSubmit($post_id, $post);
				}
			}
		}

		public function callOnLoadContent($object,$box)
		{
			add_action( 'add_meta_boxes', 'lb_meta_box' );
			return $this->OnLoadContent($object, $box);
		}

		public function callOnLoadColumn($column, $post_id) 
		{ 
			return $this->OnLoadColumn($column, $post_id); 
		}

		public function callOnLoadColumns($columns) 
		{ 
			return $this->OnLoadColumns($columns); 
		}


		// default actions -> to be overrided..
		public function OnFormSubmit($post_id, $post)
		{ 
		//	var_dump($_POST);
			//die();
			foreach ($_POST as $key => $value) 
			{
				if (mb_strpos($key, $this->options['user_field_prefix'])===0 )
				{
					if (is_array($value))
					{
						$value = implode(',',$value);
					}
					$this->SaveMetaValue($post_id, $key,$value);
					//var_dump($post_id, $key, $value);
				}
			}	
		}
		
		// called right after constructor
		protected function OnInitialize( ) { }
		protected function OnLoadContent($object, $box) { }
		protected function OnLoadColumn($column, $post_id) { return $column; }
		protected function OnLoadColumns($columns) { return $columns; }

		public function AddMetaBox()
		{
			 add_meta_box(
		        $this->typeName.'_'.$this->boxTitle.'_metabox_',          // Unique ID
		        esc_html__( $this->boxTitle, $this->boxTitle ),      // Title
		        array(&$this,'callOnLoadContent'),     // Callback function
		        $this->typeName,                 // Admin page (or post type)
		        'normal',                 // Context
		        'default'                   // Priority
	    	);
		}

		public static function SaveMetaValue($post_id, $meta_key,$new_meta_value)
		{
			//if ($new_meta_value === null) return 0;

			$meta_value = get_post_meta( $post_id, $meta_key, true );
			
			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_meta_value && '' == $meta_value )
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			/* If the new meta value does not match the old value, update it. */
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			/* If there is no new meta value but an old value exists, delete it. */
			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $post_id, $meta_key, $meta_value );
		}





	}

?>