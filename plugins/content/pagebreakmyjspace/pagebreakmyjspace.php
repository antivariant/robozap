<?php
/**
* @version $Id: pagebreakmyjspace.php (based on pagebreak.php) $
* @version		2.2.1 29/09/2013
* @package		plg_pagebreakmyjspace
* @author       Bernard Saulmé
* @copyright	Copyright (C) 2010-2011-2012-2013 Bernard Saulmé
* @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.html.parameter'); // >= J1.6

/**
* Page break plugin
*
* <b>Usage:</b>
* <code><hr class="system-pagebreak" /></code>
* <code><hr class="system-pagebreak" title="The page title" /></code>
* or
* <code><hr class="system-pagebreak" alt="The first page" /></code>
* or
* <code><hr class="system-pagebreak" title="The page title" alt="The first page" /></code>
* or
* <code><hr class="system-pagebreak" alt="The first page" title="The page title" /></code>
*
*/

class plgContentPagebreakMyjspace extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_content_pagebreakmyjspace', JPATH_ADMINISTRATOR);
	}

	/**
	 * @param	string	The context of the content being passed to the plugin. (since 1.6 only)
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 *
	 * @return	void
	 * @since	
	 */
	 
	public function onBeforeDisplayContent(&$row, &$params, $page = 0)
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
			return;

		$context = JRequest::getCmd('option') .'.'. JRequest::getCmd('view');
		$index_range = $this->params->get('index_range_flexicontent', 0);
		
		if (JRequest::getVar("isflexicontent") == 'yes' && $context == 'com_content.article' && $index_range == 32) { // option=com_flexicontent&view=item || option=com_content&view=item (rewriten)
			$context = 'com_flexicontent.article';
			$this->plgContentPagebreakMyjspace_fct($context, $row, $params, $page, $index_range);
		}
	}

	public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
	{
		if (version_compare(JVERSION, '1.6.0', 'lt')) // 1.6+
			return;

		$index_range = $this->params->get('index_range_flexicontent', 0);
		
		if (JRequest::getVar("isflexicontent") == 'yes' && $context == 'com_content.article' && $index_range == 32) { // option=com_flexicontent&view=item || option=com_content&view=item (rewriten)
			$context = 'com_flexicontent.article';
			$this->plgContentPagebreakMyjspace_fct($context, $row, $params, $page, $index_range);
		}
	}

	public function onContentPrepare($context, &$row, &$params, $page = 0) // 1.6+
	{
		if (version_compare(JVERSION, '1.6.0', 'lt'))
			return;

		// Get Plugin info
		$index_range = $this->params->get('index_range_myjspace', 1) + $this->params->get('index_range_content', 0) + $this->params->get('index_range_k2itemlist', 0) + $this->params->get('index_range_k2_item', 0) + $this->params->get('index_range_zoo_item', 0);

		if ((($index_range & 1) && $context == 'com_content.myjspace') || (($index_range & 2) && $context == 'com_content.article') || (($index_range & 4) && $context == 'com_k2.itemlist') || (($index_range & 8) && $context == 'com_k2.item') || (($index_range & 16) && $context == 'com_zoo.element.textarea'))
			$this->plgContentPagebreakMyjspace_fct($context, $row, $params, $page, $index_range);
	}

	public function onPrepareContent(&$row, &$params, $page = 0) // 1.5
	{
		if (version_compare(JVERSION, '1.6.0', 'ge'))
			return;

		$index_range = $this->params->get('index_range_myjspace', 1) + $this->params->get('index_range_content', 0) + $this->params->get('index_range_k2itemlist', 0) + $this->params->get('index_range_k2_item', 0) + $this->params->get('index_range_zoo_item', 0);

		$context = JRequest::getCmd('option') .'.'. JRequest::getCmd('view');

		if ($context == 'com_myjspace.see')
			$context = 'com_content.myjspace';
		else if ($context == 'com_zoo.item')
			$context = 'com_zoo.element.textarea';

		if ((($index_range & 1) && $context == 'com_content.myjspace') || (($index_range & 2) && $context == 'com_content.article') || (($index_range & 4) && $context == 'com_k2.itemlist') || (($index_range & 8) && $context == 'com_k2.item') || (($index_range & 16) && $context == 'com_zoo.element.textarea'))
			$this->plgContentPagebreakMyjspace_fct($context, $row, $params, $page, $index_range);
	}
	
	protected function plgContentPagebreakMyjspace_fct($context = null, &$row, &$params, $page = 0, $index_range = 1)
	{
		$document = JFactory::getDocument();
		$config = new JConfig();
	
		// Expression to search for
		$regex = '#<hr([^>]*?)class=(\"|\')system-pagebreak(\"|\')([^>]*?)\/*>#iU';
		$regex_img = '#<img([^>]*?)>#iU';
 
		// Get Plugin/component param
		$print = JRequest::getBool('print');
		$view = JRequest::getCmd('view');
		$showall = JRequest::getVar('showall');
		$page = JRequest::getVar('limitstart');
		$idmyjsp = JRequest::getVar('idmyjsp', 0);
		$index_style = $this->params->get('index_style', 0);
				
		// Double check some options :)
		if ($showall == '' && $page == '') {
			$showall = $this->params->get('default_showall', 0);
			$page = 0;
		} else	if ($showall == 1 && $page == '') {
			$page = 0;
		} else
			$showall = 0;

		if ($page < 0) // Workaround (page 0 => -1) to compatible with AseSEF (no difference beetween '' & 0 else)
			$page = 0;
		
		if (!$this->params->get('enabled', 1)) {
			$print = true;
		}
		
		if ($context == 'com_k2.itemlist') {
			$row->pagebreak_text = $row->text;
			$row->text = $this->k2_text($row->id); // temporary replace the text
			$showall = 0;
			$print = false;
		}

		// Simple performance check to determine whether bot should process further (if no page break = nothing to do)
		if (strpos($row->text, 'class="system-pagebreak') === false && strpos($row->text, 'class=\'system-pagebreak') === false) {
			if ($context == 'com_k2.itemlist')
				$row->text = $row->pagebreak_text;
			return true;
		}

		if ($print) {
			$row->text = preg_replace($regex, '<br />', $row->text);
			return true;
		}
			
		$item_text = 0;
		if ($context == 'com_zoo.element.textarea') { // BS ZOO
			$item_text = crc32($row->text);
			if ($idmyjsp != 0 && $item_text != $idmyjsp) {
				$page = 0;
				if ($this->params->get('default_showall', 0) != 1)
					$showall = 0;
			}
		}

		// BS if (!JPluginHelper::isEnabled('content', 'pagebreak') || $params->get('intro_only')|| $params->get('popup') || $view != 'article') {
		if ($params->get('intro_only') || $params->get('popup')) {
			$row->text = preg_replace($regex, '', $row->text);
			return true;
		}

		// BS CSS for template with no pagebreak style, same a detault Joomla template
		if ($this->params->get('use_css', 1)) {
			if (version_compare(JVERSION, '1.6.0', 'ge'))
				$document->addStyleSheet(JURI::root().'plugins/content/pagebreakmyjspace/assets/css/pagebreakmyjspace.css' );
			else
				$document->addStyleSheet(JURI::root().'plugins/content/assets/css/pagebreakmyjspace.css' );
		}
		
		// find all instances of plugin and put in $matches
		$matches = array();
		preg_match_all($regex, $row->text, $matches, PREG_SET_ORDER);

		// Title for the first page with a pagebreak if no text before
		$title_page1 = '';
		if ($this->params->get('usefirstpagebreak', 1)) {
			$pattern_page1 = '#^<hr ([^>]*?)class=(\"|\')system-pagebreak(\"|\')#';
			if ($context == 'com_content.myjspace')
				$pattern_page1 = '#^<div class="myjspace-prefix"></div><div class="myjspace-content"></div><hr ([^>]*?)class=(\"|\')system-pagebreak(\"|\')#';
			
			if (preg_match($pattern_page1, $row->text)) { // Text start with pagebreak for title
				$row->text = preg_replace($regex, '', $row->text, 1);
				$match = (array) JUtility::parseAttributes($matches[0][0]);
				if (isset($match['alt'])) {
					$title_page1 = stripslashes($match['alt']);
				} elseif (isset($match['title'])) {
					$title_page1 = stripslashes($match['title']);
				}
				unset($matches[0]);
			}
		}

		$del_img_n = intval($this->params->get('del_img_n', 0));
		if ($page >= $del_img_n && $del_img_n != 0)
			$row->text = preg_replace($regex_img, '', $row->text);

		if ($showall && $this->params->get('showall', 1)) { // Showall

			if ($context == 'com_content.article') {
				$current = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid).'&showall=1');
			} else {
				if ($config->sef == 0)
					$current = JRoute::_('index.php').'&amp;showall=1';
				else
					$current = JURI::current().'?showall=1';
			}
			$uri = JURI::getInstance();
			$current = $uri->toString(array('scheme', 'host', 'port')).$current;
			$document->addHeadLink($current, 'canonical', 'rel'); // Canonical link		
		
			$hasToc = $this->params->get('multipage_toc', 1);
			if ($hasToc) {
				// Display TOC
				$page = 1;
				$this->plgContentCreateTOC($params, $row, $matches, $page, $index_range, $item_text, $context, $title_page1);
			} else {
				$row->toc = '';
			}
			$row->text = preg_replace($regex, '<br/>', $row->text);

			// K2 item 'TOC' display emulation
			if ($context == 'com_k2.item') {
				if (!(isset($row->metadesc) && $row->metadesc != ''))
					$row->metadesc = $this->k2_metadesc($row);

				if (strstr($row->text, '{K2Splitter}')) {// Introtext may due to <hr id="system-readmore" /> for example
					@list($introtext, $fulltext) = explode('{K2Splitter}', $row->text, 2);
					if (strlen($fulltext) != 0)
						$row->text = $row->toc.'<div class="itemIntroText">'.$introtext.'</div>'.$fulltext;
					else
						$row->text = $row->toc.$row->text;
				} else
					$row->text = $row->toc.$row->text;
			}
			// ZOO 'TOC' display emulation
			if ($context == 'com_zoo.element.textarea') {
				$row->text = $row->toc . $row->text;
			}

			return true;
		}

		// split the text around the plugin
		$text = preg_split($regex, $row->text);

		// count the number of pages
		$n = count($text);
		
		$row->pagebreaktitle = $row->title;
		
		// we have found at least one plugin, therefore at least 2 pages
		if ($n > 1) {
			// Get plugin parameters
			$title	= $this->params->get('title', 1);
			$hasToc = $this->params->get('multipage_toc', 1);

			// add heading or title to <site> Title
			if ($title) {
				if ($page) {
					$page_text = $page + 1;
					if ($page && @$matches[$page-1][2]) {
						$attrs = JUtility::parseAttributes($matches[$page-1][0]);

						if (@$attrs['title']) {
							$row->title = $row->title.' - '.$attrs['title'];
						} else {
							$thispage = $page + 1;
							$row->title = $row->title.' - '.JText::sprintf('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_NUM', $thispage);					
						}
					}
				}
			}

			// reset the text, we already hold it in the $text array
			$row->text = '';

			if (is_numeric($index_style)) {

				if ($context == 'com_content.article') {
					$current = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid).'&showall=1');
				} else {
					if ($config->sef == 0)
						$current = JRoute::_('index.php').'&amp;showall=1';
					else
						$current = JURI::current().'?showall=1';
				}
				$uri = JURI::getInstance();
				$current = $uri->toString(array('scheme', 'host', 'port')).$current;
				$document->addHeadLink($current, 'canonical', 'rel'); // Canonical link
	
				// display TOC
				if ($hasToc) {
					$this->plgContentCreateTOC($params, $row, $matches, $page, $index_range, $item_text, $context, $title_page1);
				} else {
					$row->toc = '';
				}

				// traditional mos page navigation
				jimport('joomla.html.pagination');
				jimport('joomla.html.html.sliders'); // J1.7
				jimport('joomla.html.html.tabs'); // J1.7

				$pageNav = new JPagination($n, $page, 1);

				// page counter
				$pagenavcounter = $this->params->get('pagenavcounter', 'hide');
				if ($pagenavcounter != 'hide' && $pagenavcounter != 'arrows' && $pagenavcounter != '0') { // test 0 for previous version
					$row->text .= '<div class="pagenavcounter myjsp-counter" style="float:'.$pagenavcounter.'" >';
					$row->text .= $pageNav->getPagesCounter();
					$row->text .= '</div>';
				}

				// page text
				if (isset($text[$page])) { // BS
					$text[$page] = str_replace("<hr id=\"\"system-readmore\"\" />", "", $text[$page]);
					$row->text .= $text[$page];
				}

				if ($index_style != 3 && $index_style != 4) {
					if ($this->params->get('class_pagination', '') == '') {
						if (version_compare(JVERSION, '3.0.0', 'ge')) // Style J! 3.0 ...
							$row->text .= '<div class="pager">';
						else
							$row->text .= '<div class="pagination">'; // J1.5 & 2.5
					} else {
						$row->text .= '<div class="'.$this->params->get('class_pagination', '').'">';
					}
					$row->text .= '<br />';
				}

				// add navigation between pages to bottom of text
				if ($hasToc && $context != 'com_k2.itemlist') {				
					if ($index_style == 3 || $index_style == 4) {
						$toc_tmp = $row->toc;
						$row->toc = '';
					} else
						$toc_tmp = '';
//						$attrs3=JUtility::parseAttributes($matches[$page][1]);
//						echo $attrs3['title'];
					$row->text .= $this->plgContentCreateNavigation($matches, $row, $page, $n, $item_text, $context, $toc_tmp);				
				}

				// page links shown at bottom of page if TOC disabled
				if (!$hasToc) {
					$row->text .= $pageNav->getPagesLinks();
				}

				if ($index_style != 3 && $index_style != 4) {
					$row->text .= '</div><br />';
				}

				// K2 'TOC' display emulation
				// For $context == 'com_k2.itemlist' only show the first page ... not very useful option
				if ($context == 'com_k2.item') {
					if (!(isset($row->metadesc) && $row->metadesc != ''))
						$row->metadesc = $this->k2_metadesc($row);

					if ($this->params->get('hide_K2_itemimage', 1) == 1 && $page > 0) // Not into plugin parameters yet (avoid to much parameters ..)
						$document->addStyleDeclaration('.itemImageBlock {display:none;}');
	
					if (strstr($row->text, '{K2Splitter}')) {// Introtext may due to <hr id="system-readmore" /> for example
						if (($page+1) == $n) { // last page
							$row->text = $row->toc.str_replace('{K2Splitter}', '', $row->text);
						} else { // for the first page
							@list($introtext, $fulltext) = explode('{K2Splitter}', $row->text);
							$row->text = $row->toc.'<div class="itemIntroText">'.$introtext.'</div>'.$fulltext;
						}
					} else
						$row->text = $row->toc.$row->text;
				}

				if ($context == 'com_k2.itemlist') {
					$row->text = $row->toc . $row->pagebreak_text;

					$text = preg_split($regex, $row->text); // Only one page is some pagebreak if no introtext
					if (isset($text[0]))
						$row->text = $text[0];
					else
						$row->text = '';
				}

				// ZOO 'TOC' display emulation
				if ($context == 'com_zoo.element.textarea') {
					$row->text = $row->toc.$row->text;
				}

				if ($index_style == 4) { // Case double dropdown. Have distinct id
					$toc_tmp = str_replace('mjsp-menu-select', 'mjsp-menu-select1', $toc_tmp);
					$row->text = $this->plgContentCreateNavigation($matches, $row, $page, $n, $item_text, $context, $toc_tmp, 'myjsp-prev-next1').$row->text;
				}

			} else {
				$t[] = $text[0];
				$t[] = (string) JHtml::_($index_style.'.start');

				foreach ($text as $key => $subtext) {
					if ($key >= 1) {
						$match = $matches[$key - 1];
						$match = (array) JUtility::parseAttributes($match[0]);
						if (isset($match['alt'])) {
							$title = stripslashes($match['alt']);
						} elseif (isset($match['title'])) {
							$title = stripslashes($match['title']);
						} else {
							$title = JText::sprintf('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_NUM', $key);
						}
						$t[] = (string) JHtml::_($index_style.'.panel', $title, 'basic-details'.$key);
					}
					$t[] = (string) $subtext;
				}
				$t[] = (string) JHtml::_($index_style.'.end');
				$row->text = implode(' ', $t);
			}
		}

		return true;
	}

	protected function k2_metadesc(&$row)
	{
		$metadesc = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $row->introtext.' '.$row->fulltext);
		$metadesc = @strip_tags(trim($metadesc));	
		$find = array("/\r|\n/", "/\t/", "/\s\s+/");
		$replace = array(" ", " ", " ");
		$metadesc = preg_replace($find, $replace, $metadesc);
		$metadesc = substr($metadesc, 0, 150);
		$metadesc = htmlentities($metadesc, ENT_QUOTES, 'utf-8');
		return $metadesc;
	}

	// Retreive K2 text
	protected function k2_text($id = 0)
	{
	  	$db	= JFactory::getDBO();
		$query = "SELECT `fulltext`, `introtext` FROM `#__k2_items` WHERE `id` = ".$db->Quote($id);
		$db->setQuery($query);
		$row = $db->loadObject();

		$result = $row->fulltext;
		if (!$result)
			$result = $row->introtext;
	
		return $result;
	}
	
	protected function my_index($context = null , &$row = null)
	{
		$url = '';
		$frontend = false;

		if ($context == 'com_k2.itemlist' || $context == 'com_k2.item') {
			return 'index.php?option=com_k2&view=item&id='.$row->id.':'.$row->alias;
		}

		if (version_compare(JVERSION, '1.6.0', 'lt')) {
			$menu = & JSite::getMenu();
			if ($menu->getActive() == $menu->getDefault())
				$frontend = true;
		} else {
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			if ($menu->getActive() == $menu->getDefault())
				$frontend = true;
		}

		if ($frontend == true) {
			$option = JRequest::getCmd('option');
			$view = JRequest::getCmd('view');
			$layout = JRequest::getCmd('layout');
			$id = JRequest::getCmd('id');
			$pagename = JRequest::getCmd('pagename');
			$Itemid = JRequest::getInt('Itemid');

			$url = "index.php?option=".$option;
			if ($view)
				$url .= "&view=".$view;
			if ($layout)
				$url .= "&layout=".$layout;
			if ($id)
				$url .= "&id=".$id;
			if ($pagename)
				$url .= "&pagename=".$pagename;
			if ($Itemid)
				$url .= "&Itemid=".$Itemid;
		}

		if ($context == 'com_flexicontent.article')
			$url = str_replace('option=com_content', 'option=com_flexicontent', $url);
			
		return $url;		
	}

	protected function plgContentCreateTOC(&$params, &$row, &$matches, &$page, $index_range, $idmyjsp = 0, $context = 'com_content.article', $title_page1 = '')
	{
		if ($index_range == 0)
			return;

		// BS Heading
		if ($this->params->get('article_first_url', 2) == 0)
			$heading = '';
		else if ($title_page1 != '')
			$heading = $title_page1;
		else if ($this->params->get('article_first_url', 2) == 1)
			$heading = $this->params->get('article_first_url_text', '');		
		else
			$heading = isset($row->title) ? $row->title : $this->params->get('article_first_url_text', '');

		// BS Param
		$showall = JRequest::getInt('showall', 0);
		$Itemid = JRequest::getInt('Itemid', 0);
		$show_firstpagenum = $this->params->get('show_firstpagenum', 0);
		
		// Menu Style BS
		$index_style = $this->params->get('index_style', 0);

		// TOC header.
		if ($index_style == 0)
			$menu_id = "article-index";
		else if ($index_style == 1) {
			$menu_id = "mjsp-menu";

			$row->toc .= '
<!--[if IE 7]>
<style type="text/css">
#mjsp-menu li {
	position: static;
}
#mjsp-menu ul li ul {
	top: auto;
/*	position: static; */
 	margin: auto;
}
</style>
<![endif]-->
';
		} else { // == 2
			$menu_id = "mjsp-menu-select";

			$document = JFactory::getDocument();
			$js_content = '

function navigateTo(sel, target) {
    var url = sel.options[sel.selectedIndex].value;
    window[target].location.href = url;
}
';
			$document->addScriptDeclaration($js_content);			
		}

		// Position : inherit, left or right position
		$index_position = $this->params->get('index_position', 'inherit');
		if ($index_position != 'inherit' && $index_style != 3 && $index_style != 4) {
			$menu_style = ' style="float:'.$index_position.'"';
		} else {
			if (version_compare(JVERSION, '3.0.0', 'ge') && $this->params->get('use_css', 1) != 1)
				$menu_style = '	class="pull-right article-index"';
			else
				$menu_style = '';
		}
		
		if ($index_style == 3 || $index_style == 4)
			$menu_style2 = ' class="enligne"';
		else
			$menu_style2 = '';

		$row->toc .= '<div id="'.$menu_id.'"'.$menu_style.$menu_style2.">\n";

		// Index text
		$headingtext = $this->params->get('article_index_text', 'Index'); // BS
		if ($this->params->get('article_index', 1) == 0) { 
			$headingtext = '';
		} else if ($this->params->get('article_index', 1) == 2) {
			if (isset($row->category_title)) // Article | Personal Pages
				$headingtext = $row->category_title;
			if (isset($row->category) && isset($row->category->name)) // K2
				$headingtext = $row->category->name;
		}

		// TOC first Page link.
		$class = ($page === 0 && $showall === 0) ? 'toclink active' : 'toclink';

		// BS
		if ($index_style == 0) {
			if ($this->params->get('article_index', 1) != 0)
				$row->toc .='<h3>'.$headingtext.'</h3>';
		} else if ($index_style == 1 ) {
			$row->toc .= "<ul style=\"list-style-type:none\">\n<li><div class=\"".$this->params->get('class_style1', 'readmore')."\"><p><a href=\"#\">".$headingtext."</a></p></div>\n";
		} else  { // == 2
			$row->toc .= '<select onchange="navigateTo(this, \'window\');">'."\n";
		}
		if ($index_style == 0 || $index_style == 1) {
			if (version_compare(JVERSION, '3.0.0', 'ge') && $this->params->get('use_css', 1) != 1)
				$row->toc .= '<ul class="nav nav-tabs nav-stacked">'."\n";
			else
				$row->toc .= "<ul>\n";
		}

		// If == 2 no index title

		if ($idmyjsp != 0)
			$id_txt = '&idmyjsp='.$idmyjsp;
		else
			$id_txt = '';

		$url_index = $this->my_index($context, $row);
		
		// First page URL
		if ($show_firstpagenum == 0) {
			if ($context == 'com_content.article')
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid));
			else if ($this->params->get('sh404sef_handler', 0) == 1)
				$link = JRoute::_($url_index.'&Itemid='.$Itemid.$id_txt.'&limitstart=0');
			else
				$link = JRoute::_($url_index.'&Itemid='.$Itemid.$id_txt);
		} else {
			if ($context == 'com_content.article')
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid).'&showall=&limitstart=');
			else
				$link = JRoute::_($url_index.'&Itemid='.$Itemid.'&showall=&limitstart=-1'.$id_txt);
		}

				
		if ($this->params->get('article_first_url', 2) != 0) {
			if ($index_style == 0 || $index_style == 1)
				$row->toc .= '<li><a href="'. $link .'" class="'.$class.'">'.$heading."</a></li>\n";
			else // == 2
				$row->toc .='<option value="'. $link .'">'.$heading.'</option>'."\n";
		}

		$i = 2;

		foreach ($matches as $bot) {
//	BS
			if ($context == 'com_content.article')
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid).'&showall=&limitstart='. ($i - 1));
			else if ($this->params->get('sh404sef_handler', 0) == 1)
				$link = JRoute::_($url_index.'&Itemid='.$Itemid.'&showall=&limit=1&limitstart='. ($i-1)) . $id_txt;
			else
				$link = JRoute::_($url_index.'&Itemid='.$Itemid.'&showall=&limitstart='. ($i-1)) . $id_txt;

			if (@$bot[0]) {
				$attrs2 = JUtility::parseAttributes($bot[0]);

				if (@$attrs2['alt']) {
					$title	= stripslashes($attrs2['alt']);
				} elseif (@$attrs2['title']) {
					$title	= stripslashes($attrs2['title']);
				} else {
					$title	= JText::sprintf('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_NUM', $i);
				}
			} else {
				$title	= JText::sprintf('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_NUM', $i);
			}
			$class = ($page == $i-1) ? 'toclink active' : 'toclink';
			
			if ($index_style == 0 || $index_style == 1)
				$row->toc .= "<li>\n".'<a href="'. $link . '" class="'.$class.'">'.$title."</a>\n</li>\n";
			else {
				if ($showall == 0 && (($i-1) == $page || ($page == 0 && $i == 0)))
					$row->toc .= '<option value="'. $link . '" selected="selected">'.$title."</option>\n";
				else
					$row->toc .= '<option value="'. $link . '">'.$title."</option>\n";
			}
			
			$i++;
		}

		if ($this->params->get('showall', 1) == 1 && $index_style != 3 && $index_style != 4) { // BS maj valeur par defaut et test
			if ($context == 'com_content.article')
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid).'&showall=1&limitstart=');
			else
				$link = JRoute::_($url_index.'&Itemid='.$Itemid.'&showall=1&limitstart='.$id_txt);
