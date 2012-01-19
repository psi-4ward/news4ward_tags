<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * News4ward
 * a contentelement driven news/blog-system
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media GbR <http://www.4wardmedia.de>
 * @package news4ward_tags
 * @filesource
 * @licence LGPL
 */


/**
 * @copyright 4ward.media 2011 <http://www.4wardmedia.de>
 * @author Christoph Wiechert <wio@psitrax.de>
 */
 
// FE-Modules
$GLOBALS['FE_MOD']['news4ward']['news4wardTags'] = 'ModuleNews4wardTags';

// News4wardList Filter HOOK
$GLOBALS['TL_HOOKS']['News4wardListFilter'][] = array('News4wardTagsHelper','listFilter');