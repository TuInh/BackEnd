<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$trashed   = $this->state->get('filter.published') == -2 ? true : false;
$canOrder  = $user->authorise('core.edit.state', 'com_defect.category');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_defect&task=defects.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
$assoc		= JLanguageAssociations::isEnabled();
?>

<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	function inputFile(value){
		document.getElementById('upload-file-info').innerHTML = value ;
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_defect'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			
			
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_DEFECT_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_DEFECT_SEARCH_IN_NAME'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value = '';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>
		<table class="table table-striped" id="articleList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_DEFECT_HEADING_CASECODE', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_DEFECT_FIELD_LINKED_BINARY_LABEL', 'linked_binary', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_DEFECT_FIELD_LINKED_PROJECT_LABEL', 'linked_project', $listDirn, $listOrder); ?>
                    </th>
                     <th class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_DEFECT_TYPE_LABEL', 'a.type', $listDirn, $listOrder); ?>
                    </th>
                     <th class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_DEFECT_RESOLUTION_LABEL', 'a.resolution_status', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_DEFECT_FIELD_LINKED_USER_LABEL', 'linked_user', $listDirn, $listOrder); ?>
                    </th>
					<?php if ($assoc) : ?>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_DEFECT_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
					</th>
					<?php endif;?>
					
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$n = count($this->items);
			foreach ($this->items as $i => $item) :
				$ordering   = $listOrder == 'a.ordering';
				$canCreate  = $user->authorise('core.create',     'com_defect');
				$canEdit    = $user->authorise('core.edit',       'com_defect');
				$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == 0;
				$canEditOwn = $user->authorise('core.edit.own',   'com_defect') && $item->created_by == $userId;
				$canChange  = $user->authorise('core.edit.state', 'com_defect') && $canCheckin;

				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';
						if (!$canChange)
						{
							$iconClass = ' inactive';
						}
						elseif (!$saveOrder)
						{
							$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
						}
						?>
						<span class="sortable-handler<?php echo $iconClass ?>">
							<i class="icon-menu"></i>
						</span>
						<?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5"
								value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
						<?php endif; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'defects.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
							<?php
							$action = $trashed ? 'untrash' : 'trash';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'defects');

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
							?>
						</div>
					</td>
					<td class="nowrap has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'defects.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_defect&task=defect.edit&id='.(int) $item->id); ?>">
								<?php echo $this->escape($item->alias); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->alias); ?>
							<?php endif; ?>
							<span class="small">
								<?php echo JText::_('JGLOBAL_TITLE') . ' : '. $this->escape($item->name);?>
							</span>

						</div>
					</td>
					 <td align="center" class="small hidden-phone">
                        <?php if (!empty($item->linked_binary)) : ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_binary&task=binary.edit&id='.$item->binary_id);?>"><?php echo $item->linked_binary;?></a>
                        <?php endif; ?>
                    </td>
                    
                     <td align="center" class="small hidden-phone">
                        <?php if (!empty($item->linked_project)) : ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_project&task=project.edit&id='.$item->project_id);?>"><?php echo $item->linked_project;?></a>
                        <?php endif; ?>
                    </td>
					
					<td align="center" class="small hidden-phone">
                        <?php 
								switch ($item->type) {
								    case 1:
								        echo JText::_('COM_DEFECT_TYPE_MANUAL_LABEL');
								        break;
								    case 0:
								        echo JText::_('COM_DEFECT_TYPE_PERFORMANCE_LABEL');
								        break;
								    case 2:
								        echo JText::_('COM_DEFECT_TYPE_API_LABEL');
								        break;
							        case 3:
							        	echo JText::_('COM_DEFECT_TYPE_CODE_OPTIMAZE_LABEL');
							        	break;
								    default:
								        echo "";
								}
                        ?>
                    </td>
                    
                    <td align="center" class="small hidden-phone">
                        <?php 
								switch ($item->resolution_status) {
								    case 1:
								        echo JText::_('COM_DEFECT_RESOLUTION_MODIFICATION_COMPLETED_LABEL');
								        break;
								    case 0:
								        echo JText::_('COM_DEFECT_RESOLUTION_MAINTAIN_LABEL');
								        break;
								    case 2:
								        echo JText::_('COM_DEFECT_RESOLUTION_SOURCE_MODIFICATION_LABEL');
								        break;
								    case 3:
								        echo JText::_('COM_DEFECT_RESOLUTION_CONCEPT_LABEL');
								        break;
								    case 4:
								        echo JText::_('COM_DEFECT_RESOLUTION_INSUFFICIENT_LABEL');
								        break;
							        case 5:
							        	echo JText::_('COM_DEFECT_RESOLUTION_IRREPRODUCE_LABEL');
							        	break;
						        	case 6:
						        		echo JText::_('COM_DEFECT_RESOLUTION_DUPLICATED_LABEL');
						        		break;
								    default:
								        echo "";
								}
                        ?>
                    </td>
					
                    <td align="center" class="small hidden-phone">
                        <?php if (!empty($item->linked_user)) : ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.$item->user_id);?>"><?php echo $item->linked_user;?></a>
                        <?php endif; ?>
                    </td>

					<td align="center" class="hidden-phone">
						<?php echo $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>


		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<form action="<?php echo JRoute::_('index.php?option=com_defect&task=defects.import'); ?>"
		  method="post" enctype="multipart/form-data">
		<div  id="fileModal" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php echo JText::_('COM_DEFECT_NEW_FILE_HEADER');?></h3>
			</div>
			<div class="modal-body">
				 <div style="position:relative;">
				<a class='btn btn-primary' href='#'>
					Choose File...
					<input type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="file_source" id="file_source" size="40"  onchange='inputFile(this.value)'>
				</a>
				<span class='label label-info' id="upload-file-info"></span>
			</div>
				
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_DEFECT_BUTTON_CLOSE'); ?></a>
				<button class="btn btn-primary" type="submit"><?php echo JText::_('COM_DEFECT_BUTTON_IMPORT'); ?></button>
			</div>
		</div>
		<?php echo JHtml::_('form.token'); ?>
</form>