//			if (!strstr($link, 'showall=1'))
//				$link .= '&showall=1';
			$class = ($showall == 1) ? 'toclink active' : 'toclink';
			
			if ($index_style == 0 || $index_style == 1)
				$row->toc .= "<li>\n".'<a href="'. $link .'" class="'.$class.'">'.JText::_('PLG_CONTENT_PAGEBREAKMYJSPACE_ALL_PAGES')."</a>\n</li>\n";
			else if ($showall == 1)
				$row->toc .= '<option value="'. $link .'" selected="selected">'.JText::_('PLG_CONTENT_PAGEBREAKMYJSPACE_ALL_PAGES')."</option>\n";
			else
				$row->toc .= '<option value="'. $link .'">'.JText::_('PLG_CONTENT_PAGEBREAKMYJSPACE_ALL_PAGES')."</option>\n";
		}
		
		if ($index_style == 0) { // BS
			$row->toc .= "</ul></div>\n";
		} else if ($index_style == 1) {
			$row->toc .= "</ul></li></ul></div>\n";
		} else
			$row->toc .= "</select>\n</div>\n";

	}

	// Navigation Prev/Next arrows
	protected function plgContentCreateNavigation(&$matches, &$row, $page, $n , $idmyjsp = 0, $context = 'com_content.article', $toc_tmp = '', $class = "myjsp-prev-next")
	{

		//$attrs3=JUtility::parseAttributes($matches[$page][1]);
		//echo $attrs3['title'];
		


		$document = JFactory::getDocument();
		$img_prev = $this->params->get('img_prev', '');
		$img_next = $this->params->get('img_next', '');
		$Itemid = JRequest::getInt('Itemid', 0);
		$Itemid_str = ($Itemid > 0) ? '&Itemid='.$Itemid : '';

		if ($this->params->get('arrow_position', 'inherit') == 'none')
			return '';
		
		$url_index = $this->my_index($context, $row);

		if ($idmyjsp != 0)
			$id_txt = '&idmyjsp='.$idmyjsp;
		else
			$id_txt = '';

		$uri = JURI::getInstance();
		$racine = $uri->toString(array('scheme', 'host', 'port'));
		
		// Next >>
		if ($page < $n-1) {
			$page_next = $page + 1;

			if ($context == 'com_content.article')
				$link_next = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid).'&showall=&limitstart='.($page_next));
			else if ($this->params->get('sh404sef_handler', 0) == 1)
				$link_next = JRoute::_($url_index.$Itemid_str.'&limit=1&limitstart='.($page_next).$id_txt);
			else
				$link_next = JRoute::_($url_index.$Itemid_str.'&limitstart='.($page_next).$id_txt);

		   $attrs3=JUtility::parseAttributes($matches[$page][1]);
		   //echo $attrs3['title'];

			if ($img_next == '')
				//$next = '<a href="'.$link_next.'">'.JText::_('JNEXT').' '.JText::_('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_NEXT').'</a>';
				$next = '<a href="'.$link_next.'">'.$attrs3['title'].' '.JText::_('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_NEXT').'</a>';
			else
				$next = '<a href="'.$link_next.'"><img src="'.$img_next.'" alt="" ></a>';

			$document->addHeadLink($racine.$link_next, 'next', 'rel');
		} else {
			$class_next = ' class="disabled"';
			if ($img_next == '')
				$next = JText::_('JNEXT');
			else
				$next = '<img src="'.$img_next.'" class="opaque" alt="" >';
		}

		// << Prev
		if ($page > 0) {
			$page_prev = $page - 1 == 0 ? "" : $page - 1;

			if ($context == 'com_content.article')
				$link_prev = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid).'&showall=&limitstart='.($page_prev));
			else if ($this->params->get('sh404sef_handler', 0) == 1)
				$link_prev = JRoute::_($url_index.$Itemid_str.'&limit=1&limitstart='.($page_prev).$id_txt);
			else
				$link_prev = JRoute::_($url_index.$Itemid_str.'&limitstart='.($page_prev).$id_txt);


            $attrs4=JUtility::parseAttributes($matches[$page-2][1]);
            if ($attrs4['title']=='') $attrs4['title'] = $this->params->get('article_first_url_text', '');

			if ($img_prev == '')
				//$prev = '<a href="'.$link_prev.'">'.JText::_('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_PREV').' ' .JText::_('JPREV').'</a>';
				$prev = '<a href="'.$link_prev.'">'.JText::_('PLG_CONTENT_PAGEBREAKMYJSPACE_PAGE_PREV').' ' .$attrs4['title'].'</a>';
			else
				$prev = '<a href="'.$link_prev.'"><img src="'.$img_prev.'" alt="" ></a>';

			$document->addHeadLink($racine.$link_prev, 'prev', 'rel');
		} else {
			$class_prev = ' class="disabled"';
			if ($img_prev == '')
				$prev = JText::_('JPREV');
			else
				$prev = '<img src="'.$img_prev.'" class="opaque" alt="" >';
		}

		if ($toc_tmp == '')
			$inter_arrow = ' ';
		else
			$inter_arrow = $toc_tmp;

		$arrow_position = $this->params->get('arrow_position', 'inherit');

		if ($this->params->get('pagenavcounter', 'hide') == 'arrows')
			$page_num = '<span class="myjsp-counter"> '.($page+1).'/'.$n.' </span>';
		else
			$page_num = '';

		$return = '<div class="'.$class.'" style="text-align:'.$arrow_position.';">'."\n".'<span class="myjsp-prev">'.$prev.'</span>'.$inter_arrow.$page_num.'<span class="myjsp-next">'.$next."</span>\n</div>";
		return $return;		
	}

} // End of class plgContentPagebreakMyjspace

?>
