<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_team
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$assoc = JLanguageAssociations::isEnabled();
?>

<form action="<?php echo JRoute::_('index.php?option=com_team&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">

	

 <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_DETAILS'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
                     
            
                    <?php foreach ($this->form->getFieldset() as $field): ?>
                        <div class="control-group">
                         <div class="control-label" ><?php echo $field->label; ?></div>
                          <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="team.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>
