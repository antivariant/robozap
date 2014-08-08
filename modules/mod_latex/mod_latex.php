<?php
  
/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   (C) 2012 http://jomla-code.ru
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
  
// No direct access to this file
defined('_JEXEC') or die;
$document=&JFactory::getDocument();
//$document->addScript("/modules/mod_latex/latexit.js");
$document->addScript("/modules/mod_latex/config.js","text/x-mathjax-config;executed=true");
$document->addScript("http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML");
//$document->addScript("http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML/modules/mod_latex/config.js");
?>
