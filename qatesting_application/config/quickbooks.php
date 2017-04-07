<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Turn on some error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Turn off auto-loading (possible conflict with CI?)
if (!defined('QUICKBOOKS_LOADER_AUTOLOADER'))
{
	define('QUICKBOOKS_LOADER_AUTOLOADER', FALSE); // Edited as per LOADER file
}

// A username and password you'll use in: 
//	a) Your .QWC file
//	b) The Web Connector
//	c) The QuickBooks framework
//
// 	NOTE: This has *no relationship* with QuickBooks usernames, Windows usernames, etc. 
// 		It is *only* used for the Web Connector and SOAP server! 
$config['quickbooks_user'] = 'quickbooks';
$config['quickbooks_pass'] = 'patches123';

// Time zone that QuickBooks is in
$config['quickbooks_tz'] = 'America/Boise';

//$config['quickbooks_loglevel'] = QUICKBOOKS_LOG_NORMAL;
//$config['quickbooks_loglevel'] = QUICKBOOKS_LOG_VERBOSE;
//$config['quickbooks_loglevel'] = QUICKBOOKS_LOG_DEBUG;				
$config['quickbooks_loglevel'] = QUICKBOOKS_LOG_DEVELOP;

//  Memory limit
$config['quickbooks_memorylimit'] = '512M';
