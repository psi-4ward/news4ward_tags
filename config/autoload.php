<?php

/**
 * News4ward
 * a contentelement driven news/blog-system
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media GbR <http://www.4wardmedia.de>
 * @package news4ward_categories
 * @filesource
 * @licence LGPL
 */


// Register the namespace
ClassLoader::addNamespace('Psi');

// Register the classes
ClassLoader::addClasses(array
(
	'Psi\News4ward\Module\Tags'   	=> 'system/modules/news4ward_tags/Module/Tags.php',
	'Psi\News4ward\TagsHelper'   		=> 'system/modules/news4ward_tags/TagsHelper.php',
));

// Register the templates
TemplateLoader::addFiles(array
(
	'mod_news4ward_tags' 					=> 'system/modules/news4ward_tags/templates',
));

?>
