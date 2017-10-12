<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['404_override'] = '';  
$route['Motorcycle_Gear_Brands'] = 'shopping/brands';
$route['Motorcycle_List'] = 'motorcycle_ci/benzProduct';
$route['motorcycle/([a-zA-z0-9_-]+)'] = 'motorcycle_ci/benzDetails/$1/$2';
$route['streetbikeparts'] = 'streetbikeparts/index';
$route['dirtbikeparts'] = 'dirtbikeparts/index';
$route['vtwin'] = 'vtwin/index';
$route['marine'] = 'marine/index';
$route['checkout'] = 'checkout/index';

$route['atvparts'] = 'atvparts/index';
$route['utvparts'] = 'utvparts/index';
$route['admin'] = 'admin/index';

$route["streetbikepart"] = "streetbikeparts/redirectToHome";
$route["dirtbikepart"] = "dirtbikeparts/redirectToHome";
$route["shopping/productlist/dirt-bike-parts"] = "dirtbikeparts/redirectToHome";
$route["shopping/productlist/dirt-bike-parts_dirt-bike-parts"] = "dirtbikeparts/redirectToHome";
$route["shopping/productlist/street-bike-parts"] = "streetbikeparts/redirectToHome";
$route["shopping/productlist/v-twin-parts_v-twin-parts"] = "vtwin/redirectToHome";
$route["shopping/productlist/v-twin-parts"] = "vtwin/redirectToHome";
$route["shopping/productlist/atv-parts"] = "atvparts/redirectToHome";
$route["shopping/productlist/utv-parts"] = "utvparts/redirectToHome";

$route['atv/(:any)/(:any)'] = 'motorcycle_ci/benzDetails/$1/$2';
$route['utv/(:any)/(:any)'] = 'motorcycle_ci/benzDetails/$1/$2';
$route['streetbike/(:any)/(:any)'] = 'motorcycle_ci/benzDetails/$1/$2';
$route['dirtbike/(:any)/(:any)'] = 'motorcycle_ci/benzDetails/$1/$2';
$route['watercraft/(:any)/(:any)'] = 'motorcycle_ci/benzDetails/$1/$2';
$route['snowmobile/(:any)/(:any)'] = 'motorcycle_ci/benzDetails/$1/$2';
$route['utility/(:any)/(:any)'] = 'motorcycle_ci/benzDetails/$1/$2';

$route['vault'] = 'vault/index';

$route['([a-zA-z0-9_-]+)'] = "shopping/brand/$1/$2";
//$route['atvparts'] = 'atvparts/index';
//$route['streetbikeparts'] = 'streetbikeparts/index';
//$route['utvparts'] = 'utvparts/index';
//$route['welcome/new_account'] = 'welcome/new_account';
//$route['(:any)/(:any)'] = "shopping/item/$1/$2";
//$route['pardy'] = "shopping/item/$1";

/* End of file routes.php */
/* Location: ./application/config/routes.php */