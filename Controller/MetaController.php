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

	public function admin_index() {
		$filter = $this->_parseFilter();
		$this->{$this->modelClass}->recursive = 1;
		$this->Session->write($this->name.'_admin_params', $this->params['named']);
		$conditions = am($filter, $this->params['named']);
		unset($conditions['limit']);
		unset($conditions['show']);
		unset($conditions['sort']);
		unset($conditions['page']);
		unset($conditions['direction']);
		unset($conditions['step']);
		foreach ($conditions as $key => $condition) {
			if (is_string($condition)) {
				if (!strpos($key, '.')) {
					unset($conditions[$key]);
					$conditions[$this->modelClass . '.' . $key] =  $condition;
				}
			}

			if (is_array($key)) {
				foreach ($key as $sub_key => $sub_condition) {
					if (!strpos($sub_key, '.')) {
						unset($conditions[$key][$sub_key]);
						$conditions[$key][$this->modelClass . '.' . $sub_key] =  $sub_condition;
					}
				}
			}
		}
		$this->data = $this->paginate($conditions);
		$this->render('admin_index');
	}

	protected function _parseFilter() {
		$operators = array('equal' => '= ', 'notEqual' => '!= ', 'null' => 'NULL', 'greatherThan' => '> ', 'lessThan' => '< ', 'like' => 'LIKE ', 'between' => 'BETWEEN ', 'in' => 'in');
		$this->set('filterOptions', $operators);

		$filter = array();
		if ($this->data) {
			$operator = false;
			foreach ($this->data as $alias => $fields) {
				if (isset($this->$alias)) {
					$inst = $this->$alias;
				} elseif(isset($inst->{$this->modelClass}->$alias)) {
					$inst = $inst->{$this->modelClass}->$alias;
				} else {
					$inst = ClassRegistry::init($alias);
				}
				$i = 0;
				foreach ($fields as $field => $value) {
					$i++;
					if ($i % 2) {
						if ($value) {
							$operator = $operators[$value];
						} else {
							$operator = '';
						}
						if ($value == 'null') {
							$field = str_replace('_type', '', $field);
							$filter[$alias . '.' . $field] = null;
						}
					} elseif ($value !== null && $value !== '') {
						if (!$operator) {
							$this->data[$alias][$field . '_type'] = 'equal';
							$operator = '= ';
						}
						if ($operator == 'in') {
							$filter[$alias . '.' . $field] = explode(',', $value);
						} elseif (is_array($value)) {
							$value = $inst->deconstruct($field, $value);
							if ($value) {
								$filter[$alias . '.' . $field] = $operator . $value;
							}
						} else {
							$filter[$alias . '.' . $field] = $operator . $value;
						}
					}
				}
			}
			$this->Session->write($this->modelClass . '.filter', $filter);
			$this->Session->write($this->modelClass . '.filterForm', $this->data);
		} elseif ($this->Session->check($this->modelClass . '.filter')) {
			$filter = $this->Session->read($this->modelClass . '.filter');
		}
		return $filter;
	}

	public function admin_add () {
		if (!empty ($this->data)) {
			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash($this->{$this->modelClass}->name . ' added');
				$url = am(array('action' => 'index'), $this->Session->read($this->name.'_admin_params'));
				$this->redirect($url, null, true);
			} else {
				$this->Session->setFlash('Error encountered while saving.');
			}
		}
		$this->render('admin_edit');
	}

	public function admin_delete($id = null) {
		if ($this->{$this->modelClass}->delete($id)) {
			$this->Session->setFlash($this->modelClass . ' with id ' . $id . ' deleted');
		} else {
			$this->Session->setFlash('Can\'t delete ' . $this->modelClass . ' with id ' . $id);
		}

		$url = am(array('action' => 'index'), $this->Session->read($this->name.'_admin_params'));
	}

	public function admin_edit($id = null) {
		if (!empty ($this->data)) {
			if ($this->{$this->modelClass}->save($this->data)) {
				if($this->RequestHandler->isAjax()) {
					unset($this->request->data[$this->modelClass]['id']);
					$value = array_pop($this->request->data[$this->modelClass]);
					echo $value;
					$this->render(null, 'ajax', '/common/ajax');
					exit;
				}
				$this->Session->setFlash($this->{$this->modelClass}->alias . ' updated');
				$url = am(array('action' => 'index'), $this->Session->read($this->name.'_admin_params'));
				$this->redirect($url, null, true);
			} else {
				$this->Session->setFlash('Error encountered while saving.');
			}
		} else {
			$this->data = $this->{$this->modelClass}->read(null, $id);
		}
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
			'.svn',
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
				} elseif (!stristr($file, 'admin_') && stristr($file, '.ctp')) {
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
		$description = preg_replace('/\s\s+/', ' ', $description); // strip whitespace
		$description = preg_replace('/\[\{\[.*\]\}\]/', '', $description); // strip element plugins
		$description = substr($description, 0, 150);
		return $description;
	}
}
