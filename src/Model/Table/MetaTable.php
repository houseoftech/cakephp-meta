<?php
namespace Meta\Model\Table;

use Cake\ORM\Table;

class MetaTable extends Table {
	public function initialize(array $config)
	{
		$this->addBehavior('Timestamp');
	}
}
