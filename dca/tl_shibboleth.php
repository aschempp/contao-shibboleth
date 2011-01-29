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


/**
 * System configuration for Shibboleth authentication
 */
$GLOBALS['TL_DCA']['tl_shibboleth'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'			=> 'File',
		'closed'				=> true,
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'			=> array('shibForceBackend'),
		'default'				=> '{url_legend},shibLoginURL,shibLogoutURL,shibSSL;{backend_legend},shibForceBackend',
	),
	
	// Subpalettes
	'subpalettes' => array
	(
		'shibForceBackend'		=> 'shibForceHosts',
	),

	// Fields
	'fields' => array
	(
		'shibLoginURL' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_shibboleth']['shibLoginURL'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'url', 'decodeEntities'=>true, 'tl_class'=>'long'),
		),
		'shibLogoutURL' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_shibboleth']['shibLogoutURL'],
			'inputType'			=> 'text',
			'eval'				=> array('mandatory'=>true, 'rgxp'=>'url', 'decodeEntities'=>true, 'tl_class'=>'long'),
		),
		'shibSSL' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_shibboleth']['shibSSL'],
			'inputType'			=> 'checkbox',
			'eval'				=> array('isBoolean'=>true, 'tl_class'=>'clr'),
		),
		'shibForceBackend' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_shibboleth']['shibForceBackend'],
			'inputType'			=> 'checkbox',
			'eval'				=> array('isBoolean'=>true, 'submitOnChange'=>true),
		),
		'shibForceHosts' => array
		(
			'label'				=> &$GLOBALS['TL_LANG']['tl_shibboleth']['shibForceHosts'],
			'inputType'			=> 'textarea',
			'eval'				=> array('style'=>'height:40px', 'tl_class'=>'clr'),
		),
	)
);

