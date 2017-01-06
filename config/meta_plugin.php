<?php
/**
 * Default config values for the Meta plugin
 *
 * To modify the config, copy to your app's config folder
 */

return [
	'Meta' => [
		// Default meta content for any page which no record is found for
		'defaultTitle' => '',
		'defaultDescription' => '',
		'defaultKeywords' => '',
		
		// Tables to auto_load meta records for (e.g. Pages)
		/**
		 * Format for searchTables
		 *
		 * Provide associated array where key is table name
		 * and value is array mapping meta fields to table fields
		 *
		 *	[
		 *		'Pages' => [
		 *			'path' => '/pages/{path}',
		 *			'controller' => 'Pages',
		 *			'action' => 'display',
		 *			'pass' => '{path}',
		 *			'title' => '{name}',
		 *			'description' => '{content}',
		 *		]
		 *	]
		 *
		 */
		'searchTables' => [],
	]
];
