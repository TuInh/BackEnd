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
 * View to edit a defect.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 * @since       1.6
 */
class DefectViewDefect extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() == 'modal')
		{
			$this->form->setFieldAttribute('language', 'readonly', 'true');
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		JToolbarHelper::title(JText::_('COM_DEFECT_MANAGER_DEFECT'), 'address defect');

		// Build the actions for new and existing records.
		if ($isNew)
		{
				JToolbarHelper::apply('defect.apply');
				JToolbarHelper::save('defect.save');
				JToolbarHelper::save2new('defect.save2new');

				JToolbarHelper::cancel('defect.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				
				require_once JPATH_ADMINISTRATOR . '/components/com_defect/models/defect.php';
				$defectModel = JModelLegacy::getInstance('defect', 'DefectModel');

				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ( $this->item->user_id == $userId || $defectModel->isSameProject($user, $this->item))
				{
					JToolbarHelper::apply('defect.apply');
					JToolbarHelper::save('defect.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					JToolbarHelper::save2new('defect.save2new');
				}
			}

			// If checked out, we can still save
		    JToolbarHelper::save2copy('defect.save2copy');


			if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_defect.defect', $this->item->id);
			}

			JToolbarHelper::cancel('defect.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
	}
}
