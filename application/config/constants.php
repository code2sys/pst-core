<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


if (!defined('FOOTER_PAGE_LIMIT')) {
    define('FOOTER_PAGE_LIMIT', 8);
}

if (!defined('TOP_LEVEL_CAT_SNOW')) {
    define('TOP_LEVEL_CAT_SNOW', -1);
}
if (!defined('TOP_LEVEL_PAGE_ID_SNOW')) {
    define('TOP_LEVEL_PAGE_ID_SNOW', -1);
}

if (!defined('LIGHTSPEED_PST_USERNAME')) {
    define('LIGHTSPEED_PST_USERNAME', 'PowersportsTechnologies');
}

if (!defined('LIGHTSPEED_PST_PASSWORD')) {
    define('LIGHTSPEED_PST_PASSWORD', 'myoTm0s01AqlfnR');
}

if (!defined('LIGHTSPEED_PST_LICENSE_KEY')) {
    define('LIGHTSPEED_PST_LICENSE_KEY', 'PowersportsTechnologies');
}

if (!defined('ENABLE_CRM')) {
    define('ENABLE_CRM', false);
}

/* End of file constants.php */
/* Location: ./application/config/constants.php */