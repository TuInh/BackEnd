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
 * View class for a list of binaries.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_binary
 * @since       1.6
 */
class BinaryViewBinaries extends JViewLegacy
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

		BinaryHelper::addSubmenu('binaries');

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
		$canDo	= JHelperContent::getActions('com_binary', 'category', $this->state->get('filter.category_id'));
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_BINARY_MANAGER_BINARYS'), 'address binary');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_binary', 'core.create'))) > 0)
		{
			JToolbarHelper::addNew('binary.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('binary.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('binaries.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('binaries.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('binaries.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'binaries.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('binaries.trash');
		}

//		if ($user->authorise('core.admin', 'com_binary'))
//		{
//			JToolbarHelper::preferences('com_binary');
//		}


		JHtmlSidebar::setAction('index.php?option=com_binary');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0)), 'value', 'text', $this->state->get('filter.published'), true)
		);

//		JHtmlSidebar::addFilter(
//			JText::_('JOPTION_SELECT_CATEGORY'),
//			'filter_category_id',
//			JHtml::_('select.options', JHtml::_('category.options', 'com_binary'), 'value', 'text', $this->state->get('filter.category_id'))
//		);

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
		
		require_once JPATH_ADMINISTRATOR . '/components/com_project/models/project.php';
		$projectModel = JModelLegacy::getInstance('project', 'ProjectModel');
		$projectList = $projectModel->getProject();
		
		$projectOptions = array();
		if ($projectList)
		{
			foreach($projectList as $message)
			{
				$projectOptions[] = JHtml::_('select.option', $message->id, $message->name);
			}
		}
		
		JHtmlSidebar::addFilter(
			JText::_('COM_BINARY_SELECT_PROJECT'),
			'filter_project',
			JHtml::_('select.options', $projectOptions, "value", "text", $this->state->get('filter.project'), true)
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
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
