<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die ();

JLoader::register ( 'DefectHelper', JPATH_ADMINISTRATOR . '/components/com_defect/helpers/defect.php' );

/**
 * Item Model for a Defect.
 *
 * @package Joomla.Administrator
 * @subpackage com_defect
 * @since 1.6
 */
class DefectModelDefect extends JModelAdmin {
	/**
	 * The type alias for this content type.
	 *
	 * @var string
	 * @since 3.2
	 */
	public $typeAlias = 'com_defect.defect';
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param object $record
	 *        	A record object.
	 *        	
	 * @return boolean True if allowed to delete the record. Defaults to the permission set in the component.
	 *        
	 * @since 1.6
	 */
	protected function canDelete($record) {
		if (! empty ( $record->id )) {
			if ($record->published != - 2) {
				return;
			}
			$user = JFactory::getUser ();
			return $user->authorise ( 'core.delete', 'com_defect' );
		}
	}
	
	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param object $record
	 *        	A record object.
	 *        	
	 * @return boolean True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *        
	 * @since 1.6
	 */
	protected function canEditState($record) {
		$user = JFactory::getUser ();
		
		// Check against the category.
		if (! empty ( $record->catid )) {
			return $user->authorise ( 'core.edit.state', 'com_defect' );
		} 		// Default to component settings if category not known.
		else {
			return parent::canEditState ( $record );
		}
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param type $type
	 *        	The table type to instantiate
	 * @param string $prefix
	 *        	A prefix for the table class name. Optional.
	 * @param array $config
	 *        	Configuration array for model. Optional.
	 *        	
	 * @return JTable A database object
	 *        
	 * @since 1.6
	 */
	public function getTable($type = 'Defect', $prefix = 'DefectTable', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}
	
	/**
	 * Method to get the row form.
	 *
	 * @param array $data
	 *        	Data for the form.
	 * @param boolean $loadData
	 *        	True if the form is to load its own data (default case), false if not.
	 *        	
	 * @return mixed A JForm object on success, false on failure
	 *        
	 * @since 1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		JForm::addFieldPath ( 'JPATH_ADMINISTRATOR/components/com_users/models/fields' );
		
		// Get the form.
		$form = $this->loadForm ( 'com_defect.defect', 'defect', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		if (empty ( $form )) {
			return false;
		}
		
		// Modify the form based on access controls.
		if (! $this->canEditState ( ( object ) $data )) {
			// Disable fields for display.
			$form->setFieldAttribute ( 'featured', 'disabled', 'true' );
			$form->setFieldAttribute ( 'ordering', 'disabled', 'true' );
			$form->setFieldAttribute ( 'published', 'disabled', 'true' );
			
			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute ( 'featured', 'filter', 'unset' );
			$form->setFieldAttribute ( 'ordering', 'filter', 'unset' );
			$form->setFieldAttribute ( 'published', 'filter', 'unset' );
		}
		$user = JFactory::getUser();
		
		if ($user->authorise('project.view.all_project', 'com_defect') || $user->authorise('project.view.all_team', 'com_defect') || $user->authorise('project.view.all', 'com_defect'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('user_id', 'readonly', 'false');
		}
		
		return $form;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param integer $pk
	 *        	The id of the primary key.
	 *        	
	 * @return mixed Object on success, false on failure.
	 *        
	 * @since 1.6
	 */
	public function getItem($pk = null) {
		if ($item = parent::getItem ( $pk )) {
			// Convert the metadata field to an array.
			$registry = new JRegistry ();
		}
		
		// Load associated defect items
		$app = JFactory::getApplication ();
		$assoc = JLanguageAssociations::isEnabled ();
		
		if ($assoc) {
			$item->associations = array ();
			
			if ($item->id != null) {
				$associations = JLanguageAssociations::getAssociations ( 'com_defect', '#__defect', 'com_defect.item', $item->id );
				
				foreach ( $associations as $tag => $association ) {
					$item->associations [$tag] = $association->id;
				}
			}
		}
		
		return $item;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed The data for the form.
	 *        
	 * @since 1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication ()->getUserState ( 'com_defect.edit.defect.data', array () );
		
		if (empty ( $data )) {
			$data = $this->getItem ();
		}
		
		$this->preprocessData ( 'com_defect.defect', $data );
		
		return $data;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param
	 *        	array The form data.
	 *        	
	 * @return boolean True on success.
	 *        
	 * @since 3.0
	 */
	public function save($data) {
		$app = JFactory::getApplication ();
		
		// Alter the title for save as copy
		if ($app->input->get ( 'task' ) == 'save2copy') {
			list ( $name, $alias ) = $this->generateNewTitle ( $categoryId = 0, $data ['alias'], $data ['name'] );
			$data ['name'] = $name;
			$data ['alias'] = $alias;
			$data ['published'] = 0;
		}
		
		$links = array (
				'linka',
				'linkb',
				'linkc',
				'linkd',
				'linke' 
		);
		
		foreach ( $links as $link ) {
			if ($data ['params'] [$link]) {
				$data ['params'] [$link] = JStringPunycode::urlToPunycode ( $data ['params'] [$link] );
			}
		}
		
		if (parent::save ( $data )) {
			
			$assoc = JLanguageAssociations::isEnabled ();
			if ($assoc) {
				$id = ( int ) $this->getState ( $this->getName () . '.id' );
				$item = $this->getItem ( $id );
				
				// Adding self to the association
				$associations = $data ['associations'];
				
				foreach ( $associations as $tag => $id ) {
					if (empty ( $id )) {
						unset ( $associations [$tag] );
					}
				}
				
				// Detecting all item menus
				$all_language = $item->language == '*';
				
				if ($all_language && ! empty ( $associations )) {
					JError::raiseNotice ( 403, JText::_ ( 'COM_DEFECT_ERROR_ALL_LANGUAGE_ASSOCIATED' ) );
				}
				
				$associations [$item->language] = $item->id;
				
				// Deleting old association for these items
				$db = JFactory::getDbo ();
				$query = $db->getQuery ( true )->delete ( '#__associations' )->where ( 'context=' . $db->quote ( 'com_defect.item' ) )->where ( 'id IN (' . implode ( ',', $associations ) . ')' );
				$db->setQuery ( $query );
				$db->execute ();
				
				if ($error = $db->getErrorMsg ()) {
					$this->setError ( $error );
					return false;
				}
				
				if (! $all_language && count ( $associations )) {
					// Adding new association for these items
					$key = md5 ( json_encode ( $associations ) );
					$query->clear ()->insert ( '#__associations' );
					
					foreach ( $associations as $id ) {
						$query->values ( $id . ',' . $db->quote ( 'com_defect.item' ) . ',' . $db->quote ( $key ) );
					}
					
					$db->setQuery ( $query );
					$db->execute ();
					
					if ($error = $db->getErrorMsg ()) {
						$this->setError ( $error );
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
	 * @param JTable $table
	 *        	The JTable object
	 *        	
	 * @return void
	 *
	 * @since 1.6
	 */
	protected function prepareTable($table) {
		$date = JFactory::getDate ();
		$user = JFactory::getUser ();
		
		$table->name = htmlspecialchars_decode ( $table->name, ENT_QUOTES );
		
		$table->generateAlias ();
		
		if (empty ( $table->id )) {
			// Set the values
			$table->created = $date->toSql ();
			
			// Set ordering to the last item if not set
			if (empty ( $table->ordering )) {
				$db = JFactory::getDbo ();
				$query = $db->getQuery ( true )->select ( 'MAX(ordering)' )->from ( $db->quoteName ( '#__defect' ) );
				$db->setQuery ( $query );
				$max = $db->loadResult ();
				
				$table->ordering = $max + 1;
			}
		} else {
			// Set the values
			$table->modified = $date->toSql ();
			$table->modified_by = $user->get ( 'id' );
		}
		
		// Increment the content version number.
		$table->version ++;
	}
	protected function preprocessForm(JForm $form, $data, $group = 'content') {
		// Association content items
		$app = JFactory::getApplication ();
		$assoc = JLanguageAssociations::isEnabled ();
		if ($assoc) {
			$languages = JLanguageHelper::getLanguages ( 'lang_code' );
			$addform = new SimpleXMLElement ( '<form />' );
			$fields = $addform->addChild ( 'fields' );
			$fields->addAttribute ( 'name', 'associations' );
			$fieldset = $fields->addChild ( 'fieldset' );
			$fieldset->addAttribute ( 'name', 'item_associations' );
			$fieldset->addAttribute ( 'description', 'COM_DEFECT_ITEM_ASSOCIATIONS_FIELDSET_DESC' );
			$add = false;
			foreach ( $languages as $tag => $language ) {
				if (empty ( $data->language ) || $tag != $data->language) {
					$add = true;
					$field = $fieldset->addChild ( 'field' );
					$field->addAttribute ( 'name', $tag );
					$field->addAttribute ( 'type', 'modal_defect' );
					$field->addAttribute ( 'language', $tag );
					$field->addAttribute ( 'label', $language->title );
					$field->addAttribute ( 'translate_label', 'false' );
					$field->addAttribute ( 'edit', 'true' );
					$field->addAttribute ( 'clear', 'true' );
				}
			}
			if ($add) {
				$form->load ( $addform, false );
			}
		}
		
		parent::preprocessForm ( $form, $data, $group );
	}
	
	/**
	 * Method to toggle the featured setting of defects.
	 *
	 * @param array $pks
	 *        	The ids of the items to toggle.
	 * @param integer $value
	 *        	The value to toggle to.
	 *        	
	 * @return boolean True on success.
	 *        
	 * @since 1.6
	 */
	public function featured($pks, $value = 0) {
		// Sanitize the ids.
		$pks = ( array ) $pks;
		JArrayHelper::toInteger ( $pks );
		
		if (empty ( $pks )) {
			$this->setError ( JText::_ ( 'COM_DEFECT_NO_ITEM_SELECTED' ) );
			return false;
		}
		
		$table = $this->getTable ();
		
		try {
			$db = $this->getDbo ();
			
			$query = $db->getQuery ( true );
			$query->update ( '#__defect' );
			$query->set ( 'featured = ' . ( int ) $value );
			$query->where ( 'id IN (' . implode ( ',', $pks ) . ')' );
			$db->setQuery ( $query );
			
			$db->execute ();
		} catch ( Exception $e ) {
			$this->setError ( $e->getMessage () );
			return false;
		}
		
		$table->reorder ();
		
		// Clean component's cache
		$this->cleanCache ();
		
		return true;
	}
	
	/**
	 * Method to get defects by ids.
	 *
	 * @param array $pks
	 *        	The ids of the items to toggle.
	 * @param integer $value
	 *        	The value to toggle to.
	 *        	
	 * @return boolean True on success.
	 *        
	 * @since 1.6
	 */
	public function getItemByIds($pks) {
		// Sanitize the ids.
		$pks = ( array ) $pks;
		JArrayHelper::toInteger ( $pks );
		
		if (empty ( $pks )) {
			$this->setError ( JText::_ ( 'COM_DEFECT_NO_ITEM_SELECTED' ) );
			return false;
		}
		
		$table = $this->getTable ();
		$defectList = null;
		try {
			$db = $this->getDbo ();
			$db = JFactory::getDbo ();
			$query = $db->getQuery ( true )->select ( '*' )->from ( $db->quoteName ( '#__defect' ) );
			$query->where ( 'id IN (' . implode ( ',', $pks ) . ')' );
			$db->setQuery ( $query );
			
			$defectList = $db->loadObjectList ();
		} catch ( Exception $e ) {
			JError::raiseWarning ( 500, $e->getMessage () );
		}
		
		return $defectList;
	}
	
	/**
	 * Method to change the title & alias.
	 *
	 * @param integer $parent_id
	 *        	The id of the parent.
	 * @param string $alias
	 *        	The alias.
	 * @param string $title
	 *        	The title.
	 *        	
	 * @return array Contains the modified title and alias.
	 *        
	 * @since 3.1
	 */
	protected function generateNewTitle($categoryId, $alias, $name) {
		// Alter the title & alias
		$table = $this->getTable ();
		while ( $table->load ( array (
				'alias' => $alias 
		) ) ) {
			if ($name == $table->name) {
				$name = JString::increment ( $name );
			}
			
			$alias = JString::increment ( $alias, 'dash' );
		}
		
		return array (
				$name,
				$alias 
		);
	}
	
	// check is same project
	public function isSameProject($user = null, $defect = null){
		
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
	
		$query->select('project_id');
	
		$query->from ( '#__binary AS a' );
		if($defect != null){
			$query->where('a.id = ' . $defect->binary_id);
		}
	
		$db->setQuery ( $query );
		$objects = $db->loadObject();
			
		// check is leader
		require_once JPATH_ADMINISTRATOR . '/components/com_project/models/project.php';
		$projectModel = JModelLegacy::getInstance('project', 'ProjectModel');
		if($projectModel->getProject($objects->project_id)[0]->project_leader_id == $user->id){
			return true;
		}
		// check same project
		$projects = $this->getAllProjectByUser($user->id);
		if($projects != null){
			foreach ($projects as $project) {
				if($project->project_id == $objects->project_id) {
					return true;
				}
			}
		}
		
		return  false;
	}
	
	
	// check is same project
	public function getAllProjectByUser($userId = null){
		require_once JPATH_ADMINISTRATOR . '/components/com_binary/models/binary.php';
		$binaryModel = JModelLegacy::getInstance('binary', 'BinaryModel');
		
		$userBinarys = $binaryModel->loadUserBinary(null, $userId);
		
		$binaryArray = array();
		
		foreach ($userBinarys as $userBinary) {
			$binaryArray[] = $userBinary->binary_id;
		}
		
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
	
		$query->select('DISTINCT project_id ');
	
		$query->from ( '#__binary AS a' );
		if($binaryArray != null){
			$query->where('a.id IN (' . implode(',', array_values($binaryArray)) . ')');
		}
		
		$db->setQuery ( $query );
		$objects = $db->loadObjectList();
		return  $objects;
	}
	
	/**
	 * Method to get defects.
	 */
	public function getDefect($start = null , $end = null, $binaryId = null) {
	
		$defectList = null;
		try {
			$db = $this->getDbo ();
			$db = JFactory::getDbo ();
			$query = $db->getQuery ( true )->select ( '*' )->from ( $db->quoteName ( '#__defect' ) );
			$db->setQuery ($query);
			if($start!=null && $end != null){
				$query->where('created >= "' . $start . '" AND created < "'. $end.'"');
			}
			if($binaryId != null){
				$query->where('binary_id = "' . $binaryId . '"');
			}
			
			$defectList = $db->loadObjectList ();
		} catch ( Exception $e ) {
			JError::raiseWarning ( 500, $e->getMessage () );
		}
	
		return $defectList;
	}
	
	
}
