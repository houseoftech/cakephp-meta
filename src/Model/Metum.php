<?php
namespace Model;

class Metum extends MetaAppModel {
	var $name = 'Metum';
	var $useTable = 'meta';
	var $useDbConfig = 'default';
	
	function __construct() {
		if (defined('CHILD_APP_DIR') && file_exists(ROOT . DS . CHILD_APP_DIR . DS . 'Config' . DS . 'database.php')) {
			$this->setDataSource('alt');
		}
		parent::__construct();
	}
}
?>