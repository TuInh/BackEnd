<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_team
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die ();

JHtml::addIncludePath ( JPATH_COMPONENT . '/helpers/html' );

JHtml::_ ( 'bootstrap.tooltip' );
JHtml::_ ( 'behavior.multiselect' );
JHtml::_ ( 'formbehavior.chosen', 'select' );

$app = JFactory::getApplication ();
$user = JFactory::getUser ();
$userId = $user->get ( 'id' );
$listOrder = $this->escape ( $this->state->get ( 'list.ordering' ) );
$listDirn = $this->escape ( $this->state->get ( 'list.direction' ) );
$trashed = $this->state->get ( 'filter.published' ) == - 2 ? true : false;
$canOrder = $user->authorise ( 'core.edit.state', 'com_team.category' );
$saveOrder = $listOrder == 'a.ordering';
$model = JModelLegacy::getInstance ( 'team', 'TeamModel' );
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_team&task=teams.saveOrderAjax&tmpl=component';
	JHtml::_ ( 'sortablelist.sortable', 'articleList', 'adminForm', strtolower ( $listDirn ), $saveOrderingUrl );
}

$sortFields = $this->getSortFields ();
$assoc = JLanguageAssociations::isEnabled ();
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
<form action="<?php echo JRoute::_('index.php?option=com_team'); ?>"
	method="post" name="adminForm" id="adminForm">
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
					<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_TEAM_FILTER_SEARCH_DESC');?></label>
					<input type="text" name="filter_search" id="filter_search"
						placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
						value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
						class="hasTooltip"
						title="<?php echo JHtml::tooltipText('COM_TEAM_SEARCH_IN_NAME'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip"
						title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip"
						title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>"
						onclick="document.getElementById('filter_search').value = '';this.form.submit();">
						<i class="icon-remove"></i>
					</button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<select name="directionTable" id="directionTable"
						class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<option value="asc"
							<?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
						<option value="desc"
							<?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
					<select name="sortTable" id="sortTable" class="input-medium"
						onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>

						<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
						<th class="left" style="vertical-align: bottom;" width="1%"
							class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'COM_TEAM_HEADING_NO', 'id', $listDirn, $listOrder); ?>
						</th>
						<th width="1%">
						<?php echo JHtml::_('grid.sort', 'COM_TEAM_HEADING_TEAM_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
						<th width="5%">
						<?php echo JHtml::_('grid.sort', 'COM_TEAM_HEADING_TEAM_NAME', 'a.name', $listDirn, $listOrder); ?>
						
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_TEAM_HEADING_TEAM_MEMBER', 'a.description', $listDirn, $listOrder); ?>
					</th>
						<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_TEAM_HEADING_TEAM_DESC', 'a.description', $listDirn, $listOrder); ?>
					</th>



					</tr>
				</thead>
				<tbody>
			<?php
			
			$n = count ( $this->items );
			$index =0;
			
			foreach ( $this->items as $i => $item ) :
			$index ++;
			$userlst= $model->getMembers($item->id);
				?>
				<tr class="row<?php echo $i % 2; ?>"
						sortable-group-id="<?php echo $item->catid?>">

						<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						
					</td>
						<td width="5%" class="left">
							<?php echo $index; ?>
						</td>

						<td width="5%" align="center" class="hidden-phone">
						<?php echo $item->id; ?>
					</td>

						<td width="10%" align="center" class="hidden-phone">
						<?php echo $item->name; ?>
					</td>
					<td class="center hidden-phone">
						
							<table style="width: 100%">

						<?php
					
				foreach ( $userlst as $eachuser ) :
					
					?>
										<tr width="25%">
									<td><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $eachuser->name; ?></a></td>

								</tr>
										<?php endforeach; ?>
							</table>
						</td>
						<td width="10%" align="center" class="hidden-phone">
						<?php echo $item->description; ?>
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


			<input type="hidden" name="task" value="" /> <input type="hidden"
				name="boxchecked" value="0" /> <input type="hidden"
				name="filter_order" value="<?php echo $listOrder; ?>" /> <input
				type="hidden" name="filter_order_Dir"
				value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>
