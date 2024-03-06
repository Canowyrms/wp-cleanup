<?php
/**
 * @wordpress-plugin
 * Plugin Name: WP Cleanup
 * Plugin URI:  http://TODO.dev/
 * Description: TODO
 * Version:     0.0.0
 * Author:      TODO
 * Author URI:  TODO
 * License:     TODO
 * License URI: TODO
 */

declare(strict_types=1);

namespace BK\WPCleanup;

use BK\WPCleanup\Hooks\{
	Activator,
	Deactivator,
	Uninstaller
};
//

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Plugin constants
 */

//define('WPCLEANUP_CONST', '');

/**
 * Plugin hooks
 */

//@fmt:off
//register_activation_hook(  __FILE__, fn () => Activator::init());   // TODO - Plugin activator
//register_deactivation_hook(__FILE__, fn () => Deactivator::init()); // TODO - Plugin deactivator
//register_uninstall_hook(   __FILE__, fn () => Uninstaller::init()); // TODO - Plugin uninstaller
//@fmt:on

/**
 * Boot up
 */

$wpcleanup = new WPCleanup();

/**
 * Testing
 */
