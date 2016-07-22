<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_binary
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Binary component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_binary
 * @since       1.6
 */
class BinaryHelper extends JHelperContent
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
			JText::_('COM_BINARY_SUBMENU_BINARIES'),
			'index.php?option=com_binary&view=binaries',
			$vName == 'binaries'
		);
	}

}
