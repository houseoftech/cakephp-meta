<?php
namespace Meta\View\Helper;

use Cake\View\Helper;
use Cake\Event\Event;

class MetaHelper extends Helper
{
	public $helpers = ['Html'];
	
	public function afterRender(Event $event, $viewFile)
    {
		$metaPluginData = $this->_View->get('metaPluginData');
		$defaultTitle = $this->_View->get('defaultTitle');
		$defaultDescription = $this->_View->get('defaultDescription');
		$defaultKeywords = $this->_View->get('defaultKeywords');
		
		if (isset($metaPluginData)) {
			$meta['title'] = $metaPluginData->title;
			$meta['description'] = $metaPluginData->description;
			$meta['keywords'] = $metaPluginData->keywords;
		}
		/**
		 * Set default site title to domain name
		 */
		
		if (empty($defaultTitle) && isset($_SERVER['HTTP_HOST'])) {
			$defaultTitle = $_SERVER['HTTP_HOST'];
		}
		
		/**
		 * Set up an alternate title in case $meta is not set for page
		 */
		
		$altTitle = $defaultTitle;
		$altTitle .= ': ' . \Cake\Utility\Inflector::humanize($this->request->controller);
		$altTitle .= ' - '. \Cake\Utility\Inflector::humanize($this->request->action);
		
		/**
		 * Set the subtitle using the passed variables of the controller
		 * if longer than 3 characters (try to avoid IDs and stuff).
		 */
		
		$subTitle = '';
		foreach ($this->request->pass as $var) {
			if (strlen($var) > 3 )
				$subTitle .= \Cake\Utility\Inflector::humanize($var) . ' - ';
		}
		$altTitle = $subTitle . $altTitle;
		
		/**
		 * Override the App defaults if there is a record specifically set
		 */
		
		if (isset($meta) && !empty($meta['title'])) {
			$metaTitle = $meta['title'];
		} elseif ($this->_View->fetch('title')) {
			$metaTitle = $this->_View->fetch('title');
		} else {
			$metaTitle = $altTitle;
		}
		
		$metaDescription = '';
		if (isset($meta) && !empty($meta['description'])) {
			$metaDescription = $meta['description'];
		} elseif (isset($defaultDescription)) {
			$metaDescription = $defaultDescription;
		}
		
		$metaKeywords = '';
		if (isset($meta) && !empty($meta['keywords'])) {
			$metaKeywords = $meta['keywords'];
		} elseif (isset($defaultKeywords)) {
			$metaKeywords = $defaultKeywords;
		}
		
		$this->_View->assign('title', stripslashes($metaTitle));
		$this->_View->start('meta');
		echo $this->Html->meta('description', htmlspecialchars($metaDescription));
		echo $this->Html->meta('keywords', htmlspecialchars($metaKeywords));
		echo $this->_View->fetch('meta');
		$this->_View->end();
    }
}
