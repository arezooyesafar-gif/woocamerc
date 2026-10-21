<?php
/**
 * Plugin Name:       Advanced Database Cleaner - Premium
 * Plugin URI:        https://sigmaplugin.com/downloads/wordpress-advanced-database-cleaner
 * Description:       The most advanced Database Cleaner for WordPress. Clean database by deleting orphaned items such as old "revisions", "old drafts", optimize database, and more.
 * Version:           4.1.1
 * Author:            SigmaPlugin
 * Author URI:        https://sigmaplugin.com
 * Contributors:      symptote
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://sigmaplugin.com/downloads/wordpress-advanced-database-cleaner
 * Text Domain:       advanced-database-cleaner
 * Domain Path:       /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

// Return if not in main site
if ( ! is_main_site() )
	return;
require_once('loader.php');