<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_project
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of projects.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_project
 * @since       1.6
 */
class ProjectViewProjects extends JViewLegacy
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

		ProjectHelper::addSubmenu('projects');

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

		$this->addToolbar();
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
		$canDo	= JHelperContent::getActions('com_project', 'category', $this->state->get('filter.category_id'));
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_PROJECT_MANAGER_PROJECTS'), 'address project');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_project', 'core.create'))) > 0)
		{
			JToolbarHelper::addNew('project.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('project.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('projects.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('projects.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('projects.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'projects.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('projects.trash');
		}

//		if ($user->authorise('core.admin', 'com_project'))
//		{
//			JToolbarHelper::preferences('com_project');
//		}

		JToolbarHelper::custom('projects.export', 'download.png', 'featured_f2.png', 'EXPORT', true);
		JHtmlSidebar::setAction('index.php?option=com_project');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0)), 'value', 'text', $this->state->get('filter.published'), true)
		);

//		JHtmlSidebar::addFilter(
//			JText::_('JOPTION_SELECT_CATEGORY'),
//			'filter_category_id',
//			JHtml::_('select.options', JHtml::_('category.options', 'com_project'), 'value', 'text', $this->state->get('filter.category_id'))
//		);
//
//		

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);

// 		JHtmlSidebar::addFilter(
// 		JText::_('JOPTION_SELECT_TAG'),
// 		'filter_tag',
// 		JHtml::_('select.options', JHtml::_('tag.options', true, true), 'value', 'text', $this->state->get('filter.tag'))
// 		);

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
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
//			'a.featured' => JText::_('JFEATURED'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
