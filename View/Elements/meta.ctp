<?php
$meta = $this->requestAction('/meta/meta/index/'.$this->request->controller .'/'. $this->request->action .'/'. implode('#', $this->request->pass));

if (isset($meta) && is_array($meta)) {
	extract($meta);
	// replace variables with field values
	$tags = array('id', 'name', 'created', 'modified');
	if (isset($this->viewVars['data'][$this->viewVars['modelClass']])) {
		$modelData = $this->viewVars['data'][$this->viewVars['modelClass']];
	} elseif (isset($this->viewVars['data'][0][$this->viewVars['modelClass']])) {
		$modelData = $this->viewVars['data'][0][$this->viewVars['modelClass']];
	}
	
	if (isset($modelData)) {
		foreach($tags as $tag) {
			if (isset($modelData[$tag])) {
				if (isset($Metum['title']) && !empty($Metum['title'])) {
				     $Metum['title'] = str_replace('{'.$tag.'}', $modelData[$tag], $Metum['title']);
				}
				if (isset($Metum['description']) && !empty($Metum['description'])) {
				     $Metum['description'] = str_replace('{'.$tag.'}', $modelData[$tag], $Metum['description']);
				}
				if (isset($Metum['keywords']) && !empty($Metum['keywords'])) {
				     $Metum['keywords'] = str_replace('{'.$tag.'}', $modelData[$tag], $Metum['keywords']);
				}
			}
		}
	}
}
/**
 * Set default site title to domain name
 */

$siteTitle = $_SERVER['HTTP_HOST'];

/**
 * Read from configuration file
 * @example APP/config/site_config.php $config['site']['name'] = 'My Sweet Site';
 */

if (Configure::read('site.name')) {
	$siteTitle = Configure::read('site.name');
}

/**
 * Set up an alternate title in case $meta is not set for page
 */

$altTitle = $siteTitle;
$altTitle .= ': ' . Inflector::humanize($this->request->controller);
$altTitle .= ' - '. Inflector::humanize($this->request->action);

/**
 * Set the subtitle using the passed variables of the controller
 * if longer than 3 characters (try to avoid IDs and stuff).
 */

$subTitle = '';
foreach ($this->request->pass as $var) {
	if (strlen($var) > 3 )
		$subTitle .= Inflector::humanize($var) . ' - ';
}
$altTitle = $subTitle . $altTitle;

/**
 * Override the Cake App defaults if there is a record specifically set
 */

if (isset($Metum) && !empty($Metum['title'])) {
	$metaTitle = $Metum['title'];
} elseif (isset($title_for_layout)) {
	$metaTitle = $title_for_layout;
} else {
	$metaTitle = $altTitle;
}

$metaDescription = '';
if (isset($Metum) && !empty($Metum['description'])) {
	$metaDescription = $Metum['description'];
} elseif (isset($description_for_layout)) {
	$metaDescription = $description_for_layout;
}

$metaKeywords = '';
if (isset($Metum) && !empty($Metum['keywords'])) {
	$metaKeywords = $Metum['keywords'];
} elseif (isset($keywords_for_layout)) {
	$metaKeywords = $keywords_for_layout;
}
?>

<?php echo $this->Html->tag('title', stripslashes($metaTitle));?> 
<?php echo $this->Html->meta('description', htmlspecialchars($metaDescription));?> 
<?php echo $this->Html->meta('keywords', htmlspecialchars($metaKeywords));?>