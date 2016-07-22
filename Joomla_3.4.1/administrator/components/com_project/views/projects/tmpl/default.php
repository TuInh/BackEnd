<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_project
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
$canOrder  = $user->authorise('core.edit.state', 'com_project.category');
$saveOrder = $listOrder == 'a.ordering';
$model = JModelLegacy::getInstance ( 'project', 'ProjectModel' );

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_project&task=projects.saveOrderAjax&tmpl=component';
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
</script>
<form action="<?php echo JRoute::_('index.php?option=com_project'); ?>" method="post" name="adminForm" id="adminForm">
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
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_PROJECT_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_PROJECT_SEARCH_IN_NAME'); ?>" />
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
						<th width="1%" style="min-width: 55px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_PROJECT_NO', 'a.id', $listDirn, $listOrder); ?>
					</th>
						<th width="1%" style="min-width: 55px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_PROJECT_HEADING_TITLE', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort', 'COM_PROJECT_HANDLE_TEAM', 'a.name', $listDirn, $listOrder); ?>
					</th>
                    <th class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_PROJECT_FIELD_LINKED_USER_LABEL', 'ul.name', $listDirn, $listOrder); ?>
                    </th>
						<th width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_PROJECT_NO_API', 'ul.noapis', $listDirn, $listOrder); ?>
                    </th>
						<th width="10%">
							<table style="width: 100%">
								<tr align="center">
									<td colspan="5" style="text-align: center">No of TCs</td>

								</tr>
								<tr>
									<td width="20%" class="title-center">Unit</td>
									<td width="20%" class="title-center">Inte</td>
									<td width="20%" class="title-center">MT</td>
									<td width="20%" class="title-center">Robustness</td>
									<td width="20%" class="title-center">Perfomance</td>
								</tr>

							</table>
						</th>




						<th width="5%">
						<?php echo JHtml::_('grid.sort', 'COM_PROJECT_1ST', 'a.id', $listDirn, $listOrder); ?>
					</th>
						<th width="5%">
                        <?php echo JText::_('COM_PROJECT_NO_BINARY'); ?>
			
			</th>
						<th width="10%">
							<table style="width: 100%">
								<tr align="center">
									<td colspan="5" style="text-align: center">Issue Status</td>

								</tr>
								<tr>
									<td width="20%" class="title-center">API</td>
									<td width="20%" class="title-center">Manual</td>
									<td width="20%" class="title-center">Performance</td>
									<td width="20%" class="title-center">CO</td>

								</tr>

							</table>
						</th>
					</tr>
				</thead>
				<tbody>
			<?php
			$n = count ( $this->items );
			$no = 0;
			foreach ( $this->items as $i => $item ) :
				$no ++;
				$ordering = $listOrder == 'a.ordering';
				$canCreate = $user->authorise ( 'core.create', 'com_project' );
				$canEdit = $user->authorise ( 'core.edit', 'com_project' );
				$canCheckin = $user->authorise ( 'core.manage', 'com_checkin' ) || $item->checked_out == 0;
				$canEditOwn = $user->authorise ( 'core.edit.own', 'com_project' ) && $item->created_by == $userId;
				$canChange = $user->authorise ( 'core.edit.state', 'com_project' ) && $canCheckin;
				
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
					</td>
						<td class="center hidden-phone">
						<?php echo $no; ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'projects.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
							<?php
							$action = $trashed ? 'untrash' : 'trash';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'projects');

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
							?>
						</div>
					</td>
					<td class="nowrap has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'projects.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_project&task=project.edit&id='.(int) $item->id); ?>">
								<?php echo $this->escape($item->name); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->name); ?>
							<?php endif; ?>
							
						</div>
						</td>
						<td width="5%" align="center">
						<?php
				
				$teamname = $model->gethandleTeam ( $item->team_id );
				echo $teamname;
				?>
					</td>

						<td align="center" width="5%" align="center">
                        <?php if (!empty($item->linked_user)) : ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.$item->project_leader_id);?>"><?php echo $item->linked_user;?></a>
                        <?php endif; ?>
                    </td>
						<td width="5%" align="center">
						<?php echo $item->noapis; ?>
					</td>
						<td>
							<table style="width: 100%" align="center">
								<tr width="20%" align="center">
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $item->unit; ?></a></td>
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $item->integration; ?></a></td>
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $item->robustness; ?></a></td>
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $item->menutree; ?></a></td>
									<td><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $item->performance; ?></a></td>
								</tr>
							</table>
						</td>
						<td align="center" width="5%">
						<?php $firstbinary = $model->get1stbinary ( $item->id ); if(count($firstbinary) > 0) {echo $firstbinary[0]->publish_up;} ?>
					</td>
						<td align="center" width="5%">
						<?php  echo count($firstbinary); ?>
					</td>
						<td width="5%">
					<?php $issuestatus = $model->getBinaryListByProjectId ( $item->id );?>
					<table style="width: 100%" align="center">
								<tr width="20%" align="center">
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issuestatus[0]; ?></a></td>
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issuestatus[1]; ?></a></td>
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issuestatus[2]; ?></a></td>
									<td align="center"><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issuestatus[3]; ?></a></td>

								</tr>
							</table>
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
