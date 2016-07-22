<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_binary
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_binary
 */
class BinaryControllerBinary extends JControllerForm
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
		$model  = $this->getModel('binary');
		$params = JComponentHelper::getParams('com_binary');
		$stub   = $this->input->getString('id');
		$id     = (int) $stub;

		// Get the data from POST
		$data    = $this->input->post->get('jform', array(), 'array');
		$binary = $model->getItem($id);

		$params->merge($binary->params);

		// Check for a valid session cookie
		if ($params->get('validate_session', 0))
		{
			if (JFactory::getSession()->getState() != 'active')
			{
				JError::raiseWarning(403, JText::_('COM_BINARY_SESSION_INVALID'));

				// Save the data in the session.
				$app->setUserState('com_binary.binary.data', $data);

				// Redirect back to the binary form.
				$this->setRedirect(JRoute::_('index.php?option=com_binary&view=binary&id=' . $stub, false));

				return false;
			}
		}

		// Binary plugins
		JPluginHelper::importPlugin('binary');
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
			$app->setUserState('com_binary.binary.data', $data);

			// Redirect back to the binary form.
			$this->setRedirect(JRoute::_('index.php?option=com_binary&view=binary&id=' . $stub, false));

			return false;
		}

		// Validation succeeded, continue with custom handlers
		$results = $dispatcher->trigger('onValidateBinary', array(&$binary, &$data));

		foreach ($results as $result)
		{
			if ($result instanceof Exception)
			{
				return false;
			}
		}

		// Passed Validation: Process the binary plugins to integrate with other applications
		$dispatcher->trigger('onSubmitBinary', array(&$binary, &$data));

		// Send the email
		$sent = false;

		if (!$params->get('custom_reply'))
		{
			$sent = $this->_sendEmail($data, $binary, $params->get('show_email_copy'));
		}

		// Set the success message if it was a success
		if (!($sent instanceof Exception))
		{
			$msg = JText::_('COM_BINARY_EMAIL_THANKS');
		}
		else
		{
			$msg = '';
		}

		// Flush the data from the session
		$app->setUserState('com_binary.binary.data', null);

		// Redirect if it is set in the parameters, otherwise redirect back to where we came from
		if ($binary->params->get('redirect'))
		{
			$this->setRedirect($binary->params->get('redirect'), $msg);
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_binary&view=binary&id=' . $stub, false), $msg);
		}

		return true;
	}

	private function _sendEmail($data, $binary, $copy_email_activated)
	{
			$app = JFactory::getApplication();

			if ($binary->email_to == '' && $binary->user_id != 0)
			{
				$binary_user      = JUser::getInstance($binary->user_id);
				$binary->email_to = $binary_user->get('email');
			}

			$mailfrom = $app->get('mailfrom');
			$fromname = $app->get('fromname');
			$sitename = $app->get('sitename');

			$name    = $data['binary_name'];
			$email   = JStringPunycode::emailToPunycode($data['binary_email']);
			$subject = $data['binary_subject'];
			$body    = $data['binary_message'];

			// Prepare email body
			$prefix = JText::sprintf('COM_BINARY_ENQUIRY_TEXT', JUri::base());
			$body	= $prefix . "\n" . $name . ' <' . $email . '>' . "\r\n\r\n" . stripslashes($body);

			$mail = JFactory::getMailer();
			$mail->addRecipient($binary->email_to);
			$mail->addReplyTo(array($email, $name));
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($sitename . ': ' . $subject);
			$mail->setBody($body);
			$sent = $mail->Send();

			// If we are supposed to copy the sender, do so.

			// Check whether email copy function activated
			if ($copy_email_activated == true && !empty($data['binary_email_copy']))
			{
				$copytext    = JText::sprintf('COM_BINARY_COPYTEXT_OF', $binary->name, $sitename);
				$copytext    .= "\r\n\r\n" . $body;
				$copysubject = JText::sprintf('COM_BINARY_COPYSUBJECT_OF', $subject);

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
