<?php
namespace Controller\Component;

class MetaComponent extends Component {
	public $controllerName = null;
	public $actionName = null;
	public $passArray = null;
	public $passString = null;

	public function beforeRender(Controller $controller) {
		if ($controller->name == 'CakeError') {
			return;
		}
		$this->Controller = $controller;
		
		// don't do anything if request is requested (not going to render a page)
		if ($this->Controller->request->is('requested')) {
			return;
		}
		
		$this->controllerName = $this->Controller->request->params['controller'];
		$this->actionName = $this->Controller->request->params['action'];
		$this->passArray = $this->Controller->request->params['pass'];
		
		$data = $this->_lookup();
		$this->Controller->set('metaPluginData', $data);
	}
	
    private function _lookup() {
		$this->Metum = ClassRegistry::init('Meta.Metum');
		$conditions = array();
		$conditions['Metum.controller'] = $this->controllerName;
		$conditions['Metum.action'] = $this->actionName;
		
		// look for deepest level templates first
		$conditions['template'] = 1;
		if (isset($this->passArray) && !empty($this->passArray)) {
			$this->passString = implode('/', $this->passArray);
			$passArray = array_reverse($this->passArray);
			foreach($passArray as $passPart) {
				$conditions['Metum.pass'] = str_replace($passPart, '*', $this->passString);
				$data = $this->Metum->find('first', array('conditions' => $conditions));
				if ($data && count($data)) {
					return $data;
				}
			}
		}
		
		// no specific templates found. search for single record
		unset($conditions['template']);
		if (isset($this->passString) && !empty($this->passString)) {
			$conditions['Metum.pass'] = $this->passString;
		}
		$data = $this->Metum->find('first', array('conditions' => $conditions));
		if (count($data)) {
			return $data;
		}
		
		// search for general template
		$conditions['template'] = 1;
		unset($conditions['Metum.pass']);
		$data = $this->Metum->find('first', array('conditions' => $conditions));
		
		return $data;
    }
}
