<?php
class MetaController extends MetaAppController {
	var $name = 'Meta';
	var $uses = array('Meta.Metum');
	var $allowedActions = array('index');
	
	function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->Auth)) {
			$this->Auth->mapActions(array(
				'create' => array('admin_initialize')
			));
		}
	}
	
	public function index($controller = '', $action = '', $pass = '') {
		if (!isset($this->params['requested'])) {
			$this->redirect('/');
		}
			
		if (empty($controller) || empty($action)) {
			return false;
		}
		
		$conditions = array();
		$conditions['Metum.controller'] = $controller;
		$conditions['Metum.action'] = $action;
		if (isset($pass) && !empty($pass)) {
			$conditions['Metum.pass'] = str_replace('#', '/', $pass);
		}
		return $this->Metum->find('first', array('conditions' => $conditions));
	}
	
	public function admin_initialize() {
		$count = 0;
		if ($this->loadModel('Page')) {
			if ($pages = $this->Page->find('all', array('fields' => array('path', 'name', 'content')))) {
				$data = $this->Metum->find('all', array('fields' => array('path')));
				$data = Set::extract($data, '{n}.Metum.path');
				foreach ($pages as $page) {
					if (empty($data) || in_array('/pages/'.$page['Page']['path'], $data) == false) {
						$newData['Metum']['path'] = '/pages/'.$page['Page']['path'];
						$newData['Metum']['controller'] = 'pages';
						$newData['Metum']['action'] = 'display';
						$newData['Metum']['pass'] = $page['Page']['path'];
						$newData['Metum']['title'] = $page['Page']['name'];
						$newData['Metum']['description'] = $this->_extractDescription($page['Page']['content']);
						$this->Metum->create();
						$this->Metum->save($newData);
						$count++;
						unset($newData);
					}
				}
			}
		}
		
		$data = $this->Metum->find('all', array('fields' => array('path')));
		$data = Set::extract($data, '{n}.Metum.path');
		
		$newPaths = array();
		$newPaths = $this->_findPaths(APP.'View');
		if (count($newPaths)) {
			foreach ($newPaths as $path => $info) {
				if (in_array($path, $data) == false) {
					$newData['Metum']['path'] = $path;
					
					$pathArray = explode('/', substr($path, 1), 3);
					$newData['Metum']['controller'] = $pathArray[0];
					if (stristr($pathArray[0], 'pages')) {
						$newData['Metum']['path'] = str_replace('Pages', 'pages', $newData['Metum']['path']);
						$newData['Metum']['controller'] = 'pages';
						$newData['Metum']['action'] = 'display';
						$newData['Metum']['pass'] = $pathArray[1];
						if (isset($pathArray[2])) {
							$newData['Metum']['pass'] .= '/'.$pathArray[2];
						}
					} else {
						$newData['Metum']['action'] = $pathArray[1];
						if (isset($pathArray[2])) {
							$newData['Metum']['pass'] = $pathArray[2];
						}
					}
					
					$newData['Metum']['title'] = $info['title'];
					$newData['Metum']['description'] = $info['description'];
					$this->Metum->create();
					$this->Metum->save($newData);
					$count++;
					unset($newData);
				}
			}
		}
		
		if ($count) {
			$this->Session->setFlash("$count new paths found and saved.");
		} else {
			$this->Session->setFlash("No new paths found.");
		}
		
		$this->redirect(array('action' => 'index'));
	}
	
	private function _findPaths($dir) {
		if (!is_dir($dir)) {
			return array();
		}
			
		$exclusions = array(
			'.',
			'..',
			'.DS_Store',
			'empty',
			'ajax.ctp',
			'Elements',
			'Emails',
			'Errors',
			'Helper',
			'Layouts',
			'Scaffolds',
			'Themed',
			'display.ctp'
		);
		$paths = array();
		$dir = dir($dir);
	
		while (($file = $dir->read()) !== false) {
			if (array_search($file, $exclusions) === false) {
				$filePath = $dir->path . DS . $file;
				if (is_dir($filePath)) {
					$paths = array_merge($paths, $this->_findPaths($filePath));
				} elseif (!stristr($file, 'admin_')) {
					$fileExt = strchr($file, '.');
					$fileName = basename($file, $fileExt);
					$path = str_replace('\\', '/', str_replace(APP.'View', '', $dir->path . DS . $fileName));
					
					$title = str_replace('/Pages/', '', $path);
					$title = Inflector::humanize(str_replace('/', ' - ', $title));
					if (($fileHandle = fopen($filePath, 'r')) !== false) {
						$description = $this->_extractDescription(fread($fileHandle, 3072));
					}
					$paths = array_merge($paths, array($path => array('title' => $title, 'description' => $description)));
				}
			}
		}
		$dir->close();
		
		ksort($paths);
		return $paths;
	}
	
	private function _extractDescription($content) {
		$description = strip_tags($content);
		$description = preg_replace('/\s\s+/', ' ', $description);
		$description = substr($description, 0, 150);
		return $description;
	}
}
?>