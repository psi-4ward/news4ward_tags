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
namespace Psi\News4ward;

class TagsHelper extends \Controller
{

	protected static $arrJumpTo = array();


	/**
	 * Return the WHERE-condition if a the url has an tag-parameter
	 * @return bool|string
	 */
	public function listFilter()
	{
		if(!$this->Input->get('tag')) return false;
		$tag = self::decodeTag($this->Input->get('tag'));
		return array
		(
			'where'=>'EXISTS (SELECT * FROM tl_news4ward_tag WHERE tl_news4ward_article.id=tl_news4ward_tag.pid AND tag=?)',
			'values'=>array($tag)
		);
	}


	/**
	 * Add tags to the template
	 *
	 * @param Object $obj
	 * @param array $arrArticles
	 * @param FrontendTemplate $objTemplate
	 */
	public function tagsParseArticle($obj, $arrArticles, $objTemplate)
	{
		$this->import('Database');

		$arrTags = array();

        $arrArticles = $arrArticles[0];

		$objTags = $this->Database->prepare('SELECT tag FROM tl_news4ward_tag WHERE pid=?')->execute($arrArticles['id']);
		if(!$objTags->numRows)
		{
			$objTemplate->tags = array();
			return;
		}


		if(!isset(self::$arrJumpTo[$arrArticles['pid']]))
		{
			$objJumpTo = $this->Database->prepare('SELECT tl_page.id, tl_page.alias
													FROM tl_page
													LEFT JOIN tl_news4ward ON (tl_page.id=tl_news4ward.jumpToList)
													WHERE tl_news4ward.id=?')
								->execute($arrArticles['pid']);
			if($objJumpTo->numRows)
			{
				self::$arrJumpTo[$arrArticles['pid']] = $objJumpTo->row();
			}
			else
			{
				self::$arrJumpTo[$arrArticles['pid']] = false;
			}
		}

		while($objTags->next())
		{
			if(self::$arrJumpTo[$arrArticles['pid']])
			{
				$arrTags[] = array
				(
					'tag' 	=> $objTags->tag,
					'href'	=> $this->generateFrontendUrl(self::$arrJumpTo[$arrArticles['pid']],'/tag/'.self::encodeTag($objTags->tag))
				);
			}
			else
			{
				$arrTags[] = array
				(
					'tag' 	=> $objTags->tag,
					'href'	=> $this->generateFrontendUrl($GLOBALS['objPage']->row(),'/tag/'.self::encodeTag($objTags->tag))
				);
			}
		}

		$objTemplate->tags = $arrTags;

	}


	public static function encodeTag($tag)
	{
		$tag = str_replace('/', '--', $tag);
		return urlencode($tag);
	}


	public static function decodeTag($tag)
	{
		$tag = urldecode($tag);
		return str_replace('--', '/', $tag);
	}
}
