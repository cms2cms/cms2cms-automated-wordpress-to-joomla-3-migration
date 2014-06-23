<?php
/**
 * @copyright	Copyright (C) 2013 - 2014 Cms2Cms. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.legacy.controller');

class CmsController extends JControllerLegacy
{
    function getAuth()
	{
   	    $dataProvider = new CmsPluginData();
		$response = $dataProvider->getOptions();

		echo json_encode($response);
		die(); // this is required to return a proper result
	}
	
	function saveAuth()
	{
		$dataProvider = new CmsPluginData();
		$response = $dataProvider->saveOptions();
		
		echo json_encode($response);
		die(); // this is required to return a proper result
	}
	
	function clearAuth()
	{
		$dataProvider = new CmsPluginData();
		$response = $dataProvider->clearOptions();
		
		parent::display();
	}

}
