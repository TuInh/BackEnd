<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_team
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Team component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_team
 * @since       1.6
 */
class TeamHelper extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_TEAM_SUBMENU_Teams'),
			'index.php?option=com_team&view=teams',
			$vName == 'teams'
		);
	}

// 	public static function getActions($messageId = 0)
// 	{
	
// 		$result	= new JObject;
	
// 		if (empty($messageId)) {
// 			$assetName = 'com_team';
// 		}
// 		else {
// 			$assetName = 'com_team.message.'.(int) $messageId;
// 		}
	
// 		$actions = JAccess::getActions('com_team', 'component');
	
// 		foreach ($actions as $action) {
// 			$result->set($action->name, JFactory::getUser()->authorise($action->name, $assetName));
// 		}
	
// 		return $result;
// 	}
}
