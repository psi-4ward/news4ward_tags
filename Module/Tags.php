<?php

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
namespace Psi\News4ward\Module;

class Tags extends Module
{
    /**
   	 * Template
   	 * @var string
   	 */
   	protected $strTemplate = 'mod_news4ward_tags';


    /**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### News4ward Tagcloud ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->news_archives = $this->sortOutProtected(deserialize($this->news4ward_archives));

		// Threshold can never be 1, that would lead to a division by zero
		if ($this->news4ward_tags_tresholds == 1) {
			$this->news4ward_tags_tresholds = 2;
		}

		// Return if there are no archives
		if (!is_array($this->news_archives) || count($this->news_archives) < 1)
		{
			return '';
		}

		$strBuffer = parent::generate();

		if (count($this->Template->tags) == 0)
		{
			return '';
		}

		return $strBuffer;
	}


	/**
	 * Generate module
	 */
	protected function compile()
    {
		$objTags = $this->Database->prepare('SELECT tag, count(*) AS cnt
											 FROM tl_news4ward_tag
											 LEFT JOIN tl_news4ward_article ON (tl_news4ward_article.id = tl_news4ward_tag.pid)
											 WHERE tl_news4ward_article.pid IN ('.implode(',',$this->news_archives).')
											 '.((!BE_USER_LOGGED_IN) ? " AND (tl_news4ward_article.start='' OR tl_news4ward_article.start<".time().")
											 							 AND (tl_news4ward_article.stop='' OR tl_news4ward_article.stop>".time().")
											 							 AND tl_news4ward_article.status='published'" : '').'
											 GROUP BY tag
											 ORDER BY ' . ($this->news4ward_tags_random ? 'RAND()' : 'cnt') . ' DESC');

		// limit tag count
		if($this->news4ward_tags_count > 0)
			$objTags->limit($this->news4ward_tags_count);

		$objTags = $objTags->execute();

		// just return if on empty result
		if(!$objTags->numRows)
		{
			$this->Template->tags = array();
			return;
		}

		$arrTags = $objTags->fetchAllAssoc();
		$maxCount = $arrTags[0]['cnt'];
		$minCount = $arrTags[count($arrTags)-1]['cnt'];

		// get jumpTo page
		if($this->jumpTo)
		{
			$objJumpTo = $this->Database->prepare('SELECT id,alias FROM tl_page WHERE id=?')->execute($this->jumpTo);
			if(!$objJumpTo->numRows)
				$objJumpTo = $GLOBALS['objPage'];
		}
		else
		{
			$objJumpTo = $GLOBALS['objPage'];
		}

		// calc font-sizes and jumpto-pages
		foreach($arrTags as $k => $v)
		{
			// skip empty tags
			if(!strlen(trim($v['tag']))) continue;

			$arrTags[$k]['size'] = $this->GetTagSizeLogarithmic($v['cnt'],$minCount,$maxCount);
			$arrTags[$k]['href'] = $this->generateFrontendUrl($objJumpTo->row(),'/tag/'.\News4ward\TagsHelper::encodeTag($v['tag']));
			$arrTags[$k]['active'] = ($this->Input->get('tag') == $v['tag']);

			// set active item for the active filter hinting
			if($this->Input->get('tag') == $v['tag'])
			{
				if(!isset($GLOBALS['news4ward_filter_hint']))
				{
					$GLOBALS['news4ward_filter_hint'] = array();
				}

				$GLOBALS['news4ward_filter_hint']['tag'] = array
				(
					'hint'  	=> $this->news4ward_filterHint,
					'value'		=> $v['tag']
				);
			}
		}

		if($this->news4ward_tags_shuffle)
		{
			// randomly sort the array
			if(!$_SESSION['news4wardTagsRandSeed']) $_SESSION['news4wardTagsRandSeed'] = mt_rand();
			mt_srand($_SESSION['news4wardTagsRandSeed']);
			$order = array_map(create_function('$val', 'return mt_rand();'), range(1, count($arrTags)));
			array_multisort($order, $arrTags);
		}
		else
		{
			// sort by tag
			natsort($arrTags);
		}
		$this->Template->tags = $arrTags;

		$this->Template->unit = $this->news4ward_tags_unit;
	}


	/**
	 * Helper function to logarithmic calculate the font-sizes
	 * @param int $count occurrence of the tag
	 * @param int $mincount lowest occureence of a tag
	 * @param int $maxcount highest occureence of a tag
	 * @return int resulting font-size
	 */
	protected function GetTagSizeLogarithmic($count, $mincount, $maxcount)
	{
		// If min and max font size are identical, we don't need to do anything
		if ($mincount == $maxcount) {
			return $mincount;
		}

		$treshold = ($this->news4ward_tags_maxsize-$this->news4ward_tags_minsize)/($this->news4ward_tags_tresholds-1);
		$a = $this->news4ward_tags_tresholds*log($count - $mincount+2)/log($maxcount - $mincount+2)-1;
		return round($this->news4ward_tags_minsize+round($a)*$treshold);
	}

}
?>
