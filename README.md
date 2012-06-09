#Meta Plugin for CakePHP 2.x

Meta is an SEO Plugin for CakePHP 2.x which manages title, meta description, and meta keywords for each page in your application.
- - -

##Installation

1.	Put the Meta folder in your App's Plugin folder.
2.	Add the meta table to your database.

		CREATE TABLE `meta` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`controller` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`pass` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		`title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		`description` text COLLATE utf8_unicode_ci,
		`keywords` text COLLATE utf8_unicode_ci,
		`created` datetime DEFAULT NULL,
		`modified` datetime DEFAULT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 ;

3.	Call the Meta element in your layout's head.

		<?php echo $this->element('meta', array(), array('plugin' => 'Meta'));?>

##How to Use

Navigate to the Meta admin page: *http://your_domain/admin/meta/meta*

You can add records manually via the form or you can use the link called 'Find New Paths'. This is an initializer which searches your existing pages located under Views/Pages/ and the pages table in your database. The initializer will attempt to extract descriptions as well.

##Meta Record Fields

Given a URL such as *http://your_domain/pages/My-Wonderful-Page-In-Which-I-Explain-All-Things* we can break it down to the following fields stored in the meta table.

1.	Path - This is for your benefit, so you know which page it corresponds to. */pages/My-Wonderful-Page-In-Which-I-Explain-All-Things*
2.	Controller - *pages*
3.	Action - *display*
4.	Pass - *My-Wonderful-Page-In-Which-I-Explain-All-Things*
5.	Title - *The Explanation of All Things*
6.	Description - *All things need some sort of explanation. Explaning all things is not always easy, but I will attempt to do so anyways.*
7.	Keywords - *all things, explanation, explaining, all things explained, explanation of all things*