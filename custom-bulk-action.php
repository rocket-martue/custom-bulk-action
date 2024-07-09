<?php
/**
 * Plugin Name: Custom Bulk Action
 * Description: 各カスタム投稿タイプにカスタムバルクアクションを追加します。
 * Version: 1.1.1
 * Author: Rocket Martue
 * Text Domain: custom-bulk-action
 * @package CustomBulkAction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-bulk-action.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bulk-action-handler.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-bulk-action-admin.php';

CustomBulkAction\CustomBulkAction::get_instance();
CustomBulkAction\CustomBulkActionAdmin::get_instance();
