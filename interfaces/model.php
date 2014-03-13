<?php  namespace Framework;

interface ModelInterface
{
	public function getItem($itemID, $loadLinkedObjects = true);
	public function getItems($params, $loadLinkedObjects = true);
	//public function saveItem($params);
}

?>