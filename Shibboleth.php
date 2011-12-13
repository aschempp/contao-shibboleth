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


class Shibboleth extends Controller
{

	public function authenticateBackend($strBuffer)
	{
		$script = basename($this->Environment->script);
		$backend = (version_compare(VERSION, '2.9', '<') ? 'typolight/' : 'contao/');
		
		if ($script == 'index.php')
		{
			if (!$this->sessionActive() && $GLOBALS['TL_CONFIG']['shibForceBackend'])
			{
				if ($GLOBALS['TL_CONFIG']['shibForceBackend'] != '')
				{
					$arrDomains = trimsplit(',', $GLOBALS['TL_CONFIG']['shibForceHosts']);

					if (!in_array($this->Environment->host, $arrDomains))
					{
						return $strBuffer;
					}
				}

				$strUrl = ($GLOBALS['TL_CONFIG']['shibSSL'] ? str_replace('http://', 'https://', $this->Environment->base) : $this->Environment->base) . $backend . 'index.php';

				$this->redirect($GLOBALS['TL_CONFIG']['shibLoginURL'] . '?target=' . urlencode($strUrl));
			}
			elseif ($this->Input->get('logout') && $this->sessionActive())
			{
				$this->redirect($GLOBALS['TL_CONFIG']['shibLogoutURL']);
			}
			elseif ($this->sessionActive())
			{
				$this->import('Database');

				$eppn = explode('@', $_SERVER['eppn']);

				$objUser = $this->Database->prepare("SELECT * FROM tl_user WHERE username=?")->limit(1)->execute($eppn[0]);

				if ($objUser->numRows && $this->login($objUser))
				{
					$strUrl = $backend . 'main.php';

					// Redirect to last page visited
					if (strlen($this->Input->get('referer', true)))
					{
						$strUrl = base64_decode($this->Input->get('referer', true));
					}

					$this->redirect($strUrl);
				}
			}
		}
		elseif ($script == 'main.php')
		{
			return str_replace($backend.'index.php', $backend.'index.php?logout=1', $strBuffer);
		}

		return $strBuffer;
	}


	public function replaceTags($strTag)
	{
		list($tag, $key) = explode('::', $strTag);

		if ($tag == 'shibboleth')
		{
			$arrMapper = deserialize($GLOBALS['TL_CONFIG']['shibInsertTags'], true);

			foreach ($arrMapper as $arrItem)
			{
				if ($arrItem['key'] == $key)
				{
					return trim($_SERVER[$arrItem['value']]);
				}
			}

			return '';
		}

		return false;
	}


	public function authenticateFrontend($blnForce=false)
	{
		if ($this->sessionActive())
		{
			$this->import('Database');
			
			$eppn = explode('@', $_SERVER['eppn']);
			
			$objUser = $this->Database->prepare("SELECT * FROM tl_member WHERE username=?")->limit(1)->execute($eppn[0]);
			
			if (!$objUser->numRows)
			{
				$_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
				return false;
			}
			elseif ($this->login($objUser, 'tl_member', 'FE_USER_AUTH'))
			{
				return $objUser;
			}
			
			return false;
		}
		elseif ($blnForce)
		{
			$strUrl = ($GLOBALS['TL_CONFIG']['shibSSL'] ? str_replace('http://', 'https://', $this->Environment->base) : $this->Environment->base) . $this->Environment->request . (strpos($this->Environment->request, '?') === false ? '?' : '&') . 'shibauth=1';
			$this->redirect($GLOBALS['TL_CONFIG']['shibLoginURL'] . '?target=' . urlencode($strUrl));
		}
		
		return false;
	}
	
	
	private function login($objUser, $strTable='tl_user', $strCookie='BE_USER_AUTH')
	{
		$time = time();

		$blnAccountError = false;

		// Check whether account is locked
		if (($objUser->locked + $GLOBALS['TL_CONFIG']['lockPeriod']) > $time)
		{
			$blnAccountError = true;
			$_SESSION['TL_ERROR'][] = sprintf($GLOBALS['TL_LANG']['ERR']['accountLocked'], ceil((($objUser->locked + $GLOBALS['TL_CONFIG']['lockPeriod']) - $time) / 60));
		}

		// Check whether account is disabled
		elseif ($objUser->disable)
		{
			$blnAccountError = true;

			$_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
			$this->log('The account has been disabled', get_class($this) . ' login()', TL_ACCESS);
		}

		// Check wether login is allowed (front end only)
		elseif ($strTable == 'tl_member' && !$objUser->login)
		{
			$blnAccountError = true;

			$_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
			$this->log('User "' . $objUser->username . '" is not allowed to log in', 'Shibboleth login()', TL_ACCESS);
		}

		// Check whether account is not active yet or anymore
		elseif (strlen($objUser->start) || strlen($objUser->stop))
		{
			if (strlen($objUser->start) && $objUser->start > $time)
			{
				$blnAccountError = true;

				$_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
				$this->log('The account was not active yet (activation date: ' . $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objUser->start) . ')', 'Shibboleth login()', TL_ACCESS);
			}

			if (strlen($objUser->stop) && $objUser->stop < $time)
			{
				$blnAccountError = true;

				$_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
				$this->log('The account was not active anymore (deactivation date: ' . $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objUser->stop) . ')', 'Shibboleth login()', TL_ACCESS);
			}
		}

		// Redirect to login screen if there is an error
		if ($blnAccountError)
		{
			return false;
		}

		// Save user
		$arrSet = array();
		$arrSet['loginCount'] = 3;
		$arrSet['lastLogin'] = $objUser->currentLogin;
		$arrSet['currentLogin'] = $time;
		$this->Database->prepare("UPDATE $strTable %s WHERE id=?")->set($arrSet)->execute($objUser->id);

		// Generate hash
		$strHash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? $this->Environment->ip : '') . $strCookie);

		// Clean up old sessions
		$this->Database->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")
					   ->execute(($time - $GLOBALS['TL_CONFIG']['sessionTimeout']), $strHash);

		// Save session in the database
		$this->Database->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
					   ->execute($objUser->id, $time, $strCookie, session_id(), $this->Environment->ip, $strHash);

		// Set authentication cookie
		$this->setCookie($strCookie, $strHash, ($time + $GLOBALS['TL_CONFIG']['sessionTimeout']), $GLOBALS['TL_CONFIG']['websitePath']);

		// Add login status for cache
		$_SESSION['TL_USER_LOGGED_IN'] = true;

		// Add a log entry
		$this->log('User "' . $objUser->username . '" has logged in', 'Shibboleth login()', TL_ACCESS);
		return true;
	}
	
	
	private function sessionActive()
	{
		if ($_SERVER['Shib-Session-ID'] == '' && $_SERVER['HTTP_SHIB_IDENTITY_PROVIDER'] == '')
			return false;
		
		return true;
	}
}

