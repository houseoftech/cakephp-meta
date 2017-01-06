<?php
use Cake\Core\Configure;

// Optionally load additional config defaults from app
if (file_exists(ROOT . DS . 'config' . DS . 'meta_plugin.php')) {
	Configure::load('meta_plugin');
}
