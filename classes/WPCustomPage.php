<?php


/**
 * Creates custom menu page with subpages
 */
class WPCustomPage extends WPCustomAdmin
{
	private $menu_title = '';
	private $position = 0;
	public $menu_slug = '';
	protected $subPages = array();
	protected $templates = array();

	public function __construct($menu_title = '', $position = 0, $page_title = '', $template = '' )
	{
		$this->menu_title = $menu_title;
		//$this->template_title = $template;
		
		$this->position = $position;
		
		$this->menu_slug =  sanitize_title($menu_title);
		$this->templates[$this->menu_slug] = $template;
	
		
		//var_dump('is_admin');
		if (is_admin())
		{
			add_action('admin_menu', array(&$this, 'InitMenu'));
			

		}
		
		
	}


	
	/*
		checks for duplicate position numbers and
		prevent conflicts
	*/
	private function getSafePosition($menu,$position)
	{
		
		$keys = array_keys($menu);
		while (in_array($position, $keys))
		{
			$position+= 0.1;
		}
		
		return (string) $position;
	}
	public  function InitMenu()
	{
		global $menu;
		
		$this->position = $this->getSafePosition($menu, $this->position);
		add_menu_page( $this->menu_title, $this->menu_title, 'edit_posts', $this->menu_slug, array(&$this, 'render'), '', $this->position);
		
		


		foreach ($this->subPages as $key => $item) 
		{
			$slug = $this->menu_slug.'-'.sanitize_title($item['menu_title']);
			$this->templates[$slug] = $item['template'];
			add_submenu_page( $this->menu_slug, $this->item['page_title'], $item['menu_title'],'edit_posts', $slug,array(&$this, 'render'));

		}
		
	}

	public function render()
	{
		//var_dump($a, $b ,$c);
		
			if (isset($_GET['page']))
			{
				$page = $_GET['page'];
				//var_dump($this->templates);
				echo '
				<div class="wrap">
					<div class="icon32" id="icon-edit"><br></div>';
				//	<h2>'.$this->templates[$page].'</h2>';
					$this-> displayForm($this->templates[$page],$page );
				echo '</div>';
			}
		
	}

	protected function getData($formName, $data)
	{
		//var_dump($formName);
		return $data;
	}
	protected function displayForm($template, $page)
	{
		$result = $this->saveForm ($page);
		
		$controller = new BaseController();
		$data = $this->getData($page,json_decode(get_option($page),true));
		if ($result)
		{
			$data['message'] =  'Dati saglabāti';
		}
		$controller->renderMustache($template, $data, array('template_dir'=>get_template_directory().'/templates/admin') );
	}

	protected function saveForm($page)
	{

		//var_dump(is_admin() , $_GET['page'] == $page,$formName);
		//var_dump($this->subPages);
		if (is_admin() && $_GET['page'] == $page)
		{
			if(!empty($_POST))
			{
				$oldPostData = (json_decode(get_option($page), true));
				if(!is_array($oldPostData))
				{
					$oldPostData = array();
				}

				$_POST = array_merge($oldPostData, $_POST, $this->saveFile());

				update_option($page, json_encode($_POST));
				
				return true;
			}
		}
		return false;
	}

	protected function saveFile()
	{
		$fileArray = array();
		if (!empty($_FILES))
		{
			foreach ($_FILES as $key => $file)
			{
				$upload_overrides = array( 'test_form' => false ); 
				$uploaded_file = wp_handle_upload($file, $upload_overrides);

				if(isset($uploaded_file['file'])) 
				{
					$arr_file_type = wp_check_filetype(basename($file['name']));
		            $uploaded_file_type = $arr_file_type['ext'];

		            $allowedExtension = array('png', 'jpg', 'jpeg', 'gif', 'pdf');

		            if(in_array($uploaded_file_type, $allowedExtension))
		            {
			            $file_name_and_location = $uploaded_file['file'];
						$file_title_for_media_library = $file['name'];

						$attachment = array(
			                'post_mime_type' => $uploaded_file_type,
			                'post_title' => addslashes($file_title_for_media_library),
			                'post_content' => '',
			                'post_status' => 'inherit'
			                // 'post_parent' => $post_id,
			                // 'post_name'	=>$namespace.'-'.addslashes($file_title_for_media_library)
			            );
			            $attach_id = wp_insert_attachment( $attachment, $file_name_and_location );
			            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_name_and_location );
			            wp_update_attachment_metadata($attach_id,  $attach_data);
			            // WPCustomMetabox::SaveMetaValue($attach_id, 'link', $_POST['link']);

			        	$fileArray[$key.'-small'] = \Framework\wpUtils::resizeImage(wp_get_attachment_url($attach_id), 100,100);

			            $fileArray[$key] = wp_get_attachment_url($attach_id);
		            }
		            else
		            {
		            	return array();
		            	//throw new Exception('Nepareizs faila formāts!');
		            }
	        	}
			}
			
    	}
    	return $fileArray;
	}

	public function addSubPage($menu_title = '', $template = '' )
	{
		
		$this->subPages[$this->menu_slug.'-'.sanitize_title($menu_title) ] = array('menu_title'=> $menu_title , 'template'=>$template );
	}
}