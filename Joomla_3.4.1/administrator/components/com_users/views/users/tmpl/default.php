<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die ();

JHtml::_ ( 'bootstrap.tooltip' );
JHtml::_ ( 'behavior.multiselect' );
JHtml::_ ( 'formbehavior.chosen', 'select' );

$listOrder = $this->escape ( $this->state->get ( 'list.ordering' ) );
$listDirn = $this->escape ( $this->state->get ( 'list.direction' ) );
$loggeduser = JFactory::getUser ();

$model = JModelLegacy::getInstance ( 'user', 'UsersModel' );
$user = JFactory::getUser();
?>
<form
	action="<?php echo JRoute::_('index.php?option=com_users&view=users');?>"
	method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
		<?php
		// Search tools bar
		echo JLayoutHelper::render ( 'joomla.searchtools.default', array (
				'view' => $this 
		) );
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="userList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th class="left" style="vertical-align: bottom;" width="3%"
							class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HEADING_NO', 'id', $listDirn, $listOrder); ?>
						</th>
						<th width="3%" class="left">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
						</th>

						<th width="5%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
						</th>
						<th width="2%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
						<th width="3%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_TEAM_NAME', 'a.block', $listDirn, $listOrder); ?>
						</th>
						<th width="3%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_POSITION', 'a.block', $listDirn, $listOrder); ?>
						</th>
						<th width="6%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_INVOLVE_PROJECT', 'a.email', $listDirn, $listOrder); ?>
						</th>
						<th width="2%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_HR', 'a.email', $listDirn, $listOrder); ?>
						</th>
						<th width="6%" class="nowrap center">

							<table style="width: 100%">
								<tr align="center">
									<td colspan="4" style="text-align: center">Issue Status</td>

								</tr>
								<tr>
									<td width="25%" class="title-center">API</td>
									<td width="25%" class="title-center">Manual</td>
									<td width="25%" class="title-center">Performance</td>
									<td width="25%" class="title-center">CO</td>
								</tr>

							</table>
						</th>
						<th width="3%" class=" center ">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_USER_FIELD_STC_LABEL', 'a.email', $listDirn, $listOrder); ?>
						</th>
						<th width="3%" class=" center ">
							<?php echo JHtml::_('searchtools.sort', 'COM_USERS_USER_FIELD_TOEIC_LABEL', 'a.lastvisitDate', $listDirn, $listOrder); ?>
						</th>


					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="15">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php $index =0;?>
				<?php $isSuperUser= $model->isSuperUser() ?>
				<?php
			
			foreach ( $this->items as $i => $item ) :
				
				$index ++;
				$canEdit = $this->canDo->get ( 'core.edit' );
				$canChange = $loggeduser->authorise ( 'core.edit.state', 'com_users' );
				
				// If this group is super admin and this user is not super admin, $canEdit is false
				if ((! $loggeduser->authorise ( 'core.admin' )) && JAccess::check ( $item->id, 'core.admin' )) {
					$canEdit = false;
					$canChange = false;
				}
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php if ($canEdit) : ?>
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							<?php endif; ?>
						</td>
						<td class="left">
							<?php echo $index; ?>
						</td>
						<td>
							<div class="name">
							<?php if ($canEdit) : ?>
								<?php if (($user->authorise('core.edit.team', 'com_users')  && ($model->isMemberofTeam( (int) $item->id) )|| $user->id ==  (int) $item->id) || ($isSuperUser==true) ) : ?>
								<a
									href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->id); ?>"
									title="<?php echo JText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
									<?php echo $this->escape($item->name); ?></a>
									<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
							<?php else : ?>
								<?php echo $this->escape($item->name); ?>
							<?php endif; ?>
							</div>
							<div class="btn-group">
								<?php echo JHtml::_('users.filterNotes', $item->note_count, $item->id); ?>
								<?php echo JHtml::_('users.notes', $item->note_count, $item->id); ?>
								<?php echo JHtml::_('users.addNote', $item->id); ?>
							</div>
							<?php echo JHtml::_('users.notesModal', $item->note_count, $item->id); ?>
							<?php if ($item->requireReset == '1') : ?>
								<span class="label label-warning"><?php echo JText::_('COM_USERS_PASSWORD_RESET_REQUIRED'); ?></span>
							<?php endif; ?>
							<?php if (JDEBUG) : ?>
								<div class="small">
								<a
									href="<?php echo JRoute::_('index.php?option=com_users&view=debuguser&user_id=' . (int) $item->id);?>">
								<?php echo JText::_('COM_USERS_DEBUG_USER');?></a>
							</div>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo $this->escape($item->username); ?>
						</td>

						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
						<td class="center">
							<?php
				$teamname = $model->getTeamName ( $item->teamid );
				echo $teamname->name;
				?>
						</td>

						<td class="center">
							<?php if (substr_count($item->group_names, "\n") > 1) : ?>
								<span class="hasTooltip"
							title="<?php echo JHtml::tooltipText(JText::_('COM_USERS_HEADING_GROUPS'), nl2br($item->group_names), 0); ?>"><?php echo JText::_('COM_USERS_USERS_MULTIPLE_GROUPS'); ?></span>
							<?php else : ?>
								<?php echo nl2br($item->group_names); ?>
							<?php endif; ?>
						</td>

						<td class="center hidden-phone">

							<table style="width: 100%">
							
							
									<?php
				$projectlst = $model->getProjectname ( $item->id )['projectlstname'];
				$projectlstId = $model->getProjectname ( $item->id )['projectlstid'];
				
				for ($i = 0; $i < count($projectlst); $i++) :
					
					?>
										<tr width="25%">
									<td><a  href="<?php echo JRoute::_('index.php?option=com_project&task=project.edit&id=' . (int) $projectlstId[$i]);?>"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $projectlst[$i]; ?></a></td>
								</tr>
										<?php endfor; ?>
									
									
							</table>
						</td>
						<td class="center hidden-phone">
						
							<table style="width: 100%">

						<?php
					$projectIDlst = $model->getProjectInvlove ( $item->id );
				foreach ( $projectIDlst as $projectID ) :
					$mandate = $model->getMandateEachMember ( $item->id, $projectID );
					?>
										<tr width="25%">
									<td> <?php echo $mandate; ?></td>

								</tr>
										<?php endforeach; ?>
							</table>
						</td>
						<td class="center hidden-phone">
							<table style="width: 100%">
							
							
									<?php
				$projectIDlst = $model->getProjectInvlove ( $item->id );
				foreach ( $projectIDlst as $projectID ) :
					$issue = $model->getAllBinaryinProject ( $projectID, $item->id );
					?>
										<tr width="25%">
									<td><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issue[0]; ?></a></td>
									<td><a href="#"`
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issue[1]; ?></a></td>
									<td><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issue[2]; ?></a></td>
									<td><a href="#"
										style="text-align: center; padding-bottom: 0px !important;"> <?php echo $issue[3]; ?></a></td>
								</tr>
										<?php endforeach; ?>
									
									
							</table>

						</td>
						<td class="center hidden-phone">
								<?php echo  $item->stc; ?>
						</td>
						<td class="center hidden-phone">
								<?php echo  $item->toeic; ?>
						</td>

					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php //Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" /> <input type="hidden"
				name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>
