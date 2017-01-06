<?php
namespace Meta\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class MetaController extends AppController
{
	public function index()
	{
		$meta = TableRegistry::get('Meta.Meta');
		$this->set('data', $this->paginate($meta));
	}

	public function add()
	{
		$metaTable = TableRegistry::get('Meta.Meta');
		if (!empty($this->request->data)) {
			$data = $metaTable->newEntity($this->request->data());
			if ($metaTable->save($data)) {
				$this->Flash->success('Meta record added.');
				$this->redirect(['action' => 'index'], null, true);
			} else {
				$this->Flash->error('Error encountered while saving.');
			}
		}
		$this->set('data', $metaTable->newEntity());
		$this->render('edit');
	}

	public function delete($id = null)
	{
		$metaTable = TableRegistry::get('Meta.Meta');
		$data = $metaTable->get($id);
		if ($metaTable->delete($data)) {
			$this->Flash->success('Meta record deleted.');
		} else {
			$this->Flash->error('Error encourntered while deleting.');
		}
		$this->redirect(['action' => 'index'], null, true);
	}

	public function edit($id = null)
	{
		$metaTable = TableRegistry::get('Meta.Meta');
		$data = $metaTable->get($id);
		
		if (!empty($this->request->data)) {
			$metaTable->patchEntity($data, $this->request->data());
			if ($metaTable->save($data)) {
				$this->Flash->success('Meta record updated.');
				$this->redirect(['action' => 'index'], null, true);
			} else {
				$this->Flash->error('Error encountered while saving.');
			}
		}
		$this->set('data', $data);
	}

	public function autoAdd()
	{
		$count = 0;
		$searchTables = Configure::read('Meta.searchTables');
		
		$metaTable = TableRegistry::get('Meta.Meta');
		$meta = $metaTable->find('list', [
			'valueField' => 'path'
		]);
		$meta = $meta->toArray();
		
		// add meta data for database records
		if (is_array($searchTables) && count($searchTables)) {
			foreach($searchTables as $searchTable => $tableMap) {
				if ($table = TableRegistry::get($searchTable)) {
					$data = $table->find('all');
					
					foreach($data as $row) {
						$row = $row->toArray();
						foreach($tableMap as $metaField => &$tableField) {
							if (strstr($tableField, '{')) {
								foreach($row as $field => $value) {
									$tableField = str_replace('{'.$field.'}', $value, $tableField);
									
									if ($metaField == 'description' && (strlen($tableField) > 160 || strstr($tableField, '<'))) {
										$tableField = $this->_extractDescription($tableField);
									}
								}
							}
						}
						
						$tableMap['controller'] = ucfirst($tableMap['controller']);
						
						if (empty($meta) || in_array($tableMap['path'], $meta) == false) {
							$newMeta = $metaTable->newEntity($tableMap);
							$metaTable->save($newMeta);
							$count++;
						}
					}
				}
			}
		}
		
		// add meta data for view files
		$newPaths = [];
		$newPaths = $this->_findPaths(APP . 'Template');
		if (count($newPaths)) {
			foreach ($newPaths as $path => $info) {
				if (in_array($path, $meta) == false) {
					$newData['Meta']['path'] = $path;

					$pathArray = explode('/', substr($path, 1), 3);
					$newData['Meta']['controller'] = ucfirst($pathArray[0]);
					if (stristr($pathArray[0], 'pages')) {
						$newData['Meta']['action'] = 'display';
						$newData['Meta']['pass'] = $pathArray[1];
						if (isset($pathArray[2])) {
							$newData['Meta']['pass'] .= '/'.$pathArray[2];
						}
					} else {
						$newData['Meta']['action'] = $pathArray[1];
						if (isset($pathArray[2])) {
							$newData['Meta']['pass'] = $pathArray[2];
						}
					}

					$newData['Meta']['title'] = $info['title'];
					$newData['Meta']['description'] = $info['description'];
					$newMeta = $metaTable->newEntity($newData);
					$metaTable->save($newMeta);
					$count++;
					unset($newData);
				}
			}
		}

		if ($count) {
			$this->Flash->success("$count new paths found and saved.");
		} else {
			$this->Flash->error("No new paths found.");
		}

		$this->redirect(['action' => 'index']);
	}

	private function _findPaths($dir)
	{
		if (!is_dir($dir)) {
			return [];
		}

		$exclusions = [
			'.',
			'..',
			'.DS_Store',
			'.svn',
			'empty',
			'ajax.ctp',
			'Admin',
			'Element',
			'Email',
			'Error',
			'Layout',
			'display.ctp'
		];
		$paths = [];
		$dir = dir($dir);

		while (($file = $dir->read()) !== false) {
			if (array_search($file, $exclusions) === false) {
				$filePath = $dir->path . DS . $file;
				if (is_dir($filePath)) {
					$paths = array_merge($paths, $this->_findPaths($filePath));
				} elseif (stristr($file, '.ctp')) {
					$fileExt = strchr($file, '.');
					$fileName = basename($file, $fileExt);
					$path = str_replace('\\', '/', str_replace(APP . 'Template', '', $dir->path . DS . $fileName));
					$path = strtolower($path);
					
					$pathParts = array_values(array_filter(explode('/', $path)));
					if ($pathParts[0] == 'Pages') {
						unset($pathParts[0]);
					}
					$title = \Cake\Utility\Inflector::humanize(implode(' - ', $pathParts));
					
					if (($fileHandle = fopen($filePath, 'r')) !== false) {
						$description = $this->_extractDescription(fread($fileHandle, 3072));
					}
					$paths = array_merge($paths, [$path => ['title' => $title, 'description' => $description]]);
				}
			}
		}
		$dir->close();

		ksort($paths);
		return $paths;
	}

	private function _extractDescription($content)
	{
		$description = strip_tags($content);
		$description = preg_replace('/\s\s+/', ' ', $description); // strip whitespace
		$description = preg_replace('/\[\{\[.*\]\}\]/', '', $description); // strip element plugins
		$description = substr($description, 0, 150);
		return $description;
	}
}
