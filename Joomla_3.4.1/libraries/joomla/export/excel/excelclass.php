<?php
/**
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;
require('PHPExcel.php');
/**
 * A Folder handling class
 *
 * @since  11.1
 */
class JExcelClass
{
	/**
	 * Create a new PHPExcel with one Worksheet
	 */
	public function __construct(){
		
	}
	
	public function getPhpExcel(){
		
		$phpExcel = new PHPExcel();
		return $phpExcel;
	}
}
