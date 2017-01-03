<?php
namespace Controller;

class MetaAppController extends AppController {
	var $paginate = array('limit' => 40);
	var $viewClass = 'Theme';
	var $theme = 'default';
	
	function beforeFilter(Event $event) {
		parent::beforeFilter($event);
	}
}
