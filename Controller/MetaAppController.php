<?php
class MetaAppController extends AppController {
	var $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'Time', 'Text');
	var $paginate = array('limit' => 40);
	var $theme = 'default';
	
	function beforeFilter() {
		parent::beforeFilter();
	}
}
?>