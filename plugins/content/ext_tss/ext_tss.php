<?php
/**
 * @package		plg_ext_tss
 * @copyright	Copyright (C) 2013 joomext.ru All rights reserved.
 * @author 		Yuliya Popova seoelle@gmail.com
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');

class plgContentExt_tss extends JPlugin
{

    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {

	if ($context=='com_finder.indexer') return true;

  	$document= JFactory::getDocument();
	$jquery = $this->params->get( 'jquery');	
	 
    if (version_compare(JVERSION, '3', 'ge')) JHtml::_('jquery.framework');
	
    if (isset($jquery)){
          $document->addScript('//ajax.googleapis.com/ajax/libs/jquery/'.$jquery.'/jquery.min.js');
    }
    $document->addScript(JURI::base().'media/ext_tss/assets/js/script.js');
    $document->addStyleSheet(JURI::base().'media/ext_tss/assets/css/style.css');

	if ($this->params->get('tabs')=='1'){
	$this->_replace_tab($row, $params, $page = 0);
	}
	if ($this->params->get('sliders')=='1'){
	$this->_replace_slider($row, $params, $page = 0);
	}
	if ($this->params->get('spoilers')=='1'){
	$this->_replace_spoiler($row, $params, $page = 0);
	}

	   if(!preg_match("#{tab=.+?}|{slider=.+?}|{spoiler=.+?}#s", $row->text)) return;

	}


	// --- Tabs ---
    function _replace_tab(&$row, &$params, $page = 0) {

		if(JRequest::getCmd('format')!='pdf' || !JRequest::getCmd('print')){
			$a=1;
			unset($tabs);
			if(preg_match_all("/{tab=.+?}{tab=.+?}|{tab=.+?}|{\/tabs}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				foreach($matches[0] as $match) {
					if($a==1 && $match!="{/tabs}") {
						$tabs[] = 1;
						$a=2;
					} elseif($match=="{/tabs}"){
						$tabs[]=3;
						$a=1;
					} elseif(preg_match("/{tab=.+?}{tab=.+?}/", $match)){
						$tabs[]=2;
						$tabs[]=1;
						$a=2;
					} else {
						$tabs[]=2;
					}
				}
			}
			@reset($tabs);
			$tabscount = 0;
			if(preg_match_all("/{tab=.+?}|{\/tabs}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				$tabid=1;
				foreach($matches[0] as $match) {
					if($tabs[$tabscount]==1) {
						$match = str_replace("{tab=", "", $match);
						$match = str_replace("}", "", $match);
						$row->text = str_replace("{tab=".$match."}", '<dl class="tabs" id="tabs_tab'.$tabid.'"><dt class="selected">'.$match.'</dt><dd class="selected"><div class="tab-content">', $row->text);
						$tabid++;
					} elseif($tabs[$tabscount]==2) {
						$match = str_replace("{tab=", "", $match);
						$match = str_replace("}", "", $match);
						$row->text = str_replace("{tab=".$match."}", '</div></dd><dt>'.$match.'</dt><dd><div class="tab-content">', $row->text);
					} elseif($tabs[$tabscount]==3) {
						$row->text = str_replace("{/tabs}", '</div></dd></dl><div class="tabs_clr"></div>', $row->text);
					}
					$tabscount++;
				}
			}
		} else {
			if(preg_match_all("/{tab=.+?}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				foreach($matches[0] as $match) {
					$match = str_replace("{tab=", "", $match);
					$match = str_replace("}", "", $match);
					$row->text = str_replace("{tab=".$match."}", '<h3>'.$match.'</h3>', $row->text);
					$row->text = str_replace("{/tabs}", '', $row->text);
				}
			}
		}
	}

	// --- Slider ---
    function _replace_slider(&$row, &$params, $page = 0) {

		if(JRequest::getCmd('format')!='pdf' || !JRequest::getCmd('print')){
			$b=1;
			unset($sliders);
			if(preg_match_all("/{slider=.+?}{slider=.+?}|{slider=.+?}|{\/sliders}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				foreach($matches[0] as $match) {
					if($b==1 && $match!="{/sliders}") {
						$sliders[] = 1;
						$b=2;
					} elseif($match=="{/sliders}"){
						$sliders[]=3;
						$b=1;
					} elseif(preg_match("/{slider=.+?}{slider=.+?}/", $match)){
						$sliders[]=2;
						$sliders[]=1;
						$b=2;
					} else {
						$sliders[]=2;
					}
				}
			}
			@reset($sliders);
			$sliderscount = 0;
			if(preg_match_all("/{slider=.+?}|{\/sliders}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				$sliderid=1;
				foreach($matches[0] as $match) {
					if($sliders[$sliderscount]==1) {
						$match = str_replace("{slider=", "", $match);
						$match = str_replace("}", "", $match);
						$row->text = str_replace("{slider=".$match."}", '<div class="sliders" id="slider'.$sliderid.'"><div class="title">'.$match.'</div><div class="desc">', $row->text);
						$tabid++;
					} elseif($sliders[$sliderscount]==2) {
						$match = str_replace("{slider=", "", $match);
						$match = str_replace("}", "", $match);
						$row->text = str_replace("{slider=".$match."}", '</div><div class="title">'.$match.'</div><div class="desc">', $row->text);
					} elseif($sliders[$sliderscount]==3) {
						$row->text = str_replace("{/sliders}", '</div></div><div class="sliders_clr"></div>', $row->text);
					}
					$sliderscount++;
				}
			}
		} else {
			if(preg_match_all("/{slider=.+?}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				foreach($matches[0] as $match) {
					$match = str_replace("{slider=", "", $match);
					$match = str_replace("}", "", $match);
					$row->text = str_replace("{slider=".$match."}", '<h3>'.$match.'</h3>', $row->text);
					$row->text = str_replace("{/sliders}", '', $row->text);
				}
			}
		}
	}

	// --- Spoiler ---
    function _replace_spoiler(&$row, &$params, $page = 0) {

		if(JRequest::getCmd('format')!='pdf' || !JRequest::getCmd('print')){
			$c=1;
			unset($spoilers);

			if(preg_match_all("/{spoiler=.+?}{spoiler=.+?}|{spoiler=.+?}|{\/spoilers}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				foreach($matches[0] as $match) {
					if($c==1 && $match!="{/spoilers}") {
						$spoilers[] = 1;
						$c=2;
					} elseif($match=="{/spoilers}"){
						$spoilers[]=3;
						$c=1;
					} elseif(preg_match("/{spoiler=.+?}{spoiler=.+?}/", $match)){
						$spoilers[]=2;
						$spoilers[]=1;
						$c=2;
					} else {
						$spoilers[]=2;
					}
				}
			}
			@reset($spoilers);
			$spoilerscount = 0;
			if(preg_match_all("/{spoiler=.+?}|{\/spoilers}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				$spoilerid=1;
				foreach($matches[0] as $match) {
				 $ereg = '/}(.+){/';
						$mat = str_replace($ereg, "", $match);

					if($spoilers[$spoilerscount]==1) {

						$match = str_replace("{spoiler=", "", $match);
						$match = str_replace("}", "", $match);
						$row->text = str_replace("{spoiler=".$match."}", '<div class="spoilers" id="spoiler'.$spoilerid.'"><div class="title">'.$match.'</div><div class="desc">', $row->text);


						$spoilerid++;
					} elseif($spoilers[$spoilerscount]==2) {
						$match = str_replace("{spoiler=", "", $match);
						$match = str_replace("}", "", $match);
						$row->text = str_replace("{spoiler=".$match."}", '</div><div class="title">'.$match.'</div><div class="desc">', $row->text);

					} elseif($spoilers[$spoilerscount]==3) {
						$row->text = str_replace("{/spoilers}", '</div></div><div class="spoilers_clr"></div>', $row->text);
					}
					$spoilerscount++;
				}
			}
		} else {
			if(preg_match_all("/{spoiler=.+?}/", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
				foreach($matches[0] as $match) {
					$match = str_replace("{spoiler=", "", $match);
					$match = str_replace("}", "", $match);
					$row->text = str_replace("{spoiler=".$match."}", '<h3>'.$match.'</h3>', $row->text);
					$row->text = str_replace("{/spoilers}", '', $row->text);
				}
			}
		}
	}
}
?>
