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


// tags field
$GLOBALS['TL_DCA']['tl_news4ward_article']['fields']['tags'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news4ward_article']['tags'],
	'exclude'                 => true,
	'inputType'				  => 'multiColumnWizard',
	'save_callback'			  => array(array('tl_news4ward_article_tags','saveTags')),
	'load_callback'			  => array(array('tl_news4ward_article_tags','loadTags')),
	'eval'					  => array
	(
		'flatArray' => true,
		'doNotSaveEmpty' => true,
		'columnFields' => array
		(
			'tag' => array
			(
				'label'		=> ' ',
				'inputType' => 'autocompleterTextfield',
				'foreignKey' => 'tl_news4ward_tag.tag'
			)
		)
	)
);

// alter the palette
$GLOBALS['TL_DCA']['tl_news4ward_article']['palettes']['default'] = str_replace(';{expert_legend',';{tags_legend},tags;{expert_legend',$GLOBALS['TL_DCA']['tl_news4ward_article']['palettes']['default']);


/**
 * Class tl_news4ward_article_tags
 * provids mehtods to load and save tags in an relation-table
 */
class tl_news4ward_article_tags extends System
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
	}


	/**
	 * Load Tags from the tags-table
	 * @param string $varValue
	 * @param DataContainer $dc
	 * @return array
	 */
	public function loadTags($varValue, DataContainer $dc)
	{
		$objTag = $this->Database->prepare('SELECT * FROM tl_news4ward_tag WHERE pid=? ORDER BY tag')->execute($dc->id);
		return $objTag->fetchEach('tag');
	}


	/**
	 * Save Tags in the tags-table
	 * @param string $varValue
	 * @param DataContainer $dc
	 * @return string empty
	 */
	public function saveTags($varValue, DataContainer $dc)
	{
		$objTag = $this->Database->prepare('SELECT id,tag FROM tl_news4ward_tag WHERE pid=? ORDER BY tag')->execute($dc->id);
		$arrOldTags = array();
		while($objTag->next())	$arrOldTags[$objTag->id] = $objTag->tag;

		$varValue = deserialize($varValue,true);

		// calc difference
		$newTags = array_diff($varValue,$arrOldTags);
		$removedTags = array_diff($arrOldTags,$varValue);

		// insert new tags
		foreach($newTags as $tag)
		{
			$this->Database->prepare('INSERT INTO tl_news4ward_tag SET tstamp=?, pid=?, tag=?')->execute(time(),$dc->id,$tag);
		}

		// remove old tags
		if(count($removedTags))
		{
			$this->Database->execute('DELETE FROM tl_news4ward_tag WHERE pid='.$dc->id.' AND id IN ('.implode(',',array_keys($removedTags)).') ');
		}

		return '';
	}

}