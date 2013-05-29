<?php
class Metum extends MetaAppModel {
	var $name = 'Metum';
	var $useTable = 'meta';
	var $order = array('Metum.template' => 'DESC', 'Metum.id' => 'ASC');
	
	function __construct() {
		if (defined('PARENT_CORE') && file_exists(ROOT . DS . CHILD_APP_DIR . DS . 'Config' . DS . 'database.php')) {
			$this->setDataSource('alt');
		}
		parent::__construct();
	}
}
?>