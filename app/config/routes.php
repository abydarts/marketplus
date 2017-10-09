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

$route['default_controller']	= "home";
$route['404_override'] = 'not_found';

//this for the admininstration console
$route['admin']					= 'admin/dashboard';
$route['admin/media/(:any)']		= 'admin/media/$1';
				// Admin media console
									// Contact
	$route['login']					= 'user/login';			// login 
	$route['logout']					= 'user/logout';			// logout
	$route['register']				= 'user/register';			// Register 
	$route['dashboard']				= 'user/my_account';			// Dashboard 
	$route['update']				= 'user/update_account';			// Dashboard 
	$route['downloads']				= 'user/my_downloads';			// Dashboard 
	
	$route['deposit']				= 'user/my_deposit';			// Dashboard 
	$route['order-history']				= 'user/order_history';			// Dashboard 
	$route['wishlist']				= 'user/wishlist';			// Dashboard 
	$route['withdraw']				= 'user/my_withdraw';			// Dashboard 
	$route['MyBalance']				= 'user/my_balance';			// Dashboard 
	$route['add-item']				= 'products/form';			// Dashboard 
	$route['MyUploads']				= 'user/my_uploads';			// Dashboard 
	$route['profile/(:any)']				= 'user/profile/$1';			// Dashboard 
	
	$route['TopAuthors']				= 'user/top_authors';			// Dashboard 
	$route['TopProducts']				= 'products/top_products';			// Dashboard 
	$route['all']				= 'cart/category';			// Dashboard 
	//$route['all/(:any)']				= 'cart/category/$1';			// Dashboard 
	$route['Authors']				= 'user/all_authors';			// Dashboard
	$route['LastAdded']				= 'cart/category/$1/new';			// Dashboard
	$route['ZeroDownloads']				= 'cart/category/$1/zero';			// Dashboard
	$route['updates']	= "secure/updates"; //updates
	$route['updates/(:any)'] = "secure/details/$1"; //updates details
	$route['blog']	= "secure/blog"; //blogs
	$route['unsubscribe/(:any)'] = "home/unsubscribe/$1"; //unsubscribe details
	$route['blog/(:any)'] = "secure/blog_details/$1"; //blogs details
	$route['Demo/(:any)'] = "products/live_demo/$1"; //blogs details
	$route['All/(:any)'] = "cart/search/$1"; //blogs details
	$route['forum']	= "forum/index"; //forum
	$route['forums/(:any)'] = "forum/forum_details/$1"; //forum details
	$route['forum/search/'] = "forum/forum_search/"; //forum details
	$route['forum/category/(:any)']		= 'forum/forum_cat/$1';				// forum 

	$route['Cart']					= 'cart/view_cart';	
	$route['Confirm']					= 'checkout/step_4';	
	$route['ChangePay']					= 'checkout/step_3';	
	$route['StepTwo'] 					= 'checkout/step_2';
	$route['Search/(:any)'] 					= 'cart/search/$1/';
	$route['TagSearch/(:any)'] 					= 'cart/keysearch/$1/$1';
	//$route['blg']					= 'blogscat';
	//$route['blg']					= 'blogsdetail';
	$route['blogscat/(:any)']					= 'blogs/blog_cat/$1';
	$route['blogsdetail/(:any)']					= 'blogs/blog_details/$1';
	$route['sitemap']					= 'home/sitemap';
	
