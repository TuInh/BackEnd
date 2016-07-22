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
 * View class for a list of teams.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_team
 * @since       1.6
 */
class TeamViewTeams extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		TeamHelper::addSubmenu('teams');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
	
		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}

		if ($this->getLayout() !== 'modal') {
			$this->addToolBar();
		}
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo	= JHelperContent::getActions('com_team', 'component');
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_TEAM_MANAGER_TEAMS'), 'address team');
		
		$group = JUserHelper::getUserGroups($user->id);
		
		$canAction = true;
		
		array_key_exists('6', $group);
		if(count($group) == 1 && isset($group['6']) && $group['6'] == 6){
			$canAction = false;
		}
		
		if (($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_team', 'core.create'))) > 0) && $canAction)
		{
			JToolbarHelper::addNew('team.add');
		}

		if ((($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) && $canAction)
		{
			JToolbarHelper::editList('team.edit');
		}

		if ($canDo->get('core.delete') && $canAction) 
		{
			JToolBarHelper::deleteList('', 'teams.delete', 'JTOOLBAR_DELETE');
		}
		elseif ($canDo->get('core.edit.state') && $canAction)
		{
			JToolbarHelper::trash('teams.trash');
		}


		JHtmlSidebar::setAction('index.php?option=com_team');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0)), 'value', 'text', $this->state->get('filter.published'), true)
		);


		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);

	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
