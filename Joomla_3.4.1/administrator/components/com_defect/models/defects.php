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
 * Methods supporting a list of defect records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 */
class DefectModelDefects extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
                'user_id', 'a.user_id', 'linked_user',
				'binary_id', 'a.binary_id','linked_binary','linked_project',
				'name', 'a.name',
				'type', 'a.type',
				'resolution_status','a.resolution_status',
                'mobile', 'a.mobile',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);

			$app = JFactory::getApplication();
			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
		$type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '');
		$this->setState('filter.type', $type);
		
		$resolution_status = $this->getUserStateFromRequest($this->context . '.filter.resolution_status', 'filter_resolution_status', '');
		$this->setState('filter.resolution_status', $resolution_status);
		
		// force a language
		$forcedLanguage = $app->input->get('forcedLanguage');
		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}

		$tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id    A prefix for the store id.
	 *
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.language');
		$id .= ':' . $this->getState('filter.type');
		$id .= ':' . $this->getState('filter.resolution_status');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time , a.user_id ,a.type, a.resolution_status' .
					', a.published, a.created, a.created_by, a.ordering, a.featured, a.language' .
					', a.publish_up, a.publish_down'
			)
		);
		$query->from('#__defect AS a');

        // Join over the users for the linked user.
        $query->select('ul.name AS linked_user')
            ->join('LEFT', '#__users AS ul ON ul.id=a.user_id');
        
        // Join over the users for the linked binary.
        $query->select('bl.name AS linked_binary')
        ->join('LEFT', '#__binary AS bl ON bl.id=a.binary_id');
        
        // Join over the users for the linked binary.
        $query->select('pl.name AS linked_project')
        ->join('LEFT', '#__project AS pl ON pl.id = (SELECT b.project_id FROM ' . $db->quoteName('#__binary') . ' AS b WHERE b.id = a.binary_id)');
        
		// Join over the language
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');


		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(uc.name LIKE ' . $search . ' OR uc.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}
		
		// Filter on the type.
		$type = $this->getState('filter.type');
		
		if ($type != '' && $type != '*' )
		{
			$query->where('a.type = ' . $db->quote($type));
		}
		
		// Filter on the resolution_status.
		$resolution_status = $this->getState('filter.resolution_status');
		
		if ($resolution_status != '' && $resolution_status != '*' )
		{
			$query->where('a.resolution_status = ' . $db->quote($resolution_status));
		}
		
		
		// check user is in project
		$user_id = $user->id;
		
		if($user_id  && !$user->authorise('project.view.all', 'com_defect')){
			require_once JPATH_ADMINISTRATOR . '/components/com_binary/models/binary.php';
			$binaryModel = JModelLegacy::getInstance('binary', 'BinaryModel');
				
			$userBinarys = $binaryModel->loadUserBinary(null, $user_id);
				
			$binaryArray = array();
				
			foreach ($userBinarys as $userBinary) {
				$binaryArray[] = $userBinary->binary_id;
			}
				
			if (count($binaryArray) == 0) {
				$binaryArray[] = 9999999999;
			}
			
			if ($user->authorise('project.view.all_project', 'com_defect'))
			{
				$binarys = $binaryModel->getBinaryListByProjectId($binaryArray);
				$binaryArray = array();
				foreach ($binarys as $binary) {
					$binaryArray[] = $binary->id;
				}
					
				if (count($binaryArray) == 0) {
					$binaryArray[] = 9999999999;
				}
			} 
			$query->where('a.binary_id IN (' . implode(',', array_values($binaryArray)) . ')');
			
		} else if($user->authorise('project.view.all_team', 'com_defect')  && !$user->authorise('project.view.all', 'com_defect') ) {
				$groups = JUserHelper::getUserGroups($user_id);
				$userList = $this->getMemberByTeam($user->team_id);
				
				$userArray = array();
				
				foreach ($userList as $item) {
					$userArray[] = $item->id;
				}
				
				if (count($userArray) == 0) {
					$userArray[] = 9999999999;
				}
				
				$query->where('a.user_id IN (' . implode(',', array_values($userArray)) . ')');
		}
			
		
		// Filter by a single tag.
		$tagId = $this->getState('filter.tag');
		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_defect.defect')
				);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'asc');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'c.title ' . $orderDirn . ', a.ordering';
		}
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
	
	public function  getMemberByTeam($team_id = null)
	{
		$db = JFactory::getDBO ();
		$query = $db->getQuery ( true );
		$query->select ( '*' );
		$query->from ( '#__users' );
	
		if($team_id != null){
			$query->where ( 'team_id = ' . $team_id);
		}
	
		$db->setQuery ( ( string ) $query );
	
		$messages = $db->loadObjectList();
	
		return  $messages;
	}
	
}
