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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_shibboleth']['shibLoginURL']		= array('Login URL', 'The URL to your login gateway. Defaults to "Shibboleth.sso/Login" on the active domain.');
$GLOBALS['TL_LANG']['tl_shibboleth']['shibLogoutURL']		= array('Logout URL', 'The URL to your logout gateway. Defaults to "Shibboleth.sso/Logout" on the active domain.');
$GLOBALS['TL_LANG']['tl_shibboleth']['shibSSL']				= array('SSL authentication', 'Check if SSL should be used for login.');
$GLOBALS['TL_LANG']['tl_shibboleth']['shibForceBackend']	= array('Force backend login', 'Check here if users should automatically be redirected to the shibboleth authentication if they try to access the backend.');
$GLOBALS['TL_LANG']['tl_shibboleth']['shibForceHosts']		= array('Shibboleth Hosts', 'Please enter a comma separated list of host names/domains to force backend login for.');
$GLOBALS['TL_LANG']['tl_shibboleth']['shibInsertTags']		= array('Insert tags mapper', 'Here you can enter the insert tags that will be used to map.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_shibboleth']['edit']					= 'Configure Shibboleth authentication for this system';
$GLOBALS['TL_LANG']['tl_shibboleth']['shibInsertTags']['key']	= array('Key');
$GLOBALS['TL_LANG']['tl_shibboleth']['shibInsertTags']['value']	= array('Value');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_shibboleth']['url_legend']			= 'URLs';
$GLOBALS['TL_LANG']['tl_shibboleth']['backend_legend']		= 'Backend Configuration';
$GLOBALS['TL_LANG']['tl_shibboleth']['inserttags_legend']	= 'InsertTags';

