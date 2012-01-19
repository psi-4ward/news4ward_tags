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

class News4wardTagsHelper extends System
{

	/**
	 * Return the WHERE-condition if a the url has an tag-parameter
	 * @return bool|string
	 */
	public function listFilter()
	{
		if(!$this->Input->get('tag')) return false;

		$tag = mysql_real_escape_string(urldecode($this->Input->get('tag')));

		return 'EXISTS (SELECT * FROM tl_news4ward_tag WHERE tl_news4ward_article.id=tl_news4ward_tag.pid AND tag="'.$tag.'")';
	}
}

?>