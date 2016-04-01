<?php

/** @var string Directory containing all of the site's files */
$root_dir = dirname(__DIR__);

/** @var string Document Root */
$webroot_dir = $root_dir . '/web';

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = new Dotenv\Dotenv($root_dir);
if (file_exists($root_dir . '/.env')) {
	$dotenv->load();
	$dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL']);
}

// Define Environments - may be a string or array of options for an environment
$environments = array(
	'development' 	=> array('.local', 'local.', '192.'),
	'staging' => array('demo.')
);

// Get Server name
$server_name = $_SERVER['SERVER_NAME'];

// Define Constants: Environment
foreach($environments AS $key => $env){
	if ( is_array($env) ) {
		foreach ($env as $option) {
			if ( stristr($server_name, $option) ) {
				define('WP_ENV', $key);
				break;
			}
		}
	} else {
		if ( stristr($server_name, $env) ) {
			define('WP_ENV', $key);
			break;
		}
	}
}

/**
 * Set up our global environment constant and load its config first
 * Default: development
 */
 $env_config = "";
 if (!defined('WP_ENV')) {
	if (file_exists($root_dir . '/.env')) {
		define('WP_ENV', env('WP_ENV') ?: 'development');
		$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';
	} else {
		define('WP_ENV', 'production');
		$env_config = __DIR__ . '/environments/my-config/' . WP_ENV . '.php';
	}
}

if (file_exists($env_config)) {
		require_once $env_config;
}

/* THIS IS CUSTOM CODE CREATED AT ZEROFRACTAL TO MAKE SITE ACCESS DYNAMIC */
$currenthost 	= 'http://' . $_SERVER['HTTP_HOST'];
$currentpath 	= preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME']));
$currentpath 	= preg_replace('/\/wp.+/','',$currentpath);
$siteurl 	= $currenthost . $currentpath;

/**
 * Access Site by Folder
 */
$mylocal = false;
$local_workspace = array('sonang.local', '192.168.0.161');
foreach($local_workspace as $lw) {
	if(strpos($_SERVER['SERVER_NAME'], $lw) !== false) {
		$mylocal = true; 
		break;
	}
}

/**
 * URLs
 */
if (file_exists($root_dir . '/.env')) {
	define('WP_HOME', env('WP_HOME'));
	define('WP_SITEURL', env('WP_SITEURL'));
} else {
	if ( ! (WP_ENV === 'production') && $mylocal ) { // Put server name here
		define('WP_HOME', $siteurl);
		define('WP_SITEURL', $siteurl . '/wp'); // no alias
	} else {
		define('WP_HOME', $currenthost);
		define('WP_SITEURL', WP_HOME . '/wp');
	}
}
 
/**
 * Custom Content Directory
 */
define('CONTENT_DIR', '/app');
define('WP_CONTENT_DIR', $webroot_dir . CONTENT_DIR);
define('WP_CONTENT_URL', WP_HOME . CONTENT_DIR);

/**
 * Authentication Unique Keys and Salts
 * https://api.wordpress.org/secret-key/1.1/salt/
 */
define('AUTH_KEY',         '');
define('SECURE_AUTH_KEY',  '');
define('LOGGED_IN_KEY',    '');
define('NONCE_KEY',        '');
define('AUTH_SALT',        '');
define('SECURE_AUTH_SALT', '');
define('LOGGED_IN_SALT',   '');
define('NONCE_SALT',       '');

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
		define('ABSPATH', $webroot_dir . '/wp/');
}

/**
 * DEBUG
 */
// print_r($_SERVER) ;
// echo WP_ENV . "<br>";
// echo WP_HOME . "<br>";
// echo WP_SITEURL . "<br>";
// echo WP_CONTENT_DIR . "<br>";
// echo WP_CONTENT_URL . "<br>";