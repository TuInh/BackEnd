<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * TeamModelTeam Model
 *
 * @since  0.0.1
 */
class TeamModelTeam extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Team', $prefix = 'TeamTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_team.team',
			'team',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_team.edit.team.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function getMembers($teamId)
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,name');
		$query->from('#__users');
		$query->where('teamid = ' . $teamId);
		
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();	
			
		//echo "<pre>";	var_dump($messages); echo "</pre>";
		return $messages;
	}
	public function getHelloWorld($id)
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,username,idLeader');
		$query->from('#__meo');
		$query->where('id = ' . $id);
	
		$db->setQuery((string) $query);
		$messages = $db->loadObject();
	
	
		return $messages;
	}
	public function getidLeader()
	{
		$db    = JFactory::getDBO();
		$query = "SELECT DISTINCT idLeader FROM #__meo";
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();
		$lst = array();
		$index=0;
		foreach ($messages as $value) {
			$lst[$index] = $value->idLeader;
			$index ++;
		}
		
		return $lst;
	}
	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return JFactory::getUser()->authorise( "core.delete", "com_helloworld.message." . $record->id );
		}
	}
}
