<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_team
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_team
 * @since       1.5
 */
class TeamModelTeam extends JModelForm
{
	/**
	 * @since   1.6
	 */
	protected $view_item = 'team';

	protected $_item = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_team.team';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('team.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_team')) &&  (!$user->authorise('core.edit', 'com_team')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

	/**
	 * Method to get the team form.
	 * The base form is loaded from XML and then an event is fired
	 *
	 * @param   array    $data      An optional array of data for the form to interrogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * 
	 * @return  JForm  A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_team.team', 'team', array('control' => 'jform', 'load_data' => true));

		if (empty($form))
		{
			return false;
		}

		$id = $this->getState('team.id');
		$params = $this->getState('params');
		$team = $this->_item[$id];
		$params->merge($team->params);

		if (!$params->get('show_email_copy', 0)){
			$form->removeField('team_email_copy');
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = (array) JFactory::getApplication()->getUserState('com_team.team.data', array());

		$this->preprocessData('com_team.team', $data);

		return $data;
	}

	/**
	 * Gets a team
	 *
	 * @param   integer  $pk  Id for the team
	 *
	 * @return mixed Object or null
	 */
	public function &getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('team.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}


		if (!isset($this->_item[$pk]))
		{
			try
			{

                // Create a new query object.
                $db = $this->getDbo();
                $query = $db->getQuery(true);

                // Select required fields from the categories.
                $query->select($this->getState('list.select', 'a.*'))
                    ->from($db->quoteName('#__team') . ' AS a')
 					->where('a.id = ' . (int) $pk);

				$db->setQuery($query);

				$data = $db->loadObject();

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}

	protected function getTeamQuery($pk = null)
	{
		// @todo Cache on the fingerprint of the arguments
		$db		= $this->getDbo();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSql());
		$user	= JFactory::getUser();
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('team.id');
		$query	= $db->getQuery(true);

		if ($pk)
		{
            // Select required fields from the categories.
            $query->select($this->getState('list.select', 'a.*'))
                ->from($db->quoteName('#__team') . ' AS a')
                ->where('a.id = ' . (int) $pk);

		try
			{
				$db->setQuery($query);
				$result = $db->loadObject();

				if (empty($result))
				{
					throw new Exception(JText::_('COM_TEAM_ERROR_TEAM_NOT_FOUND'), 404);
				}

				// If we are showing a team list, then the team parameters take priority
				// So merge the team parameters with the merged parameters
				if ($this->getState('params')->get('show_team_list'))
				{
					$registry = new JRegistry;
					$registry->loadString($result->params);
					$this->getState('params')->merge($registry);
				}
			}
			catch (Exception $e)
			{
				$this->setError($e);
				return false;
			}

			return $result;

		}
	}

	/**
	 * Increment the hit counter for the team.
	 *
	 * @param   integer  $pk  Optional primary key of the team to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 *
	 * @since   3.0
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('team.id');

			$table = JTable::getInstance('Team', 'TeamTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}
}
