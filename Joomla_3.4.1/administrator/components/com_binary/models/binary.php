<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_binary
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('BinaryHelper', JPATH_ADMINISTRATOR . '/components/com_binary/helpers/binary.php');

/**
 * Item Model for a Binary.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_binary
 * @since       1.6
 */
class BinaryModelBinary extends JModelAdmin
{
	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_binary.binary';

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
			return $user->authorise('core.delete', 'com_binary');
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
			return $user->authorise('core.edit.state', 'com_binary');
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
	public function getTable($type = 'Binary', $prefix = 'BinaryTable', $config = array())
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
		$form = $this->loadForm('com_binary.binary', 'binary', array('control' => 'jform', 'load_data' => $loadData));
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

		// Load associated binary items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();
		
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
		$data = JFactory::getApplication()->getUserState('com_binary.edit.binary.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}
		$this->preprocessData('com_binary.binary', $data);

		if($data->id){
			$data->memberlist = $this->loadDetails($data->id);
		}
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
					JError::raiseNotice(403, JText::_('COM_BINARY_ERROR_ALL_LANGUAGE_ASSOCIATED'));
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where('context=' . $db->quote('com_binary.item'))
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
						$query->values($id . ',' . $db->quote('com_binary.item') . ',' . $db->quote($key));
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

// 		$table->generateAlias();
				
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
					->from($db->quoteName('#__binary'));
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
			$fieldset->addAttribute('description', 'COM_BINARY_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_binary');
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
	 * Method to toggle the featured setting of binaries.
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
			$this->setError(JText::_('COM_BINARY_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();

			$query = $db->getQuery(true);
			$query->update('#__binary');
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
	protected function generateNewTitle( $categoryId,$alias, $name)
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

	public function saveMember($memberId , $binaryId){
		$db = $this->getDbo();
		$columns = array('user_id', 'binary_id');
		$query = $db->getQuery(true)
		->insert($db->quoteName('#__user_binary_ref'))
		->columns($db->quoteName($columns))
		->values($memberId . ', '. $binaryId);
		$db->setQuery($query);
		$db->execute();
	}
	
	public function deleteMember($memberId , $binaryId){
	
			$db = $this->getDbo();
			$query = $db->getQuery(true)
			->delete($db->quoteName('#__user_binary_ref'))
			->where('binary_id = ' . $binaryId . ' AND user_id = '. $memberId );
			$db->setQuery($query);
			$db->execute();
	}
	
	
	public function saveDetails($details = null , $binaryId) {
	
		if($details) {
			$i = 0;
			foreach($details->id as $index) {
				if($details->task[$i] == 'delete'){
					$this->deleteMember($details->user_id[$i],$binaryId);
				} else if($details->task[$i] == 'create') {
					$this->saveMember($details->user_id[$i],$binaryId);
				}
				
				$i++;
			}
		}
	}
	
	public function loadDetails($binary_id = null) {
		$details = new stdClass();
		$details->id = array();
		$details->binary_id = $binary_id;
		$details->user_id = array();
		$details->name = array();
		$details->username = array();

		
		$userBinarys = $this->loadUserBinary($binary_id);
		
		if($userBinarys != null){
			foreach ($userBinarys as $userBinaryDetail) {
				$details->id[] = $userBinaryDetail->id;
				$details->user_id[] = $userBinaryDetail->user_id;
				
				$user = JFactory::getUser($userBinaryDetail->user_id);
				
				$details->name[] = $user->name;
				$details->username[] = $user->username;
			}
		}
		
		return $details;
	}
	public function loadUserBinary($binaryId = null , $userId = null){
	
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( '*' );
		$query->from ( '#__user_binary_ref' );
	
		if($binaryId != null){
			$query->where ( 'binary_id = '.  $binaryId );
		}
	
		if($userId != null){
			$query->where ( 'user_id = '.  $userId );
		}
	
		$db->setQuery ( $query );
	
		$objects = $db->loadObjectList();
			
		return  $objects;
	}
	
	// get project id from binary id
	public function getBinaryListByProjectId($binaryIds = null){
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
	
		$query->select('*');
	
		$query->from ( '#__binary AS a' );
		if($binaryIds != null){
			$query->where('a.project_id IN (SELECT DISTINCT project_id FROM ' . $db->quoteName('#__binary') . ' AS b WHERE b.id IN (' . implode(',', array_values($binaryIds)) . '))');
		}
		
		$db->setQuery ( $query );
		$objects = $db->loadObjectList();
		return  $objects;
	}
	
	// load data to gerate calendar
	public function generateCalendarEvent(){
		$binaryList = $this->getBinaryListByProjectId();
				
		$event = '[';
		
		foreach ($binaryList as $binary){
			$event.='{
		              id: '.$binary->id.',
		              title: "'.$binary->name.'",
		              start: "'.$binary->publish_up.'",
		              end: "'.$binary->publish_down.'",
		              allDay: true
		            },';
		}
		
		return $event.']';
	}
	
	public function checkDuplicateBinary($binaryName, $projectId){
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		$query->select('*');
		
		$query->from ( '#__binary AS a' );
		$query->where('a.project_id = "'.$projectId. '" AND a.name = "'.$binaryName.'"');

		$db->setQuery ( $query );
		$objects = $db->loadObjectList();
		
		var_dump(count($objects)); die();
		if(count($objects) > 0 ){
			return true;
		}
		return false;
	}
	
	private function countDefectsInBinary($binaryId)
	{
	// check is leader
		require_once JPATH_ADMINISTRATOR . '/components/com_defect/models/defect.php';
		$defectModel = JModelLegacy::getInstance('defect', 'DefectModel');
		// get defect by binaryId
		$defectList = $defectModel->getDefect(null,null,$binaryId);
	
		try
		{
			$count = count($defectList);
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
	
			return false;
		}

		return $count;
	}
	/**
	 * Method to delete groups.
	 *
	 * @param   array  $itemIds  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function delete(&$itemIds)
	{			
		$dispatcher = JEventDispatcher::getInstance();
	
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
	
		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId)
		{
			
				if ($this->countDefectsInBinary($itemId) > 0)
				{
					$this->setError(JText::_('COM_BINARY_DELETE_NOT_ALLOWED_EXISTING_DEFECT'));
	
					return false;
				}
				
		}
		parent::delete($itemIds);
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}
}
