<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2010
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */



class FormShibbolethCourse extends FormSelectMenu
{

	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$this->arrCourses = preg_split('/;?\{[A-Z\.0-9]+\}/', $_SERVER['umnCourse']);
		
		if (!is_array($this->arrCourses) || count($this->arrCourses) < 1)
		{
			return parent::generate();
		}

		$arrTerms = array
		(
			1 => 'Winter',
			2 => 'Spring',
			3 => 'Summer1',
			4 => 'Summer2',
			5 => 'Fall'
		);

		// Generate options
		foreach ($this->arrCourses as $strCourse)
		{
			if ($strCourse == '')
				continue;
			
			$intYear = substr($strCourse, 0, 2);
			$strTerm = $arrTerms[substr($strCourse, 2, 1)];
			$strDesignator = str_replace('_', '', substr($strCourse, 4, 4));
			$strNumber = str_replace('_', '', substr($strCourse, 8, 5));
			$intSection = substr($strCourse, 13, 3);
			$intLecture = substr($strCourse, 16, 1);

			// Add course
			$this->arrOptions[] = array
			(
				'value' => $strCourse,
				'label' => sprintf('20%s %s - %s%s - section %s - lecture/lab code %s', $intYear, $strTerm, $strDesignator, $strNumber, $intSection, $intLecture)
			);
		}
		
		return parent::generate();
	}
}