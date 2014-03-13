<?php 


class Model
{
	private static $models = array();

	static function register($name, $model)
	{
		 Model::$models[$name] = $model;
	}

	static function getByName($name)
	{
		return Model::$models[$name];
	}

	static function __callStatic($name, $arguments)
	{
		return Model::$models[$name];
	}

	static function getModels()
	{
		return array_keys(Model::$models);
	}
}


?>