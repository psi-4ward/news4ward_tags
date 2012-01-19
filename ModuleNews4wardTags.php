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

class ModuleNews4wardTags extends News4ward
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
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### News4ward Tagcloud ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->news_archives = $this->sortOutProtected(deserialize($this->news4ward_archives));

		// Return if there are no archives
		if (!is_array($this->news_archives) || count($this->news_archives) < 1)
		{
			return '';
		}

		return parent::generate();
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
											 GROUP BY tag
											 ORDER BY cnt DESC');

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
			$arrTags[$k]['size'] = $this->GetTagSizeLogarithmic($v['cnt'],$minCount,$maxCount);
			$arrTags[$k]['href'] = $this->generateFrontendUrl($objJumpTo->row(),'/tag/'.urlencode($v['tag']));
		}

		// randomly sort the array
		shuffle($arrTags);
		$this->Template->tags = $arrTags;
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
		$treshold = ($this->news4ward_tags_maxsize-$this->news4ward_tags_minsize)/($this->news4ward_tags_tresholds-1);
		$a = $this->news4ward_tags_tresholds*log($count - $mincount+2)/log($maxcount - $mincount+2)-1;
		return round($this->news4ward_tags_minsize+round($a)*$treshold);
	}

}
?>