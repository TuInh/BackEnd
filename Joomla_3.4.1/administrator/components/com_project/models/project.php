<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_project
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ProjectHelper', JPATH_ADMINISTRATOR . '/components/com_project/helpers/project.php');

/**
 * Item Model for a Project.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_project
 * @since       1.6
 */
class ProjectModelProject extends JModelAdmin
{
	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_project.project';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_project');
		}
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check against the category.
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_project');
		}
		// Default to component settings if category not known.
		else
		{
			return parent::canEditState($record);
		}
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Project', $prefix = 'ProjectTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_users/models/fields');

		// Get the form.
		$form = $this->loadForm('com_project.project', 'project', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the metadata field to an array.
			$registry = new JRegistry;
		}

		// Load associated project items
		$app = JFactory::getApplication();

		return $item;
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
		$data = JFactory::getApplication()->getUserState('com_project.edit.project.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_project.project', $data);

		$data->notcs = $data->unit + $data->integration + $data->robustness + $data->menutree + $data->performance ;

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since    3.0
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();

		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($name, $alias) = $this->generateNewTitle( $categoryId = 0, $data['alias'], $data['name']);
			$data['name'] = $name;
			$data['alias'] = $alias;
			
			$data['published'] = 0;
		}

		$links = array('linka', 'linkb', 'linkc', 'linkd', 'linke');

		foreach ($links as $link)
		{
			if ($data['params'][$link])
			{
				$data['params'][$link] = JStringPunycode::urlToPunycode($data['params'][$link]);
			}
		}

		if (parent::save($data))
		{

			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$id = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && !empty($associations))
				{
					JError::raiseNotice(403, JText::_('COM_PROJECT_ERROR_ALL_LANGUAGE_ASSOCIATED'));
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where('context=' . $db->quote('com_project.item'))
					->where('id IN (' . implode(',', $associations) . ')');
				$db->setQuery($query);
				$db->execute();

				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);
					return false;
				}

				if (!$all_language && count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear()
						->insert('#__associations');

					foreach ($associations as $id)
					{
						$query->values($id . ',' . $db->quote('com_project.item') . ',' . $db->quote($key));
					}

					$db->setQuery($query);
					$db->execute();

					if ($error = $db->getErrorMsg())
					{
						$this->setError($error);
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  The JTable object
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

		$table->generateAlias();

		if (empty($table->id))
		{
			// Set the values
			$table->created = $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName('#__project'));
				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified = $date->toSql();
			$table->modified_by = $user->get('id');
		}
		// Increment the content version number.
		$table->version++;
	}

	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Association content items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_PROJECT_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_project');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to toggle the featured setting of projects.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_PROJECT_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();

			$query = $db->getQuery(true);
			$query->update('#__project');
			$query->set('featured = ' . (int) $value);
			$query->where('id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);

			$db->execute();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$table->reorder();

		// Clean component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $parent_id  The id of the parent.
	 * @param   string   $alias      The alias.
	 * @param   string   $title      The title.
	 *
	 * @return  array  Contains the modified title and alias.
	 *
	 * @since   3.1
	 */
	protected function generateNewTitle( $categoryId,$alias = "ssssss", $name)
	{
		// Alter the title & alias
		$table = $this->getTable();
		
		
		while ($table->load(array('alias' => $alias)))
		{
			if ($name == $table->name)
			{
				$name = JString::increment($name);
			}

			$alias = JString::increment($alias, 'dash');
			
		}

		return array($name, $alias);
	}
	
	public function getProject($id = null){
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		$query->select('*');
		
		$query->from ( '#__project AS a' );
		if($id != null){
			$query->where('a.id = ' . $id);
		}
		
		$db->setQuery ( $query );
		$objects = $db->loadObjectList();
		
		return  $objects;
	}
	
//	vananh

	public function gethandleTeam($teamid) {
		
		$db = JFactory::getDBO ();
		$query2 = $db->getQuery ( true );
		$query2->select ( 'name' );
		$query2->from ( '#__teams' );
		$query2->where ( 'id = ' . $teamid );
		$db->setQuery ( ( string ) $query2 );
		$teamname = $db->loadObject ()->name;
		
		return $teamname;
	}
	public function getprojectLeader($leaderID)
	{
		$db = JFactory::getDBO ();
		$query2 = $db->getQuery ( true );
		$query2->select ( 'name' );
		$query2->from ( '#__users' );
		$query2->where ( 'id = ' . $leaderID );
		$db->setQuery ( ( string ) $query2 );
		$teamname = $db->loadObject ()->name;
		
		return $teamname;
	}
	public function get1stbinary($projectid) {
		$db = JFactory::getDBO ();
		$query = $db->getQuery ( true );
		$query->select ( 'publish_up' );
		$query->from ( '#__binary' );
		$query->where ( 'project_id= ' . $projectid );
		$query->order ( 'publish_up' );
		$db->setQuery ( ( string ) $query );
		$publish_up = $db->loadObjectList ();
		// var_dump($publish_up);die();
		return $publish_up;
	}
	public function getIssueStatus($projectid) {
		$db = JFactory::getDBO ();
		$query = $db->getQuery ( true );
		$query->select ( 'publish_up' );
		
		$query->from ( '#__project' );
		$query->where ( 'project_id= ' . $projectid );
		$query->order ( 'publish_up' );
		$db->setQuery ( ( string ) $query );
		$publish_up = $db->loadObjectList ();
		// var_dump($publish_up);die();
		return $publish_up;
	}
	public function getBinaryListByProjectId($projectId) {
		$db = JFactory::getDBO ();
		$query = $db->getQuery ( true );
		$query->select ( 'id' );
		
		$query->from ( '#__binary' );
		$query->where ( 'project_id= ' . $projectId );
		$db->setQuery ( ( string ) $query );
		$binaryLst = $db->loadObjectList ();
		
		
		$issuestatus = array (
				0,
				0,
				0,
				0
		);
		for($i = 0; $i < count ( $binaryLst ); $i ++) {
			
			$queryIssueStt = $db->getQuery ( true );
			$queryIssueStt->select ( 'type' );
			$queryIssueStt->from ( '#__defect' );
			$queryIssueStt->where ( 'binary_id = ' . $binaryLst [$i]->id );
			$db->setQuery ( ( string ) $queryIssueStt );
			
			$type = $db->loadObjectList ();
			
			if ($type != NULL) {
				for($j = 0; $j < count ( $type ); $j ++) {
					
					if ($type [$j]->type == 0) {
						$issuestatus [0] ++;
					} elseif ($type [$j]->type == 1) {
						
						$issuestatus [1] ++;
					} elseif ($type [$j]->type == 2) {
						$issuestatus [2] ++;
					} elseif ($type [$j]->type == 3) {
						$issuestatus [3] ++;
					}
				}
			}
		}
		//echo '<pre>'; var_dump($issuestatus);die(); echo '</pre>';
		return $issuestatus;
		
	}
	
	public function getItemByIds($pks) {
	
		
		// Sanitize the ids.
		$pks = ( array ) $pks;
		JArrayHelper::toInteger ( $pks );
	
		if (empty ( $pks )) {
			$this->setError ( JText::_ ( 'COM_DEFECT_NO_ITEM_SELECTED' ) );
			return false;
		}
	
		$table = $this->getTable ();
		$projectlst = null;
		try {
			$db = $this->getDbo ();
			$db = JFactory::getDbo ();
			$query = $db->getQuery ( true )->select ( '*' )->from ( $db->quoteName ( '#__project' ) );
			$query->where ( 'id IN (' . implode ( ',', $pks ) . ')' );
			$db->setQuery ( $query );
			$projectlst = $db->loadObjectList ();
	
			foreach ($projectlst as $eachproject)
			{
				$eachproject->handlleteam= $this->gethandleTeam($eachproject->team_id);
				$eachproject->projectleader= $this->getprojectLeader($eachproject->project_leader_id);
				 $binarylst = $this->get1stbinary($eachproject->id);
				 if(count($binarylst)>0)
				 {
				 	$eachproject->firstbinary = $binarylst[0]->publish_up;
				 	
				 }
				 $eachproject->noofbinary = count($binarylst);
				
				 $eachproject->Issuestatus = $this->getBinaryListByProjectId($eachproject->id);
				// var_dump( $eachproject->Issuestatus);die();
			}
		}
		
		 catch ( Exception $e ) {
			JError::raiseWarning ( 500, $e->getMessage () );
		}
	
		return $projectlst;
	}
}
