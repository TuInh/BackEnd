<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_binary
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

<?php 
    JHtml::script(Juri::base() . 'components/com_binary/asserts/js/custom.js');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'binary.cancel' || document.formvalidator.isValid(document.id('binary-form')))
		{
			<?php echo $this->form->getField('misc')->save(); ?>
			Joomla.submitform(task, document.getElementById('binary-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_binary&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="binary-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('COM_BINARY_NEW_BINARY', true) : JText::_('COM_BINARY_EDIT_BINARY', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span9">
                        <?php echo $this->form->renderField('project_id'); ?>	
                        <?php echo $this->form->renderField('target'); ?>	  	
                        <?php echo $this->form->renderField('publish_up'); ?>	
                        <?php echo $this->form->renderField('publish_down'); ?>	  
                        <?php echo $this->form->renderField('memberlist'); ?>	                  
					</div>
					
				</div>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'misc', JText::_('JGLOBAL_FIELDSET_MISCELLANEOUS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
				<div class="form-vertical">
					<?php echo $this->form->renderField('misc'); ?>
				</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.informationdata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php if ($assoc) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
			<?php echo $this->loadTemplate('associations'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	
	
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="delete_confirm_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">		
		<h3 id="myModalLabel">Delete Product</h3>
	</div>
	
	<div class="modal-body">
		<p>Are you sure to delete this product?</p>
	</div>
	<div class="modal-footer">
		<input type="hidden" id="product_id" value="" />
		<button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
		<button onclick="deleteMember(this)" data-dismiss="modal" class="btn btn-primary delete">Yes</button>
	</div>
</div>