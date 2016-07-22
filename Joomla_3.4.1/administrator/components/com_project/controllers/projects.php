<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_project
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Articles list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_project
 * @since       1.6
 */
class ProjectControllerProjects extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  ProjectControllerProjects
     * 
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unfeatured',	'featured');
		$this->registerTask ( 'export', 'export' );
	}

	/**
	 * Method to toggle the featured setting of a list of projects.
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
			JError::raiseWarning(500, JText::_('COM_PROJECT_NO_ITEM_SELECTED'));
		}
		else
		{
			// Publish the items.
			if (!$model->featured($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_project&view=projects');
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
	public function getModel($name = 'Project', $prefix = 'ProjectModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	
	public function export() {
		
		// Run the packager
		jimport ( 'joomla.export.excel.excelclass' );
	
		//Check for request forgeries
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
	
		$user = JFactory::getUser ();
		$ids = $this->input->get ( 'cid', array (), 'array' );
	
		$task = $this->getTask ();
	
		// Get the model.
		$model = $this->getModel ();
	
		if (empty ( $ids )) {
			JError::raiseWarning ( 500, JText::_ ( 'COM_DEFECT_NO_ITEM_SELECTED' ) );
		} else {
			try {
	
				$projectlst = $model->getItemByIds( $ids );
				
				$this->generateExcel ( $projectlst );
			} catch ( Exception $e ) {
				JError::raiseWarning ( 500, $e->getMessage () );
			}
		}
	
	}
	public function generateExcel($projectlst) {
	
		//echo "<pre>";	var_dump($usernamelst); echo "</pre>";die();
		define ( 'EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />' );
	
		// Create new PHPExcel object
		echo date ( 'H:i:s' ), " Create new PHPExcel object", EOL;
	
		$JExcelClass = new JExcelClass ();
		$objPHPExcel = $JExcelClass->getPhpExcel();
	
		// Add some data
	
		$style = array(
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
				)
		);
	
	
	
	
		//$objPHPExcel->getDefaultStyle()->applyFromArray($style);
	
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', 'No' )->setCellValue ( 'B1', 'Project Name' )
		->setCellValue ( 'C1', 'Handled Team' )->setCellValue ( 'D1', 'Project Leader' )->setCellValue ( 'E1', 'No of API' )->setCellValue ( 'F1', 'No of TCs' )
		->setCellValue ( 'F2', 'Unit' )->setCellValue ( 'G2', 'Inte' )->setCellValue ( 'H2', 'MT' )->setCellValue ( 'I2', 'Robustness' )
		->setCellValue ( 'J2', 'Performance' )->setCellValue ( 'K1', '1st Binary' )->setCellValue ( 'L1', 'No Binary' )->setCellValue ( 'M1', 'Issue Status' )
		->setCellValue ( 'M2', 'API' )->setCellValue ( 'N2', 'Manual' )->setCellValue ( 'O2', 'Performance' )->setCellValue ( 'P2', 'CO' )
		;
	
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F1:J1')->mergeCells('A1:A2')->mergeCells('B1:B2')->mergeCells('C1:C2')
		->mergeCells('D1:D2')->mergeCells('E1:E2')->mergeCells('K1:K2')
		->mergeCells('L1:L2')->mergeCells('M1:P1')
		;
		$previousLine= 0;
	
		for ($i = 0; $i < count($projectlst); $i++) {
						$posA = "A";
						$posA .= $i + 3;
			
						$posB = "B";
						$posB .= $i + 3;
			
						$posC = "C";
						$posC .= $i + 3;
			
						$posD = "D";
						$posD .= $i + 3;
			
			
						$posE = "E";
						$posE .= $i + 3;
			
						$posF = "F";
						$posF .= $i + 3;
			
						$posG = "G";
						$posG .= $i + 3;
						
						$posH = "H";
						$posH .= $i + 3;
						
						$posI = "I";
						$posI .= $i + 3;
						
						
						$posJ = "J";
						$posJ .= $i + 3;
						
						$posK = "K";
						$posK .= $i + 3;
						
						$posL = "L";
						$posL .= $i + 3;
						
						$posM = "M";
						$posM .= $i + 3;
			
						$posN = "N";
						$posN .= $i + 3;
						
						$posO = "O";
						$posO .= $i + 3;
						
						$posP = "P";
						$posP .= $i + 3;
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $posA, $i + 1 )->setCellValue ( $posB, $projectlst [$i]->name )
			->setCellValue ( $posC, $projectlst [$i]->handlleteam )->setCellValue ( $posD, $projectlst [$i]->projectleader )
 			->setCellValue ( $posE, $projectlst [$i]->noapis )->setCellValue ( $posF, $projectlst [$i]->unit )
			->setCellValue ( $posG, $projectlst [$i]->integration )->setCellValue ( $posH, $projectlst [$i]->robustness )
			->setCellValue ( $posI, $projectlst [$i]->menutree )->setCellValue ( $posJ, $projectlst [$i]->performance )
			->setCellValue ( $posK, $projectlst [$i]->firstbinary )->setCellValue ( $posL, $projectlst [$i]->noofbinary )
			->setCellValue ( $posK, $projectlst [$i]->firstbinary )->setCellValue ( $posL, $projectlst [$i]->noofbinary )
			->setCellValue ( $posM, $projectlst [$i]->Issuestatus[0] )->setCellValue ( $posN, $projectlst [$i]->Issuestatus[1] )
			->setCellValue ( $posO, $projectlst [$i]->Issuestatus[2] )->setCellValue ( $posP, $projectlst [$i]->Issuestatus[3] );
		}
		
	
				
				
		$style = array(
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
				)
		);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:P2')->applyFromArray(
    array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FF9900')
        )
    )
);
	
		// Save Excel 2007 file
		$previousLine= count($projectlst) +2;
		//var_dump($previousLine);die();
 		$posPfinal = "P";
		$posPfinal .= $previousLine ;
  		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$posPfinal)->applyFromArray($style);
 		unset($style);
	
		$currentTime = new JDate ( 'now' ); // Current date and time
		$date = $currentTime->year . $currentTime->month . $currentTime->day . "_" . $currentTime->hour . $currentTime->minute . $currentTime->second . "";
	
		$file = JURI::root ();
		$file .= 'report/report_' . $date . '.xlsx';
	
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
		$objWriter->save ( str_replace ( '.php', '.xlsx', JPATH_ROOT . '/report/' . 'report_' . $date . '.xlsx' ) );
	
	
		// download
	
		ob_end_clean ();
		header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header ( 'Content-Disposition: attachment;filename="report_"' . $date . '.xlsx"' );
		header ( 'Cache-Control: max-age=0' );
	
		$objWriter->save ( 'php://output' );
	
		exit ();
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

}
