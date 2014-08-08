<?php
/**
 * @version		$Id$
 * @package		codeis.com
 * @subpackage	Content
 * @copyright	Copyright (C) 2011 http://www.codeis.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');

/**
 * Codehighlighter Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.6
 */
class plgContentCodehighlighter extends JPlugin
{
	var $_alreadyRun=FALSE;
	/**
	 *
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The content object.  Note $article->text is also available
	 * @param	object	The content params
	 * @param	int		The 'page' number
	 * @since	1.6
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		if($this->_alreadyRun){
			return;
		}
		$this->_alreadyRun=TRUE;
		$version=$this->params->get('version');
		$syntaxPath=JURI::root(true).'/plugins/content/codehighlighter/syntaxhighlighter3/';
		if($version==2){
			$syntaxPath=JURI::root(true).'/plugins/content/codehighlighter/syntaxhighlighter2/';
			$this->loadV2($syntaxPath);
		}else {
			$this->loadV3($syntaxPath);
		}


	}
	private function v3autoload($syntaxPath){
		$document =& JFactory::getDocument();
		$document->addScript($syntaxPath.'scripts/shCore.js');
		$document->addScript($syntaxPath.'scripts/shAutoloader.js');
		$document->addStyleSheet($syntaxPath.'styles/shCore.css');
		$document->addStyleSheet($syntaxPath.'styles/shThemeDefault.css');
		$scriptPath=$syntaxPath.'scripts/';
		$autoloadScript="
	  SyntaxHighlighter.autoloader(
	  'applescript            ".$scriptPath."shBrushAppleScript.js',
	  'actionscript3 as3      ".$scriptPath."shBrushAS3.js',
	  'bash shell             ".$scriptPath."shBrushBash.js',
	  'coldfusion cf          ".$scriptPath."shBrushColdFusion.js',
	  'cpp c                  ".$scriptPath."shBrushCpp.js',
	  'c# c-sharp csharp      ".$scriptPath."shBrushCSharp.js',
	  'css                    ".$scriptPath."shBrushCss.js',
	  'delphi pascal          ".$scriptPath."shBrushDelphi.js',
	  'diff patch pas         ".$scriptPath."shBrushDiff.js',
	  'erl erlang             ".$scriptPath."shBrushErlang.js',
	  'groovy                 ".$scriptPath."shBrushGroovy.js',
	  'java                   ".$scriptPath."shBrushJava.js',
	  'jfx javafx             ".$scriptPath."shBrushJavaFX.js',
	  'js jscript javascript  ".$scriptPath."shBrushJScript.js',
	  'perl pl                ".$scriptPath."shBrushPerl.js',
	  'php                    ".$scriptPath."shBrushPhp.js',
	  'text plain             ".$scriptPath."shBrushPlain.js',
	  'py python              ".$scriptPath."shBrushPython.js',
	  'ruby rails ror rb      ".$scriptPath."shBrushRuby.js',
	  'sass scss              ".$scriptPath."shBrushSass.js',
	  'scala                  ".$scriptPath."shBrushScala.js',
	  'sql                    ".$scriptPath."shBrushSql.js',
	  'vb vbnet               ".$scriptPath."shBrushVb.js',
	  'xml xhtml xslt html    ".$scriptPath."shBrushXml.js'
          );
	 SyntaxHighlighter.all();
		 ";		
		$document->addScriptDeclaration($autoloadScript);
	}
	
	private function loadV3($syntaxPath){
		$document =& JFactory::getDocument();
		$document->addScript($syntaxPath.'scripts/shCore.js');
		$languages=array('AS3','Bash','Cpp','CSharp','Css','Delphi','Diff','Groovy','Java','JavaFX','JScript','Perl','Php','Plain','PowerShell','Python','Ruby','Scala','Sql','Vb','Xml');
		foreach($languages as $language){
			if($this->params->get($language)){
				$document->addScript( $syntaxPath.'scripts/shBrush'.$language.'.js' );
			}
		}
        $theme=$this->params->get('theme','Default');        
		$document->addStyleSheet($syntaxPath.'styles/shCore'.$theme.'.css');
		$document->addStyleSheet($syntaxPath.'styles/shTheme'.$theme.'.css');
		$document->addScriptDeclaration('
			  SyntaxHighlighter.all();
		 ');
	}
	private function loadV2($syntaxPath){
		$document =& JFactory::getDocument();
		$document->addScript($syntaxPath.'scripts/shCore.js');
		$languages=array('AS3','Bash','Cpp','CSharp','Css','Delphi','Diff','Groovy','Java','JavaFX','JScript','Perl','Php','Plain','PowerShell','Python','Ruby','Scala','Sql','Vb','Xml');
		foreach($languages as $language){
			if($this->params->get($language)){
				$document->addScript( $syntaxPath.'scripts/shBrush'.$language.'.js' );
			}
		}
		$theme=$this->params->get('theme','Default');   
		$document->addStyleSheet($syntaxPath.'styles/shCore.css');
		$document->addStyleSheet($syntaxPath.'styles/shTheme'.$theme.'.css');
		$document->addScriptDeclaration('
		   SyntaxHighlighter.config.clipboardSwf = "'.$syntaxPath.'scripts/clipboard.swf";
		   SyntaxHighlighter.all();
		 ');
	}


}
