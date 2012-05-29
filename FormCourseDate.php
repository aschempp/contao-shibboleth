<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *
 * PHP version 5
 * @copyright  University of Minnesota 2012
 * @author     Andreas Schempp <andreas.schempp@iserv.ch>
 * @author     Jan Reuteler <jan.reuteler@iserv.ch>
 * @license    commercial
 */


class FormCourseDate extends FormTextField
{

	public function __construct($arrAttributes=false)
	{
		parent::__construct($arrAttributes);
		
		$this->rgxp = 'date';
	}
	
	public function generate()
	{				
		$strBuffer = parent::generate();
		
		if ($this->readonly || $this->disabled)
			return $strBuffer;
		
		$GLOBALS['TL_CSS'][] = 'plugins/calendar/css/calendar.css';
		$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/calendar/js/calendar.js';
		
		// before navigation_ blocks: [],
		$blockedDays = array(
			date('j n Y', strtotime('+1 days')),
			date('j n Y', strtotime('+2 days')),
			date('j n Y', strtotime('+3 days')),
			date('j n Y', strtotime('+4 days')),
			date('j n Y', strtotime('+5 days')),
			date('j n Y', strtotime('+6 days'))
		);	  
				
		$nextYear = strtotime('+1 day +1 year');
		$blockedDays[] = date('j-31 n Y', $nextYear);
		$nextYear = strtotime('+1 month', $nextYear);
		$month = date('n', $nextYear);
		
		while( $month < 12 )
		{
			$blockedDays[] = date('1-31 n Y', $nextYear);
			$nextYear = strtotime('+1 month', $nextYear);
			$month = date('n', $nextYear);
		}
		
		$blockedDays[] = date('1-31 * Y-2100', strtotime('+2 year'));
		
		$strBuffer .= "<script type=\"text/javascript\"><!--//--><![CDATA[//><!--
  window.addEvent('domready', function() { new Calendar({ ctrl_" . $this->strId . ": '" . $GLOBALS['TL_CONFIG']['dateFormat'] . "' }, { blocked: ['" . implode("','", $blockedDays) . "'], navigation: 2, days: ['" . implode("','", $GLOBALS['TL_LANG']['DAYS']) . "'], months: ['" . implode("','", $GLOBALS['TL_LANG']['MONTHS']) . "'], offset: ". intval($GLOBALS['TL_LANG']['MSC']['weekOffset']) . ", titleFormat: '" . $GLOBALS['TL_LANG']['MSC']['titleFormat'] . "', direction: 1}); });
  //--><!]]></script>";
  
  		return $strBuffer;
	}
	
	
	public function validator($varInput)
	{			
		parent::validator($varInput);
		
		if (!$this->hasErrors()) 
		{
			$objDate = new Date($varInput, $GLOBALS['TL_CONFIG']['dateFormat']);
			$timestamp = $objDate->tstamp;
						
			$minimumTimestamp = strtotime("+7 days 00:00:00");
			$maximumTimestamp = strtotime("+1 year 00:00:00");		
			
			if ($timestamp < $minimumTimestamp || $timestamp > $maximumTimestamp) 
			{
				$this->addError( sprintf( $GLOBALS['TL_LANG']['ERR']['courseDate'],
										$this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $minimumTimestamp),
										$this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $maximumTimestamp) ));
			}	
						
		}
		
		return $varInput;
	}
}

