<?php
/**
 * @version     2.0.1
 * @package     K2 Links for JCE
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

defined('_WF_EXT') or die('ERROR_403');

/**
 * This class fetches K2 Users
 */
class K2linksUsers extends JObject
{

	var $_option = 'com_k2';
	var $_task = 'user';

	/**
	 * Constructor activating the default information of the class
	 *
	 * @access	protected
	 */
	function __construct($options = array())
	{
	}

	/**
	 * Returns a reference to a editor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser =JContentEditor::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function getInstance()
	{
		static $instance;

		if (!is_object($instance))
		{
			$instance = new K2linksTags();
		}
		return $instance;
	}

	public function getOption()
	{
		return $this->_option;
	}

	public function getTask()
	{
		return $this->_task;
	}

	public function getList()
	{
		$advlink = WFEditorPlugin::getInstance();
		$list = '';
		if ($advlink->checkAccess('k2links.users', '1'))
		{
			$list = '<li id="index.php?option=com_k2&task=user"><div class="tree-row"><div class="tree-image"></div><span class="folder content nolink"><a href="javascript:;">'.JText::_('K2 Users').'</a></span></div></li>';
		}
		return $list;
	}

	function _getK2Users()
	{
		$db = JFactory::getDBO();
		$query = "SELECT juser.id, juser.name FROM #__users as juser
        RIGHT JOIN #__k2_users as k2user ON juser.id = k2user.userID";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function _getK2Items($userID = '')
	{
		$db = JFactory::getDBO();
		$query = "SELECT item.id, item.title, item.alias, item.catid, category.alias AS categoryAlias FROM #__k2_items AS item
        LEFT JOIN #__k2_categories AS category ON item.catid = category.id
        WHERE item.created_by = ".(int)$userID." AND item.created_by_alias = ''";
		$query .= ' AND item.published = 1 AND category.published = 1 ';
		$user = JFactory::getUser();
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$query .= ' AND item.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';
			$query .= ' AND category.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';
		}
		else
		{
			$query .= " AND item.access <=".(int)$user->get('aid');
			$query .= " AND category.access <=".(int)$user->get('aid');
		}
		$query .= " ORDER BY title, created ASC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function getLinks($args)
	{
		$mainframe = JFactory::getApplication();

		$advlink = WFEditorPlugin::getInstance();

		require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');
		require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');

		$items = array();
		$view = isset($args->view) ? $args->view : '';

		switch ($view)
		{

			default :
				$users = self::_getK2Users();
				foreach ($users as $user)
				{
					if ($user->id)
					{
						$items[] = array(
							'id' => K2HelperRoute::getUserRoute($user->id),
							'name' => $user->name,
							'class' => 'folder content'
						);
					}
				}
				break;

			case 'itemlist' :
				$itemlist = self::_getK2Items($args->id);
				foreach ($itemlist as $item)
				{
					$items[] = array(
						'id' => K2HelperRoute::getItemRoute($item->id.':'.$item->alias, $item->catid.':'.$item->categoryAlias),
						'name' => $item->title,
						'class' => 'file'
					);
				}
				break;

			case 'item' :
				break;
		}
		return $items;
	}

}
