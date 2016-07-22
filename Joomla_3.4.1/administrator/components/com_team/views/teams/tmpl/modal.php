<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');

// Load tooltip behavior
JHtml::_('behavior.tooltip');
// var_dump($this->state);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$function = JFactory::getApplication()->input->getCmd('function', 'jSelectTeam');
?>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
<table class="table table-striped">
    <thead>
        <tr>
            <th width="10%">
                <?php echo JHtml::_('grid.sort', ' Team ID', 'id'); ?>
            </th>
            <th width="30%">
                <?php echo JHtml::_('grid.sort', 'Team Name', 'name'); ?>
            </th>
             <th>
                <?php echo JHtml::_('grid.sort', 'Description', 'description'); ?>
            </th>
           
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
    </tfoot>
    <tbody>
<?php foreach ($this->items as $i => $item) : ?>
        <tr class="row<?php echo $i % 2; ?>">
        	<td><?php echo $item->id; ?></td>
            <td>
                <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');"><?php echo $this->escape($item->name); ?></a>
            </td>
			<td><?php echo $item->description; ?></td>
            
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</div>
</form>