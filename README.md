#Meta Plugin for CakePHP 3.x

Meta is an SEO Plugin for CakePHP 3.x which manages title, meta description, and meta keywords for each page in your application.

For 2.x compatible version, see the 2.x branch.
- - -

##Installation

1. Install manually by putting the contents of this repository in a folder named Meta in your App's plugins folder. Install with composer by adding the following to your composer.json:

	````
	"require": {
		"houseoftech/cakephp-meta": "3.*"
	}
	````

2. Add the meta table to your database.

	````
	CREATE TABLE `meta` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		`controller` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		`action` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		`pass` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
		`title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
		`description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
		`keywords` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
		`template` tinyint(1) NOT NULL DEFAULT '0',
		`created` datetime DEFAULT NULL,
		`modified` datetime DEFAULT NULL,
		PRIMARY KEY (`id`),
		KEY `path` (`path`),
		KEY `controller` (`controller`),
		KEY `action` (`action`),
		KEY `pass` (`pass`),
		KEY `template` (`template`),
		KEY `created` (`created`),
		KEY `modified` (`modified`)
	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 PACK_KEYS=0;
	````

3. Load the plugin in your bootstrap file.

	````
	Plugin::load('Meta', ['bootstrap' => true]);
	````

4. Add the component to your AppController.

	````
	public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Meta.Meta');
    }
	````

5. Add the helper to your AppView.

	````
	public function initialize()
    {
		$this->loadHelper('Meta.Meta');
    }
	````

6. Copy Meta/config/meta_plugin.php file to your app config/ folder if you want to specify database tables to search.

##How to Use

The MetaHelper, if loaded in your AppView, will automatically add tags during the `afterRender()` callback. All you need to do is make sure the following is in your layout or view file:

	````
	<title><?= $this->fetch('title') ?></title>
	<?= $this->fetch('meta') ?>
	````

##How to Manage Meta Records

Navigate to the Meta admin page: *http://your_domain/admin/meta*

You can add records manually via the form or you can use the link called 'Find New Paths'. This is an initializer which searches your existing pages located under src/Template/ and the specified tables in your database. The initializer will attempt to extract descriptions as well.

##Meta Record Fields

Given a URL such as *http://your_domain/pages/My-Wonderful-Page-In-Which-I-Explain-All-Things* we can break it down to the following fields stored in the meta table.

###Path

This is for your benefit, so you know which page it corresponds to. */pages/My-Wonderful-Page-In-Which-I-Explain-All-Things*

To create a template use an asterisk as a wildcard. /articles/view/* would match any Path beginning with /articles/view/

###Controller
*pages*

###Action
*display*

###Pass
*My-Wonderful-Page-In-Which-I-Explain-All-Things*

When creating a template, leave this field blank.

###Title
*The Explanation of All Things*

###Description
*All things need some sort of explanation. Explaining all things is not always easy, but I will attempt to do so anyways.*

###Keywords
*all things, explanation, explaining, all things explained, explanation of all things*

##License
This project is licensed under the terms of the MIT license.
