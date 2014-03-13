<?php namespace Framework;


class BaseController implements \Framework\ControllerInterface
{


	// public function renderMustache($template, $data, $options = array())
	// {
	// 	$template_dir =  isset( $options['template_dir']) ? $options['template_dir'] : get_template_directory().'/templates';
		
	// 	$m = new \Mustache_Engine(array(
	// 		'loader' => new \Mustache_Loader_FilesystemLoader($template_dir,array('extension'=>'moustache')),
	// 		'cache' => get_template_directory().'/cache/moustache',
	// 	));
		
	// 	$echo = isset($options['echo']) ? $options['echo'] : true;
		
	// 	$document =  $m ->render($template,$data);

	// 	if ($echo)	echo $document;
	// 	return $document;
	// }
	// public function render($template, $data, $options = array())
	// {	
	// 	$template_dir =  isset( $options['template_dir']) ? $options['template_dir'] : get_template_directory().'/templates';
	// 	$echo = isset($options['echo']) ? $options['echo'] : true;

	// 	$parser = new \HamlParser($template_dir);

	// 	$parser->setTmp(get_template_directory().'/cache/haml');
	// 	//var_dump('clean',$data);
	// 	$data = get_object_vars($data);
	// 	$parser->append($data);
		
		
	// 	//var_dump('dirty',$data);

	// 	$document = $parser->setFile($template.'.haml');
		
	// 	if ($echo)	echo $document;
	// 	return $document;
	// }

	// public function resizeImage($image, $width, $height)
	// {
	// 	return wpUtils::resizeImage($image, $width, $height);
	// }
	public function index($params = null) 
	{
		wp_die('Properly implemented this is not');
	}
}

?>