<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of defects.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 * @since       1.6
 */
class DefectViewDefects extends JViewLegacy
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

		DefectHelper::addSubmenu('defects');

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
		$canDo	= JHelperContent::getActions('com_defect', 'category', $this->state->get('filter.category_id'));
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_DEFECT_MANAGER_DEFECTS'), 'address defect');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_defect', 'core.create'))) > 0)
		{
			JToolbarHelper::addNew('defect.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('defect.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('defects.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('defects.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('defects.checkin');
		}
// 			JToolbarHelper::custom('defects.export', 'download.png', 'featured_f2.png', 'EXPORT', true);

			JToolbarHelper::modal('fileModal', 'icon-upload', 'Import');
		
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'defects.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('defects.trash');
		}


		JHtmlSidebar::setAction('index.php?option=com_defect');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0)), 'value', 'text', $this->state->get('filter.published'), true)
		);

//		JHtmlSidebar::addFilter(
//			JText::_('JOPTION_SELECT_CATEGORY'),
//			'filter_category_id',
//			JHtml::_('select.options', JHtml::_('category.options', 'com_defect'), 'value', 'text', $this->state->get('filter.category_id'))
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
		$typeoptions        = array();       
		$typeoptions[]      = JHtml::_('select.option', '1', 'COM_DEFECT_TYPE_MANUAL_LABEL');       
		$typeoptions[]      = JHtml::_('select.option', '0', 'COM_DEFECT_TYPE_PERFORMANCE_LABEL');       
		$typeoptions[]      = JHtml::_('select.option', '2', 'COM_DEFECT_TYPE_API_LABEL');       
		$typeoptions[]      = JHtml::_('select.option', '3', 'COM_DEFECT_TYPE_CODE_OPTIMAZE_LABEL');       
		$typeoptions[]      = JHtml::_('select.option', '*', 'JALL');
		JHtmlSidebar::addFilter(
		    JText::_('COM_DEFECT_SELECT_TYPE'),
		    'filter_type',
		    JHtml::_('select.options', $typeoptions, "value", "text", $this->state->get('filter.type'), true)
		);
		
		$resolution_statusoptions        = array();
		$resolution_statusoptions[]      = JHtml::_('select.option', '1', 'COM_DEFECT_RESOLUTION_MODIFICATION_COMPLETED_LABEL');
		$resolution_statusoptions[]      = JHtml::_('select.option', '0', 'COM_DEFECT_RESOLUTION_MAINTAIN_LABEL');
		$resolution_statusoptions[]      = JHtml::_('select.option', '2', 'COM_DEFECT_RESOLUTION_SOURCE_MODIFICATION_LABEL');
		$resolution_statusoptions[]      = JHtml::_('select.option', '3', 'COM_DEFECT_RESOLUTION_CONCEPT_LABEL');
		$resolution_statusoptions[]      = JHtml::_('select.option', '4', 'COM_DEFECT_RESOLUTION_INSUFFICIENT_LABEL');
		$resolution_statusoptions[]      = JHtml::_('select.option', '5', 'COM_DEFECT_RESOLUTION_IRREPRODUCE_LABEL');
		$resolution_statusoptions[]      = JHtml::_('select.option', '5', 'COM_DEFECT_RESOLUTION_DUPLICATED_LABEL');
		$resolution_statusoptions[]      = JHtml::_('select.option', '*', 'JALL');
		JHtmlSidebar::addFilter(
		JText::_('COM_DEFECT_SELECT_RESOLUTION_STATUS'),
		'filter_resolution_status',
		JHtml::_('select.options', $resolution_statusoptions, "value", "text", $this->state->get('filter.resolution_status'), true)
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
