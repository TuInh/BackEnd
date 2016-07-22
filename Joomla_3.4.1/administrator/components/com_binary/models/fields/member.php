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
class JFormFieldMember extends JFormField {
	/**
	 * The form field type.
	 *
	 * @var string
	 * @since 1.6
	 */
	public $type = 'Member';
	
        
	/**
	 * Method to get the Member field input markup.
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
		JHtml::script(Juri::base() . 'components/com_binary/asserts/js/custom.js');
		// Build the script
 		$script = array ();
 		$script [] = 'var formName = \''.$this->name.'\';';		
 		JFactory::getDocument()->addScriptDeclaration ( implode ( "\n", $script ) );
                
		// Setup variables for display
		$order_requester = 0;
 		
		$html = array ();
		$link = 'index.php?option=com_users&amp;view=users&amp;layout=memberModal&amp;tmpl=component&amp;function=jSelectUser_' . $this->id;
		if (empty ( $title )) {
			$title = JText::_ ( 'JLIB_FORM_SELECT_MEMBER' );
		}
		$title = htmlspecialchars ( $title, ENT_QUOTES, 'UTF-8' );
		
		// Create a dummy text field with the user name.
		$html [] = '<div class="input-append">';
		// Create the user select button.
		$html [] = '<a class="btn btn-primary modal btn-addMember" title="' . JText::_ ( 'JLIB_FORM_CHANGE_MEMBER' ) . '" href="' . $link . '"' . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html [] = '<i class="icon-plus"></i></a>';
		$html [] = '</div>';
		// class='required' for client side validation
		$class = array("table", "table-condensed", "table-bordered", "table-hover", "table-member-list");
		if ($this->required) {
			$class[] ='required modal-value';
		}
                
                $html [] = '<table id = "table-member-list" class="'.implode(" ", $class).'" cellpadding="0" cellspacing="0">';
                $html [] = '	<thead>';
                $html [] = '    	<tr>';
                $html [] = '			<th style="width: 250px">Member ID</th>';
				$html [] = '    		<th style="width: 250px">Name</th>';
				$html [] = '			<th style="width: 250px">UserName</th>';
				$html [] = '			<th style="width: 40px">Action</th>';
				$html [] = '		</tr>';
				$html [] = '	</thead>';
				$html [] = '    <tbody>';
				
// 				echo "<pre>" ; var_dump($this->value); echo "</pre>" ; die();	
							
                if($this->value && $this->value->id && count($this->value)>0) {
                    
                	$i = 0;
                    foreach( $this->value->user_id as $index ){
                    	
                    	$user = JFactory::getUser($index);
                    	
                    	$script = array ();
                    	$script [] = 'memberArray.push(""+'.$index.');';
                    	JFactory::getDocument ()->addScriptDeclaration ( implode ( "\n", $script ) );
                    	
                        $html [] = '<tr data-member-id='.$index.'>';
                        $html [] = '<input type="hidden" name="'.$this->name.'[id][]" value="'.$this->value->id[$i].'" />';
                        $html [] = '<input class="ajax-detail-id" type="hidden" name="'.$this->name.'[task][]" value="update" />';
                        $html [] = '<td>'.$index.'<input type="hidden" name="'.$this->name.'[user_id][]" value="'.$index.'"  ></td>';
                        $html [] = '<td>'.$user->name.'</td>';
                        $html [] = '<td>'.$user->username.'</td>';
                        $html [] = '<td><a onclick="deleteConfirmation(this)" href="#delete_confirm_modal" data-toggle="modal" id="member-id-'.$index.'" class="btn btn-danger"> <i class="icon-trash"></i></a></td>';
                        $html [] = '</tr>';
                        
                        $i++;
                    }
                
                }
                
		$html [] = '	</tbody>';
		$html [] = '</table>';
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
