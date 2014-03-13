<?php namespace Framework;

class MVCGenerator extends BaseController
{
	public function index($params = null) 
	{
		

		if (!empty($_POST))
		{

			$data = new BaseViewModel($_POST);
		//echo 'aa';
		 //header('content-type: text');
		//	header('charset: utf8');
			if (trim($data->{'internal-name'})=='') return;

			$data->name = strtolower($data->name);
			$data->Name = ucfirst($data->name);

			$data->{'name-plural'} = strtolower($data->{'name-plural'});
			$data->{'Name-plural'} = ucfirst($data->{'name-plural'});

			$data->{'internal-name'} = strtolower($data->{'internal-name'});
			$data->{'Internal-name'} = ucfirst($data->{'internal-name'}); 
			
			$data->customFields = array();
			$i = 1;

			while(isset($data->{'customfield-name-'.$i}))
			{
				if (trim($data->{'customfield-name-'.$i}) !== '')
				{
					$item = array(
						'caption'	=> $data->{'customfield-caption-'.$i}, 
						'name'		=> $data->{'customfield-name-'.$i} , 
						'control'	=> $data->{'customfield-control-'.$i},
						'kind'		=> $data->{'customfield-kind-'.$i}	
						 );
					

					if (in_array($data->{'customfield-control-'.$i}, array('Select', 'Multiselect')))
					{
						$item['source'] =  $data->{'customfield-source-'.$i};
					}

					if (in_array($data->{'customfield-control-'.$i}, array( 'Multiselect')))
					{
						$item['array'] =  1;
					}
					else
					{
						$item['singular'] = 1;
					}
					$data->customFields[] = $item;
				}
				unset($data->{'customfield-caption-'.$i});
				unset($data->{'customfield-name-'.$i});	
				unset($data->{'customfield-control-'.$i});
				unset($data->{'customfield-kind-'.$i});
				unset($data->{'customfield-source-'.$i});
				$i++;
			}

			
			$model = $this->renderMustache('modelTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			$controller = $this->renderMustache('controllerTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			$viewmodel = $this->renderMustache('viewmodelTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			$view = $this->renderMustache('viewTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			$admin = $this->renderMustache('adminTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			//---
			$listcontroller = $this->renderMustache('listControllerTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			$listviewmodel = $this->renderMustache('listViewmodelTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			$listview = $this->renderMustache('listViewTemplate', $data,  array('echo'=> false,'template_dir'=>dirname(__FILE__).'/../templates/code'  ));
			
			
		
			// saving files
			$log = array();
			if (isset($data->{'create-model'}))
			{
				$model_filename = get_template_directory().'/models/'.$data->{'internal-name'}.'.php';


				file_put_contents($model_filename, $model);
				$log[] = $model_filename;
			}
			if (isset($data->{'create-controller'}))
			{
				$controller_filename = get_template_directory().'/controllers/'.$data->{'internal-name'}.'.php';
				file_put_contents($controller_filename, $controller);
				$log[] = $controller_filename;
			}
			if (isset($data->{'create-viewmodel'}))
			{
				$viewmodel_filename = get_template_directory().'/viewmodels/'.$data->{'internal-name'}.'.php';
				file_put_contents($viewmodel_filename, $viewmodel);
				$log[] = $viewmodel_filename;
			}
			if (isset($data->{'create-view'}))
			{
				$view_filename = get_template_directory().'/templates/'.$data->{'internal-name'}.'.haml';
				file_put_contents($view_filename, $view);
				$log[] = $view_filename;
			}
			if (isset($data->{'create-metabox'}))
			{
				$admin_filename = get_template_directory().'/admin/'.$data->{'internal-name'}.'.php';
				file_put_contents($admin_filename, $admin);
				$log[] = $admin_filename;
			}
			//----------------
			if (isset($data->{'create-listcontroller'}))
			{
				$controller_filename = get_template_directory().'/controllers/'.$data->{'internal-name'}.'List.php';
				file_put_contents($controller_filename, $listcontroller);
				$log[] = $controller_filename;
			}
			if (isset($data->{'create-listview'}))
			{
				$view_filename = get_template_directory().'/templates/'.$data->{'internal-name'}.'List.haml';
				file_put_contents($view_filename, $listview);
				$log[] = $view_filename;
			}
			if (isset($data->{'create-listviewmodel'}))
			{
				$viewmodel_filename = get_template_directory().'/viewmodels/'.$data->{'internal-name'}.'List.php';
				file_put_contents($viewmodel_filename, $listviewmodel);
				$log[] = $viewmodel_filename;
			}
			
			if (isset($data->{'create-routes'}))
			{
				// updating routes table
				$routes_filename = get_template_directory().'/routes.php';
				$routes = file_get_contents($routes_filename);
				$routes = explode(chr(13), $routes);
				$route = array(sprintf(chr(9)."\Framework\Routes::Set('/%s\/([0-9a-z_-]+)/',new %sController());", $data->{'internal-name'},$data->{'Internal-name'}) );
				array_splice( $routes, 1, 0, $route );
				$routes = implode(chr(13), $routes);
				file_put_contents($routes_filename, $routes);
				$log[] = $routes_filename;
			}
			if (isset($data->{'create-listroutes'}))
			{
				// updating routes table
				$routes_filename = get_template_directory().'/routes.php';
				$routes = file_get_contents($routes_filename);
				$routes = explode(chr(13), $routes);
				$route = array(sprintf(chr(9)."\Framework\Routes::Set('/%sList/',new %sListController(),'%s');", $data->{'internal-name'},$data->{'Internal-name'},$data->{'Name-plural'}) );
				array_splice( $routes, 1, 0, $route );
				$routes = implode(chr(13), $routes);
				file_put_contents($routes_filename, $routes);
				$log[] = $routes_filename;
			}
			
			//var_dump($data);
			//var_dump($log);
			$view = new MVCGeneratorViewModel($data);
			
			$view->log = $log;
			$view->wp_admin_url = admin_url( 'edit.php?post_type='.$view->{'internal-name'});
			$view->mvc_generator_url = get_bloginfo('home').'/mvc-generator';
			$this->render('MVCGeneratorDone', $view, array('template_dir'=>dirname(__FILE__).'/../templates'  ));
			//var_dump($data);
		}
		else
		{
			$view = new MVCGeneratorViewModel();
			$view->models = \Model::getModels();
			$this->render('MVCGenerator', $view, array('template_dir'=>dirname(__FILE__).'/../templates'  ));
			
		}
	}
}

//Routes::Set('/mvc-generator/',new MVCGenerator());