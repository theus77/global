<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 */


	$language = substr(Router::url(''), 1, 2);
	$languages = array_keys(Configure::read('Config.languages'));
	
	if(!in_array($language, $languages)) {
		if(preg_match('/language:(fr|nl|en)/', Router::url(''), $matches)){
			$language = $matches[1];
		}
		else {
			$language = DEFAULT_LANGUAGE;
		}
	}

	Configure::write('Config.language', $language);/**/

	//Configure::write('Routing.prefixes', array('admin'));
	
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	//homepage en fr
	Router::connect( '/', array('controller' => 'pages', 'action' => 'home', 'language' => 'fr'));

	//homepages dans les différentds langues
	Router::connect('/:language', 
			array(
				'controller' => 'pages',
				'action' => 'home',
			),
			array('language' => '[a-z]{2}'));

	//to prefix all other url with the language
	Router::connect('/:language/:controller/:action/*',
			array(),
			array('language' => '[a-z]{2}'));
	
	
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
// 	Router::connect( '/fr/nos-services', array('controller' => 'pages', 'action' => 'display', 'our-services', 'language' => 'fr'));
// 	Router::connect( '/en/our-services', array('controller' => 'pages', 'action' => 'display', 'our-services', 'language' => 'en'));
// 	Router::connect( '/nl/onze-diensten', array('controller' => 'pages', 'action' => 'display', 'our-services', 'language' => 'nl'));
	
// 	Router::connect( '/fr/recherche/*', array('controller' => 'pages', 'action' => 'display', 'advanced-search', 'language' => 'fr'));
// 	Router::connect( '/en/search/*', array('controller' => 'pages', 'action' => 'display', 'advanced-search', 'language' => 'en'));
// 	Router::connect( '/nl/zoeken/*', array('controller' => 'pages', 'action' => 'display', 'advanced-search', 'language' => 'nl'));
	
// 	Router::connect( '/fr/phototheque', array('controller' => 'galleries', 'action' => 'library', 'language' => 'fr'));
// 	Router::connect( '/en/library', array('controller' => 'galleries', 'action' => 'library', 'language' => 'en'));
// 	Router::connect( '/nl/bibliotheek', array('controller' => 'galleries', 'action' => 'library', 'language' => 'nl'));
	
// 	Router::connect( '/fr/galerie/*', array('controller' => 'galleries', 'action' => 'view', 'language' => 'fr'));
// 	Router::connect( '/en/gallery/*', array('controller' => 'galleries', 'action' => 'view', 'language' => 'en'));
// 	Router::connect( '/nl/galerij/*', array('controller' => 'galleries', 'action' => 'view', 'language' => 'nl'));
	
// 	Router::connect( '/fr/sujet/*', array('controller' => 'aperture', 'action' => 'image', 'language' => 'fr'));
// 	Router::connect( '/en/subject/*', array('controller' => 'aperture', 'action' => 'image', 'language' => 'en'));
// 	Router::connect( '/nl/onderwerp/*', array('controller' => 'aperture', 'action' => 'image', 'language' => 'nl'));
	
// 	Router::connect( '/fr/emplacements', array('controller' => 'aperture', 'action' => 'locations', 'language' => 'fr'));
// 	Router::connect( '/en/locations', array('controller' => 'aperture', 'action' => 'locations', 'language' => 'en'));
// 	Router::connect( '/nl/locaties', array('controller' => 'aperture', 'action' => 'locations', 'language' => 'nl'));
	
// 	Router::connect( '/fr/lieu/*', array('controller' => 'aperture', 'action' => 'place', 'language' => 'fr'));
// 	Router::connect( '/en/place/*', array('controller' => 'aperture', 'action' => 'place', 'language' => 'en'));
// 	Router::connect( '/nl/plaats/*', array('controller' => 'aperture', 'action' => 'place', 'language' => 'nl'));
	
	Router::connect( '/login', array('controller' => 'users', 'action' => 'login'));
	Router::connect( '/logout', array('controller' => 'users', 'action' => 'logout'));
	
	Router::connect( '/img/:language/:uuid/thumb.jpg', array(
		'controller' => 'img', 
		'action' => 's3',
		'resize' => 'fill',
		'width' => '320',
		'quality' => 50,
		'height' => '320',
		'route' => 'thumb.jpg',
// 		'radius' => '3',
		'background' => 'ffffff'
	), array('pass' => array('uuid')));	
	
	Router::connect( '/img/:language/:key/image.jpg', array(
		'controller' => 'img',
		'action' => 's3',
		'resize' => 'fill',
		'height' => '800',
		'width' => '1200',
		'watermark' => WWW_ROOT.'img/watermark.png',
		'quality' => 75,
		'route' => 'image.jpg',
		'background' => '000000'
	), array('pass' => array('key')));	
	
	Router::connect( '/img/:language/:key/preview.jpg', array(
		'controller' => 'img',
		'action' => 's3',
		'resize' => 'fillArea',
		'height' => '200',
		'width' => '300',
		'quality' => 50,
		'route' => 'preview.jpg',
	), array('pass' => array('key')));	
	
	
	/*Router::connect( '/img/thumbnails/:language/*', array(
		'controller' => 'img', 
		'action' => 's3',
		'resize' => 'fill',
		'width' => '320',
		'quality' => 50,
		'height' => '320',
		'radius' => '3',
		'background' => 'dbdbdb'
	));	
	
	Router::connect( '/img/previews/*', array( 
		'controller' => 'img', 
		'action' => 's3',
		'resize' => 'fillArea',
		'watermark' => WWW_ROOT.'img/watermark.png',
		'height' => '900',
		'width' => '1600',
	));	
	
	Router::connect( '/img/:key/thumb', array(
		'controller' => 'img',
		'action' => 's3',
		'resize' => 'fillArea',
		'height' => '200',
		'width' => '300',
	), array('pass' => array('key')));
	
	Router::connect( '/img/:key/preview', array(
		'controller' => 'img',
		'action' => 's3',
		'resize' => 'fill',
		'height' => '1400',
		'width' => '2100',
		'quality' => 4,
		'background' => '000000'
	), array('pass' => array('key')));
	

	
	Router::connect( '/img/:uuid/banner.png', array(
		'controller' => 'img',
		'action' => 's3',
		'resize' => 'fillArea',
		'height' => '400',
		'width' => '2100',
	), array('pass' => array('key')));
	
	Router::connect( '/img/:uuid/teaser.png', array(
		'controller' => 'img',
		'action' => 's3',
		'resize' => 'fillArea',
		'height' => '1200',
		'watermark' => WWW_ROOT.'img/watermark.png',
		'width' => '2100',
	), array('pass' => array('key')));
	
	Router::connect( '/img/:uuid/marketing.png', array(
		'controller' => 'img',
		'action' => 's3',
		'resize' => 'fillArea',
		'height' => '500',
		'width' => '500',
	), array('pass' => array('uuid')));*/
	
	
	Router::parseExtensions('json');
	
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
