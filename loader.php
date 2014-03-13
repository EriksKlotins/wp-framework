<?php namespace Framework;
	

	


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
	require dirname(__FILE__).'/models/baseModel.php';
	require dirname(__FILE__).'/models/mainMenuModel.php';
	require dirname(__FILE__).'/viewmodels/baseViewModel.php';
	require dirname(__FILE__).'/controllers/baseController.php';


	// APP LOADER
	
	$appPath = get_stylesheet_directory();

	if (get_stylesheet_directory() == get_template_directory())
	{
		$frameworkUrl = content_url().'/mu-plugins/the-framework/';
	}
	else
	{
		$frameworkUrl = content_url().'/plugins/the-framework/';
	}
	
	if (!defined('FRAMEWORK_URL')) define('FRAMEWORK_URL', $frameworkUrl);

	// helpers
	wpUtils::requireDirectory($appPath.'/helpers');

	// models
	wpUtils::requireDirectory($appPath.'/models');

	// models
	wpUtils::requireDirectory($appPath.'/viewmodels');

	// controllers
	wpUtils::requireDirectory($appPath.'/controllers', true);

	// admin stuff
	wpUtils::requireDirectory($appPath.'/admin', true);



	

