<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_defect
 * @since       1.6
 */
class DefectControllerDefects extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  DefectControllerDefects
     * 
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unfeatured',	'featured');
		$this->registerTask('export', 'export');
		$this->registerTask('import', 'import');
	}

	/**
	 * Method to toggle the featured setting of a list of defects.
	 *
	 * @return  void
	 * @since   1.6
	 */
	public function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user   = JFactory::getUser();
		$ids    = $this->input->get('cid', array(), 'array');
		$values = array('featured' => 1, 'unfeatured' => 0);
		$task   = $this->getTask();
		$value  = JArrayHelper::getValue($values, $task, 0, 'int');

		// Get the model.
		$model  = $this->getModel();

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_DEFECT_NO_ITEM_SELECTED'));
		}
		else
		{
			// Publish the items.
			if (!$model->featured($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_defect&view=defects');
	}
	
	public function export(){
		// Run the packager
		jimport('joomla.export.excel.excelclass');
		
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$user   = JFactory::getUser();
		$ids    = $this->input->get('cid', array(), 'array');
		
		$task   = $this->getTask();
		
		
		// Get the model.
		$model  = $this->getModel();
		
		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_DEFECT_NO_ITEM_SELECTED'));
		}
		else
		{
			try {
				$defectList = $model->getItemByIds($ids);
				$this->generateExcel($defectList);
			} catch (Exception $e) {
				JError::raiseWarning(500, $e->getMessage());
			}
			
			
		}
		
		$this->setRedirect('index.php?option=com_defect&view=defects');
	}
	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the PHP class name.
	 *
	 * @return  JModel
	 * @since   1.6
	 */
	public function getModel($name = 'Defect', $prefix = 'DefectModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $ids    The array of ids for items being deleted.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
	
	public function generateExcel($defectList){
		
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		
		
		// Create new PHPExcel object
		echo date('H:i:s') , " Create new PHPExcel object" , EOL;
		
		$JExcelClass = new JExcelClass();
		
		$objPHPExcel = $JExcelClass->getPhpExcel();

		// Add some data
		echo date('H:i:s') , " Add some data" , EOL;
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'CaseCode')
		->setCellValue('B1', 'Title');
				
		for ($i = 0; $i < count($defectList); $i++) {
			$posA="A";
			$posA.=$i+2;
			
			$posB="B";
			$posB.=$i+2;
			
		    $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($posA, $defectList[$i]->alias)
			->setCellValue($posB, $defectList[$i]->name);
		}
		
 		// Save Excel 2007 file
 		
		$currentTime = new JDate('now'); // Current date and time
		$date = $currentTime->year.$currentTime->month.$currentTime->day."_".$currentTime->hour. $currentTime->minute. $currentTime->second."";
		
		$file = JURI::root();
		$file.='report/report_'.$date.'.xlsx';

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(str_replace('.php', '.xlsx', JPATH_ROOT.'/report/'.'report_'.$date.'.xlsx'));
		
		//download
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="report_"'.$date.'.xlsx"');
		header('Cache-Control: max-age=0');
		
		$objWriter->save('php://output');
		exit();
	}
	
	public function import(){
		
		$input = JFactory::getApplication()->input;
		$userfile = $input->files->get('file_source');

		// Make sure that file uploads are enabled in php.
		if (!(bool) ini_get('file_uploads'))
		{
			JError::raiseWarning('', JText::_('COM_DEFECT_MSG_INSTALL_WARNINSTALLFILE'));
		
			return false;
		}
		
		// Make sure that zlib is loaded so that the package can be unpacked.
		if (!extension_loaded('zlib'))
		{
			JError::raiseWarning('', JText::_('COM_DEFECT_MSG_INSTALL_WARNINSTALLZLIB'));
		
			return false;
		}
		
		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile))
		{
			JError::raiseWarning('', JText::_('COM_DEFECT_MSG_INSTALL_NO_FILE_SELECTED'));
		
			return false;
		}
		
		// Is the PHP tmp directory missing?
		if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_NO_TMP_DIR))
		{
			JError::raiseWarning('', JText::_('COM_DEFECT_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_PHPUPLOADNOTSET'));
		
			return false;
		}
		
		// Is the max upload size too small in php.ini?
		if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_INI_SIZE))
		{
			JError::raiseWarning('', JText::_('COM_DEFECT_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_SMALLUPLOADSIZE'));
		
			return false;
		}
		
		// Check if there was a different problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1)
		{
			JError::raiseWarning('', JText::_('COM_DEFECT_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
		
			return false;
		}
		
		// Build the appropriate paths.
		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path') . '/' . $userfile['name'];
		$tmp_src	= $userfile['tmp_name'];
		$tmp_src	= $userfile['tmp_name'];
		
		// Move uploaded file.
		jimport('joomla.filesystem.file');
		JFile::upload($tmp_src, $tmp_dest, false, true);
		
		// Unpack the downloaded package file.
		$package = JInstallerHelper::unpack($tmp_dest, true);
		
		$cmd= JPATH_ROOT."\setup\ConsoleApplication6.exe"." ".$tmp_dest;
		
		$output = shell_exec($cmd);
		
		$this->setRedirect('index.php?option=com_defect&view=defects');
	}
}
