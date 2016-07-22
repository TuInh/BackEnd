<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die ();

/**
 * Users list controller class.
 *
 * @since 1.6
 */
class UsersControllerUsers extends JControllerAdmin {
	/**
	 *
	 * @var string The prefix to use with controller messages.
	 * @since 1.6
	 */
	protected $text_prefix = 'COM_USERS_USERS';
	
	/**
	 * Constructor.
	 *
	 * @param array $config
	 *        	An optional associative array of configuration settings.
	 *        	
	 * @since 1.6
	 * @see JController
	 */
	public function __construct($config = array()) {
		parent::__construct ( $config );
		
		$this->registerTask ( 'block', 'changeBlock' );
		$this->registerTask ( 'unblock', 'changeBlock' );
		$this->registerTask ( 'export', 'export' );
	}
	public function export() {
		// Run the packager
		jimport ( 'joomla.export.excel.excelclass' );
		
		// Check for request forgeries
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
				
				$usernamelst = $model->getItemByIds ( $ids );
				$this->generateExcel ( $usernamelst );
			} catch ( Exception $e ) {
				JError::raiseWarning ( 500, $e->getMessage () );
			}
		}

	}
	public function generateExcel($usernamelst) {
	
		//echo "<pre>";	var_dump($usernamelst); echo "</pre>";die();
		define ( 'EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />' );
		
		// Create new PHPExcel object
		echo date ( 'H:i:s' ), " Create new PHPExcel object", EOL;
		
		$JExcelClass = new JExcelClass ();
		
		$objPHPExcel = $JExcelClass->getPhpExcel ();
		
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
		
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', 'No' )->setCellValue ( 'B1', 'Name' )->setCellValue ( 'C1', 'Username' )
		->setCellValue ( 'D1', 'ID' )->setCellValue ( 'E1', 'Team' )->setCellValue ( 'F1', 'Position' )->setCellValue ( 'G1', 'Invlove Project' )
		->setCellValue ( 'H1', 'Human Resource' )->setCellValue ( 'I1', 'Issue Status' )->setCellValue ( 'I2', 'API' )->setCellValue ( 'J2', 'Manual' )
		->setCellValue ( 'K2', 'Performance' )->setCellValue ( 'L2', 'CO' )->setCellValue ( 'I1', 'Issue Status' )->setCellValue ( 'I1', 'Issue Status' )
		->setCellValue ( 'M1', 'STC Score' )->setCellValue ( 'N1', 'TOEIC' )
		;
		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('I1:L1')->mergeCells('A1:A2')->mergeCells('B1:B2')->mergeCells('C1:C2')
		->mergeCells('D1:D2')->mergeCells('E1:E2')->mergeCells('F1:F2')
		->mergeCells('G1:G2')->mergeCells('H1:H2')->mergeCells('C1:C2')
		->mergeCells('M1:M2')->mergeCells('N1:N2');
		$previousLine= 0;
		
		for($i = 0; $i < count ( $usernamelst ); $i ++) {
			
			if($i==0)
			{
				$length = 3;
			}
			else
				if(count($usernamelst [$i-1]->projectnameInvolve)==0)
					$length =1;
				else
					$length = count($usernamelst [$i-1]->projectnameInvolve);
			
			
			
			$posA = "A";
			$posA .= $previousLine + $length;
			
			$posB = "B";
			$posB .= $previousLine + $length;
			
			$posC = "C";
			$posC .= $previousLine + $length;
			
			$posD = "D";
			$posD .= $previousLine + $length;
			
		
			$posE = "E";
			$posE .= $previousLine + $length;
						
			$posF = "F";
			$posF .= $previousLine + $length;
			
			$posM = "M";
			$posM .= $previousLine + $length;
			
			$posN = "N";
			$posN .= $previousLine + $length;
			
			//var_dump($posD);
			
			
			
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $posA, $i +1 )->setCellValue ( $posB, $usernamelst [$i]->name )->setCellValue ( $posC, $usernamelst [$i]->username )->setCellValue ( $posD, $usernamelst [$i]->id )
			->setCellValue ( $posE, $usernamelst [$i]->teamname )->setCellValue ( $posF, $usernamelst [$i]->position )->setCellValue ( $posM, $usernamelst [$i]->stc )
			->setCellValue ( $posN, $usernamelst [$i]->toeic );
			$lengthcurr= count($usernamelst [$i]->projectnameInvolve);
			if($lengthcurr >1)
			{
				$posAto = "A";
				$posAto .= $previousLine + $length + $lengthcurr -1;
					
				$posBto = "B";
				$posBto .= $previousLine + $length + $lengthcurr -1;
				
				$posCto = "C";
				$posCto .= $previousLine + $length + $lengthcurr -1;
				
				$posDto = "D";
				$posDto .= $previousLine + $length + $lengthcurr -1;
				
				$posEto = "E";
				$posEto .= $previousLine + $length + $lengthcurr -1;
				
				$posFto = "F";
				$posFto .= $previousLine + $length + $lengthcurr -1;
				
				$posMto = "M";
				$posMto .= $previousLine + $length + $lengthcurr -1;
				
				$posNto = "N";
				$posNto .= $previousLine + $length + $lengthcurr -1;
				
				//var_dump($posAto);die();
				
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells( $posA .':'.$posAto)->mergeCells( $posB .':'.$posBto)->mergeCells( $posC .':'.$posCto)
				->mergeCells( $posD .':'.$posDto)->mergeCells( $posE .':'.$posEto)->mergeCells( $posF .':'.$posFto)->mergeCells( $posM.':'.$posMto)
				->mergeCells( $posN .':'.$posNto);
					
				
				
				for ($j = 0; $j < $lengthcurr; $j++) {
					$posG =NULL;
					$posG ="G";
					$posG .= $previousLine + $length + $j;
				
					$posH =NULL;
					$posH ="H";
					$posH .= $previousLine + $length + $j;
				
					$posI =NULL;
					$posI ="I";
					$posI .= $previousLine + $length + $j;
				
					$posJ =NULL;
					$posJ ="J";
					$posJ .= $previousLine + $length + $j;
				
					$posK =NULL;
					$posK ="K";
					$posK .= $previousLine + $length + $j;
				
					$posL =NULL;
					$posL ="L";
					$posL .= $previousLine + $length + $j;
				
					$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $posG, $usernamelst [$i]->projectnameInvolve[$j] )
					->setCellValue ( $posH, $usernamelst [$i]->mandate[$j] )
					->setCellValue ( $posI, $usernamelst [$i]->issue[$j][0] )
					->setCellValue ( $posJ, $usernamelst [$i]->issue[$j][1] )
					->setCellValue ( $posK, $usernamelst [$i]->issue[$j][2] )
					->setCellValue ( $posL, $usernamelst [$i]->issue[$j][3] );
				
					//echo "<pre>";	var_dump($usernamelst [$i]->issue[$j]); echo "</pre>";die();
				}
			}
			
			
			
			$previousLine = $previousLine +$length;
			
			//var_dump($usernamelst [$i]->projectnameInvolve);die();
			//$objPHPExcel->getActiveSheet()->fromArray($usernamelst [$i]->projectnameInvolve,NULL,$posE .":".$posEto);
		}
		
		// Save Excel 2007 file
		$objPHPExcel->getActiveSheet()->getStyle('A1:N2')->applyFromArray(
				array(
						'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'FF9900')
						)
				)
		);
		
		
		$posNfinal = "N";
		$posNfinal .= $previousLine ;
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$posNfinal)->applyFromArray($style);
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
	 * Proxy for getModel.
	 *
	 * @param string $name
	 *        	The model name. Optional.
	 * @param string $prefix
	 *        	The class prefix. Optional.
	 * @param array $config
	 *        	Configuration array for model. Optional.
	 *        	
	 * @return object The model.
	 *        
	 * @since 1.6
	 */
	public function getModel($name = 'User', $prefix = 'UsersModel', $config = array('ignore_request' => true)) {
		return parent::getModel ( $name, $prefix, $config );
	}
	
	/**
	 * Method to change the block status on a record.
	 *
	 * @return void
	 *
	 * @since 1.6
	 */
	public function changeBlock() {
		// Check for request forgeries.
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
		
		$ids = $this->input->get ( 'cid', array (), 'array' );
		$values = array (
				'block' => 1,
				'unblock' => 0 
		);
		$task = $this->getTask ();
		$value = JArrayHelper::getValue ( $values, $task, 0, 'int' );
		
		if (empty ( $ids )) {
			JError::raiseWarning ( 500, JText::_ ( 'COM_USERS_USERS_NO_ITEM_SELECTED' ) );
		} else {
			// Get the model.
			$model = $this->getModel ();
			
			// Change the state of the records.
			if (! $model->block ( $ids, $value )) {
				JError::raiseWarning ( 500, $model->getError () );
			} else {
				if ($value == 1) {
					$this->setMessage ( JText::plural ( 'COM_USERS_N_USERS_BLOCKED', count ( $ids ) ) );
				} elseif ($value == 0) {
					$this->setMessage ( JText::plural ( 'COM_USERS_N_USERS_UNBLOCKED', count ( $ids ) ) );
				}
			}
		}
		
		$this->setRedirect ( 'index.php?option=com_users&view=users' );
	}
	
	/**
	 * Method to activate a record.
	 *
	 * @return void
	 *
	 * @since 1.6
	 */
	public function activate() {
		// Check for request forgeries.
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
		
		$ids = $this->input->get ( 'cid', array (), 'array' );
		
		if (empty ( $ids )) {
			JError::raiseWarning ( 500, JText::_ ( 'COM_USERS_USERS_NO_ITEM_SELECTED' ) );
		} else {
			// Get the model.
			$model = $this->getModel ();
			
			// Change the state of the records.
			if (! $model->activate ( $ids )) {
				JError::raiseWarning ( 500, $model->getError () );
			} else {
				$this->setMessage ( JText::plural ( 'COM_USERS_N_USERS_ACTIVATED', count ( $ids ) ) );
			}
		}
		
		$this->setRedirect ( 'index.php?option=com_users&view=users' );
	}
}
