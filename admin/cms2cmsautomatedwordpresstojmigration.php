<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * components/com_hello/hello.php
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

define('CMS2CMS_VERSION', '1.0.0');

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Require the base controller

require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'controller.php' );
include JPATH_COMPONENT_ADMINISTRATOR . DS  . 'data.php';
include JPATH_COMPONENT_ADMINISTRATOR . DS  . 'view.php';

// Create the controller
$classname    = 'CmsController';
$controller   = new $classname( );
 
// Perform the Request task
$controller->execute( JRequest::getWord( 'task' ) );
 
// Redirect if set by the controller
$controller->redirect();
