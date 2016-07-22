<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined ( 'JPATH_PLATFORM' ) or die ();

/**
 * Field to select a user ID from a modal list.
 *
 * @package Joomla.Libraries
 * @subpackage Form
 * @since 1.6
 */
class JFormFieldProject extends JFormField {
	/**
	 * The form field type.
	 *
	 * @var string
	 * @since 1.6
	 */
	public $type = 'Project';
	
	/**
	 * Method to get the Project field input markup.
	 *
	 * @return string The field input markup.
	 *        
	 * @since 1.6
	 */
	/**
	 * Method to get the field input markup
	 */
	protected function getInput() {
		// Load modal behavior
		JHtml::_ ( 'behavior.modal', 'a.modal' );
		
		// Build the script
		$script = array ();
		$script [] = '    function jSelectProject_' . $this->id . '(id, title, object) {';
		$script [] = '        document.id("' . $this->id . '_id").value = id;';
		$script [] = '        document.id("' . $this->id . '_name").value = title;';
		$script [] = '        SqueezeBox.close();';
		$script [] = '    }';
		
		// Add to document head
		JFactory::getDocument ()->addScriptDeclaration ( implode ( "\n", $script ) );
		
		// Setup variables for display
		$html = array ();
		$link = 'index.php?option=com_project&amp;view=projects&amp;layout=modal' . '&amp;tmpl=component&amp;function=jSelectProject_' . $this->id;
		

		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( '*' );
		$query->from ( '#__project' );
		$query->where ( 'id=' . ( int ) $this->value );
		$db->setQuery ( $query );
		
		$object = $db->loadObject();
		
		
		if($object)
		{
			$title = $object->name;
		}
		
		if (empty ( $title )) {
			$title = JText::_ ( 'COM_BINARY_FORM_SELECT_PROJECT' );
		}
		$title = htmlspecialchars ( $title, ENT_QUOTES, 'UTF-8' );
		
		// Create a dummy text field with the user name.
		$html [] = '<div class="input-append">';
		$html [] = '	<input type="text" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
		
		// Create the user select button.
		if ($this->readonly === false)
		{
			$html [] = '		<a class="btn btn-primary modal" title="' . JText::_ ( 'JLIB_FORM_CHANGE_PROJECT' ) . '" href="' . $link . '"' . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html [] = '<i class="icon-user"></i></a>';
		}
		$html [] = '</div>';
		
		// The active book id field
		if (0 == ( int ) $this->value) {
			$value = '';
		} else {
			$value = ( int ) $this->value;
		}
		
		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}
		
		$html [] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';
		
		return implode ( "\n", $html );
	}
	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return mixed array of filtering groups or null.
	 *        
	 * @since 1.6
	 */
	protected function getGroups() {
		return null;
	}
	
	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return mixed Array of users to exclude or null to to not exclude them
	 *        
	 * @since 1.6
	 */
	protected function getExcluded() {
		return null;
	}
}
