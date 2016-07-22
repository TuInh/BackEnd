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
 * View to edit a team.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_team
 * @since       1.6
 */
class TeamViewTeam extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		
		// Get the Data
		$this->form = $this->get ( 'Form' );
		$this->item = $this->get ( 'Item' );
		$this->state	= $this->get('State');
		
		
		require_once JPATH_COMPONENT . '/helpers/team.php';
		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = JHelperContent::getActions('com_team');
		
		// Check for errors.
		if (count ( $errors = $this->get ( 'Errors' ) )) {
			JError::raiseError ( 500, implode ( '<br />', $errors ) );
			return false;
		}
		
		// Set the toolbar
		$this->addToolBar ();
		
		// Display the template
		parent::display ( $tpl );
		
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		
	$input = JFactory::getApplication ()->input;
	
	// Hide Joomla Administrator Main menu
	$input->set ( 'hidemainmenu', true );
	
	$isNew = ($this->item->id == 0);
	
	JToolBarHelper::title ( $isNew ? JText::_ ( 'COM_TEAM_MANAGER_TEAM' ) : JText::_ ( 'COM_TEAM_MANAGER_TEAM' ), 'team' );
	// Build the actions for new and existing records.
	if ($isNew) {
		
		// For new records, check the create permission.
		if ($this->canDo->get ( 'core.create' )) {
			
			JToolBarHelper::apply ( 'team.apply', 'JTOOLBAR_APPLY' );
			JToolBarHelper::save ( 'team.save', 'JTOOLBAR_SAVE' );
			JToolBarHelper::custom ( 'team.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
		}
		JToolBarHelper::cancel ( 'team.cancel', 'JTOOLBAR_CANCEL' );
	}
	
	
	else {
		if ($this->canDo->get ( 'core.edit' )) {
			// We can save the new record
			JToolBarHelper::apply ( 'team.apply', 'JTOOLBAR_APPLY' );
			JToolBarHelper::save ( 'team.save', 'JTOOLBAR_SAVE' );
	
			// We can save this record, but check the create permission to see
			// if we can return to make a new one.
			if ($this->canDo->get ( 'core.create' )) {
				JToolBarHelper::custom ( 'team.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
			}
		}
		if ($this->canDo->get ( 'core.create' )) {
			JToolBarHelper::custom ( 'team.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false );
		}
		JToolBarHelper::cancel ( 'team.cancel', 'JTOOLBAR_CLOSE' );
	}
	
	// JToolBarHelper::title ( $title, 'helloworld' );
	// JToolBarHelper::save ( 'helloworld.save' );
	// JToolBarHelper::cancel ( 'helloworld.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE' );
	}
}
