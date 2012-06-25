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


// Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['news4ward_tags_count'] = array
(
	'label'		=> &$GLOBALS['TL_LANG']['tl_module']['news4ward_tags_count'],
	'inputType'	=> 'text',
	'default'	=> 0,
	'eval'		=> array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['news4ward_tags_minsize'] = array
(
	'label'		=> &$GLOBALS['TL_LANG']['tl_module']['news4ward_tags_minsize'],
	'inputType'	=> 'text',
	'default'	=> 10,
	'eval'		=> array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['news4ward_tags_maxsize'] = array
(
	'label'		=> &$GLOBALS['TL_LANG']['tl_module']['news4ward_tags_maxsize'],
	'inputType'	=> 'text',
	'default'	=> 24,
	'eval'		=> array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['news4ward_tags_tresholds'] = array
(
	'label'		=> &$GLOBALS['TL_LANG']['tl_module']['news4ward_tags_tresholds'],
	'inputType'	=> 'text',
	'default'	=> 7,
	'eval'		=> array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_module']['fields']['news4ward_tags_unit'] = array
(
	'label'		=> &$GLOBALS['TL_LANG']['tl_module']['news4ward_tags_unit'],
	'inputType'	=> 'select',
	'default'	=> 'px',
	'options'	=> array('px','%','pt','em'),
	'eval'		=> array('mandatory'=>true, 'tl_class'=>'w50')
);

// Palette
$GLOBALS['TL_DCA']['tl_module']['palettes']['news4wardTags']    = '{title_legend},name,headline,type;{config_legend},news4ward_archives,news4ward_filterHint,news4ward_tags_count,news4ward_tags_minsize,news4ward_tags_maxsize,news4ward_tags_tresholds,news4ward_tags_unit;{redirect_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

?>