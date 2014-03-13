<?php namespace Framework;
	


	
	// izskatās, ka šis nav vajadzīgs EK 18.10
	if (!defined('FRAMEWORK_URL')) define('FRAMEWORK_URL', content_url().'/mu-plugins/the-framework/');
	// FRAMEWORK
 	
	
	require dirname(__FILE__).'/classes/wputils.php';
	require dirname(__FILE__).'/classes/model.php';
	require dirname(__FILE__).'/classes/WPCustomAdmin.php';
	require dirname(__FILE__).'/classes/WPCustomMetabox.php';
	require dirname(__FILE__).'/classes/WPCustomPage.php';
	require dirname(__FILE__).'/classes/WPThemeConfiguration.php';
	

	// INTERFACES
	require dirname(__FILE__).'/interfaces/model.php';
	require dirname(__FILE__).'/interfaces/viewModel.php';
	require dirname(__FILE__).'/interfaces/controller.php';
	

	// BASE CLASSES
	
	//require dirname(__FILE__).'/models/DataType.php';
//	require dirname(__FILE__).'/models/QueryBuilder.php';
	require dirname(__FILE__).'/models/baseModel.php';
	require dirname(__FILE__).'/models/mainMenuModel.php';
	
	//require dirname(__FILE__).'/models/baseMySqlModel.php'; // removed
	
	
	//	require dirname(__FILE__).'/models/testModel.php'; // removed
	require dirname(__FILE__).'/viewmodels/baseViewModel.php';
	require dirname(__FILE__).'/controllers/baseController.php';

	// Framework generator tools
	require dirname(__FILE__).'/controllers/MVCGeneratorController.php';
	require dirname(__FILE__).'/viewmodels/MVCGeneratorViewModel.php';

	

	

	// APP LOADER

	// helpers
	wpUtils::requireDirectory(get_template_directory().'/helpers');

	// models
	wpUtils::requireDirectory(get_template_directory().'/models');

	// models
	wpUtils::requireDirectory(get_template_directory().'/viewmodels');

	// controllers
	wpUtils::requireDirectory(get_template_directory().'/controllers', true);

	// admin stuff
	wpUtils::requireDirectory(get_template_directory().'/admin', true);



	// Routes
	if (!is_admin())
	{
		require get_template_directory().'/routes.php';
	}
	

