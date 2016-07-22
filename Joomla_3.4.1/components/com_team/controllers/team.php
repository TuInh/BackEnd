<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_team
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_team
 */
class TeamControllerTeam extends JControllerForm
{
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	public function submit()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app    = JFactory::getApplication();
		$model  = $this->getModel('team');
		$params = JComponentHelper::getParams('com_team');
		$stub   = $this->input->getString('id');
		$id     = (int) $stub;

		// Get the data from POST
		$data    = $this->input->post->get('jform', array(), 'array');
		$team = $model->getItem($id);

		$params->merge($team->params);

		// Check for a valid session cookie
		if ($params->get('validate_session', 0))
		{
			if (JFactory::getSession()->getState() != 'active')
			{
				JError::raiseWarning(403, JText::_('COM_TEAM_SESSION_INVALID'));

				// Save the data in the session.
				$app->setUserState('com_team.team.data', $data);

				// Redirect back to the team form.
				$this->setRedirect(JRoute::_('index.php?option=com_team&view=team&id=' . $stub, false));

				return false;
			}
		}

		// Team plugins
		JPluginHelper::importPlugin('team');
		$dispatcher = JEventDispatcher::getInstance();

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		$validate = $model->validate($form, $data);

		if ($validate === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_team.team.data', $data);

			// Redirect back to the team form.
			$this->setRedirect(JRoute::_('index.php?option=com_team&view=team&id=' . $stub, false));

			return false;
		}

		// Validation succeeded, continue with custom handlers
		$results = $dispatcher->trigger('onValidateTeam', array(&$team, &$data));

		foreach ($results as $result)
		{
			if ($result instanceof Exception)
			{
				return false;
			}
		}

		// Passed Validation: Process the team plugins to integrate with other applications
		$dispatcher->trigger('onSubmitTeam', array(&$team, &$data));

		// Send the email
		$sent = false;

		if (!$params->get('custom_reply'))
		{
			$sent = $this->_sendEmail($data, $team, $params->get('show_email_copy'));
		}

		// Set the success message if it was a success
		if (!($sent instanceof Exception))
		{
			$msg = JText::_('COM_TEAM_EMAIL_THANKS');
		}
		else
		{
			$msg = '';
		}

		// Flush the data from the session
		$app->setUserState('com_team.team.data', null);

		// Redirect if it is set in the parameters, otherwise redirect back to where we came from
		if ($team->params->get('redirect'))
		{
			$this->setRedirect($team->params->get('redirect'), $msg);
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_team&view=team&id=' . $stub, false), $msg);
		}

		return true;
	}

	private function _sendEmail($data, $team, $copy_email_activated)
	{
			$app = JFactory::getApplication();

			if ($team->email_to == '' && $team->user_id != 0)
			{
				$team_user      = JUser::getInstance($team->user_id);
				$team->email_to = $team_user->get('email');
			}

			$mailfrom = $app->get('mailfrom');
			$fromname = $app->get('fromname');
			$sitename = $app->get('sitename');

			$name    = $data['team_name'];
			$email   = JStringPunycode::emailToPunycode($data['team_email']);
			$subject = $data['team_subject'];
			$body    = $data['team_message'];

			// Prepare email body
			$prefix = JText::sprintf('COM_TEAM_ENQUIRY_TEXT', JUri::base());
			$body	= $prefix . "\n" . $name . ' <' . $email . '>' . "\r\n\r\n" . stripslashes($body);

			$mail = JFactory::getMailer();
			$mail->addRecipient($team->email_to);
			$mail->addReplyTo(array($email, $name));
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($sitename . ': ' . $subject);
			$mail->setBody($body);
			$sent = $mail->Send();

			// If we are supposed to copy the sender, do so.

			// Check whether email copy function activated
			if ($copy_email_activated == true && !empty($data['team_email_copy']))
			{
				$copytext    = JText::sprintf('COM_TEAM_COPYTEXT_OF', $team->name, $sitename);
				$copytext    .= "\r\n\r\n" . $body;
				$copysubject = JText::sprintf('COM_TEAM_COPYSUBJECT_OF', $subject);

				$mail = JFactory::getMailer();
				$mail->addRecipient($email);
				$mail->addReplyTo(array($email, $name));
				$mail->setSender(array($mailfrom, $fromname));
				$mail->setSubject($copysubject);
				$mail->setBody($copytext);
				$sent = $mail->Send();
			}

			return $sent;
	}
}
