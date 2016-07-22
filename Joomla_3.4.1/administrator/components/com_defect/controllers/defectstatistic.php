<?php
/**
 * @package     pro-it
 * @subpackage  com_warrantyorder
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once 'controllersjson.php';
require_once JPATH_ADMINISTRATOR . '/components/com_defect/models/defect.php';
/**
 * Articles list controller class.
 *
 * @package     pro-it
 * @subpackage  com_warrantyorder
 * @since       1.0
 */
class DefectControllerDefectStatistic extends itrControllerJson 
{
	var $defectModel = null;
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  AgentControllerAgents
	 * @see     JController
	 * @since   1.0
	 */
	public function __construct($config = array())
	{
		$this->defectModel = JModelLegacy::getInstance('defect', 'DefectModel');
		parent::__construct($config);

	}

	public function listDefectDetail()
	{

		$data = JRequest::getString('data', '');

		
		$defectList = $this->getDefectByYear($data);
		
		
		$this->addResponse('message', $defectList, 200);
		
		$this->display();
	}
	
	public function getDefectByYear($year){
		$data = array(
				'api' => array(0,0,0,0,0,0,0,0,0,0,0,0),
				'performance' => array(0,0,0,0,0,0,0,0,0,0,0,0),
				'manual' => array(0,0,0,0,0,0,0,0,0,0,0,0),
				'code_optimize' => array(0,0,0,0,0,0,0,0,0,0,0,0)
		);
		
		for ($i = 0; $i < 12; $i++) {
			$defect = $this->getDefectByMonth($year.'-'.($i+1));
			$data['api'][$i] = $defect['api'][0];
			$data['performance'][$i] = $defect['performance'][0];
			$data['manual'][$i] = $defect['manual'][0];
			$data['code_optimize'][$i] = $defect['code_optimize'][0];
		}
		
		return $data;
	}
	
	public function getDefectByMonth($month){
		
		try {
			$date = new DateTime($month);			
		} catch (Exception $e) {
			return null;
		}
		
		$start = $date->format('Y-m-d H:i:s');
		
		$date->modify( 'next month' );
		$end = $date->format('Y-m-d H:i:s');
		
		$defectList = $this->defectModel->getDefect($start , $end);

		$data = array(
				'api' => array(0),
				'performance' => array(0),
				'manual' => array(0),
				'code_optimize' => array(0)
		);
		
		foreach ($defectList as $defect)
		{
			if($defect->type == 1)
			{
				$data['manual'][0] ++;
			} else if($defect->type == 0)
			{
				$data['performance'][0] ++;
			} else if($defect->type == 2)
			{
				$data['api'][0] ++;
			} else if($defect->type == 3)
			{
				$data['code_optimize'][0] ++;
			} 
		}
		
		return  $data;
	}
	
	public function generateDefectStatus(){
	
		
	
		$defectList = $this->defectModel->getDefect();
	
		$data = array(
				'total' => 0,
				'open' => 0,
				'resolve' => 0,
				'close' => 0
		);
	
		foreach ($defectList as $defect)
		{
			if($defect->defect_status == 1)
			{
				$data['resolve'] ++;
			} else if($defect->defect_status == 0)
			{
				$data['open'] ++;
			} else if($defect->defect_status == 2)
			{
				$data['close'] ++;
			}
		}
		
		$data['total'] = $data['open']+$data['resolve']+$data['close'];
		$this->addResponse('message', $data, 200);
		
		$this->display();
	}
	
}
