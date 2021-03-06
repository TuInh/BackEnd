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
 * HTML Team View class for the Team component
 *
 * @package     Joomla.Site
 * @subpackage  com_team
 * @since       1.5
 */
class TeamViewTeam extends JViewLegacy
{
	protected $state;

	protected $form;

	protected $item;

	protected $return_page;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{

		$app        = JFactory::getApplication();
		$user       = JFactory::getUser();
		$state      = $this->get('State');
		$item       = $this->get('Item');
		$this->form = $this->get('Form');

		// Get the parameters
		$params = JComponentHelper::getParams('com_team');

		if ($item)
		{
			// If we found an item, merge the item parameters
			$params->merge($item->params);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

//		// Check if access is not public
//		$groups	= $user->getAuthorisedViewLevels();
//
//		$return = '';
//
//		$options['order by']	= 'a.default_con DESC, a.ordering ASC';
//
//
//
//		// Manage the display mode for team detail groups
//		switch ($params->get('team_icons'))
//		{
//			case 1 :
//				// Text
//				$params->set('marker_address',   JText::_('COM_TEAM_ADDRESS') . ": ");
//				$params->set('marker_email',     JText::_('JGLOBAL_EMAIL') . ": ");
//				$params->set('marker_telephone', JText::_('COM_TEAM_TELEPHONE') . ": ");
//				$params->set('marker_fax',       JText::_('COM_TEAM_FAX') . ": ");
//				$params->set('marker_mobile',    JText::_('COM_TEAM_MOBILE') . ": ");
//				$params->set('marker_misc',      JText::_('COM_TEAM_OTHER_INFORMATION') . ": ");
//				$params->set('marker_class',     'jicons-text');
//				break;
//
//			case 2 :
//				// None
//				$params->set('marker_address',   '');
//				$params->set('marker_email',     '');
//				$params->set('marker_telephone', '');
//				$params->set('marker_mobile',    '');
//				$params->set('marker_fax',       '');
//				$params->set('marker_misc',      '');
//				$params->set('marker_class',     'jicons-none');
//				break;
//
//			default :
//				if ($params->get('icon_address'))
//				{
//					$image1 = JHtml::_('image', $params->get('icon_address', 'con_address.png'), JText::_('COM_TEAM_ADDRESS') . ": ", null, false);
//				}
//				else
//				{
//					$image1 = JHtml::_('image', 'teams/'.$params->get('icon_address', 'con_address.png'), JText::_('COM_TEAM_ADDRESS').": ", null, true);
//				}
//
//				if ($params->get('icon_email'))
//				{
//					$image2 = JHtml::_('image', $params->get('icon_email', 'emailButton.png'), JText::_('JGLOBAL_EMAIL') . ": ", null, false);
//				}
//				else
//				{
//					$image2 = JHtml::_('image', 'teams/' . $params->get('icon_email', 'emailButton.png'), JText::_('JGLOBAL_EMAIL') . ": ", null, true);
//				}
//
//				if ($params->get('icon_telephone'))
//				{
//					$image3 = JHtml::_('image', $params->get('icon_telephone', 'con_tel.png'), JText::_('COM_TEAM_TELEPHONE') . ": ", null, false);
//				}
//				else
//				{
//					$image3 = JHtml::_('image', 'teams/' . $params->get('icon_telephone', 'con_tel.png'), JText::_('COM_TEAM_TELEPHONE') . ": ", null, true);
//				}
//
//				if ($params->get('icon_fax'))
//				{
//					$image4 = JHtml::_('image', $params->get('icon_fax', 'con_fax.png'), JText::_('COM_TEAM_FAX') . ": ", null, false);
//				}
//				else
//				{
//					$image4 = JHtml::_('image', 'teams/' . $params->get('icon_fax', 'con_fax.png'), JText::_('COM_TEAM_FAX') . ": ", null, true);
//				}
//
//				if ($params->get('icon_misc'))
//				{
//					$image5 = JHtml::_('image', $params->get('icon_misc', 'con_info.png'), JText::_('COM_TEAM_OTHER_INFORMATION') . ": ", null, false);
//				}
//				else
//				{
//					$image5 = JHtml::_('image', 'teams/' . $params->get('icon_misc', 'con_info.png'), JText::_('COM_TEAM_OTHER_INFORMATION') . ": ", null, true);
//				}
//
//				if ($params->get('icon_mobile'))
//				{
//					$image6 = JHtml::_('image', $params->get('icon_mobile', 'con_mobile.png'), JText::_('COM_TEAM_MOBILE') . ": ", null, false);
//				}
//				else
//				{
//					$image6 = JHtml::_('image', 'teams/' . $params->get('icon_mobile', 'con_mobile.png'), JText::_('COM_TEAM_MOBILE') . ": ", null, true);
//				}
//
//				$params->set('marker_address',   $image1);
//				$params->set('marker_email',     $image2);
//				$params->set('marker_telephone', $image3);
//				$params->set('marker_fax',       $image4);
//				$params->set('marker_misc',      $image5);
//				$params->set('marker_mobile',    $image6);
//				$params->set('marker_class',     'jicons-icons');
//				break;
//		}


		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->team  = &$item;
		$this->params   = &$params;
		$this->return   = &$return;
		$this->state    = &$state;
		$this->item     = &$item;
		$this->user     = &$user;
		$this->teams = &$teams;

		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_team.team', $this->item->id);

		// Override the layout only if this is not the active menu item
		// If it is the active menu item, then the view and item id will match
		$active	= $app->getMenu()->getActive();
		if ((!$active) || ((strpos($active->link, 'view=team') === false) || (strpos($active->link, '&id=' . (string) $this->item->id) === false)))
		{
			if ($layout = $params->get('team_layout'))
			{
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout']))
		{
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		$model = $this->getModel();
		$model->hit();
		$this->_prepareDocument();

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 * 
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_TEAM_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this team
		if ($menu && ($menu->query['option'] != 'com_team' || $menu->query['view'] != 'team' || $id != $this->item->id))
		{

			// If this is not a single team menu item, set the page title to the team title
			if ($this->item->name)
			{
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->team->name, 'link' => ''));

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		if (empty($title))
		{
			$title = $this->item->name;
		}
		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

	}
}
